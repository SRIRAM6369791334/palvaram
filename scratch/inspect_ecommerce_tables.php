<?php
require __DIR__ . '/../palVarnan/vendor/autoload.php';
$app = require_once __DIR__ . '/../palVarnan/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;

$tables = ['carts', 'wishlists', 'user_addresses', 'product_orders', 'product_order_items', 'product_slots'];
foreach ($tables as $table) {
    if (Schema::hasTable($table)) {
        echo strtoupper($table) . ": " . implode(", ", Schema::getColumnListing($table)) . "\n\n";
    } else {
        echo strtoupper($table) . ": Table does not exist\n\n";
    }
}
