<?php
require __DIR__ . '/../palVarnanDashboard/vendor/autoload.php';
$app = require_once __DIR__ . '/../palVarnanDashboard/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;

echo "PRODUCT_VARIENT COLUMNS:\n" . implode(", ", Schema::getColumnListing('product_varient')) . "\n\n";

$sampleGiftVar = DB::table('product_varient')->whereNotNull('size_id')->orWhereNotNull('color_id')->first();
echo "SAMPLE GIFT VARIANT:\n" . json_encode($sampleGiftVar, JSON_PRETTY_PRINT) . "\n\n";

$sampleGroceryVar = DB::table('product_varient')->whereNotNull('unit_id')->first();
echo "SAMPLE GROCERY VARIANT:\n" . json_encode($sampleGroceryVar, JSON_PRETTY_PRINT) . "\n\n";
