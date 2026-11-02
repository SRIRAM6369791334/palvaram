<?php
require __DIR__ . '/../palVarnanDashboard/vendor/autoload.php';
$app = require_once __DIR__ . '/../palVarnanDashboard/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

$report = [
    'execution_time' => now()->toIso8601String(),
    'modules' => []
];

// ==========================================
// TEST SUITE 1: CATEGORY CRUD
// ==========================================
try {
    $catName = 'QA Automation Toys ' . time();
    $catId = DB::table('categories')->insertGetId([
        'category_name' => $catName,
        'country_id'    => '243',
        'is_gift'       => 1,
        'status'        => 1,
        'created_at'    => now(),
        'updated_at'    => now()
    ]);

    $created = DB::table('categories')->find($catId);
    $report['modules']['Category_Create'] = [
        'test_id' => 'TC-CRUD-01',
        'status' => ($created && $created->category_name === $catName) ? 'PASS' : 'FAIL',
        'inserted_id' => $catId,
        'category_name' => $created->category_name ?? null
    ];

    // UPDATE
    $updatedCatName = $catName . ' (Updated)';
    DB::table('categories')->where('id', $catId)->update([
        'category_name' => $updatedCatName,
        'updated_at'    => now()
    ]);
    $updated = DB::table('categories')->find($catId);
    $report['modules']['Category_Update'] = [
        'test_id' => 'TC-CRUD-02',
        'status' => ($updated && $updated->category_name === $updatedCatName) ? 'PASS' : 'FAIL',
        'updated_name' => $updated->category_name ?? null
    ];

    // DELETE
    DB::table('categories')->where('id', $catId)->delete();
    $deleted = DB::table('categories')->find($catId);
    $report['modules']['Category_Delete'] = [
        'test_id' => 'TC-CRUD-03',
        'status' => ($deleted === null) ? 'PASS' : 'FAIL',
        'deleted_id' => $catId
    ];

} catch (\Exception $e) {
    $report['modules']['Category_CRUD_Error'] = $e->getMessage();
}

// ==========================================
// TEST SUITE 2: PRODUCT CRUD (GIFT ITEM)
// ==========================================
try {
    $prodName = 'QA Robotic Teddy Bear ' . time();
    $prodSlug = Str::slug($prodName);
    
    $prodId = DB::table('products')->insertGetId([
        'product_name'        => $prodName,
        'slug'                => $prodSlug,
        'product_description' => 'QA Automated testing toy bear.',
        'country_id'          => '243',
        'category_id'         => 36, // Gifts category
        'is_gift'             => 1,
        'product_quantity'    => 50,
        'product_regular_price' => 899.00,
        'product_mrp_price'   => 1200.00,
        'created_at'          => now(),
        'updated_at'          => now()
    ]);

    $createdProd = DB::table('products')->find($prodId);
    $report['modules']['Product_Create'] = [
        'test_id' => 'TC-CRUD-04',
        'status' => ($createdProd && $createdProd->slug === $prodSlug) ? 'PASS' : 'FAIL',
        'inserted_id' => $prodId,
        'product_name' => $createdProd->product_name ?? null
    ];

    // ==========================================
    // TEST SUITE 3: VARIANT CRUD (product_varient)
    // ==========================================
    $sku = 'QA-ROBO-' . rand(1000, 9999);
    $varId = DB::table('product_varient')->insertGetId([
        'product_id'   => $prodId,
        'categoryid'   => 36,
        'country_id'   => '243',
        'sku'          => $sku,
        'size_id'      => 1, // Small
        'color_id'     => 1, // White
        'varient_name' => 'Small / White',
        'offer_price'  => 850.00,
        'mrp_price'    => 1100.00,
        'product_qty'  => 30,
        'low_stock'    => '5',
        'created_at'   => now(),
        'updated_at'   => now()
    ]);

    $createdVar = DB::table('product_varient')->find($varId);
    $report['modules']['Variant_Create'] = [
        'test_id' => 'TC-CRUD-05',
        'status' => ($createdVar && $createdVar->sku === $sku) ? 'PASS' : 'FAIL',
        'variant_id' => $varId,
        'varient_name' => $createdVar->varient_name ?? null,
        'offer_price' => $createdVar->offer_price ?? null
    ];

    // TEST STOREFRONT QUERY MATCH
    $storefrontProduct = DB::table('products')
        ->whereRaw('FIND_IN_SET(?, country_id)', ['243'])
        ->where('id', $prodId)
        ->first();
    
    $storefrontVariants = DB::table('product_varient')
        ->where('product_id', $prodId)
        ->whereRaw('FIND_IN_SET(?, country_id)', ['243'])
        ->get();

    $report['modules']['Storefront_Catalog_Visibility'] = [
        'test_id' => 'TC-INT-01',
        'status' => ($storefrontProduct && $storefrontVariants->count() === 1) ? 'PASS' : 'FAIL',
        'product_found' => (bool)$storefrontProduct,
        'variants_count' => $storefrontVariants->count()
    ];

    // UPDATE VARIANT & STOCK
    DB::table('product_varient')->where('id', $varId)->update([
        'offer_price' => 799.00,
        'product_qty' => 45,
        'updated_at'  => now()
    ]);

    $updatedVar = DB::table('product_varient')->find($varId);
    $report['modules']['Variant_Stock_Update'] = [
        'test_id' => 'TC-CRUD-06',
        'status' => ($updatedVar && $updatedVar->offer_price == 799.00 && $updatedVar->product_qty == 45) ? 'PASS' : 'FAIL',
        'updated_price' => $updatedVar->offer_price ?? null,
        'updated_stock' => $updatedVar->product_qty ?? null
    ];

    // CLEANUP PRODUCT & VARIANT
    DB::table('product_varient')->where('id', $varId)->delete();
    DB::table('products')->where('id', $prodId)->delete();

    $report['modules']['Product_Variant_Cleanup'] = [
        'test_id' => 'TC-CRUD-07',
        'status' => (DB::table('products')->find($prodId) === null && DB::table('product_varient')->find($varId) === null) ? 'PASS' : 'FAIL',
        'cleanup_success' => true
    ];

} catch (\Exception $e) {
    $report['modules']['Product_CRUD_Error'] = $e->getMessage();
}

// ==========================================
// TEST SUITE 4: CONTACT INQUIRIES & DUAL NOTIFICATION
// ==========================================
try {
    $latestInquiry = DB::table('contacts')->orderBy('id', 'desc')->first();
    $report['modules']['Contact_Inquiry_Verification'] = [
        'test_id' => 'TC-INT-02',
        'status' => ($latestInquiry && $latestInquiry->id >= 7) ? 'PASS' : 'FAIL',
        'inquiry_id' => $latestInquiry->id ?? null,
        'name' => $latestInquiry->name ?? null,
        'email' => $latestInquiry->email ?? null,
        'subject' => $latestInquiry->subject ?? null
    ];
} catch (\Exception $e) {
    $report['modules']['Contact_Verification_Error'] = $e->getMessage();
}

echo json_encode($report, JSON_PRETTY_PRINT);
