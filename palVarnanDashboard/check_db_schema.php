<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$tables = [
    'product_orders',
    'product_slots',
    'product_order_items',
    'product_order_user_addresses',
    'user_addresses',
    'product_transaction_logs',
    'product_tracking',
    'product_refunds',
    'productstocks',
    'product_varient',
    'carts',
    'users',
    'coupons',
    'states',
    'districts'
];

$output = [];
foreach ($tables as $table) {
    if (Illuminate\Support\Facades\Schema::hasTable($table)) {
        $columns = Illuminate\Support\Facades\Schema::getColumnListing($table);
        $output[$table] = [
            'exists' => true,
            'columns' => $columns
        ];
    } else {
        $output[$table] = [
            'exists' => false
        ];
    }
}

file_put_contents('db_inspection_result.json', json_encode($output, JSON_PRETTY_PRINT));
echo "Inspection complete! Saved to db_inspection_result.json\n";
