<?php

use App\Http\Controllers\LocationController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AreaAssignController;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\BannerImagesController;
// use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CancelProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CompoStockController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\DashboardUserController;
use App\Http\Controllers\DeliveryPersonController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\IndexContrller;
use App\Http\Controllers\MilkOrdersController;
use App\Http\Controllers\MilkRefundController;
use App\Http\Controllers\MilkSlotController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OfferImageController;
use App\Http\Controllers\OrderSummeryController;
use App\Http\Controllers\packingCompleteController;
use App\Http\Controllers\PackingDeliveryController;
use App\Http\Controllers\PackingDispatchController;
use App\Http\Controllers\PackingOrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductOrdersController;
use App\Http\Controllers\ProductRefundController;
use App\Http\Controllers\ProductReturnController;
use App\Http\Controllers\ProductSlotController;
use App\Http\Controllers\ProductThumController;
use App\Http\Controllers\ProductVarientControllet;
use App\Http\Controllers\RefundController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\ProductwiseController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\SubCategoryController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\TodayDealsController;
use App\Http\Controllers\TopCustomerController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BulkOrderController;
use App\Http\Controllers\OrderwiseController;
use App\Http\Controllers\ScrollingBarController;
use App\Http\Controllers\ShippingController;
use App\Http\Controllers\NewcouponController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\GiftColorController;
use App\Http\Controllers\GiftInventoryReportController;
use App\Http\Controllers\GiftProductwiseController;
use App\Http\Controllers\GiftPurchaseOrderController;
use App\Http\Controllers\GiftPurchaseReportController;
use App\Http\Controllers\GiftSizeController;
use App\Http\Controllers\GiftSupplierController;
use App\Http\Controllers\GiftVariantController;
use App\Http\Controllers\HomePromotionController;
use App\Http\Controllers\InventoryReportController;
use App\Http\Controllers\LocationsController;
use App\Http\Controllers\ProductManagementController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\PurchaseReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\StockDistributionController;
use App\Http\Controllers\StockTrackingController;
use App\Http\Controllers\SupplierDetailController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\WareHouseBranchController;
use App\Http\Controllers\WareHouseController;
use App\Http\Controllers\ShiprocketController;
use App\Models\Category;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();

Route::middleware(['auth'])->group(function () {
    Route::get('/', [HomeController::class, "root"])->name("pages.home")->middleware("auth");
    // Route::get('/logout',[LoginController::class,"logout"]);

    // Customer Page
    Route::resource("customers", UserController::class)->only(["index", "store"]);
    Route::get("customers/{customerId}", [UserController::class, "edit"])->name("customers.edit");
    Route::post("updateUser/{userId}", [UserController::class, "update"])->name("customers.update");
    Route::post("destroyUser/{userId}", [UserController::class, "destroy"])->name("customers.destroy");
    Route::post("getProductsOptions", [UserController::class, "getProductsOptions"]);
    Route::post("getProductsverentOptions", [UserController::class, "getProductsverentOptions"]);
    Route::post("getProductsverentqty",[UserController::class,"getProductsverentqty"]);
    Route::post("getcustomersummery", [UserController::class, "getcustomersummery"])->name("customers.getcustomersummery");
    Route::post("addaddressvalue", [UserController::class, "addaddressvalue"])->name("customers.addaddressvalue");
    Route::get('Getcitys/{custid}',[UserController::class,"Getcity"])->name("customers.Getcity");

    Route::get('/get-address-details', [UserController::class, 'getAddressDetails']);
    Route::get('/get-address-details1', [UserController::class, 'getAddressDetails1']);
    // Delivery Persons Page
    Route::resource("deliveryPersons", DeliveryPersonController::class)->only(["index", "store"]);
    Route::post("updateDeliveryPerson/{id}", [DeliveryPersonController::class, "update"])->name("deliveryPersons.update");
    Route::post("destroyDeliveryPerson/{id}", [DeliveryPersonController::class, "destroy"])->name("deliveryPersons.destroy");



    // Categories Page
    Route::resource("categories", CategoryController::class)->only(["index", "store"]);
    Route::post("updateCategories/{id}", [CategoryController::class, "update"])->name("categories.update");
    Route::post("destroyCategories/{id}", [CategoryController::class, "destroy"])->name("categories.destroy");
    Route::post("validateCategoryName", [CategoryController::class, "validateCategoryName"]);

    // Brands Page
    Route::resource("brands", BrandController::class)->only(["index", "store"]);
    Route::post("updateBrands/{id}", [BrandController::class, "update"])->name("brands.update");
    Route::post("destroyBrands/{id}", [BrandController::class, "destroy"])->name("brands.destroy");

    // Countries Page
    Route::resource("countries", CountryController::class)->only(["index", "store"]);
    Route::post("updateCountries/{id}", [CountryController::class, "update"])->name("countries.update");
    Route::post("destroyCountries/{id}", [CountryController::class, "destroy"])->name("countries.destroy");

    // Sub-Categories Page
    Route::resource("subcategories", SubCategoryController::class)->only(["index","store"]);
    Route::post("updateSubCategories/{id}", [SubCategoryController::class, "update"]);
    Route::post("destroySubCategories/{id}", [SubCategoryController::class, "destroy"]);
    Route::post("validateSubCategoryName", [SubCategoryController::class, "validateSubCategoryName"]);


    // Products page
    Route::resource('products', ProductController::class)->only(["index", "store"]);
    Route::post("updateProducts/{id}", [ProductController::class, "update"])->name("products.update");
    Route::post("destroyProducts/{id}", [ProductController::class, "destroy"])->name("products.destroy");
    Route::post("productImageUpload", [ProductController::class, "productImageUpload"])->name("products.productImageUpload");
    Route::post("getProductDetail", [ProductController::class, "getProductDetail"])->name("products.getProductDetail");
    Route::post("getproductfilter",[ProductController::class, "getproductfilter"])->name("products.getProductDetail");
    // Route::get( 'getsubcategory/{id}', [ ProductController::class, 'Getsubproo' ] );
    Route::get( 'getsubcategory/{id}', [ ProductController::class, 'getProductsByCategory' ] );
    Route::get("downloadProductTemplate", [ProductController::class, "downloadTemplate"])->name("products.downloadTemplate");
    Route::post("bulkUploadProducts", [ProductController::class, "bulkUpload"])->name("products.bulkUpload");

    // Area Page
    Route::resource('areas', AreaController::class)->only(["index", "store"]);

    Route::post("updateArea/{id}", [AreaController::class, "update"])->name("areas.update");
    Route::post("destroyArea/{id}", [AreaController::class, "destroy"])->name("areas.destroy");
    Route::post("assignDeliveryPerson", [AreaAssignController::class, "assignDeliveryPerson"]);
    Route::post("deleteDeliveryPerson", [AreaAssignController::class, "deleteDeliveryPerson"]);
    Route::post("fetchAreaDeliveryPartners/{areaId}", [AreaAssignController::class, "fetchAreaDeliveryPartners"]);
    Route::post("deleteAreaDeliveryPartners/{areaId}", [AreaAssignController::class, "deleteAreaDeliveryPartners"]);
    Route::post("checkAreaValidation", [AreaController::class, "checkAreaValidation"]);
    Route::post("getPincodeAreas", [AreaController::class, "getPincodeAreas"]);



    //   varient thumb image

    Route::post('destroyVarientThumpImages/{id}',[ProductController::class,"destroyVarientThumpImages"]);
    Route::get('/getthump/{productid}',[ProductController::class,'getthump']);


Route::get('/admin/shipping-amount', [AdminController::class, 'shippingAmount'])->name('admin.shippingAmount');
Route::post('/admin/add-new-state', [AdminController::class, 'addNewState'])->name('admin.addNewState');
Route::delete('/admin/shipping/state/{id}', [AdminController::class, 'deleteState'])->name('admin.deleteShippingState');

Route::post('/admin/update-shipping-india', [AdminController::class, 'updateShippingIndia'])->name('admin.updateShippingIndia');
Route::post('/admin/update-shipping-international', [AdminController::class, 'updateShippingInternational'])->name('admin.updateShippingInternational');

Route::get('/scrollingbar', [ScrollingBarController::class, 'index'])->name('scrollingbar.index');
Route::post('/scrollingbar', [ScrollingBarController::class, 'store'])->name('scrollingbar.store');

Route::get('/admin/free-ship', [ShippingController::class, 'freeship'])->name('admin.freeship');
Route::post('/admin/free-shipping/update', [ShippingController::class, 'update'])->name('free-shipping.update');

Route::get('/admin/coupons', [NewcouponController::class, 'index'])->name('coupons.index');
Route::post('/admin/coupons/store', [NewcouponController::class, 'store'])->name('coupons.store');
Route::post('/admin/coupons/{id}/toggle', [NewcouponController::class, 'toggle'])->name('coupons.toggle');
Route::get('/admin/coupons/{id}/edit', [NewcouponController::class, 'edit'])->name('coupons.edit');
Route::put('/admin/coupons/{id}/update', [NewcouponController::class, 'update'])->name('coupons.update');
Route::delete('/admin/coupons/{id}', [NewcouponController::class, 'destroy'])->name('newcoupons.destroy');

    // Banner Image Page;
    Route::resource('bannerImages', BannerImagesController::class)->only(["index", "store"]);
    Route::post("updateBannerImages/{id}", [BannerImagesController::class, "update"])->name("bannerImages.update");
    Route::post("destroyBannerImages/{id}", [BannerImagesController::class, "destroy"])->name("bannerImages.destroy");
    Route::post("updateOrder", [BannerImagesController::class, "updateOrder"]);


    // web banner image
    Route::post('bannerwebImages',[BannerImagesController::class,"addbanner"])->name("bannerImages.addbanner");
    Route::post('updatewebBannerImages/{id}',[BannerImagesController::class,"updateimage"])->name("bannerwebImages.updateimage");
    Route::post("destroywebBannerImages/{id}", [BannerImagesController::class, "destroyweb"])->name("bannerImages.destroyweb");




    // Milk orders  Page
    Route::resource('milkOrders', MilkOrdersController::class)->only(["index", "store"]);
    Route::post("getAreaAssignedDelvieryPerson/{areaId}", [MilkOrdersController::class, "getAreaAssignedDelvieryPerson"]);
    Route::post("milkOrderDeliveryAssign", [MilkOrdersController::class, "milkOrderDeliveryAssign"]);


    // MIlk slots functionalities
    Route::get('milkOrders/{orderId}', [MilkSlotController::class, "getMilkSlots"]);
    Route::post("cancelMilkSlot", [MilkSlotController::class, "cancelMilkSlot"]);


    //MIlk order Create
    Route::post("createMilkSubscription", [ProductController::class, "createMilkSubscription"]);
    // Milk Order Payment Success create slot
    Route::post("createMilkSlot", [ProductController::class, "createMilkSlot"]);


    //Products order Create
    Route::post("createProductSubscription", [ProductController::class, "createProductSubscription"]);
    // Milk Order Payment Success create slot
    Route::post("createProductSlot", [ProductController::class, "createProductSlot"]);


    // Product slots functionalities
    Route::get('productOrders/{orderId}', [ProductSlotController::class, "getProductSlots"]);
    Route::get('ordersummerys/{orderId}', [ProductSlotController::class, "getProductSlotss"]);
    Route::post("cancelProductSlot", [ProductSlotController::class, "cancelProductSlot"]);

    // Products Orders Page
    Route::resource('productOrders', ProductOrdersController::class)->only(["index", "store"]);

    Route::post("productOrderDeliveryAssign", [ProductOrdersController::class, "productOrderDeliveryAssign"]);
    Route::get("viewProductdetail/{orderId?}", [ProductController::class, "viewProductInvoice"]);
    Route::get("viewProducts", [ProductOrdersController::class, "orderStat"]);

    Route::post("updatestatus",[ProductController::class,"upadetstatus"]);
    Route::post("pickupstatus",[ProductController::class,"pickupstatus"]);
    Route::post("updaterefund", [ProductController::class,"updaterefund"]);

    // Shiprocket Management & Sync
    Route::get('/shiprocket/pickup-locations', [ShiprocketController::class, 'pickupLocations'])->name('shiprocket.pickup');
    Route::get('/shiprocket/sync', [ShiprocketController::class, 'syncStatus'])->name('shiprocket.sync');



    // packing details


    Route::resource("productpacking",PackingOrderController::class)->only(["index"]);
    Route::post("updatestatupacking",[PackingOrderController::class,"updatepacking"])->name("productpacking.updatedispach");

    Route::post("updaterefund1", [PackingOrderController::class,"updaterefund1"]);

    // dispatch details

    Route::resource("productdispatch",PackingDispatchController::class)->only(["index"]);
    Route::post("updatestatusdispatch",[PackingDispatchController::class, "updatedispach"])->name("productdispatch.updatedispach");
    Route::post("updaterefund2", [PackingDispatchController::class,"updaterefund2"]);

     // Delivery details

    Route::resource("productdelivery",PackingDeliveryController::class)->only(["index"]);
    Route::Post("updatestatusdelivery",[PackingDeliveryController::class, "updatedelive"])->name("productdelivery.updatedelive");
    Route::Post("collectstatusdelivery",[PackingDeliveryController::class, "collectdelive"])->name("productdelivery.collectdelive");


    Route::resource("productcomplete",packingCompleteController::class)->only(["index"]);

    //order summery

    // product return

    Route::resource('productreturn',ProductReturnController::class)->only(['index']);
    Route::post("updatereturnpro",[ProductReturnController::class,"update"])->name("productreturn.update");
    Route::post("collectreturn",[ProductReturnController::class,"updateed"])->name("productreturn.updateed");

    Route::resource("ordersummery",OrderSummeryController::class)->only(["index"]);
    Route::post("getoversummery", [OrderSummeryController::class, "getoversummery"])->name("ordersummery.getoversummery");


    //MIlk Refund Page
    Route::resource("milkRefunds", MilkRefundController::class)->only(["index", "store"]);
    Route::post("getMilkRefundDatas", [MilkRefundController::class, "getRefundDatas"]);
    Route::post("refundMilkSlot", [MilkRefundController::class, "refundMilkSlot"]);

    //Product Refund Page
    Route::resource("productRefunds", ProductRefundController::class)->only(["index", "store"]);
    Route::post("getProductRefundDatas", [ProductRefundController::class, "getRefundDatas"]);
    Route::post("refundProductSlot", [ProductRefundController::class, "refundProductSlot"]);
    Route::resource("cancelproduct", CancelProductController::class)->only(["index","store"]);
    Route::post("cancelProductrequ", [CancelProductController::class, "cancelProductrequ"]);

    //Reports
    Route::get("incomeReports", [ReportsController::class, "incomeReports"])->name("incomeReports");
    Route::post("getIncomeReports", [ReportsController::class, "getIncomeReports"]);



    // Product Stock
    Route::resource("stocks",StockController::class)->only(["index", "store"]);
    Route::post("updateStack", [StockController::class, "update"])->name("stacks.update");
    Route::post("reduceStock", [StockController::class, "reduceStock"])->name("stacks.reduceStock");
    Route::get('lowstock',[StockController::class,"lowstock"])->name("lowstock");
    Route::post("updateStacklow", [StockController::class, "update1"])->name("lowstock.update1");
    Route::get("highselling",[StockController::class, "highselling"])->name("highselling");

    Route::get('transfer-stock', [StockController::class, 'transferIndex']);
    Route::post('transferStock', [StockController::class, 'transferStock']);
    Route::post('storeStock', [StockController::class, 'store'])->name('store.stock');
    Route::get('/gift-stock', [StockController::class, 'giftIndex']);
    // combo Stock

    Route::resource("combostock",CompoStockController::class)->only(["index"]);
    Route::post("updateStack1",[CompoStockController::class, "update"])->name("combostock.update");
    Route::post("reduceStock1", [CompoStockController::class, "reduceStock1"])->name("combostock.reduceStock1");

    // Coupon code
    Route::resource("coupons",CouponController::class)->only(["index", "store"]);
    Route::post("coupons",[CouponController::class,"addcoupon"])->name("coupons.addcoupon");
    Route::post("updatecoupon/{id}", [CouponController::class, "update"])->name("coupons.update");
    Route::post("destroycoupon/{id}", [CouponController::class, "destroy"])->name("coupons.destroy");

    // offer images

    Route::resource('offerImages', OfferImageController::class)->only(["index", "store"]);
    Route::post("offerImagess",[OfferImageController::class,"offerImagess"])->name("offerImages.offerImagess");
    Route::post("updateOfferImages/{id}", [OfferImageController::class, "update"])->name("offerImages.update");
    Route::post("destroyOfferImages/{id}", [OfferImageController::class, "destroy"])->name("offerImages.destroy");

    // user create
    Route::resource('users',DashboardUserController::class)->only(["index", "store"]);
    Route::post("userss",[DashboardUserController::class,"userss"])->name("users.userss");
    Route::post("updateuser/{id}",[DashboardUserController::class,"update"])->name("users.update");
    Route::post("destroyusers/{id}",[DashboardUserController::class,'destroy'])->name("users.destroy");
    Route::post("updatepass/{id}",[DashboardUserController::class,"update1"])->name("users.update1");

    // notifications
    Route::resource('review',NotificationController::class)->only(["index", "store"]);
    Route::post("reviews",[NotificationController::class,"notifications"])->name("notification.notifications");
    Route::post("updatenotifi/{id}",[NotificationController::class,"update"])->name("notification.update");
    Route::post("destroynotifi/{id}",[NotificationController::class,"destroy"])->name("notification.destroy");
    Route::get('/getproductsbycategory/{id}', [NotificationController::class, 'getProductsByCategory']);


    // Product Varient

    Route::resource('productvarient',ProductVarientControllet::class)->only(["index","store"]);
    Route::post('addproductvarient',[ProductVarientControllet::class,"addproductvarient"])->name("productvarient.addproductvarient");
    Route::post('updateProductsvarient/{id}',[ProductVarientControllet::class,'update'])->name("productvarient.update");
    Route::post('destroyvarient/{id}',[ProductVarientControllet::class,"destroy"])->name("productvarient.destroy");
    Route::get('Getproduct/{custid}',[ProductVarientControllet::class,"Getproduct"])->name("productvarient.Getproduct");
    Route::get('Getsubcategory/{custid}',[ProductVarientControllet::class,"Getsubcategory"])->name("productvarient.Getsubcategory");
    Route::post("getproductverfilter",[ProductVarientControllet::class,"getproductverfilter"])->name("productvarient.getproductverfilter");

    // product Thump
    Route::resource('productthump',ProductThumController::class)->only(["index","store"]);
    Route::post('ThumImages',[ProductThumController::class,"ThumImages"])->name(("productthump.ThumImages"));
    Route::post('updatethumImages/{id}',[ProductThumController::class,"update"])->name("productthump.update");
    Route::post("destroyThumpImages/{id}",[ProductThumController::class,"destroy"])->name("productthump.destroy");

    // top customer

    Route::resource('topcustomer',TopCustomerController::class)->only(["index"]);

     Route::post('updatePasss/{userId}', [UserController::class, 'updatePass'])->name('updatePass');

 Route::resource('bulkenquiry',BulkOrderController::class)->only(["index"]);
//  Route::resource('productwisereport',ProductwiseController::class)->only(["index"]);
// routes/web.php
Route::get('/productwisereport', [ProductwiseController::class, 'productWiseReport'])->name('product.wise.report');
Route::get('/product-wise-report/filter', [ProductwiseController::class, 'filterProductWiseReport'])->name('product.wise.report.filter');
Route::get('/report/export/excel', [ProductwiseController::class, 'exportExcel'])->name('product.wise.report.export.excel');
Route::get('/report/export/pdf', [ProductwiseController::class, 'exportPDF'])->name('product.wise.report.export.pdf');



Route::get('/orderwisereport', [OrderwiseController::class, 'orderwisereport'])->name('order.wise.report');
Route::get('/order-wise-report/filter', [OrderwiseController::class, 'filterorderWiseReport'])->name('order.wise.report.filter');
Route::get('/oreport/export/excel', [OrderwiseController::class, 'exportExcel'])->name('order.wise.report.export.excel');
Route::get('/oreport/export/pdf', [OrderwiseController::class, 'exportPDF'])->name('order.wise.report.export.pdf');
    //TODAY DEALS
    Route::resource("todaydeals", TodayDealsController::class)->only(["index","store"]);
    Route::post("updatetodaydeals/{id}", [TodayDealsController::class, "update"]);
    Route::post("destroytodaydeals/{id}", [TodayDealsController::class, "destroy"]);
    Route::post("validateSubCategoryName", [SubCategoryController::class, "validateSubCategoryName"]);

    // CANCEL REQUESTS
    Route::get('cancelrequests',[ProductSlotController::class,'cancelrequests']);
    Route::post('/approverequest',[ProductSlotController::class,'approverequest']);

    // RETURN REQUESTS
    Route::get('returnrequests',[ProductSlotController::class,'returnrequests']);
    Route::post('/reject-return-request',[ProductSlotController::class,'rejectReturnRequests']);
    // Gift Management
    Route::get('gift-categories', [App\Http\Controllers\GiftController::class, 'categoryIndex'])->name('gift_categories.index');
    Route::post('gift-categories', [App\Http\Controllers\GiftController::class, 'categoryStore'])->name('gift_categories.store');
    Route::post('updateGiftCategories/{id}', [App\Http\Controllers\GiftController::class, 'categoryUpdate'])->name('gift_categories.update');
    Route::post('destroyGiftCategories/{id}', [App\Http\Controllers\GiftController::class, 'categoryDestroy'])->name('gift_categories.destroy');

    Route::get('gift-subcategories', [App\Http\Controllers\GiftController::class, 'subcategoryIndex'])->name('gift_subcategories.index');
    Route::post('gift-subcategories', [App\Http\Controllers\GiftController::class, 'subcategoryStore'])->name('gift_subcategories.store');
    Route::post('updateGiftSubcategories/{id}', [App\Http\Controllers\GiftController::class, 'subcategoryUpdate'])->name('gift_subcategories.update');
    Route::post('destroyGiftSubcategories/{id}', [App\Http\Controllers\GiftController::class, 'subcategoryDestroy'])->name('gift_subcategories.destroy');

    Route::get('gift-products', [App\Http\Controllers\GiftController::class, 'productIndex'])->name('gift_products.index');
    Route::post('gift-products', [App\Http\Controllers\GiftController::class, 'productStore'])->name('gift_products.store');
    Route::post('updateGiftProducts/{id}', [App\Http\Controllers\GiftController::class, 'productUpdate'])->name('gift_products.update');
    Route::post('destroyGiftProducts/{id}', [App\Http\Controllers\GiftController::class, 'productDestroy'])->name('gift_products.destroy');

    // Home Promotions
    Route::get('home-promotions', [HomePromotionController::class, 'index'])->name('home_promotions.index');
    Route::post('home-promotions', [HomePromotionController::class, 'store'])->name('home_promotions.store');
    Route::post('update-home-promotions/{id}', [HomePromotionController::class, 'update'])->name('home_promotions.update');
    Route::post('destroy-home-promotions/{id}', [HomePromotionController::class, 'destroy'])->name('home_promotions.destroy');
    Route::post('update-home-promotions-order', [HomePromotionController::class, 'updateSortOrder'])->name('home_promotions.updateOrder');

    Route::get('roles', [RoleController::class, 'index'])->name('roles.index');

    Route::post('roles/store', [RoleController::class, 'store']);
    Route::post('roles/update/{id}', [RoleController::class, 'update']);
    Route::post('roles/delete/{id}', [RoleController::class, 'destroy']);

Route::get('roles/{role}/permissions', [RoleController::class, 'permissions'])
    ->name('roles.permissions');

Route::post('roles/{role}/permissions', [RoleController::class, 'updatePermissions'])
    ->name('roles.permissions.update');

    Route::post('/set-country', function (Illuminate\Http\Request $request) {
    session([
        'country_id' => $request->country_id,
        'country_name' => $request->country_name
    ]);

    return response()->json(['success' => true]);
});


Route::get('/warehouse', [WareHouseController::class, 'index'])->name('warehouse.index');
Route::post('/warehouse/store', [WarehouseController::class, 'store'])->name('warehouse.store');
Route::post('/warehouse/update/{id}', [WarehouseController::class, 'update'])->name('warehouse.update');
Route::post('/warehouse/delete/{id}', [WarehouseController::class, 'destroy'])->name('warehouse.delete');


Route::get('/warehouse-branch', [WareHouseBranchController::class, 'index'])->name('warehouse-branch.index');
Route::post('/warehouse-branch/store', [WareHouseBranchController::class, 'store']);
Route::post('/warehouse-branch/update/{id}', [WareHouseBranchController::class, 'update']);
Route::post('/warehouse-branch/delete/{id}', [WareHouseBranchController::class, 'destroy']);
Route::get('/get-warehouses/{country_id}', [WareHouseBranchController::class, 'getWarehouses']);

Route::get('/all-orders', [ProductOrdersController::class, 'allOrders'])->name('all-orders.index');

    Route::get('/location', [LocationsController::class, 'index'])
        ->name('admin.location.index');
 
    Route::get('/location/countries', [LocationsController::class, 'countries'])
        ->name('admin.location.countries');
 
    Route::get('/location/warehouses/{countryId}', [LocationsController::class, 'warehouses'])
        ->name('admin.location.warehouses');
 
    Route::get('/location/branches/{warehouseId}', [LocationsController::class, 'branches'])
        ->name('admin.location.branches');

    Route::get('/purchase-orders', [PurchaseOrderController::class, 'index']);
    Route::post('/purchase-orders', [PurchaseOrderController::class, 'store']);
    Route::post('/purchase-orders/update-status', [PurchaseOrderController::class, 'updateStatus']);
    Route::post('/purchase-orders/update', [PurchaseOrderController::class, 'update']);

    Route::view('/orders', 'pages.orders_single');
    Route::view('/product', 'pages.products_single');
    Route::view('/transfer', 'pages.stock_single');
    Route::view('/giftproducts', 'pages.giftproducts_single');

    Route::get('/reports', [ReportsController::class, 'index'])->name('reports.index');

    Route::view('/userpage', 'pages.users_single');

    Route::prefix('reports/inventory')->name('inventory.report.')->group(function () {
        Route::get('/',           [InventoryReportController::class, 'index'])          ->name('index');
        Route::get('/filter',     [InventoryReportController::class, 'filter'])         ->name('filter');
        Route::get('/excel',      [InventoryReportController::class, 'exportExcel'])    ->name('export.excel');
        Route::get('/pdf',        [InventoryReportController::class, 'exportPdf'])      ->name('export.pdf');
        Route::get('/warehouses', [InventoryReportController::class, 'getWarehouses'])  ->name('filter.warehouses');
        Route::get('/branches',   [InventoryReportController::class, 'getBranches'])    ->name('filter.branches');
        Route::get('/categories', [InventoryReportController::class, 'getCategories'])  ->name('filter.categories');
        Route::get('/filter-data', [InventoryReportController::class, 'filterData'])->name('filter.data');
    });

    Route::prefix('reports/purchase')->name('purchase.report.')->group(function () {
        Route::get('/', [PurchaseReportController::class, 'index'])->name('index');
        Route::get('/filter-data', [PurchaseReportController::class, 'filterData'])->name('filter.data');
    });

    Route::get('/get-warehouses', [InventoryReportController::class, 'getWarehouses']);
Route::get('/get-branches', [InventoryReportController::class, 'getBranches']);
Route::get('/getVariantStocks/{id}', [ProductVarientControllet::class, 'getVariantStocks']);
Route::post('/updatestatus', [ProductOrdersController::class, 'updatestatus'])
    ->name('updatestatus');

    Route::get('/other-orders', [ProductOrdersController::class, 'specialOrders'])->name('other-orders.index');

    Route::get('stock-tracking', [StockTrackingController::class, 'index'])->name('stock.tracking');

    Route::get('product-management', [ProductManagementController::class, 'index'])
        ->name('product.management');
    Route::post('product-management/return/resolve', [ProductManagementController::class, 'resolveReturn'])
        ->name('product.management.return.resolve');
    Route::post('product-management/cancel', [ProductManagementController::class, 'cancelOrder'])
        ->name('product.management.cancel');
     Route::post('product-management/damaged/log', [ProductManagementController::class, 'logDamaged'])
        ->name('product.management.damaged.log');
     Route::post('product-management/expired/writeoff', [ProductManagementController::class, 'writeOffExpired'])
        ->name('product.management.expired.writeoff');

    Route::post('/purchase-orders/receive-batches', [PurchaseOrderController::class, 'receiveBatches']);
    Route::get('/get-variants', [PurchaseOrderController::class, 'getVariants']);
    Route::get('/purchase-orders/{id}', [PurchaseOrderController::class, 'show']);
    Route::post('/purchase-orders/update-status',    [PurchaseOrderController::class, 'updateStatus']);

    Route::get('/stock-distribution',        [StockDistributionController::class, 'index']);
    Route::post('/stock-distribution/store', [StockDistributionController::class, 'store']);
    Route::get('/get-warehouses', [StockDistributionController::class, 'getWarehouses']);
    Route::get('/get-branches',   [StockDistributionController::class, 'getBranches']);
    Route::get('/get-product-variants', [StockDistributionController::class, 'getVariants']);
    Route::get('/get-stocks',     [StockDistributionController::class, 'getStocks']);
    Route::post('warehouseTransfer', [StockController::class, 'warehouseTransfer']);
    Route::post('togglecouponstatus/{id}', [CouponController::class, 'toggleStatus']);

    Route::get('/supplier', [SupplierDetailController::class, 'index']);
    Route::post('/supplier', [SupplierDetailController::class, 'store']);
    Route::post('/updateSupplier/{id}', [SupplierDetailController::class, 'update']);
    Route::post('/destroySupplier/{id}', [SupplierDetailController::class, 'destroy']);
    Route::post('/validateSupplierName', [SupplierDetailController::class, 'validateSupplierName']);
    Route::get('/get-store-stocks', [StockDistributionController::class, 'getStoreStock']);
       
    Route::get('/units',                   [UnitController::class, 'index'])->name('units.index');
    Route::post('/units',                  [UnitController::class, 'store'])->name('units.store');
    Route::post('/units/update/{id}',      [UnitController::class, 'update'])->name('units.update');
    Route::post('/units/destroy/{id}',     [UnitController::class, 'destroy'])->name('units.destroy');

    Route::get('/gift-size',                   [GiftSizeController::class, 'index'])->name('gift-size.index');
    Route::post('/gift-size',                  [GiftSizeController::class, 'store'])->name('gift-size.store');
    Route::post('/gift-size/update/{id}',      [GiftSizeController::class, 'update'])->name('gift-size.update');
    Route::post('/gift-size/destroy/{id}',     [GiftSizeController::class, 'destroy'])->name('gift-size.destroy');

    Route::get('/gift-color',                   [GiftColorController::class, 'index'])->name('gift-color.index');
    Route::post('/gift-color',                  [GiftColorController::class, 'store'])->name('gift-color.store');
    Route::post('/gift-color/update/{id}',      [GiftColorController::class, 'update'])->name('gift-color.update');
    Route::post('/gift-color/destroy/{id}',     [GiftColorController::class, 'destroy'])->name('gift-color.destroy');

    Route::get('/gift-variants',                  [GiftVariantController::class, 'index'])->name('gift_variants.index');
    Route::post('/gift-variants/store',           [GiftVariantController::class, 'store'])->name('gift_variants.store');
    Route::post('/gift-variants/update/{id}',     [GiftVariantController::class, 'update']);
    Route::post('/gift-variants/destroy/{id}',    [GiftVariantController::class, 'destroy']);
    Route::get('/gift-variants/filtered-products',[GiftVariantController::class, 'getFilteredProducts'])->name('gift_variants.filtered_products');

    Route::post('/stock-transfers/{id}/accept', [StockController::class, 'acceptTransfer']);
    Route::post('/stock-transfers/{id}/cancel', [StockController::class, 'cancelTransfer']);

    Route::post('/stock-distributions/{id}/accept', [StockDistributionController::class, 'acceptDistribution']);
    Route::post('/stock-distributions/{id}/cancel', [StockDistributionController::class, 'cancelDistribution']);

    Route::get('/get-suppliers', [App\Http\Controllers\SupplierDetailController::class, 'getByCountry']);


    Route::get('/gift-purchase-orders',                 [GiftPurchaseOrderController::class, 'index'])->name('gift.purchase.orders');
    Route::post('/gift-purchase-orders',                [GiftPurchaseOrderController::class, 'store']);
    Route::post('/gift-purchase-orders/update',         [GiftPurchaseOrderController::class, 'update']);
    Route::post('/gift-purchase-orders/update-status',  [GiftPurchaseOrderController::class, 'updateStatus']);
    Route::post('/gift-purchase-orders/receive-batches',[GiftPurchaseOrderController::class, 'receiveBatches']);
    Route::get('/gift-purchase-orders/{id}',            [GiftPurchaseOrderController::class, 'show']);


    Route::get('/gift-suppliers', [GiftSupplierController::class, 'index'])->name('gift_suppliers.index');
    Route::post('/gift-suppliers', [GiftSupplierController::class, 'store'])->name('gift_suppliers.store');
    Route::post('/updateGiftSupplier/{id}', [GiftSupplierController::class, 'update']);
    Route::post('/destroyGiftSupplier/{id}', [GiftSupplierController::class, 'destroy']);

    
    Route::get('/gift-purchase-reports', [GiftPurchaseReportController::class, 'index'])->name('gift.purchase.report.index');
    Route::get('/gift-purchase-reports/filter', [GiftPurchaseReportController::class, 'filterData'])->name('gift.purchase.report.filter.data');


    Route::get('/gift-inventory-reports', [GiftInventoryReportController::class, 'index'])->name('gift.inventory.report.index');
    Route::get('/gift-inventory-reports/filter', [GiftInventoryReportController::class, 'filterData'])->name('gift.inventory.report.filter.data');
    Route::get('/gift-inventory-reports/export-excel', [GiftInventoryReportController::class, 'exportExcel'])->name('gift.inventory.report.export.excel');
    Route::get('/gift-inventory-reports/export-pdf', [GiftInventoryReportController::class, 'exportPdf'])->name('gift.inventory.report.export.pdf');
    Route::get('/gift-inventory-reports/warehouses', [GiftInventoryReportController::class, 'getWarehouses'])->name('gift.inventory.report.filter.warehouses');
    Route::get('/gift-inventory-reports/branches', [GiftInventoryReportController::class, 'getBranches'])->name('gift.inventory.report.filter.branches');
    Route::get('/gift-inventory-reports/categories', [GiftInventoryReportController::class, 'getCategories'])->name('gift.inventory.report.filter.categories');


    Route::get('/gift-productwisereport', [GiftProductwiseController::class, 'productWiseReport'])->name('gift.product.wise.report');
    Route::get('/gift-product-wise-report/filter', [GiftProductwiseController::class, 'filterProductWiseReport'])->name('gift.product.wise.report.filter');
    Route::get('/gift-report/export/excel', [GiftProductwiseController::class, 'exportExcel'])->name('gift.product.wise.report.export.excel');
    Route::get('/gift-report/export/pdf', [GiftProductwiseController::class, 'exportPDF'])->name('gift.product.wise.report.export.pdf');
    
    Route::get('/orderwisereport', [OrderwiseController::class, 'orderwisereport'])->name('order.wise.report');
    Route::get('/order-wise-report/filter', [OrderwiseController::class, 'filterorderWiseReport'])->name('order.wise.report.filter');
    Route::get('/oreport/export/excel', [OrderwiseController::class, 'exportExcel'])->name('order.wise.report.export.excel');
    Route::get('/oreport/export/pdf', [OrderwiseController::class, 'exportPDF'])->name('order.wise.report.export.pdf');

    Route::get('/gift-stock-distribution', [StockDistributionController::class, 'giftIndex']);

    Route::get('/gift-transfer-stock', [StockController::class, 'giftTransferIndex']);

    Route::get('/gift-stock-tracking', [StockTrackingController::class, 'giftIndex']);
    

    Route::post('/stock/batches/{batch}/offer-price', [StockTrackingController::class, 'setOfferPrice'])
        ->name('stock.batches.setOfferPrice');

});





// Old Project Files
// Route::get('/', [App\Http\Controllers\HomeController::class, 'root'])->name('root');

// //Update User Detailsphp ar
// Route::post('/update-profile', [App\Http\Controllers\HomeController::class, 'updateProfile'])->name('updateProfile');
// Route::post('/update-password/{id}', [App\Http\Controllers\HomeController::class, 'updatePassword'])->name('updatePassword');

//hi Route::get('{any}', [App\Http\Controllers\HomeController::class, 'index'])->name('index');

// //Language Translation
// Route::get('index/{locale}', [App\Http\Controllers\HomeController::class, 'lang']);