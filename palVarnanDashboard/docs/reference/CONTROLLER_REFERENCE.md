# CONTROLLER_REFERENCE — Controllers, methods, routes, notes
- Source: `app/Http/Controllers` (recursive)
- Count: **76** controller files [`VERIFIED_FROM_SOURCE`]
- Auth scaffold: **6** under `app/Http/Controllers/Auth/` [`VERIFIED_FROM_SOURCE`]
- Referenced docs: [`../08-routing.md`](../08-routing.md), [`./ROUTES_AND_API_REFERENCE.md`](./ROUTES_AND_API_REFERENCE.md), [`./MIDDLEWARE_AND_AUTHORIZATION.md`](./MIDDLEWARE_AND_AUTHORIZATION.md), [`./SECURITY_AUDIT.md`](./SECURITY_AUDIT.md)

> Notes: Listing below derived from public method scan of each controller. Where a method is missing despite being referenced by routes, it is called out explicitly [`INFERRED`]. No credential/PII values included [`VERIFIED_FROM_SOURCE`].

## Auth scaffold (scaffolded; minimal methods)
| Controller | Public methods | Routes usage | Notes |
|---|---|---|---|
| `Auth\ConfirmPasswordController.php` | `__construct` | Auth scaffold | `NOT_AVAILABLE` — method-level routes not enumerated here; standard Laravel Auth [`INFERRED`]. |
| `Auth\ForgotPasswordController.php` | (none detected) | Auth scaffold | Empty/traditional traits [`INFERRED`]. |
| `Auth\LoginController.php` | `__construct`, `logout` | Auth scaffold | `VERIFIED_FROM_SOURCE`. |
| `Auth\RegisterController.php` | `__construct`, `validator`, `create` | Auth scaffold | `VERIFIED_FROM_SOURCE`. |
| `Auth\ResetPasswordController.php` | (none detected) | Auth scaffold | Trait-based [`INFERRED`]. |
| `Auth\VerificationController.php` | `__construct` | Auth scaffold | `VERIFIED_FROM_SOURCE`. |

## Core/Admin/Users/Roles
| Controller | Public methods | Routes usage (from web.php) | Notes |
|---|---|---|---|
| `AdminController.php` | `shippingAmount`, `updateShippingIndia`, `updateShippingInternational`, `addNewState`, `deleteState` | Admin shipping/free-ship/state routes (see [`./ROUTES_AND_API_REFERENCE.md`](./ROUTES_AND_API_REFERENCE.md)) [`VERIFIED_FROM_SOURCE`] | Admin operations [`VERIFIED_FROM_SOURCE`]. |
| `DashboardUserController.php` | `getAllowedCountryIds`, `index`, `userss`, `update`, `destroy`, `update1` | `users` resource only `index,store` (route 336); extra `userss`, `updateuser/{id}`, `destroyusers/{id}`, `updatepass/{id}` map to `userss`, `update`, `destroy`, `update1` (335–340) [`VERIFIED_FROM_SOURCE`] | **MISSING:** `store()` method does not exist; resource `store` cannot call missing method in normal flow? Actually `store` is requested by resource but only `index,store` are declared; implementation uses `userss()` as store action. Also `update1` changes password for arbitrary id [`SECURITY_AUDIT.md`](./SECURITY_AUDIT.md#sec-a-03). [`INFERRED`] |
| `RoleController.php` | `index`, `store`, `update`, `destroy`, `permissions`, `updatePermissions` | Role management routes present [`VERIFIED_FROM_SOURCE`] | No server-side authorization enforced at routes [`VERIFIED_FROM_SOURCE`] (see [`./MIDDLEWARE_AND_AUTHORIZATION.md`](./MIDDLEWARE_AND_AUTHORIZATION.md)). |
| `UserController.php` | `index`, `create`, `store`, `show`, `edit`, `update`, `destroy`, `getProductsOptions`, `getProductsverentOptions`, `getProductsverentqty`, `getcustomersummery`, `updatePass`, `addaddressvalue`, `Getcity`, `getAddressDetails`, `getAddressDetails1` | Customer resource (index/store/edit/update/destroy custom routes) and password route `updatePasss/{userId}` → `updatePass` (line ~370) [`VERIFIED_FROM_SOURCE`] | `updatePass` used by `updatePasss/{userId}` (no ownership/role checks) [`INFERRED`]. |

## Catalog/Products/Subcats/Categories
| Controller | Public methods | Routes usage | Notes |
|---|---|---|---|
| `CategoryController.php` | `getAllowedCountryIds`, `index`, `create`, `store`, `store`, `show`, `edit`, `update`, `update`, `destroy`, `validateCategoryName` | Category routes present [`VERIFIED_FROM_SOURCE`] | Duplicates `store`/`update` listed in method scan [`INFERRED`]. |
| `SubCategoryController.php` | `getAllowedCountryIds`, `index`, `store`, `update`, `destroy` | `subcategories` resource (index,store); extra update/destroy and `POST validateSubCategoryName` (136, 390) [`VERIFIED_FROM_SOURCE`] | **BROKEN:** `validateSubCategoryName` does **NOT** exist as public method (scan returns none). Routes call it at `routes/web.php:136` and `390` → runtime 500/undefined method [`VERIFIED_FROM_SOURCE`]. See [`./ROUTES_AND_API_REFERENCE.md`](./ROUTES_AND_API_REFERENCE.md), [`./SECURITY_AUDIT.md`](./SECURITY_AUDIT.md#sec-a-01). |
| `ProductController.php` | `index`, `destroyVarientThumpImages`, `create`, `store`, `show`, `edit`, `update`, `destroy`, `productImageUpload`, `getProductDetail`, `createMilkSubscription`, `addMilkOrderDeliveryAddress`, `addProductOrderDeliveryAddress`, `assignDeliverPersonMilkOrder`, `createProductSubscription`, `assignDeliverProductOrder`, `createMilkSlot`, `createProductSlot`, `viewProductInvoice`, `upadetstatus`, `pickupstatus`, `getproductfilter`, `updaterefund`, `Getsubproo`, `getProductsByCategory`, `getthump`, `downloadTemplate`, `bulkUpload` | Many product/order/slot helper routes [`VERIFIED_FROM_SOURCE`] | Contains hardcoded Shiprocket creds in inline cURL at `ProductController.php:869` (commented) and `1010` (active) [`VERIFIED_FROM_SOURCE`] (locations only; no values). See [`./INTEGRATION_MAP.md`](./INTEGRATION_MAP.md), [`./SECURITY_AUDIT.md`](./SECURITY_AUDIT.md#sec-a-05). |

## Orders/Milk/Slots/Packing/Dispatch/Delivery
| Controller | Public methods | Routes usage | Notes |
|---|---|---|---|
| `MilkOrdersController.php` | `index`, `getMilkSlots`, `cancelMilkSlot`, `getAreaAssignedDelvieryPerson`, `milkOrderDeliveryAssign` | Milk orders routes [`VERIFIED_FROM_SOURCE`] | — |
| `MilkSlotController.php` | `getMilkSlots`, `cancelMilkSlot` | Milk slot operations [`VERIFIED_FROM_SOURCE`] | — |
| `ProductOrdersController.php` | `getAllowedCountryIds`, `index`, `allOrders`, `orderStat`, `create`, `store`, `show`, `edit`, `update`, `destroy`, `productOrderDeliveryAssign`, `updatestatus`, `specialOrders` | Product orders resource+extras [`VERIFIED_FROM_SOURCE`] | `orderStat()` makes inline Shiprocket auth cURL (`ProductOrdersController.php:110`) [`VERIFIED_FROM_SOURCE`] (no values). |
| `ProductSlotController.php` | `getProductSlots`, `getProductSlotss`, `cancelProductSlot`, `cancelrequests`, `approverequest`, `returnrequests`, `approveReturnRequest`, `rejectReturnRequests` | Slots + cancel/return requests [`VERIFIED_FROM_SOURCE`] | Inline Shiprocket auth at `ProductSlotController.php:131` and `202` [`VERIFIED_FROM_SOURCE`] (no values). |
| `PackingOrderController.php` | `getAllowedCountryIds`, `index`, `updatepacking`, `updaterefund1` | Packing routes [`VERIFIED_FROM_SOURCE`] | — |
| `PackingDispatchController.php` | `getAllowedCountryIds`, `index`, `updatedispach`, `updaterefund2` | Dispatch routes [`VERIFIED_FROM_SOURCE`] | — |
| `PackingDeliveryController.php` | `getAllowedCountryIds`, `index`, `updatedelive`, `collectdelive` | Delivery routes [`VERIFIED_FROM_SOURCE`] | — |
| `OrderSummeryController.php` | `index`, `getoversummery` | Order summary [`VERIFIED_FROM_SOURCE`] | — |
| `OrderwiseController.php` | `orderwisereport`, `filterorderWiseReport`, `exportExcel`, `exportPDF`, `getFilteredOrderData`, `getFilteredOrderDataFromDates` | Orderwise reports [`VERIFIED_FROM_SOURCE`] | — |
| `ProductwiseController.php` | `productWiseReport`, `filterProductWiseReport`, `exportExcel`, `exportPDF`, `getFilteredReportData`, `getFilteredReportDataFromDates` | Productwise reports; resource route commented out in web.php [`VERIFIED_FROM_SOURCE`] (see [`./ROUTES_AND_API_REFERENCE.md`](./ROUTES_AND_API_REFERENCE.md)) | — |

## Stock/Inventory/Distribution/Reports
| Controller | Public methods | Routes usage | Notes |
|---|---|---|---|
| `StockController.php` | `index`, `store`, `update`, `reduceStock`, `reduceStock`, `lowstock`, `update1`, `update1`, `highselling`, `transferIndex`, `transferStock`, `warehouseTransfer`, `acceptTransfer`, `cancelTransfer`, `giftIndex`, `giftTransferIndex` | Stock mgmt [`VERIFIED_FROM_SOURCE`] | Duplicate method names in scan [`INFERRED`]. |
| `StockDistributionController.php` | `index`, `giftIndex`, `getVariants`, `getWarehouses`, `getBranches`, `getStocks`, `store`, `getStoreStock`, `acceptDistribution`, `cancelDistribution` | Distribution [`VERIFIED_FROM_SOURCE`] | — |
| `StockTrackingController.php` | `getAllowedCountryIds`, `index`, `giftIndex`, `setOfferPrice` | Tracking [`VERIFIED_FROM_SOURCE`] | — |
| `InventoryReportController.php` | `getAllowedCountryIds`, `index`, `filter`, `exportExcel`, `__construct`, `array`, `headings`, `exportPdf`, `baseQuery`, `getStockOnHand`, `getLowStock`, `getDeadStock`, `getReportData`, `getHeaders`, `getWarehouses`, `getBranches`, `getCategories`, `filterData` | Inventory reports [`VERIFIED_FROM_SOURCE`] | Scan shows odd `array`, `headings` as methods [`INFERRED`] (likely export class artifacts) — treat as implementation detail [`INFERRED`]. |
| `PurchaseOrderController.php` | `getAllowedCountryIds`, `index`, `store`, `update`, `receiveBatches`, `getVariants`, `show`, `updateStatus` | Purchase orders [`VERIFIED_FROM_SOURCE`] | — |
| `PurchaseReportController.php` | `index`, `baseQuery`, `getSupplierWise`, `getCostVariance`, `filterData` | Purchase reports [`VERIFIED_FROM_SOURCE`] | — |
| `ReportsController.php` | `incomeReports`, `getIncomeReports`, `index` | Reports [`VERIFIED_FROM_SOURCE`] | — |
| `CompoStockController.php` | `index`, `update`, `reduceStock1` | Compo stock [`VERIFIED_FROM_SOURCE`] | — |

## Gifts/Variants/Units/Countries/Areas/Brands/Suppliers/Locations
| Controller | Public methods | Routes usage | Notes |
|---|---|---|---|
| `GiftController.php` | `getAllowedCountryIds`, `filterByCountry`, `normalizeCountryIds`, `categoryIndex`, `categoryStore`, `categoryUpdate`, `categoryDestroy`, `subcategoryIndex`, `subcategoryStore`, `subcategoryUpdate`, `subcategoryDestroy`, `productIndex`, `productStore`, `productUpdate`, `productDestroy`, `getGiftProducts` | Gift mgmt [`VERIFIED_FROM_SOURCE`] | — |
| `GiftColorController.php` | `getAllowedCountryIds`, `filterByCountry`, `index`, `store`, `update`, `destroy` | Gift colors [`VERIFIED_FROM_SOURCE`] | — |
| `GiftSizeController.php` | `getAllowedCountryIds`, `filterByCountry`, `index`, `store`, `update`, `destroy` | Gift sizes [`VERIFIED_FROM_SOURCE`] | — |
| `GiftVariantController.php` | `getAllowedCountryIds`, `filterByCountry`, `normalizeCountryIds`, `getAllowedProducts`, `viewData`, `index`, `store`, `update`, `destroy`, `getFilteredProducts`, `getVariants` | Gift variants [`VERIFIED_FROM_SOURCE`] | — |
| `GiftSupplierController.php` | `getAllowedCountryIds`, `parseCountryIds`, `getFilteredSuppliers`, `index`, `store`, `update`, `destroy` | Gift suppliers [`VERIFIED_FROM_SOURCE`] | — |
| `GiftPurchaseOrderController.php` | `getAllowedCountryIds`, `getGiftVariants`, `index`, `store`, `update`, `receiveBatches`, `updateStatus`, `show` | Gift PO [`VERIFIED_FROM_SOURCE`] | — |
| `GiftInventoryReportController.php` | `getAllowedCountryIds`, `index`, `baseQuery`, `getStockOnHand`, `getLowStock`, `getDeadStock`, `filterData`, `exportExcel`, `__construct`, `array`, `headings`, `exportPdf`, `getReportData`, `getHeaders`, `getWarehouses`, `getBranches`, `getCategories` | Gift reports [`VERIFIED_FROM_SOURCE`] | Similar export artifacts [`INFERRED`]. |
| `GiftOrderWiseController.php` | `orderwisereport`, `filterorderWiseReport`, `exportExcel`, `exportPDF`, `getFilteredOrderData`, `getFilteredOrderDataFromDates` | Gift orderwise [`VERIFIED_FROM_SOURCE`] | — |
| `GiftProductwiseController.php` | `productWiseReport`, `filterProductWiseReport`, `exportExcel`, `exportPDF`, `getFilteredReportData`, `getFilteredReportDataFromDates` | Gift productwise [`VERIFIED_FROM_SOURCE`] | — |
| `GiftPurchaseReportController.php` | `index`, `baseQuery`, `getSupplierWise`, `getCostVariance`, `filterData` | Gift purchase report [`VERIFIED_FROM_SOURCE`] | — |
| `ProductVarientControllet.php` | `index`, `getVariantList`, `addproductvarient`, `update`, `destroy`, `Getsubcategory`, `Getproduct`, `getproductverfilter`, `getVariantStocks` | Product variants mgmt [`VERIFIED_FROM_SOURCE`] | Filename `ProductVarientControllet.php` (typo) [`INFERRED`]. |
| `ProductThumController.php` | `index`, `ThumImages`, `update`, `destroy` | Thumbnail mgmt [`VERIFIED_FROM_SOURCE`] | — |
| `ProductManagementController.php` | `index`, `resolveReturn`, `cancelOrder`, `logDamaged`, `writeOffExpired` | Product mgmt ops [`VERIFIED_FROM_SOURCE`] | — |
| `ProductRefundController.php` | `index`, `getRefundDatas`, `refundProductSlot` | Refunds [`VERIFIED_FROM_SOURCE`] | — |
| `ProductReturnController.php` | `index`, `update`, `updateed` | Returns [`VERIFIED_FROM_SOURCE`] | — |
| `CancelProductController.php` | `index`, `cancelProductrequ` | Cancel requests view/ops [`VERIFIED_FROM_SOURCE`] | — |
| `UnitController.php` | `getAllowedCountryIds`, `filterByCountry`, `index`, `store`, `update`, `destroy` | Units [`VERIFIED_FROM_SOURCE`] | — |
| `CountryController.php` | `index`, `store`, `update`, `destroy`, `validateCountryName` | Countries [`VERIFIED_FROM_SOURCE`] | — |
| `AreaController.php` | `index`, `create`, `store`, `show`, `edit`, `update`, `destroy`, `checkAreaValidation`, `getPincodeAreas` | Areas [`VERIFIED_FROM_SOURCE`] | — |
| `AreaAssignController.php` | `assignDeliveryPerson`, `deleteDeliveryPerson`, `fetchAreaDeliveryPartners`, `deleteAreaDeliveryPartners` | Area assignments [`VERIFIED_FROM_SOURCE`] | — |
| `BrandController.php` | `getAllowedCountryIds`, `index`, `store`, `update`, `destroy` | Brands [`VERIFIED_FROM_SOURCE`] | — |
| `SupplierDetailController.php` | `getAllowedCountryIds`, `parseCountryIds`, `index`, `store`, `update`, `destroy`, `validateSupplierName`, `getByCountry` | Suppliers [`VERIFIED_FROM_SOURCE`] | — |
| `DeliveryPersonController.php` | `index`, `create`, `store`, `show`, `edit`, `update`, `destroy` | Delivery persons [`VERIFIED_FROM_SOURCE`] | — |
| `WareHouseController.php` | `index`, `store`, `update`, `destroy` | Warehouses [`VERIFIED_FROM_SOURCE`] | — |
| `WareHouseBranchController.php` | `index`, `store`, `update`, `destroy`, `getWarehouses` | Warehouse branches [`VERIFIED_FROM_SOURCE`] | — |
| `LocationsController.php` | `index`, `countries`, `warehouses`, `branches` | Locations aggregate [`VERIFIED_FROM_SOURCE`] | — |

## CMS/Promotions/Banners/Notifications/Misc
| Controller | Public methods | Routes usage | Notes |
|---|---|---|---|
| `BannerImagesController.php` | `index`, `create`, `store`, `show`, `edit`, `update`, `updateimage`, `destroy`, `destroyweb`, `updateOrder`, `addbanner` | Banner images (web/app) [`VERIFIED_FROM_SOURCE`] | — |
| `HomePromotionController.php` | `getAllowedCountryIds`, `index`, `store`, `update`, `destroy`, `updateSortOrder` | Home promotions [`VERIFIED_FROM_SOURCE`] | — |
| `ScrollingBarController.php` | `index`, `store` | Scrolling bar [`VERIFIED_FROM_SOURCE`] | — |
| `OfferImageController.php` | `index`, `offerImagess`, `update`, `destroy` | Offer images [`VERIFIED_FROM_SOURCE`] | — |
| `NotificationController.php` | `index`, `notifications`, `notifications`, `update`, `update`, `destroy`, `getProductsByCategory` | Notifications/reviews [`VERIFIED_FROM_SOURCE`] | Duplicate `notifications`, duplicate `update` in scan [`INFERRED`]. |
| `TodayDealsController.php` | `index`, `store`, `update`, `destroy` | Today deals [`VERIFIED_FROM_SOURCE`] | — |
| `TopCustomerController.php` | `index` | Top customers [`VERIFIED_FROM_SOURCE`] | — |
| `BulkOrderController.php` | `index` | Bulk enquiry [`VERIFIED_FROM_SOURCE`] | — |

## Utilities/Shipping/Shiprocket/Test/SMS/Home/Index
| Controller | Public methods | Routes usage | Notes |
|---|---|---|---|
| `ShippingController.php` | `freeship`, `update` | Free shipping [`VERIFIED_FROM_SOURCE`] | — |
| `ShiprocketController.php` | `__construct`, `pickupLocations`, `syncStatus` | Shiprocket ops [`VERIFIED_FROM_SOURCE`] | Uses `ShiprocketService` via DI/usage pattern [`INFERRED`] (safe path) vs inline cURL in others. |
| `TestController.php` | `test` | `GET /api/test` (no auth) [`VERIFIED_FROM_SOURCE`] | Unauthenticated write + `dd()` [`SECURITY_AUDIT.md`](./SECURITY_AUDIT.md#sec-a-04). |
| `sendsms.php` | `__construct`, `__destruct`, `sendme`, `sendmessage`, `checkdlr`, `availablecredit` | Utility (not typical route controller) [`INFERRED`] | Filename lowercase [`INFERRED`]. |
| `HomeController.php` | `__construct`, `index`, `root`, `lang`, `updateProfile`, `updatePassword` | Home routes [`VERIFIED_FROM_SOURCE`] | Avatar upload code exists (SEC-05 context in docs) but routes mostly auth-grouped [`INFERRED`]. |
| `IndexContrller.php` | `index`, `create`, `store`, `show`, `edit`, `update`, `destroy` | Index (typo filename) [`INFERRED`] | — |
| `Controller.php` | (base) | Base [`VERIFIED_FROM_SOURCE`] | — |
| `CouponController.php` | `index`, `addcoupon`, `update`, `destroy`, `toggleStatus` | Coupons (legacy) [`VERIFIED_FROM_SOURCE`] | — |
| `NewcouponController.php` | `index`, `store`, `toggle`, `edit`, `update`, `destroy` | New coupons [`VERIFIED_FROM_SOURCE`] | — |

## Critical callouts (route/controller mismatches)
- **BROKEN route call:** `SubCategoryController::validateSubCategoryName` does **not exist** (routes: `routes/web.php:136`, `390`) — [`VERIFIED_FROM_SOURCE`], [`SECURITY_AUDIT.md`](./SECURITY_AUDIT.md#sec-a-01). [`CONFLICTING_EVIDENCE`/fix required]
- **Resource missing store:** `Route::resource('users', DashboardUserController::class)->only(["index","store"])` but `DashboardUserController` has no `store()` method; code uses `userss()` instead [`VERIFIED_FROM_SOURCE`]. [`INFERRED`]
- **Password endpoints (IDOR risk):** `updatepass/{id}` → `DashboardUserController::update1` and `updatePasss/{userId}` → `UserController::updatePass` allow changing others' passwords; no ownership/role checks [`VERIFIED_FROM_SOURCE`]. See [`./SECURITY_AUDIT.md`](./SECURITY_AUDIT.md#sec-a-03).

Cross-links: [`../08-routing.md`](../08-routing.md) · [`./ROUTES_AND_API_REFERENCE.md`](./ROUTES_AND_API_REFERENCE.md) · [`./MIDDLEWARE_AND_AUTHORIZATION.md`](./MIDDLEWARE_AND_AUTHORIZATION.md) · [`./INTEGRATION_MAP.md`](./INTEGRATION_MAP.md) · [`./FRONTEND_REFERENCE.md`](./FRONTEND_REFERENCE.md) · [`./SECURITY_AUDIT.md`](./SECURITY_AUDIT.md)
