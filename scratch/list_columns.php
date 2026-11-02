<?php
require __DIR__ . '/../palVarnanDashboard/vendor/autoload.php';
$app = require_once __DIR__ . '/../palVarnanDashboard/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;

echo "CATEGORIES:\n" . implode(", ", Schema::getColumnListing('categories')) . "\n\n";
echo "PRODUCTS:\n" . implode(", ", Schema::getColumnListing('products')) . "\n\n";
echo "GIFT_VARIANTS:\n" . implode(", ", Schema::getColumnListing('gift_product_variants')) . "\n\n";
echo "PRODUCT_VARIENTS:\n" . implode(", ", Schema::getColumnListing('product_varients')) . "\n\n";
