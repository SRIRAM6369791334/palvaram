<?php
require __DIR__ . '/../palVarnan/vendor/autoload.php';
$app = require_once __DIR__ . '/../palVarnan/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ProductOrder;
use App\Models\ProductOrderItem;
use App\Models\ProductSlot;

$order = ProductOrder::where('order_number', 'ORD-6AE81A4E512E2')->first();
$items = ProductOrderItem::where('order_id', $order->id)->get();
$slots = ProductSlot::where('order_id', 'ORD-6AE81A4E512E2')->get();

echo "ORDER MASTER:\n" . json_encode($order, JSON_PRETTY_PRINT) . "\n\n";
echo "ORDER ITEMS COUNT: " . count($items) . "\n";
echo "ORDER SLOTS (DASHBOARD PACKING) COUNT: " . count($slots) . "\n";
