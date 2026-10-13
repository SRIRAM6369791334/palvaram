# 09 — Controllers

84 files in `app/Http/Controllers/`. This document covers size distribution, structural patterns,
validation gaps, and cross-cutting defects.

## Size distribution

`VERIFIED` — largest files by line count:

| Controller | Lines |
| --- | --- |
| `ProductController.php` | 1031 |
| `StockController.php` | 723 |
| `GiftController.php` | 557 |
| `ProductVarientControllet.php` | 373 |
| `UserController.php` | 340 |
| `GiftVariantController.php` | 329 |
| `ProductOrdersController.php` | 317 |
| `ProductSlotController.php` | 305 |
| `PurchaseOrderController.php` | 277 |
| `InventoryReportController.php` | 263 |
| `StockTrackingController.php` | 262 |
| `GiftPurchaseOrderController.php` | 259 |
| `CategoryController.php` | 246 |
| `GiftInventoryReportController.php` | 236 |
| `ProductManagementController.php` | 232 |
| `BannerImagesController.php` | 201 |
| `StockDistributionController.php` | 170 |
| `HomeController.php` | 164 |
| `SupplierDetailController.php` | 157 |
| `NotificationController.php` | 155 |

`BROKEN` — `ProductController.php` at 1031 lines is the single largest file in the project and
holds unrelated responsibilities: catalogue CRUD, image upload, Excel import/export, milk
subscription creation, product subscription creation, slot generation, delivery-person assignment,
address capture, invoice rendering, order status update with embedded cURL, and ZIP extraction.
See [12-business-logic.md](12-business-logic.md).

## Structural patterns

### Almost no constructor middleware — `BROKEN`

`VERIFIED` — only `HomeController` declares a constructor, and it calls
`$this->middleware('auth')` (`app/Http/Controllers/HomeController.php:30`). Because
`Controller::__construct()` is **not** called with `parent::__construct()`, the middleware trait
registration from the base class is bypassed — the `auth` middleware applied this way is not
guaranteed to run, and any future base-class constructor logic is lost.

Authorization is therefore applied per-route in `routes/web.php` (group-level `auth`) and
**nowhere else**.

### Business methods exposed as routes — `BROKEN`

`VERIFIED` — several methods that read like internal helpers are registered as their own HTTP
endpoints and are publicly callable:

| Method | Route | Also called internally |
| --- | --- | --- |
| `ProductController::createMilkSubscription(Request)` | `routes/web.php:218` | — |
| `ProductController::createProductSubscription(Request)` | `routes/web.php:224` | — |
| `ProductController::createMilkSlot($orderId)` | `routes/web.php:220` | yes, from `createMilkSubscription()` |
| `ProductController::createProductSlot($orderId, $selettype, Request)` | `routes/web.php:226` | yes, from `createProductSubscription()` |
| `ProductController::viewProductInvoice($orderId = null)` | `routes/web.php:238` | — |
| `ProductController::upadetstatus(Request)` | `routes/web.php:241` | — |
| `HomeController::updateProfile(Request)` | `routes/web.php:629` (commented out) | — |

`VERIFIED` — `assignDeliverProductOrder($orderId, $user)` and
`addProductOrderDeliveryAddress($orderId, $user)` are `public` but **not routed**. They are
reachable only through `createProductSubscription()`. They should be `protected`/`private`.

Because `createMilkSlot` and `createProductSlot` are routed *and* called internally, an
authenticated user can invoke the finalisation half of an order independently of the creation half
— re-running slot creation and the stock decrement for an order that already exists. See
[13-security-findings.md](13-security-findings.md).

### Typo in a public method name — `VERIFIED`

`ProductController::upadetstatus(Request $request)` ("upadetstatus", missing `a`) is routed as
`POST /updatestatus` (`routes/web.php:503`). Any search for `updateStatus` in this controller finds
nothing.

### Dead controllers — `VERIFIED`

| Controller | Status |
| --- | --- |
| `IndexContrller` | Imported at `routes/web.php:17`, never routed. |
| `GiftOrderWiseController` | Present in `app/Http/Controllers/`, never referenced by any route. Gift order-wise reporting is served by `GiftProductwiseController` and `GiftPurchaseReportController`. |
| `sendsms` | Present, never routed. Lowercase filename, no class-name convention match. |
| `TestController` | Routed **only** in `routes/api.php`, without authentication. |

## Validation coverage — `BROKEN`

`VERIFIED` — validation is present but wildly inconsistent. Comparison of structurally parallel
endpoints:

**Well validated**

| Endpoint | Rules |
| --- | --- |
| `DashboardUserController::userss()` | `name`, `email`, `phone_number`, `role`, `status`, `password` required; `country_id` required array; `country_id.*` must exist in `countries` |
| `DashboardUserController::update()` | same, `country_id` nullable |
| `StockController::transferStock()` | `from_warehouse_id`, `from_branch_id`, `product_id`, `to_branch_id` required; `transfer_quantity` required integer min:1 |
| `StockController::warehouseTransfer()` | adds `different:from_warehouse_id` |
| `HomeController::updateProfile()` | `name`/`email` required, `avatar` nullable image mimes:jpg,jpeg,png max:1024 |

**Not validated at all**

| Endpoint | Notes |
| --- | --- |
| `StockController::store()` | Reads `$request->product_id`, `pro_ver_id`, `category_id`, `warehouse_id`, `stock_quantity` with no `validate()` call. |
| `StockController::update()` | No validation; `(int)` casts silently coerce `"abc"` to `0`. |
| `StockController::reduceStock()` | No validation; negative `stock_quantity` would **increase** stock. |
| `StockController::update1()` | No validation. |
| `StockController::acceptTransfer()` / `cancelTransfer()` | No validation of `$id`; `findOrFail` used. |
| `ProductController::createMilkSubscription()` | `plan_type`, `user_id`, `product_id`, `quantity`, `from_date`, `to_date` unvalidated. |
| `ProductController::createProductSlot()` | `product_id[]`, `productvar_id[]`, `product_quantity[]`, `from_date`, `user_id` unvalidated. |

`BROKEN` — `reduceStock()` and `update()` accept a negative `stock_quantity`, which adds rather
than subtracts stock. `(int) '-5'` is a valid integer and passes the absence of any rule.

**Business-rule validation gaps in otherwise-validated methods**

`VERIFIED` — `DashboardUserController::userss()` requires `password` with no minimum length and no
confirmation. `StockController::transferStock()` validates `transfer_quantity` as
`integer|min:1` but does not verify that the sender store has that quantity until later; the
availability check itself is done by hand and returns 422 with a message rather than a validation
error.

## Cross-cutting defects

### Raw query builder used alongside Eloquent — `INCOMPLETE`

`VERIFIED` — `ProductController::upadetstatus()` and the cancellation path
(`ProductController.php:1122-1134`) use `DB::table('productstocks')` and
`DB::table('product_varient')` directly. This bypasses model events, `updated_at` maintenance, and
global scopes. Elsewhere in the same class the Eloquent models are used for the same tables.

### Direct `$request` field access without `input()` — `VERIFIED`

`VERIFIED` — throughout `StockController`, `ProductController`, and `ProductOrdersController`,
values are read as `$request->stock_quantity`, `$request->product_id` rather than
`$request->input(...)`/`$request->validated(...)`. This is stylistic but it means no single choke
point exists for sanitising or defaulting request data.

### Exceptions swallowed into generic 500s — `BROKEN`

`VERIFIED` — `StockController::transferStock()`, `warehouseTransfer()`, `acceptTransfer()`, and
`cancelTransfer()` all wrap their bodies in `try { ... } catch (\Exception $e) {
return response()->json(['message' => $e->getMessage()], 500); }`.

Returning `$e->getMessage()` to the client leaks internal detail — SQL errors including table and
column names, file paths, and framework internals. With `APP_DEBUG=true` this is compounded by the
debug page.

### Dead code retained in-line — `INCOMPLETE`

`VERIFIED` — `StockController` retains fully commented-out earlier implementations of
`reduceStock()` and `update1()`, plus commented-out loops inside `lowstock()`. `ProductController`
retains a 20-line commented-out cURL block (`lines 858-878`) — see
[16-external-integrations.md](16-external-integrations.md).

## Controller-to-feature mapping

`VERIFIED` — grouped by route surface:

| Feature area | Controllers |
| --- | --- |
| Catalogue | `ProductController`, `ProductManagementController`, `ProductVarientControllet`, `ProductThumController`, `CategoryController`, `SubCategoryController`, `BrandController`, `UnitController` |
| Promotions/content | `TodayDealsController`, `HomePromotionController`, `CouponController`, `NewcouponController`, `OfferImageController`, `ScrollingBarController`, `BannerImagesController` |
| Orders | `ProductOrdersController`, `ProductSlotController`, `CancelProductController`, `BulkOrderController`, `OrderwiseController`, `OrderSummeryController`, `ProductwiseController`, `TopCustomerController`, `ShippingController` |
| Packing/dispatch | `PackingOrderController`, `packingCompleteController` *(lowercase `p` class name)*, `PackingDispatchController`, `PackingDeliveryController` |
| Milk | `MilkOrdersController`, `MilkSlotController`, `MilkRefundController` |
| Inventory | `StockController`, `StockDistributionController`, `StockTrackingController`, `CompoStockController`, `InventoryReportController`, `PurchaseOrderController`, `PurchaseReportController`, `SupplierDetailController` |
| Gifts | `GiftController`, `GiftVariantController`, `GiftSizeController`, `GiftColorController`, `GiftPurchaseOrderController`, `GiftInventoryReportController`, `GiftPurchaseReportController`, `GiftProductwiseController`, `GiftSupplierController` |
| Customers | `UserController`, `DashboardUserController`, `DeliveryPersonController`, `AreaController`, `AreaAssignController`, `CountryController`, `LocationsController` |
| Access | `RoleController` |
| Shipping | `ShiprocketController` |
| Misc | `HomeController`, `ReportsController`, `NotificationController` |
| Debug | `TestController`, `sendsms` (unrouted), `IndexContrller` (unrouted), `GiftOrderWiseController` (unrouted) |

`VERIFIED` — `packingCompleteController.php` declares the class as `packingCompleteController` with
a lowercase initial, violating PSR-4. It works because PHP class names are case-insensitive and
Windows filesystems are case-insensitive, but it fails PSR-4 autoloading conventions and would
break on a case-sensitive deployment toolchain.