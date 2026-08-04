<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ProductOrder;
try {
    $productOrders = ProductOrder::query()
        ->with('product', 'orderAddress.area', 'customer')
        ->where('status', 'pending')
        ->get();
    
    echo "Found " . $productOrders->count() . " orders.\n";
    foreach ($productOrders as $order) {
        echo "Order ID: " . $order->order_id . "\n";
        echo "  Customer: " . ($order->customer ? $order->customer->name : "NULL") . "\n";
        echo "  Order Address: " . ($order->orderAddress ? "Exists" : "NULL") . "\n";
        if ($order->orderAddress) {
            echo "    Area: " . ($order->orderAddress->area ? "Exists" : "NULL") . "\n";
        }
        echo "  Product: " . ($order->product ? "Exists" : "NULL") . "\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
