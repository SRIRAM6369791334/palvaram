<?php

namespace App\Http\Controllers;

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
use App\Models\UserAddress;
use App\Services\ShiprocketService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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

        return view('pages.checkout', compact(
            'user',
            'addresses',
            'selectedAddressId',
            'selectedAddress',
            'cartItems',
            'subtotal',
            'totalGst',
            'totalWeight',
            'states'
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
            return response()->json([
                'status' => 'error',
                'message' => 'Shiprocket Service Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Prepare Razorpay Order ID for standard modal checkout.
     */
    public function preparePayment(Request $request)
    {
        $user = Auth::user();
        $cartItems = $this->getCartItems();

        if ($cartItems->isEmpty()) {
            return response()->json(['status' => 'error', 'message' => 'Cart is empty.'], 400);
        }

        $subtotal = 0;
        $gstAmount = 0;
        foreach ($cartItems as $item) {
            $price = $item->variant->offer_price ?? $item->product->product_price ?? 0;
            $quantity = $item->product_quantity;
            $subtotal += $price * $quantity;
            $gstRate = $item->variant->product_gst ?? 0;
            $gstAmount += ($price * $quantity) * ($gstRate / 100);
        }

        $shipping = floatval($request->shipping_charge ?? 0);
        $discount = floatval($request->coupon_discount ?? 0);
        $total = max(($subtotal + $gstAmount + $shipping) - $discount, 1);

        try {
            $key = config('services.razorpay.key');
            $secret = config('services.razorpay.secret');

            $api = new Api($key, $secret);
            $razorpayOrder = $api->order->create([
                'receipt' => 'rcpt_' . uniqid(),
                'amount' => (int) round($total * 100),
                'currency' => 'INR',
            ]);

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
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Payment initiation failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Store the order details across multi-tables, reduce stock, clear cart, and create Shiprocket order.
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
            'shipping_street' => 'required',
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

        // Razorpay Verification if Online Payment
        if ($request->payment_method === 'Online Payment') {
            if (!$request->razorpay_payment_id || !$request->razorpay_signature || !$request->razorpay_order_id) {
                return response()->json(['status' => 'error', 'message' => 'Payment verification details missing.'], 400);
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

        $cartItems = $this->getCartItems();
        if ($cartItems->isEmpty()) {
            return response()->json(['status' => 'error', 'message' => 'Cart is empty.'], 400);
        }

        // Recalculate totals
        $subtotal = 0;
        $gstAmount = 0;
        foreach ($cartItems as $item) {
            $price = $item->variant->offer_price ?? $item->product->product_price ?? 0;
            $quantity = $item->product_quantity;
            $subtotal += $price * $quantity;
            $gstRate = $item->variant->product_gst ?? 0;
            $gstAmount += ($price * $quantity) * ($gstRate / 100);
        }

        $shipping = floatval($request->shipping_charge ?? 0);
        $discount = floatval($request->coupon_discount ?? 0);
        $total = ($subtotal + $gstAmount + $shipping) - $discount;

        $orderNumber = 'ORD-' . strtoupper(uniqid());

        DB::beginTransaction();
        try {
            // 1. Table: product_orders (Master)
            $order = ProductOrder::create([
                'user_id' => $userId,
                'order_number' => $orderNumber,
                'order_id' => $orderNumber, // For dashboard compatibility
                'billing_name' => $request->shipping_name,
                'billing_email' => $request->shipping_email,
                'billing_phone' => $request->shipping_phone,
                'billing_door_no' => $request->shipping_door_no,
                'billing_street' => $request->shipping_street,
                'billing_area' => $request->shipping_area ?? '-',
                'billing_city' => $request->shipping_city,
                'billing_state' => $request->shipping_state,
                'billing_pincode' => $request->shipping_pincode,
                'shipping_name' => $request->shipping_name,
                'shipping_email' => $request->shipping_email,
                'shipping_phone' => $request->shipping_phone,
                'shipping_door_no' => $request->shipping_door_no,
                'shipping_street' => $request->shipping_street,
                'shipping_area' => $request->shipping_area ?? '-',
                'shipping_city' => $request->shipping_city,
                'shipping_state' => $request->shipping_state,
                'shipping_pincode' => $request->shipping_pincode,
                'subtotal' => $subtotal,
                'gst_amount' => $gstAmount,
                'shipping_charge' => $shipping,
                'total_amount' => $total,
                'courier_name' => $request->selected_courier_name,
                'status' => 'Order Placed',
                'payment_method' => $request->payment_method,
                'payment_status' => ($request->payment_method === 'Online Payment') ? 'Paid' : 'Pending',
                'razorpay_order_id' => $request->razorpay_order_id,
                'razorpay_payment_id' => $request->razorpay_payment_id,
            ]);

            // 2. Table: product_order_user_addresses
            ProductOrderUserAddress::create([
                'user_id' => $userId,
                'order_id' => $orderNumber,
                'firstname' => $request->shipping_name,
                'secondname' => '',
                'address_line_one' => $request->shipping_door_no . ', ' . $request->shipping_street,
                'address_line_two' => $request->shipping_area ?? '',
                'landmark' => $request->shipping_landmark ?? '',
                'city' => $request->shipping_city,
                'state' => $request->shipping_state,
                'pincode' => $request->shipping_pincode,
                'phonecode' => '+91',
                'address_phone_number' => $request->shipping_phone,
                'address_type_id' => 1,
                'address_type_name' => 'shipping'
            ]);

            // 3. Table: product_transaction_logs
            ProductTransactionLog::create([
                'order_id' => $orderNumber,
                'slot_id' => null,
                'order_date' => now()->toDateString(),
                'order_amount' => $total,
                'amount_credited' => ($request->payment_method === 'Online Payment') ? $total : 0,
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

                // Table: product_order_items (Website Account Line Items)
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

                // Table: product_slots (Dashboard Packing & Dispatch Operational Table)
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
                    'delivery_status' => 0, // Pending
                ]);

                // Stock reduction in productstocks table
                $productStock = ProductStock::where('pro_ver_id', $item->product_varient_id)->first();
                if ($productStock) {
                    $productStock->availablestock = max($productStock->availablestock - $item->product_quantity, 0);
                    $productStock->salestock = ($productStock->salestock ?? 0) + $item->product_quantity;
                    $productStock->save();
                }

                // Variant stock sync
                $variant = ProductVarient::find($item->product_varient_id);
                if ($variant) {
                    $variant->product_qty = max($variant->product_qty - $item->product_quantity, 0);
                    $variant->save();
                }
            }

            // 6. Clear Cart
            Cart::where('user_id', $userId)->delete();

            DB::commit();

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Order creation failed in DB transaction', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['status' => 'error', 'message' => 'Order failed to save: ' . $e->getMessage()], 500);
        }

        // 7. Shiprocket Order & AWB Integration
        try {
            $shiprocket = new ShiprocketService();

            $orderItems = [];
            $totalWeight = 0;
            $maxLength = 0;
            $maxBreadth = 0;
            $maxHeight = 0;

            foreach ($cartItems as $item) {
                $orderItems[] = [
                    "name" => ($item->product->product_name ?? 'Product') . " " . ($item->variant->varient_name ?? ''),
                    "sku" => $item->variant->sku ?? $item->product->slug ?? 'SKU001',
                    "units" => $item->product_quantity,
                    "selling_price" => $item->variant->offer_price ?? $item->product->product_price ?? 0,
                    "discount" => 0,
                    "tax" => $item->variant->product_gst ?? 0,
                    "hsn" => 441122
                ];

                $itemWeight = (float) ($item->variant->weight ?? 0.5);
                $totalWeight += $itemWeight * $item->product_quantity;
                $maxLength = max($maxLength, (float) ($item->variant->length ?? 10));
                $maxBreadth = max($maxBreadth, (float) ($item->variant->breadth ?? 10));
                $maxHeight = max($maxHeight, (float) ($item->variant->height ?? 10));
            }

            $totalWeight = max($totalWeight, 0.5);
            $maxLength = max($maxLength, 10);
            $maxBreadth = max($maxBreadth, 10);
            $maxHeight = max($maxHeight, 10);

            $billingParts = explode(' ', trim($request->shipping_name));
            $lastName = count($billingParts) > 1 ? array_pop($billingParts) : 'Customer';
            $firstName = count($billingParts) > 0 ? implode(' ', $billingParts) : $request->shipping_name;

            $shiprocketOrderData = [
                "order_id" => $orderNumber,
                "order_date" => date('Y-m-d H:i'),
                "pickup_location" => config('services.shiprocket.pickup_location', 'Home'),
                "billing_customer_name" => $firstName,
                "billing_last_name" => $lastName,
                "billing_address" => $request->shipping_door_no . " " . $request->shipping_street,
                "billing_address_2" => $request->shipping_area ?? "-",
                "billing_city" => $request->shipping_city,
                "billing_pincode" => $request->shipping_pincode,
                "billing_state" => $request->shipping_state,
                "billing_country" => "India",
                "billing_email" => $request->shipping_email,
                "billing_phone" => substr(preg_replace('/[^0-9]/', '', $request->shipping_phone), -10),
                "shipping_is_billing" => true,
                "order_items" => $orderItems,
                "payment_method" => $request->payment_method === 'Online Payment' ? 'Prepaid' : 'COD',
                "shipping_charges" => $shipping,
                "giftwrap_charges" => 0,
                "transaction_charges" => 0,
                "total_discount" => $discount,
                "sub_total" => $subtotal + $gstAmount,
                "length" => $maxLength,
                "breadth" => $maxBreadth,
                "height" => $maxHeight,
                "weight" => $totalWeight
            ];

            Log::info('Creating Shiprocket order', ['order_id' => $orderNumber, 'payload' => $shiprocketOrderData]);
            $shipResponse = $shiprocket->createOrder($shiprocketOrderData);

            if (isset($shipResponse['order_id']) && isset($shipResponse['shipment_id'])) {
                $shipmentId = $shipResponse['shipment_id'];
                $order->update([
                    'shiprocket_order_id' => $shipResponse['order_id'],
                    'shiprocket_shipment_id' => $shipmentId,
                    'shiprocket_status' => $shipResponse['status'] ?? 'NEW'
                ]);

                // Assign chosen courier if selected
                if ($request->selected_courier_id) {
                    $assignResponse = $shiprocket->assignAWB([
                        'shipment_id' => $shipmentId,
                        'courier_id' => $request->selected_courier_id
                    ]);

                    if (isset($assignResponse['status']) && $assignResponse['status'] == 200) {
                        $awbData = $assignResponse['response']['data'];
                        $order->update([
                            'awb_code' => $awbData['awb_code'],
                            'courier_name' => $awbData['courier_name'],
                            'shiprocket_status' => 'AWB Assigned'
                        ]);
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Shiprocket API Error on placeOrder: ' . $e->getMessage());
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Order placed successfully!',
            'order_number' => $orderNumber,
            'redirect_url' => route('checkout.thankyou', ['order' => $orderNumber])
        ]);
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

        return redirect()->route('checkout.index', ['address_id' => $address->id])->with('success', 'Address added.');
    }

    /**
     * Address Management: Update Address.
     */
    public function updateAddress(Request $request, UserAddress $address)
    {
        abort_unless($address->user_id === Auth::id(), 403);

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

        return redirect()->route('checkout.index', ['address_id' => $address->id])->with('success', 'Address updated.');
    }

    /**
     * Address Management: Delete Address.
     */
    public function destroyAddress(UserAddress $address)
    {
        abort_unless($address->user_id === Auth::id(), 403);
        $address->delete();

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

    private function validateAddress(Request $request): array
    {
        return $request->validate([
            'first_name'               => ['required', 'string', 'max:255'],
            'last_name'                => ['required', 'string', 'max:255'],
            'phone_number'             => ['required', 'string', 'max:20'],
            'email'                    => ['required', 'email', 'max:255'],
            'address_line_one'         => ['required', 'string', 'max:255'],
            'address_line_two'         => ['nullable', 'string', 'max:255'],
            'landmark'                 => ['nullable', 'string', 'max:255'],
            'city'                     => ['required', 'string', 'max:255'],
            'state'                    => ['required', 'string', 'max:255'],
            'pincode'                  => ['required', 'string', 'max:10'],
        ]);
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

        return $query->whereHas('variant')->get();
    }
}