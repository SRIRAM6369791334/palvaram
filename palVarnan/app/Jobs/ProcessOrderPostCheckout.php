<?php

namespace App\Jobs;

use App\Mail\OrderPlacedConfirmation;
use App\Models\ProductOrder;
use App\Services\ShiprocketService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ProcessOrderPostCheckout implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 60;
    public array $backoff = [10, 30, 60];

    public function __construct(
        public int $orderId,
        public ?int $selectedCourierId = null
    ) {}

    public function handle(): void
    {
        $order = ProductOrder::with(['items.variant', 'items.product', 'user'])->find($this->orderId);
        if (!$order) {
            return;
        }

        // 1. Shiprocket Order & AWB Integration (idempotent: skip if already created)
        if (empty($order->shiprocket_order_id)) {
            try {
                $shiprocket = new ShiprocketService();

                $orderItems = [];
                $totalWeight = 0;
                $maxLength = 0;
                $maxBreadth = 0;
                $maxHeight = 0;

                foreach ($order->items as $item) {
                    $variant = $item->variant;
                    $product = $item->product;

                    $orderItems[] = [
                        'name' => trim(($item->product_name ?? 'Product') . ' ' . ($item->variant_name ?? '')),
                        'sku' => $variant->sku ?? $product->slug ?? 'SKU001',
                        'units' => (int) $item->quantity,
                        'selling_price' => (float) $item->price,
                        'discount' => 0,
                        'tax' => (float) ($item->gst_rate ?? 0),
                        'hsn' => 441122,
                    ];

                    $itemWeight = (float) ($variant->weight ?? 0.5);
                    $totalWeight += $itemWeight * (int) $item->quantity;
                    $maxLength = max($maxLength, (float) ($variant->length ?? 10));
                    $maxBreadth = max($maxBreadth, (float) ($variant->breadth ?? 10));
                    $maxHeight = max($maxHeight, (float) ($variant->height ?? 10));
                }

                $totalWeight = max($totalWeight, 0.5);
                $maxLength = max($maxLength, 10);
                $maxBreadth = max($maxBreadth, 10);
                $maxHeight = max($maxHeight, 10);

                $billingParts = explode(' ', trim((string) $order->shipping_name));
                $lastName = count($billingParts) > 1 ? array_pop($billingParts) : 'Customer';
                $firstName = count($billingParts) > 0 ? implode(' ', $billingParts) : (string) $order->shipping_name;

                $shiprocketOrderData = [
                    'order_id' => $order->order_number,
                    'order_date' => $order->created_at ? $order->created_at->format('Y-m-d H:i') : date('Y-m-d H:i'),
                    'pickup_location' => config('services.shiprocket.pickup_location', 'Home'),
                    'billing_customer_name' => $firstName,
                    'billing_last_name' => $lastName,
                    'billing_address' => trim(($order->shipping_door_no ?? '') . ' ' . ($order->shipping_street ?? '')),
                    'billing_address_2' => $order->shipping_area ?: '-',
                    'billing_city' => $order->shipping_city,
                    'billing_pincode' => $order->shipping_pincode,
                    'billing_state' => $order->shipping_state,
                    'billing_country' => 'India',
                    'billing_email' => $order->shipping_email,
                    'billing_phone' => substr(preg_replace('/[^0-9]/', '', (string) $order->shipping_phone), -10),
                    'shipping_is_billing' => true,
                    'order_items' => $orderItems,
                    'payment_method' => $order->payment_method === 'Online Payment' ? 'Prepaid' : 'COD',
                    'shipping_charges' => (float) ($order->shipping_charge ?? 0),
                    'giftwrap_charges' => 0,
                    'transaction_charges' => 0,
                    'total_discount' => (float) ($order->coupon_discount ?? 0),
                    'sub_total' => (float) ($order->subtotal ?? 0) + (float) ($order->gst_amount ?? 0),
                    'length' => $maxLength,
                    'breadth' => $maxBreadth,
                    'height' => $maxHeight,
                    'weight' => $totalWeight,
                ];

                Log::info('Creating Shiprocket order (async job)', ['order_id' => $order->order_number]);
                $shipResponse = $shiprocket->createOrder($shiprocketOrderData);

                if (isset($shipResponse['order_id']) && isset($shipResponse['shipment_id'])) {
                    $shipmentId = $shipResponse['shipment_id'];
                    $order->update([
                        'shiprocket_order_id' => $shipResponse['order_id'],
                        'shiprocket_shipment_id' => $shipmentId,
                        'shiprocket_status' => $shipResponse['status'] ?? 'NEW',
                    ]);

                    if ($this->selectedCourierId) {
                        $assignResponse = $shiprocket->assignAWB([
                            'shipment_id' => $shipmentId,
                            'courier_id' => $this->selectedCourierId,
                        ]);

                        if (isset($assignResponse['status']) && $assignResponse['status'] == 200) {
                            $awbData = $assignResponse['response']['data'];
                            $order->update([
                                'awb_code' => $awbData['awb_code'],
                                'courier_name' => $awbData['courier_name'],
                                'shiprocket_status' => 'AWB Assigned',
                            ]);
                        }
                    }
                }
            } catch (\Throwable $e) {
                Log::error('Shiprocket async job error for ' . $order->order_number . ': ' . $e->getMessage());
            }
        }

        // 2. Send Order Confirmation Email
        try {
            $recipientEmail = $order->shipping_email ?? $order->user?->email;
            if ($recipientEmail) {
                Mail::to($recipientEmail)->send(new OrderPlacedConfirmation($order));
                Log::info('Order confirmation email sent (async job) to: ' . $recipientEmail . ' for order: ' . $order->order_number);
            }
        } catch (\Throwable $mailEx) {
            Log::error('Order confirmation email async job failed for ' . $order->order_number . ': ' . $mailEx->getMessage());
        }
    }
}
