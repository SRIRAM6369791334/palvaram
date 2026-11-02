<?php
require __DIR__ . '/../palVarnan/vendor/autoload.php';
$app = require_once __DIR__ . '/../palVarnan/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Cart;
use App\Models\Wishlist;
use App\Models\Product;
use App\Models\ProductVarient;
use App\Models\ProductOrder;
use App\Models\ProductOrderItem;
use App\Models\ProductSlot;
use App\Models\ProductStock;
use App\Models\UserAddress;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

$report = [
    'time' => now()->toIso8601String(),
    'tests' => []
];

// 1. Get or Create Test Customer
$user = User::where('email', 'test@gmail.com')->first();
if (!$user) {
    $user = User::create([
        'country_id'   => 243,
        'name'         => 'Test Customer',
        'email'        => 'test@gmail.com',
        'phone_number' => '9876543210',
        'password'     => bcrypt('password123'),
        'first_name'   => 'Test',
        'last_name'    => 'Customer'
    ]);
}
$userId = $user->id;

// 2. Test Wishlist with Gift Variant
try {
    Wishlist::where('user_id', $userId)->delete();

    // Find gift variant
    $giftVar = ProductVarient::whereNotNull('size_id')->whereNotNull('color_id')->first();
    if ($giftVar) {
        $wishItem = Wishlist::create([
            'user_id'            => $userId,
            'ip_address'         => '127.0.0.1',
            'product_id'         => $giftVar->product_id,
            'product_varient_id' => $giftVar->id,
            'product_quantity'   => 1
        ]);

        $loadedWish = Wishlist::with(['product', 'variant.size', 'variant.color'])->find($wishItem->id);

        $report['tests']['Wishlist_Gift_Variant_Eager_Load'] = [
            'status' => ($loadedWish && $loadedWish->variant->size && $loadedWish->variant->color) ? 'PASS' : 'FAIL',
            'product' => $loadedWish->product->product_name ?? null,
            'size' => $loadedWish->variant->size->size ?? null,
            'color' => $loadedWish->variant->color->color ?? null,
            'price' => $loadedWish->variant->offer_price ?? null
        ];
    }
} catch (\Exception $e) {
    $report['tests']['Wishlist_Error'] = $e->getMessage();
}

// 3. Test Cart Setup with Hybrid Items (Grocery + Gift)
try {
    Cart::where('user_id', $userId)->delete();

    // Grocery item
    $grocVar = ProductVarient::whereNotNull('unit_id')->first();
    if ($grocVar) {
        Cart::create([
            'user_id'            => $userId,
            'ip_addresscart'     => '127.0.0.1',
            'product_id'         => $grocVar->product_id,
            'product_varient_id' => $grocVar->id,
            'product_quantity'   => 2,
            'pre_order'          => 0
        ]);
    }

    // Gift item
    if (isset($giftVar)) {
        Cart::create([
            'user_id'            => $userId,
            'ip_addresscart'     => '127.0.0.1',
            'product_id'         => $giftVar->product_id,
            'product_varient_id' => $giftVar->id,
            'product_quantity'   => 1,
            'pre_order'          => 0
        ]);
    }

    $cartItems = Cart::where('user_id', $userId)->with(['product', 'variant.unit', 'variant.size', 'variant.color'])->get();
    
    $calcSubtotal = 0;
    foreach ($cartItems as $ci) {
        $calcSubtotal += $ci->variant->offer_price * $ci->product_quantity;
    }
    $calcDelivery = 50.00;
    $calcTax = round($calcSubtotal * 0.16, 2);
    $calcGrandTotal = $calcSubtotal + $calcDelivery + $calcTax;

    $report['tests']['Cart_Hybrid_Calculation'] = [
        'status' => count($cartItems) >= 2 ? 'PASS' : 'FAIL',
        'item_count' => count($cartItems),
        'subtotal' => $calcSubtotal,
        'delivery' => $calcDelivery,
        'tax' => $calcTax,
        'grand_total' => $calcGrandTotal
    ];

} catch (\Exception $e) {
    $report['tests']['Cart_Error'] = $e->getMessage();
}

// 4. Test Checkout Order Placement Simulation (Cash on Delivery)
try {
    $orderNumber = 'ORD-TEST-' . strtoupper(uniqid());

    DB::beginTransaction();

    $order = ProductOrder::create([
        'user_id'            => $userId,
        'order_number'       => $orderNumber,
        'order_id'           => $orderNumber,
        'billing_name'       => 'Kavitha Ramachandran',
        'billing_email'      => 'kavitha.ram@example.com',
        'billing_phone'      => '9840123456',
        'billing_door_no'    => '12/4A',
        'billing_street'     => 'Gandhi Road',
        'billing_area'       => 'RS Puram',
        'billing_city'       => 'Coimbatore',
        'billing_state'      => 'Tamil Nadu',
        'billing_pincode'    => '641006',
        'shipping_name'      => 'Kavitha Ramachandran',
        'shipping_email'     => 'kavitha.ram@example.com',
        'shipping_phone'     => '9840123456',
        'shipping_door_no'   => '12/4A',
        'shipping_street'    => 'Gandhi Road',
        'shipping_area'      => 'RS Puram',
        'shipping_city'      => 'Coimbatore',
        'shipping_state'     => 'Tamil Nadu',
        'shipping_pincode'   => '641006',
        'subtotal'           => $calcSubtotal,
        'gst_amount'         => $calcTax,
        'shipping_charge'    => $calcDelivery,
        'total_amount'       => $calcGrandTotal,
        'courier_name'       => 'Standard Delivery',
        'status'             => 'Order Placed',
        'payment_method'     => 'Cash on Delivery',
        'payment_status'     => 'Pending',
    ]);

    $orderItemsData = [];
    foreach ($cartItems as $item) {
        $itemPrice = $item->variant->offer_price ?? 0;
        $itemTotal = $itemPrice * $item->product_quantity;

        // Line Item for Website Account
        ProductOrderItem::create([
            'order_id'           => $order->id,
            'product_id'         => $item->product_id,
            'product_variant_id' => $item->product_varient_id,
            'product_name'       => $item->product->product_name ?? 'Product',
            'product_image'      => $item->product->product_image ?? null,
            'variant_name'       => $item->variant->varient_name ?? '',
            'quantity'           => $item->product_quantity,
            'price'              => $itemPrice,
            'gst_rate'           => $item->variant->product_gst ?? 0,
            'gst_amount'         => ($itemTotal * (($item->variant->product_gst ?? 0) / 100)),
            'total'              => $itemTotal,
        ]);

        // Slot Item for Admin Dashboard Packing
        ProductSlot::create([
            'order_id'           => $orderNumber,
            'product_id'         => $item->product_id,
            'product_varient_id' => $item->product_varient_id,
            'quantity'           => $item->product_quantity,
            'product_name'       => $item->product->product_name ?? 'Product',
            'product_rate'       => $itemPrice,
            'product_total'      => $itemTotal,
            'gst_amt'            => ($itemTotal * (($item->variant->product_gst ?? 0) / 100)),
            'gst_per'            => $item->variant->product_gst ?? 0,
            'product_value'      => $itemTotal,
            'delivery_status'    => 0,
        ]);

        $orderItemsData[] = [
            'name'     => $item->product->product_name ?? 'Product',
            'variant'  => $item->variant->varient_name ?? '',
            'quantity' => $item->product_quantity,
            'price'    => $itemPrice
        ];
    }

    // Clear cart
    Cart::where('user_id', $userId)->delete();

    DB::commit();

    $report['tests']['Order_Placement_Multi_Table'] = [
        'status' => 'PASS',
        'order_id' => $order->id,
        'order_number' => $orderNumber,
        'total_amount' => $order->total_amount,
        'items_count' => count($orderItemsData),
        'slots_created' => ProductSlot::where('order_id', $orderNumber)->count(),
        'cart_cleared' => (Cart::where('user_id', $userId)->count() === 0)
    ];

    // 5. Test Email Templates Render
    $customerMailHtml = view('emails.order_customer', ['order' => $order, 'items' => $orderItemsData])->render();
    $adminMailHtml = view('emails.order_admin', ['order' => $order, 'items' => $orderItemsData])->render();

    file_put_contents(__DIR__ . '/order_rendered_customer.html', $customerMailHtml);
    file_put_contents(__DIR__ . '/order_rendered_admin.html', $adminMailHtml);

    $report['tests']['Order_Email_Templates_Render'] = [
        'status' => (!empty($customerMailHtml) && !empty($adminMailHtml)) ? 'PASS' : 'FAIL',
        'customer_html_bytes' => strlen($customerMailHtml),
        'admin_html_bytes' => strlen($adminMailHtml)
    ];

    // 6. Test Dashboard Sync
    $dashboardOrder = DB::table('product_orders')->where('order_number', $orderNumber)->first();
    $dashboardSlots = DB::table('product_slots')->where('order_id', $orderNumber)->get();

    $report['tests']['Dashboard_Order_Sync'] = [
        'status' => ($dashboardOrder && count($dashboardSlots) === count($orderItemsData)) ? 'PASS' : 'FAIL',
        'dashboard_order_found' => (bool)$dashboardOrder,
        'slots_matched' => count($dashboardSlots)
    ];

} catch (\Exception $e) {
    DB::rollBack();
    $report['tests']['Order_Placement_Error'] = $e->getMessage();
}

echo json_encode($report, JSON_PRETTY_PRINT);
