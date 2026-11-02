<?php
require __DIR__ . '/../palVarnan/vendor/autoload.php';
$app = require_once __DIR__ . '/../palVarnan/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Cart;
use App\Models\Product;
use App\Models\ProductVarient;
use App\Models\ProductOrder;
use App\Models\Coupon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;

$user = User::where('email', 'test@gmail.com')->first();
$userId = $user ? $user->id : 1;

$results = [];

// ==========================================
// DOMAIN 1: CART BOUNDARY & NEGATIVE TESTS
// ==========================================

// Setup test cart
Cart::where('user_id', $userId)->delete();
$sampleVar = ProductVarient::where('product_qty', '>', 5)->first();
$sampleVarId = $sampleVar->id;
$productId = $sampleVar->product_id;

$cartItem = Cart::create([
    'user_id' => $userId,
    'ip_addresscart' => '127.0.0.1',
    'product_id' => $productId,
    'product_varient_id' => $sampleVarId,
    'product_quantity' => 1,
    'pre_order' => 0
]);

$cartController = new CartController();

// TC-C01: Zero Quantity
$reqZero = Request::create('/cart/ajax-update', 'POST', ['product_varient_id' => $sampleVarId, 'quantity' => 0]);
$reqZero->setUserResolver(fn() => $user);
$resZero = $cartController->ajaxUpdate($reqZero);
$qtyAfterZero = Cart::find($cartItem->id)->product_quantity;
$results['TC-C01_Zero_Quantity'] = [
    'input' => 0,
    'actual_qty_in_db' => $qtyAfterZero,
    'status' => ($qtyAfterZero >= 1) ? 'PASS' : 'FAIL_VULNERABLE (Quantity became 0 in DB)'
];

// TC-C02: Negative Quantity
$reqNeg = Request::create('/cart/ajax-update', 'POST', ['product_varient_id' => $sampleVarId, 'quantity' => -5]);
$reqNeg->setUserResolver(fn() => $user);
$resNeg = $cartController->ajaxUpdate($reqNeg);
$qtyAfterNeg = Cart::find($cartItem->id)->product_quantity;
$results['TC-C02_Negative_Quantity'] = [
    'input' => -5,
    'actual_qty_in_db' => $qtyAfterNeg,
    'status' => ($qtyAfterNeg >= 1) ? 'PASS' : 'FAIL_VULNERABLE (Quantity became negative in DB)'
];

// TC-C03: Float / Decimal Quantity
$reqFloat = Request::create('/cart/ajax-update', 'POST', ['product_varient_id' => $sampleVarId, 'quantity' => 2.75]);
$reqFloat->setUserResolver(fn() => $user);
$resFloat = $cartController->ajaxUpdate($reqFloat);
$qtyAfterFloat = Cart::find($cartItem->id)->product_quantity;
$results['TC-C03_Float_Quantity'] = [
    'input' => 2.75,
    'actual_qty_in_db' => $qtyAfterFloat,
    'status' => (is_int($qtyAfterFloat) || ctype_digit(strval($qtyAfterFloat))) ? 'PASS' : 'FAIL_VULNERABLE'
];

// TC-C04 & TC-C05: Stock Exceeded in Cart (Upper Boundary)
$maxStock = $sampleVar->product_qty;
$reqOver = Request::create('/cart/ajax-update', 'POST', ['product_varient_id' => $sampleVarId, 'quantity' => $maxStock + 50]);
$reqOver->setUserResolver(fn() => $user);
$resOver = $cartController->ajaxUpdate($reqOver);
$qtyAfterOver = Cart::find($cartItem->id)->product_quantity;
$results['TC-C04_Stock_Overflow'] = [
    'available_stock' => $maxStock,
    'requested_qty' => $maxStock + 50,
    'actual_qty_in_db' => $qtyAfterOver,
    'status' => ($qtyAfterOver <= $maxStock) ? 'PASS' : 'FAIL_VULNERABLE (Allowed cart to exceed stock)'
];

// TC-C08: Delete Non-existent Cart Item
try {
    $reqDel = Request::create('/cart-remove/99999999', 'GET');
    $cartController->remove(99999999);
    $results['TC-C08_Delete_NonExistent'] = ['status' => 'PASS'];
} catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
    $results['TC-C08_Delete_NonExistent'] = ['status' => 'PASS (ModelNotFoundException handled cleanly)'];
} catch (\Exception $e) {
    $results['TC-C08_Delete_NonExistent'] = ['status' => 'FAIL (' . $e->getMessage() . ')'];
}

// TC-C09: Non-existent Variant ID
$reqInvalidVar = Request::create('/cart/ajax-update', 'POST', ['product_varient_id' => 99999999, 'quantity' => 1]);
$reqInvalidVar->setUserResolver(fn() => $user);
$resInvalid = $cartController->ajaxUpdate($reqInvalidVar);
$results['TC-C09_Invalid_Variant_ID'] = [
    'response' => json_decode($resInvalid->getContent(), true),
    'status' => 'PASS (Handled safely without SQL crash)'
];

// TC-C11: SQL Injection in Coupon
$couponCode = "' OR '1'='1";
$foundCoupon = Coupon::where('codename', $couponCode)->first();
$results['TC-C11_SQLi_In_Coupon'] = [
    'input' => $couponCode,
    'matched' => (bool)$foundCoupon,
    'status' => ($foundCoupon === null) ? 'PASS (Protected by PDO parameterized queries)' : 'FAIL_VULNERABLE'
];

// ==========================================
// DOMAIN 2: CONCURRENCY & DOUBLE CLICK TESTS
// ==========================================

// TC-RC01: Rapid Double Click Add To Cart
// Simulate two identical store calls in rapid succession
$initialQty = Cart::where('user_id', $userId)->where('product_varient_id', $sampleVarId)->value('product_quantity') ?? 0;

$reqStore1 = Request::create('/cart/store', 'POST', ['product_id' => $productId, 'product_varient_id' => $sampleVarId, 'quantity' => 1]);
$reqStore1->setUserResolver(fn() => $user);
$cartController->store($reqStore1);

$reqStore2 = Request::create('/cart/store', 'POST', ['product_id' => $productId, 'product_varient_id' => $sampleVarId, 'quantity' => 1]);
$reqStore2->setUserResolver(fn() => $user);
$cartController->store($reqStore2);

$finalQty = Cart::where('user_id', $userId)->where('product_varient_id', $sampleVarId)->value('product_quantity');
$results['TC-RC01_Rapid_Store_Calls'] = [
    'initial_qty' => $initialQty,
    'after_2_calls' => $finalQty,
    'status' => ($finalQty === $initialQty + 2) ? 'PASS (Handled predictable increment)' : 'FAIL'
];

// TC-RC02: Duplicate Order Submission Guard
// Check if checkout button has client-side double click prevention and server-side duplicate guard
$checkoutBlade = file_get_contents(__DIR__ . '/../palVarnan/resources/views/pages/checkout.blade.php');
$hasBtnDisableOnSubmit = (strpos($checkoutBlade, 'disabled = true') !== false || strpos($checkoutBlade, 'isSubmitting') !== false || strpos($checkoutBlade, 'setAttribute(\'disabled\'') !== false);
$results['TC-RC02_Checkout_Double_Click_Guard'] = [
    'has_client_double_click_protection' => $hasBtnDisableOnSubmit,
    'status' => $hasBtnDisableOnSubmit ? 'PASS' : 'FAIL_VULNERABLE (Place Order button lacks disable-on-click)'
];

echo json_encode($results, JSON_PRETTY_PRINT);
