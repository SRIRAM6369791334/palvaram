<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ProductOrder;
use App\Services\ShiprocketService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderStatusUpdated;

class SyncShiprocketStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shiprocket:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync order status from Shiprocket API';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $shiprocket = new ShiprocketService();
        
        // Orders we want to track
        $orders = ProductOrder::with('customer', 'orderAddress')
            ->whereIn('status', ['processing', 'ready_for_pickup', 'shipped', 'out_for_delivery'])
            ->whereNotNull('shiprocket_order_id')
            ->get();

        $this->info("Found " . $orders->count() . " orders to sync.");

        foreach ($orders as $order) {
            try {
                if (!$order->awb_code) continue;

                $tracking = $shiprocket->trackAWB($order->awb_code);
                
                if (!isset($tracking['tracking_data']['shipment_track'][0])) {
                    continue;
                }

                $trackData = $tracking['tracking_data']['shipment_track'][0];
                $srStatusCode = (int) $trackData['current_status_id'];
                $srStatusName = $trackData['current_status'];

                $newLocalStatus = null;

                // Mapping Shiprocket codes to local dashboard statuses
                switch ($srStatusCode) {
                    case 17: // Picked Up
                    case 18: // Shipped
                    case 19: // In Transit
                        $newLocalStatus = 'shipped'; // Dispatched Tab
                        break;
                    case 20: // Out for Delivery
                        $newLocalStatus = 'out_for_delivery';
                        break;
                    case 21: // Delivered
                        $newLocalStatus = 'delivered';
                        break;
                    case 22: // Return Initiated
                    case 24: // RTO Initiated
                    case 25: // RTO Delivered
                        $newLocalStatus = 'return';
                        break;
                }

                if ($newLocalStatus && $newLocalStatus !== $order->status) {
                    $this->info("Updating Order #{$order->order_id_display} from {$order->status} to {$newLocalStatus} (SR: {$srStatusName})");
                    $order->update([
                        'status' => $newLocalStatus,
                        'shiprocket_status' => $srStatusName
                    ]);
                    
                    // Also update product_slots for compatibility
                    \DB::table('product_slots')->where('order_id', $order->order_id_display)->update([
                        'delivery_status' => $this->getStatusNum($newLocalStatus)
                    ]);

                    // Send Email Notification to Customer if mailable exists
                    if ($order->customer && !empty($order->customer->email) && class_exists('App\Mail\OrderStatusUpdated')) {
                        try {
                            Mail::to($order->customer->email)->send(new OrderStatusUpdated($order, $newLocalStatus));
                            $this->info("Email sent to {$order->customer->email} for status {$newLocalStatus}");
                        } catch (\Exception $e) {
                            $this->error("Email notification failed for order {$order->order_id_display}: " . $e->getMessage());
                        }
                    }
                }

            } catch (\Exception $e) {
                $this->error("Failed to sync Order #{$order->order_id_display}: " . $e->getMessage());
            }
        }

        return 0;
    }

    private function getStatusNum($status)
    {
        $map = [
            'processing' => 1,
            'shipped' => 2,
            'out_for_delivery' => 3,
            'delivered' => 4,
            'ready_for_pickup' => 5,
            'return' => 6,
        ];
        return $map[$status] ?? 0;
    }
}
