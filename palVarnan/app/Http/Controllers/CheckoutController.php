<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessOrderPostCheckout;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\District;
use App\Models\ProductOrder;
use App\Models\ProductOrderItem;
use App\Models\ProductOrderUserAddress;
use App\Models\ProductSlot;
use App\Models\ProductStock;
use App\Models\ProductTracking;
use App\Models\ProductTransactionLog;
use App\Models\ProductVarient;
use App\Models\State;
use App\Models\User;
use App\Models\UserAddress;
use App\Services\ShiprocketService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Razorpay\Api\Api;

if (!class_exists('Razorpay\Api\Api')) {
    $razorpayAutoload = base_path('vendor/razorpay/razorpay/Razorpay.php');
    if (file_exists($razorpayAutoload)) {
        require_once $razorpayAutoload;
    }
}

class CheckoutController extends Controller
{
    /**
     * View the Checkout page with user cart & addresses.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $addresses = UserAddress::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->get();

        $selectedAddressId = $request->query('address_id', $addresses->first()->id ?? null);
        $selectedAddress = $addresses->firstWhere('id', $selectedAddressId) ?? $addresses->first();

        $cartItems = $this->getCartItems();

        if ($cartItems->isEmpty()) {
            return redirect('/cart')->with('error', 'Your cart is empty.');
        }

        $subtotal = 0;
        $totalGst = 0;
        $totalWeight = 0;

        foreach ($cartItems as $item) {
            $price = $item->variant->offer_price ?? $item->product->product_price ?? 0;
            $qty = $item->product_quantity;
            $subtotal += $price * $qty;

            $gstRate = $item->variant->product_gst ?? 0;
            $totalGst += ($price * $qty) * ($gstRate / 100);

            $itemWeight = (float) ($item->variant->weight ?? 0.5);
            $totalWeight += $itemWeight * $qty;
        }

        $states = State::all();

        $appliedCoupon = session('applied_coupon');
        $couponDiscount = 0;
        if ($appliedCoupon) {
            $coupon = Coupon::find($appliedCoupon['id'] ?? null);
            // same rules as CartController::applyCoupon (null status = active, start/end dates)
            if ($coupon
                && ($coupon->coupon_status == 1 || is_null($coupon->coupon_status))
                && (empty($coupon->end_date) || strtotime($coupon->end_date) >= strtotime(date('Y-m-d')))
                && (empty($coupon->start_date) || strtotime($coupon->start_date) <= strtotime(date('Y-m-d')))) {
                if (empty($coupon->mini_amt) || $subtotal >= $coupon->mini_amt) {
                    if ($coupon->discounttype == 1) { // Flat
                        $couponDiscount = min($subtotal, (float)$coupon->discount);
                    } elseif ($coupon->discounttype == 2) { // Percentage
                        $couponDiscount = min($subtotal, round($subtotal * ((float)$coupon->discount / 100), 2));
                    } else {
                        $couponDiscount = min($subtotal, (float)$coupon->discount);
                    }
                    session(['applied_coupon.discount' => $couponDiscount]);
                } else {
                    session()->forget('applied_coupon');
                    $appliedCoupon = null;
                }
            } else {
                session()->forget('applied_coupon');
                $appliedCoupon = null;
            }
        }

        return view('pages.checkout', compact(
            'user',
            'addresses',
            'selectedAddressId',
            'selectedAddress',
            'cartItems',
            'subtotal',
            'totalGst',
            'totalWeight',
            'states',
            'appliedCoupon',
            'couponDiscount'
        ));
    }

    /**
     * Get available couriers from Shiprocket based on pincode and weight.
     */
    public function getShiprocketCouriers(Request $request)
    {
        $deliveryPincode = $request->pincode;
        $paymentMethod = $request->payment_method; // 'Cash on Delivery' or 'Online Payment'
        $isCod = ($paymentMethod === 'Cash on Delivery') ? 1 : 0;

        $cartItems = $this->getCartItems();

        if ($cartItems->isEmpty()) {
            return response()->json(['status' => 'error', 'message' => 'Cart is empty.']);
        }

        // Calculate total weight (in kg)
        $totalWeight = 0;
        foreach ($cartItems as $item) {
            $totalWeight += ($item->variant->weight ?? 0.5) * $item->product_quantity;
        }

        if ($totalWeight > 10) {
            $totalWeight = $totalWeight / 1000;
        }
        $totalWeight = max($totalWeight, 0.1);

        try {
            $shiprocket = new ShiprocketService();
            $pickupPincode = config('services.shiprocket.pickup_pincode', '641006');

            $params = [
                'pickup_postcode' => $pickupPincode,
                'delivery_postcode' => $deliveryPincode,
                'weight' => $totalWeight,
                'cod' => $isCod,
            ];

            Log::info('Checking Shiprocket couriers for pincode', $params);
            $response = $shiprocket->checkServiceability($params);

            $isSuccessful = (isset($response['status']) && $response['status'] == 200) || 
                            (isset($response['status_code']) && $response['status_code'] == 200);

            if ($isSuccessful && !empty($response['data']['available_courier_companies'])) {
                $couriers = $response['data']['available_courier_companies'];

                $formattedCouriers = array_map(function ($c) use ($isCod) {
                    return [
                        'id' => $c['courier_company_id'],
                        'name' => $c['courier_name'],
                        'rate' => $isCod ? ($c['freight_charge'] + ($c['cod_charges'] ?? 0)) : $c['freight_charge'],
                        'etd' => $c['etd'] ?? 'N/A'
                    ];
                }, $couriers);

                usort($formattedCouriers, function ($a, $b) {
                    return $a['rate'] <=> $b['rate'];
                });

                // Remember the quoted rates server-side so the order total never
                // trusts a shipping amount sent back by the browser.
                session(['checkout_courier_rates.' . $isCod => collect($formattedCouriers)
                    ->mapWithKeys(fn ($c) => [(string) $c['id'] => (float) $c['rate']])
                    ->all()]);

                return response()->json([
                    'status' => 'success',
                    'couriers' => $formattedCouriers
                ]);
            }

            $message = 'No couriers available for this location.';
            if (isset($response['message'])) {
                $message = $response['message'];
            }

            return response()->json([
                'status' => 'error',
                'message' => $message
            ]);

        } catch (\Exception $e) {
            Log::error('Shiprocket Service Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Could not fetch shipping couriers right now. Please try again.'
            ]);
        }
    }

    /**
     * Prepare Razorpay Order ID for standard modal checkout.
     */
    private function snapshotCache()
    {
        try {
            if (Schema::hasTable('cache')) {
                return Cache::store('database');
            }
        } catch (\Throwable $e) {
            // Fall back to default cache store
        }

        return Cache::store();
    }

    private function dispatchPostCheckout(int $orderId, ?int $selectedCourierId): void
    {
        try {
            if (Schema::hasTable('jobs')) {
                ProcessOrderPostCheckout::dispatch($orderId, $selectedCourierId)->onConnection('database');
                return;
            }
        } catch (\Throwable $e) {
            Log::warning('Database queue dispatch failed, falling back to afterResponse: ' . $e->getMessage());
        }

        ProcessOrderPostCheckout::dispatchAfterResponse($orderId, $selectedCourierId);
    }

    public function preparePayment(Request $request)
    {
        $user = Auth::user();
        $cartItems = $this->getCartItems();

        if ($cartItems->isEmpty()) {
            return response()->json(['status' => 'error', 'message' => 'Cart is empty.'], 400);
        }

        // Amount is computed on the server only (shipping from the quoted courier
        // rate, discount from the session coupon) - never from browser values.
        $totals = $this->calculateTotals($cartItems, 'Online Payment', $request->selected_courier_id);
        $total = max($totals['total'], 1);

        try {
            $key = trim((string) config('services.razorpay.key'));
            $secret = trim((string) config('services.razorpay.secret'));

            if ($key === '' || $secret === '') {
                Log::error('Payment initiation failed: RAZORPAY_KEY or RAZORPAY_SECRET is empty.');
                return response()->json(['status' => 'error', 'message' => 'Payment gateway is not configured. Please try Cash on Delivery or contact support.'], 503);
            }

            $api = new Api($key, $secret);
            $amountPaise = (int) round($total * 100);
            $razorpayOrder = $api->order->create([
                'receipt' => 'rcpt_' . uniqid(),
                'amount' => $amountPaise,
                'currency' => 'INR',
            ]);

            // placeOrder() checks the paid Razorpay order against this.
            session(['razorpay_pending' => [
                'order_id' => $razorpayOrder['id'],
                'amount_paise' => $amountPaise,
            ]]);

            // Store temporary checkout snapshot in DB cache (NOT product_orders) so the
            // Razorpay webhook can create the Paid order if the browser closes after payment.
            try {
                $snapshot = [
                    'user_id' => $user->id,
                    'country_id' => session('country_id') ?? $user->country_id ?? 243,
                    'shipping' => [
                        'shipping_name' => $request->input('shipping_name') ?: ($user->name ?? ''),
                        'shipping_email' => $request->input('shipping_email') ?: ($user->email ?? ''),
                        'shipping_phone' => $request->input('shipping_phone') ?: ($user->phone_number ?? ''),
                        'shipping_door_no' => $request->input('shipping_door_no', ''),
                        'shipping_street' => $request->input('shipping_street', ''),
                        'shipping_area' => $request->input('shipping_area') ?: '-',
                        'shipping_landmark' => $request->input('shipping_landmark', ''),
                        'shipping_city' => $request->input('shipping_city', ''),
                        'shipping_state' => $request->input('shipping_state', ''),
                        'shipping_pincode' => $request->input('shipping_pincode', ''),
                    ],
                    'selected_courier_id' => $request->input('selected_courier_id') ? (int) $request->input('selected_courier_id') : null,
                    'selected_courier_name' => $request->input('selected_courier_name') ?: 'Standard Delivery',
                    'totals' => $totals,
                    'amount_paise' => $amountPaise,
                    'cart_item_ids' => $cartItems->pluck('id')->all(),
                ];
                $this->snapshotCache()->put('rzp_checkout_' . $razorpayOrder['id'], $snapshot, now()->addHours(24));
            } catch (\Throwable $cacheEx) {
                Log::warning('Could not store Razorpay webhook snapshot: ' . $cacheEx->getMessage());
            }

            return response()->json([
                'status' => 'success',
                'razorpay_order_id' => $razorpayOrder['id'],
                'amount' => $total,
                'user' => [
                    'name' => $user->name ?? ($user->first_name . ' ' . $user->last_name),
                    'email' => $user->email,
                    'phone' => $user->phone_number ?? ''
                ],
                'razorpay_key' => $key
            ]);
        } catch (\Throwable $e) {
            Log::error('Payment initiation failed: ' . $e->getMessage(), [
                'key_prefix' => substr((string) config('services.razorpay.key'), 0, 12),
            ]);
            return response()->json(['status' => 'error', 'message' => 'Payment initiation failed. Please try again.'], 500);
        }
    }

    /**
     * Store the order details across multi-tables, reduce stock, clear cart, and queue Shiprocket/email.
     */
    public function placeOrder(Request $request)
    {
        $userId = Auth::id();
        $user = Auth::user();

        $rules = [
            'shipping_name' => 'required|string',
            'shipping_email' => 'required|email',
            'shipping_phone' => 'required',
            'shipping_door_no' => 'required',
            // filled from the address's optional "Apartment / suite" line - must not block the order
            'shipping_street' => 'nullable|string',
            'shipping_city' => 'required',
            'shipping_state' => 'required',
            'shipping_pincode' => 'required',
            'payment_method' => 'required|in:Online Payment,Cash on Delivery',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Please fill all required fields.',
                'errors' => $validator->errors()
            ], 422);
        }

        // Fast-path idempotency: if this Razorpay payment/order already created an order
        // (double-click, retry, or webhook beat the browser), return that existing order.
        if ($request->payment_method === 'Online Payment') {
            if (!$request->razorpay_payment_id || !$request->razorpay_signature || !$request->razorpay_order_id) {
                return response()->json(['status' => 'error', 'message' => 'Payment verification details missing.'], 400);
            }

            $existingOrder = ProductOrder::where('razorpay_payment_id', $request->razorpay_payment_id)
                ->orWhere('razorpay_order_id', $request->razorpay_order_id)
                ->first();

            if ($existingOrder) {
                session()->forget(['applied_coupon', 'razorpay_pending', 'checkout_courier_rates']);
                return response()->json([
                    'status' => 'success',
                    'message' => 'Order placed successfully!',
                    'order_number' => $existingOrder->order_number,
                    'redirect_url' => route('checkout.thankyou', ['order' => $existingOrder->order_number])
                ]);
            }

            try {
                $api = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));
                $attributes = [
                    'razorpay_order_id' => $request->razorpay_order_id,
                    'razorpay_payment_id' => $request->razorpay_payment_id,
                    'razorpay_signature' => $request->razorpay_signature
                ];
                $api->utility->verifyPaymentSignature($attributes);
            } catch (\Exception $e) {
                return response()->json(['status' => 'error', 'message' => 'Payment signature verification failed.'], 400);
            }
        }

        $lockKey = $request->payment_method === 'Online Payment'
            ? 'rzp_order_lock_' . $request->razorpay_order_id
            : 'cod_order_lock_' . $userId;

        $lock = $this->snapshotCache()->lock($lockKey, 15);
        if (!$lock->get()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Your order is already being processed. Please wait a moment.'
            ], 409);
        }

        try {
            // Re-check inside lock in case webhook or a concurrent click finished just before us.
            if ($request->payment_method === 'Online Payment') {
                $existingOrder = ProductOrder::where('razorpay_payment_id', $request->razorpay_payment_id)
                    ->orWhere('razorpay_order_id', $request->razorpay_order_id)
                    ->first();

                if ($existingOrder) {
                    session()->forget(['applied_coupon', 'razorpay_pending', 'checkout_courier_rates']);
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Order placed successfully!',
                        'order_number' => $existingOrder->order_number,
                        'redirect_url' => route('checkout.thankyou', ['order' => $existingOrder->order_number])
                    ]);
                }
            }

            $cartItems = $this->getCartItems();
            if ($cartItems->isEmpty()) {
                return response()->json(['status' => 'error', 'message' => 'Cart is empty.'], 400);
            }

            // Recalculate totals on the server (shipping + discount are not taken from the request)
            $totals = $this->calculateTotals($cartItems, $request->payment_method, $request->selected_courier_id);

            // Online payment: the signed Razorpay order must be the one we created for
            // this checkout, for exactly this amount (stops "pay ₹1, get order marked Paid").
            if ($request->payment_method === 'Online Payment') {
                $pending = session('razorpay_pending');
                if (!$pending
                    || ($pending['order_id'] ?? null) !== $request->razorpay_order_id
                    || (int) ($pending['amount_paise'] ?? -1) !== (int) round(max($totals['total'], 1) * 100)) {
                    Log::warning('Razorpay amount/order mismatch on placeOrder', [
                        'user_id' => $userId,
                        'razorpay_order_id' => $request->razorpay_order_id,
                        'expected' => $pending,
                        'computed_paise' => (int) round(max($totals['total'], 1) * 100),
                    ]);
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Payment amount did not match your order. Please contact support with your payment ID.'
                    ], 400);
                }
            }

            $shippingPayload = [
                'shipping_name' => $request->shipping_name,
                'shipping_email' => $request->shipping_email,
                'shipping_phone' => $request->shipping_phone,
                'shipping_door_no' => $request->shipping_door_no,
                'shipping_street' => $request->shipping_street ?? '',
                'shipping_area' => $request->shipping_area ?? '-',
                'shipping_landmark' => $request->shipping_landmark ?? '',
                'shipping_city' => $request->shipping_city,
                'shipping_state' => $request->shipping_state,
                'shipping_pincode' => $request->shipping_pincode,
            ];

            $result = $this->createOrderInDatabase(
                $userId,
                (int) (session('country_id') ?? $user->country_id ?? 243),
                $cartItems,
                $shippingPayload,
                $totals,
                $request->payment_method,
                $request->selected_courier_name ?? 'Standard Delivery',
                $request->razorpay_order_id,
                $request->razorpay_payment_id
            );

            if (!$result['ok']) {
                return response()->json(['status' => 'error', 'message' => $result['message']], $result['status']);
            }

            $order = $result['order'];
            session()->forget(['applied_coupon', 'razorpay_pending', 'checkout_courier_rates']);

            if ($request->razorpay_order_id) {
                $this->snapshotCache()->forget('rzp_checkout_' . $request->razorpay_order_id);
            }

            // Queue Shiprocket & Confirmation Email in background so Thank You page loads immediately
            $selectedCourierId = $request->selected_courier_id ? (int) $request->selected_courier_id : null;
            $this->dispatchPostCheckout($order->id, $selectedCourierId);

            return response()->json([
                'status' => 'success',
                'message' => 'Order placed successfully!',
                'order_number' => $order->order_number,
                'redirect_url' => route('checkout.thankyou', ['order' => $order->order_number])
            ]);
        } finally {
            $lock->release();
        }
    }

    /**
     * Razorpay Server-to-Server Webhook (payment.captured).
     * Creates the Paid order from the DB cache snapshot if the customer's browser closed
     * before placeOrder() completed. Never creates unpaid/pending orders.
     */
    public function handleRazorpayWebhook(Request $request)
    {
        $webhookSecret = config('services.razorpay.webhook_secret');
        if (empty($webhookSecret)) {
            Log::error('Razorpay webhook received but RAZORPAY_WEBHOOK_SECRET is not configured.');
            return response()->json(['status' => 'error', 'message' => 'Webhook not configured'], 503);
        }

        $signature = $request->header('X-Razorpay-Signature', '');
        $rawPayload = $request->getContent();
        $expectedSignature = hash_hmac('sha256', $rawPayload, $webhookSecret);

        if (!$signature || !hash_equals($expectedSignature, $signature)) {
            Log::warning('Razorpay webhook signature verification failed.');
            return response()->json(['status' => 'error', 'message' => 'Invalid signature'], 401);
        }

        $payload = json_decode($rawPayload, true);
        $event = $payload['event'] ?? '';

        if ($event !== 'payment.captured') {
            return response()->json(['status' => 'ignored', 'event' => $event], 200);
        }

        $paymentEntity = $payload['payload']['payment']['entity'] ?? [];
        $razorpayPaymentId = $paymentEntity['id'] ?? null;
        $razorpayOrderId = $paymentEntity['order_id'] ?? null;
        $paidAmountPaise = (int) ($paymentEntity['amount'] ?? 0);

        if (!$razorpayPaymentId || !$razorpayOrderId) {
            return response()->json(['status' => 'ignored', 'message' => 'Missing payment/order ID'], 200);
        }

        $lock = $this->snapshotCache()->lock('rzp_order_lock_' . $razorpayOrderId, 15);
        if (!$lock->get()) {
            // Browser placeOrder() is currently creating this order; tell Razorpay to retry briefly if needed
            return response()->json(['status' => 'processing'], 200);
        }

        try {
            // Idempotency: if browser already created the order, ensure it's marked paid and exit cleanly
            $existingOrder = ProductOrder::where('razorpay_payment_id', $razorpayPaymentId)
                ->orWhere('razorpay_order_id', $razorpayOrderId)
                ->first();

            if ($existingOrder) {
                if ($existingOrder->payment_status !== ProductOrder::PAYMENT_PAID) {
                    $existingOrder->update([
                        'payment_status' => ProductOrder::PAYMENT_PAID,
                        'razorpay_payment_id' => $razorpayPaymentId,
                    ]);
                }
                $this->snapshotCache()->forget('rzp_checkout_' . $razorpayOrderId);
                return response()->json(['status' => 'already_processed', 'order_number' => $existingOrder->order_number], 200);
            }

            $snapshot = $this->snapshotCache()->get('rzp_checkout_' . $razorpayOrderId);
            if (!$snapshot) {
                Log::warning('Razorpay webhook: no checkout snapshot found for captured payment', [
                    'razorpay_order_id' => $razorpayOrderId,
                    'razorpay_payment_id' => $razorpayPaymentId,
                ]);
                return response()->json(['status' => 'no_snapshot'], 200);
            }

            if ((int) ($snapshot['amount_paise'] ?? -1) !== $paidAmountPaise) {
                Log::warning('Razorpay webhook: amount mismatch', [
                    'razorpay_order_id' => $razorpayOrderId,
                    'expected_paise' => $snapshot['amount_paise'] ?? null,
                    'paid_paise' => $paidAmountPaise,
                ]);
                return response()->json(['status' => 'amount_mismatch'], 200);
            }

            $userId = (int) $snapshot['user_id'];
            $cartQuery = Cart::with(['product', 'variant'])
                ->where('user_id', $userId)
                ->whereHas('variant')
                ->whereHas('product');

            if (!empty($snapshot['cart_item_ids'])) {
                $cartQuery->whereIn('id', $snapshot['cart_item_ids']);
            }
            $cartItems = $cartQuery->get();

            if ($cartItems->isEmpty()) {
                Log::warning('Razorpay webhook: cart already empty for snapshot', [
                    'user_id' => $userId,
                    'razorpay_order_id' => $razorpayOrderId,
                ]);
                return response()->json(['status' => 'cart_empty'], 200);
            }

            $result = $this->createOrderInDatabase(
                $userId,
                (int) ($snapshot['country_id'] ?? 243),
                $cartItems,
                $snapshot['shipping'],
                $snapshot['totals'],
                'Online Payment',
                $snapshot['selected_courier_name'] ?? 'Standard Delivery',
                $razorpayOrderId,
                $razorpayPaymentId
            );

            if (!$result['ok']) {
                Log::error('Razorpay webhook: order creation failed', [
                    'razorpay_order_id' => $razorpayOrderId,
                    'razorpay_payment_id' => $razorpayPaymentId,
                    'reason' => $result['message'],
                ]);
                return response()->json(['status' => 'error', 'message' => $result['message']], 200);
            }

            $order = $result['order'];
            $this->snapshotCache()->forget('rzp_checkout_' . $razorpayOrderId);
            $this->dispatchPostCheckout($order->id, $snapshot['selected_courier_id'] ?? null);

            Log::info('Razorpay webhook created Paid order successfully', [
                'order_number' => $order->order_number,
                'razorpay_payment_id' => $razorpayPaymentId,
            ]);

            return response()->json(['status' => 'created', 'order_number' => $order->order_number], 200);
        } finally {
            $lock->release();
        }
    }

    /**
     * Atomically creates order rows across all tables, deducts stock, and clears ordered cart rows.
     */
    private function createOrderInDatabase(
        int $userId,
        int $countryId,
        $cartItems,
        array $shippingData,
        array $totals,
        string $paymentMethod,
        string $courierName,
        ?string $razorpayOrderId,
        ?string $razorpayPaymentId
    ): array {
        $subtotal = $totals['subtotal'];
        $gstAmount = $totals['gst'];
        $shipping = $totals['shipping'];
        $discount = $totals['discount'];
        $total = $totals['total'];
        $orderNumber = 'ORD-' . strtoupper(uniqid());

        DB::beginTransaction();
        try {
            // Lock and verify stock availability before order insertion
            foreach ($cartItems as $item) {
                $checkVariant = ProductVarient::lockForUpdate()->find($item->product_varient_id);
                $available = (int) ($checkVariant->product_qty ?? 0);
                if (!$checkVariant || $available < $item->product_quantity) {
                    DB::rollBack();
                    $pName = $item->product->product_name ?? 'An item in your cart';
                    return [
                        'ok' => false,
                        'status' => 400,
                        'message' => "Sorry, '{$pName}' is out of stock or does not have enough quantity available."
                    ];
                }
            }

            // 1. Table: product_orders (Master)
            $orderData = [
                'user_id' => $userId,
                'country_id' => $countryId,
                'order_number' => $orderNumber,
                'order_id' => $orderNumber, // For dashboard compatibility
                'billing_name' => $shippingData['shipping_name'],
                'billing_email' => $shippingData['shipping_email'],
                'billing_phone' => $shippingData['shipping_phone'],
                'billing_door_no' => $shippingData['shipping_door_no'],
                'billing_street' => $shippingData['shipping_street'] ?? '',
                'billing_area' => $shippingData['shipping_area'] ?? '-',
                'billing_city' => $shippingData['shipping_city'],
                'billing_state' => $shippingData['shipping_state'],
                'billing_pincode' => $shippingData['shipping_pincode'],
                'shipping_name' => $shippingData['shipping_name'],
                'shipping_email' => $shippingData['shipping_email'],
                'shipping_phone' => $shippingData['shipping_phone'],
                'shipping_door_no' => $shippingData['shipping_door_no'],
                'shipping_street' => $shippingData['shipping_street'] ?? '',
                'shipping_area' => $shippingData['shipping_area'] ?? '-',
                'shipping_city' => $shippingData['shipping_city'],
                'shipping_state' => $shippingData['shipping_state'],
                'shipping_pincode' => $shippingData['shipping_pincode'],
                'subtotal' => $subtotal,
                'gst_amount' => $gstAmount,
                'shipping_charge' => $shipping,
                'total_amount' => $total,
                'courier_name' => $courierName ?: 'Standard Delivery',
                'status' => ProductOrder::STATUS_PENDING,
                'payment_method' => $paymentMethod,
                'payment_status' => ($paymentMethod === 'Online Payment') ? ProductOrder::PAYMENT_PAID : ProductOrder::PAYMENT_PENDING,
                'razorpay_order_id' => $razorpayOrderId,
                'razorpay_payment_id' => $razorpayPaymentId,
            ];

            if (Schema::hasColumn('product_orders', 'coupon_discount')) {
                $orderData['coupon_discount'] = $discount;
            }

            $order = ProductOrder::create($orderData);

            // 2. Table: product_order_user_addresses
            ProductOrderUserAddress::create([
                'user_id' => $userId,
                'order_id' => $orderNumber,
                'firstname' => $shippingData['shipping_name'],
                'secondname' => '',
                'address_line_one' => trim(($shippingData['shipping_door_no'] ?? '') . ', ' . ($shippingData['shipping_street'] ?? ''), ', '),
                'address_line_two' => $shippingData['shipping_area'] ?? '',
                'landmark' => $shippingData['shipping_landmark'] ?? '',
                'city' => $shippingData['shipping_city'],
                'state' => $shippingData['shipping_state'],
                'pincode' => $shippingData['shipping_pincode'],
                'phonecode' => '+91',
                'address_phone_number' => $shippingData['shipping_phone'],
                'address_type_id' => 1,
                'address_type_name' => 'shipping'
            ]);

            // 3. Table: product_transaction_logs
            ProductTransactionLog::create([
                'order_id' => $orderNumber,
                'slot_id' => null,
                'order_date' => now()->toDateString(),
                'order_amount' => $total,
                'amount_credited' => ($paymentMethod === 'Online Payment') ? $total : 0,
                'amount_debited' => 0,
                'user_id' => $userId
            ]);

            // 4. Table: product_tracking
            ProductTracking::create([
                'user_id' => $userId,
                'order_id' => $orderNumber,
                'delivery_status' => 0
            ]);

            // 5. Line items, slots, and stock deduction
            foreach ($cartItems as $item) {
                $itemPrice = $item->variant->offer_price ?? $item->product->product_price ?? 0;
                $itemTotal = $itemPrice * $item->product_quantity;
                $productImage = $item->product->product_image ?? null;
                $variantName = $item->variant->varient_name ?? $item->variant->varient ?? '';

                ProductOrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'product_variant_id' => $item->product_varient_id,
                    'product_name' => $item->product->product_name ?? 'Product',
                    'product_image' => $productImage,
                    'variant_name' => $variantName,
                    'quantity' => $item->product_quantity,
                    'price' => $itemPrice,
                    'gst_rate' => $item->variant->product_gst ?? 0,
                    'gst_amount' => ($itemTotal * (($item->variant->product_gst ?? 0) / 100)),
                    'total' => $itemTotal,
                ]);

                ProductSlot::create([
                    'order_id' => $orderNumber,
                    'product_id' => $item->product_id,
                    'product_varient_id' => $item->product_varient_id,
                    'quantity' => $item->product_quantity,
                    'product_name' => $item->product->product_name ?? 'Product',
                    'product_rate' => $itemPrice,
                    'product_total' => $itemTotal,
                    'gst_amt' => ($itemTotal * (($item->variant->product_gst ?? 0) / 100)),
                    'gst_per' => $item->variant->product_gst ?? 0,
                    'product_value' => $itemTotal,
                    'delivery_status' => 0,
                ]);

                $productStock = ProductStock::where('pro_ver_id', $item->product_varient_id)->first();
                if ($productStock) {
                    $productStock->availablestock = max($productStock->availablestock - $item->product_quantity, 0);
                    $productStock->salestock = ($productStock->salestock ?? 0) + $item->product_quantity;
                    $productStock->save();
                }

                $variant = ProductVarient::find($item->product_varient_id);
                if ($variant) {
                    $variant->product_qty = max($variant->product_qty - $item->product_quantity, 0);
                    $variant->save();
                }
            }

            // 6. Clear only the cart rows that were just ordered
            Cart::whereIn('id', $cartItems->pluck('id'))->delete();

            DB::commit();

            return ['ok' => true, 'order' => $order];
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Order creation failed in DB transaction', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [
                'ok' => false,
                'status' => 500,
                'message' => 'Unable to place your order right now. Please try again.'
            ];
        }
    }

    /**
     * Thank you order confirmation page.
     */
    public function thankyou($orderNumber)
    {
        $order = ProductOrder::with('items')
            ->where('order_number', $orderNumber)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return view('pages.thankyou', compact('order'));
    }

    /**
     * Address Management: Store Address.
     */
    public function storeAddress(Request $request)
    {
        $validated = $this->validateAddress($request);

        try {
            $address = UserAddress::create([
                'user_id'              => Auth::id(),
                'address_first_name'   => $validated['first_name'],
                'address_last_name'    => $validated['last_name'],
                'address_phone_number' => $validated['phone_number'],
                'email'                => $validated['email'],
                'address_line_one'     => $validated['address_line_one'],
                'address_line_two'     => $validated['address_line_two'] ?? null,
                'landmark'             => $validated['landmark'] ?? null,
                'city'                 => $validated['city'],
                'state'                => $validated['state'],
                'pincode'              => $validated['pincode'],
            ]);
        } catch (\Throwable $e) {
            Log::error('Address save failed: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Could not save address.'], 422);
            }
            return back()->withInput()->with('error', 'Could not save address.');
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Address added successfully.',
                'address' => $address,
            ]);
        }

        if (str_contains(url()->previous(), 'myaccount')) {
            return redirect('/myaccount#tab-addresses')->with('success', 'Address added successfully.')->with('tab', 'addresses');
        }

        return redirect()->route('checkout.index', ['address_id' => $address->id])->with('success', 'Address added.');
    }

    /**
     * Address Management: Update Address.
     */
    public function updateAddress(Request $request, UserAddress $address)
    {
        abort_unless((string)$address->user_id === (string)Auth::id(), 403);

        $validated = $this->validateAddress($request);

        $address->update([
            'address_first_name'   => $validated['first_name'],
            'address_last_name'    => $validated['last_name'],
            'address_phone_number' => $validated['phone_number'],
            'email'                => $validated['email'],
            'address_line_one'     => $validated['address_line_one'],
            'address_line_two'     => $validated['address_line_two'] ?? null,
            'landmark'             => $validated['landmark'] ?? null,
            'city'                 => $validated['city'],
            'state'                => $validated['state'],
            'pincode'              => $validated['pincode'],
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Address updated.',
                'address' => $address,
            ]);
        }

        if (str_contains(url()->previous(), 'myaccount')) {
            return redirect('/myaccount#tab-addresses')->with('success', 'Address updated successfully.')->with('tab', 'addresses');
        }

        return redirect()->route('checkout.index', ['address_id' => $address->id])->with('success', 'Address updated.');
    }

    /**
     * Address Management: Delete Address.
     */
    public function destroyAddress(UserAddress $address)
    {
        abort_unless((string)$address->user_id === (string)Auth::id(), 403);
        $address->delete();

        if (str_contains(url()->previous(), 'myaccount')) {
            return redirect('/myaccount#tab-addresses')->with('success', 'Address removed successfully.')->with('tab', 'addresses');
        }

        return back()->with('success', 'Address removed.');
    }

    /**
     * Get shipping charge by state name via AJAX.
     */
    public function getShippingCharge(Request $request)
    {
        $stateName = trim($request->state_name);
        $state = State::where('name', $stateName)->first();

        if ($state) {
            return response()->json([
                'status' => 'success',
                'shipping_charge' => $state->shipping_charge
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'State not found.'
        ], 404);
    }

    /**
     * Get cities / districts for a state via AJAX.
     */
    public function getCitiesByState(Request $request)
    {
        $stateName = trim($request->state_name);
        $cities = District::where('state_name', $stateName)
            ->orderBy('name', 'asc')
            ->pluck('name');

        return response()->json([
            'status' => 'success',
            'cities' => $cities
        ]);
    }

    /**
     * Real-time Pincode Lookup and Validation against India Post records.
     */
    public function lookupPincode(Request $request)
    {
        $pincode = trim($request->get('pincode', ''));

        if (!preg_match('/^[1-9][0-9]{5}$/', $pincode)) {
            return response()->json([
                'valid' => false,
                'message' => 'Please enter a valid 6-digit Indian PIN code.'
            ], 422);
        }

        $cacheKey = "pincode_lookup_{$pincode}";
        $data = \Illuminate\Support\Facades\Cache::get($cacheKey);

        if (!$data) {
            try {
                $response = \Illuminate\Support\Facades\Http::timeout(5)
                    ->withoutVerifying()
                    ->withHeaders([
                        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) PalVarnan/1.0',
                        'Accept' => 'application/json'
                    ])
                    ->get("https://api.postalpincode.in/pincode/{$pincode}");

                if ($response->successful()) {
                    $body = $response->json();
                    if (!empty($body[0]['Status']) && $body[0]['Status'] === 'Success' && !empty($body[0]['PostOffice'])) {
                        $first = $body[0]['PostOffice'][0];
                        $places = collect($body[0]['PostOffice'])->pluck('Name')->unique()->values()->all();
                        $data = [
                            'valid' => true,
                            'district' => $first['District'] ?? '',
                            'state' => $first['State'] ?? '',
                            'places' => $places
                        ];
                        \Illuminate\Support\Facades\Cache::put($cacheKey, $data, 86400 * 30);
                    } else {
                        $data = [
                            'valid' => false,
                            'message' => "Pincode {$pincode} was not found in Indian postal records."
                        ];
                        \Illuminate\Support\Facades\Cache::put($cacheKey, $data, 3600);
                    }
                }
            } catch (\Throwable $e) {
                Log::warning("Pincode lookup error for {$pincode}: " . $e->getMessage());
            }
        }

        if (!$data || empty($data['valid'])) {
            return response()->json([
                'valid' => false,
                'message' => $data['message'] ?? "Pincode {$pincode} is not recognized as a valid Indian postal code."
            ], 404);
        }

        return response()->json([
            'valid' => true,
            'city' => $data['district'],
            'state' => $data['state'],
            'places' => $data['places'] ?? [],
            'message' => "Verified: {$data['district']}, {$data['state']}"
        ]);
    }

    private function validateAddress(Request $request): array
    {
        $countryId = Auth::user()->country_id ?? session('country_id', 243);
        $isIndia = in_array((int)$countryId, [243, 99, 101]);

        $phoneRule = $isIndia 
            ? ['required', 'regex:/^[6-9][0-9]{9}$/']
            : ['required', 'regex:/^[0-9+ \-]{7,20}$/'];

        $pincodeRule = $isIndia 
            ? ['required', 'regex:/^[1-9][0-9]{5}$/']
            : ['required', 'regex:/^[0-9a-zA-Z \-]{3,10}$/'];

        $validated = $request->validate([
            'first_name'               => ['required', 'string', 'max:100'],
            'last_name'                => ['required', 'string', 'max:100'],
            'phone_number'             => $phoneRule,
            'email'                    => ['required', 'email', 'max:255'],
            'address_line_one'         => ['required', 'string', 'max:255'],
            'address_line_two'         => ['nullable', 'string', 'max:255'],
            'landmark'                 => ['nullable', 'string', 'max:255'],
            'city'                     => ['required', 'string', 'max:100'],
            'state'                    => ['required', 'string', 'max:100'],
            'pincode'                  => $pincodeRule,
        ], [
            'phone_number.regex'       => 'Please enter a valid 10-digit mobile number starting with 6, 7, 8, or 9.',
            'pincode.regex'            => 'Please enter a valid 6-digit postal pincode.',
        ]);

        if ($isIndia && preg_match('/^[1-9][0-9]{5}$/', $validated['pincode'])) {
            $pincode = $validated['pincode'];
            $inputState = strtolower(preg_replace('/[^a-z]/', '', $validated['state']));

            $cacheKey = "pincode_lookup_{$pincode}";
            $pinData = \Illuminate\Support\Facades\Cache::get($cacheKey);

            if (!$pinData) {
                try {
                    $response = \Illuminate\Support\Facades\Http::timeout(5)
                        ->withoutVerifying()
                        ->withHeaders([
                            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) PalVarnan/1.0',
                            'Accept' => 'application/json'
                        ])
                        ->get("https://api.postalpincode.in/pincode/{$pincode}");

                    if ($response->successful()) {
                        $body = $response->json();
                        if (!empty($body[0]['Status']) && $body[0]['Status'] === 'Success' && !empty($body[0]['PostOffice'])) {
                            $first = $body[0]['PostOffice'][0];
                            $pinData = [
                                'valid' => true,
                                'district' => $first['District'] ?? '',
                                'state' => $first['State'] ?? '',
                            ];
                            \Illuminate\Support\Facades\Cache::put($cacheKey, $pinData, 86400 * 30);
                        } else {
                            $pinData = ['valid' => false];
                            \Illuminate\Support\Facades\Cache::put($cacheKey, $pinData, 3600);
                        }
                    }
                } catch (\Throwable $e) {
                    Log::warning("Pincode validation timeout/error: " . $e->getMessage());
                }
            }

            if ($pinData && isset($pinData['valid']) && $pinData['valid'] === false) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'pincode' => "Pincode {$pincode} was not found in Indian postal records."
                ]);
            }

            if ($pinData && !empty($pinData['state'])) {
                $expectedState = strtolower(preg_replace('/[^a-z]/', '', $pinData['state']));
                if ($inputState !== $expectedState && !str_contains($inputState, $expectedState) && !str_contains($expectedState, $inputState)) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'state' => "Pincode {$pincode} belongs to {$pinData['state']}, but '{$validated['state']}' was entered. Please enter {$pinData['state']}.",
                        'pincode' => "Pincode {$pincode} does not match State '{$validated['state']}' (Expected: {$pinData['state']})."
                    ]);
                }
            }
        }

        return $validated;
    }

    /**
     * Server-side order totals. Shipping comes from the courier rate this server
     * quoted (getShiprocketCouriers), discount from the session coupon - never
     * from amounts posted by the browser.
     */
    private function calculateTotals($cartItems, ?string $paymentMethod, $courierId): array
    {
        $subtotal = 0;
        $gst = 0;
        foreach ($cartItems as $item) {
            $price = $item->variant->offer_price ?? $item->product->product_price ?? 0;
            $lineTotal = $price * $item->product_quantity;
            $subtotal += $lineTotal;
            $gst += $lineTotal * (($item->variant->product_gst ?? 0) / 100);
        }
        $gst = round($gst, 2);

        $isCod = ($paymentMethod === 'Cash on Delivery') ? 1 : 0;
        $rates = session('checkout_courier_rates.' . $isCod, []);
        $shipping = ($courierId !== null && $courierId !== '' && isset($rates[(string) $courierId]))
            ? max(0, (float) $rates[(string) $courierId])
            : 0.0;

        $discount = min(max(0, (float) session('applied_coupon.discount', 0)), $subtotal);

        return [
            'subtotal' => $subtotal,
            'gst' => $gst,
            'shipping' => $shipping,
            'discount' => $discount,
            'total' => round(max(0, ($subtotal + $gst + $shipping) - $discount), 2),
        ];
    }

    private function getCartItems()
    {
        $query = Cart::with(['product', 'variant']);

        if (Auth::check()) {
            $query->where('user_id', Auth::id());
        } else {
            $query->where('ip_addresscart', request()->cookie('guest_token') ?? request()->ip())
                  ->whereNull('user_id');
        }

        return $query->whereHas('variant')->whereHas('product')->get();
    }
}