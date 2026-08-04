<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ProductOrder;
$order = ProductOrder::latest()->first();
if ($order) {
    print_r($order->toArray());
} else {
    echo "No orders found.";
}
