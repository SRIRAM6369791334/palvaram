<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$tables = ['products', 'product_varient', 'sub_categories', 'categories'];
foreach ($tables as $table) {
    echo "Table: $table\n";
    $columns = DB::getSchemaBuilder()->getColumnListing($table);
    print_r($columns);
    echo "---------------------------\n";
}
