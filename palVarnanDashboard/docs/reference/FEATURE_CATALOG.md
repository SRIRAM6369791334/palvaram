# FEATURE_CATALOG

Major features/modules in Project A. Link to existing detailed docs: [12-business-logic.md](../12-business-logic.md), [09-controllers.md](../09-controllers.md), [08-routing.md](../08-routing.md).

## Orders management

- **Product orders (retail):** `ProductOrdersController`, `ProductOrder` model; lifecycle with statuses [VERIFIED_FROM_SOURCE] — [`01-project-overview.md:24-35`](../01-project-overview.md#L24-L35), [`12-business-logic.md`](../12-business-logic.md)
- **Product slots/packing:** `ProductSlotController` manages slots/packing flows [VERIFIED_FROM_SOURCE] — [`09-controllers.md`](../09-controllers.md), [web routes around slots](../../palVarnanDashboard/routes/web.php) (see slot-related routes)
- **Milk orders/slots (dairy):** `MilkOrdersController`, `MilkSlotController`, `MilkRefundController` [VERIFIED_FROM_SOURCE] — [`01-project-overview.md:18-20`](../01-project-overview.md#L18-L20)

## Stock & inventory

- **Stock management:** `StockController` with index/giftIndex, country scoping, distribution/transfers [VERIFIED_FROM_SOURCE] — [`01-project-overview.md:26-29`](../01-project-overview.md#L26-L29), [`StockController.php:33-37`](../../palVarnanDashboard/app/Http/Controllers/StockController.php#L33-L37)
- **Product stock/variants/thumbnails:** `ProductStock`, `ProductVarient`, `ProductThum`, related controllers [VERIFIED_FROM_SOURCE] — [`01-project-overview.md:24-29`](../01-project-overview.md#L24-L29), [`09-controllers.md`](../09-controllers.md)
- **Combo stock:** `CompoStockController` [VERIFIED_FROM_SOURCE] — [routes include combostock](../../palVarnanDashboard/routes/web.php#L315-321)
- **Gift inventory:** `GiftInventoryReportController`, gift stock flows [VERIFIED_FROM_SOURCE] — [`01-project-overview.md:36-44`](../01-project-overview.md#L36-L44)

## Products & catalog

- **Products, variants, thumbnails:** `ProductController`, `ProductVarientControllet`, `ProductThumController` [VERIFIED_FROM_SOURCE] — [`09-controllers.md`](../09-controllers.md), [web.php product routes](../../palVarnanDashboard/routes/web.php#L139-150), [352-365](../../palVarnanDashboard/routes/web.php#L352-L365)
- **Categories/brands/subcategories:** `CategoryController`, `BrandController`, `SubCategoryController` [VERIFIED_FROM_SOURCE] — [web.php 115-136](../../palVarnanDashboard/routes/web.php#L115-L136)
- **Gift catalog:** `GiftController`, `GiftVariantController`, `GiftSizeController`, `GiftColorController` [VERIFIED_FROM_SOURCE] — [`01-project-overview.md:36-39`](../01-project-overview.md#L36-L39)

## Users, customers, dashboard users

- **Customers:** `UserController` (handles customer addresses, summaries) [VERIFIED_FROM_SOURCE] — [web.php 96-109](../../palVarnanDashboard/routes/web.php#L96-L109)
- **Dashboard users:** `DashboardUserController` (index, userss, update, destroy, update1) [VERIFIED_FROM_SOURCE] — [web.php 336-340](../../palVarnanDashboard/routes/web.php#L336-L340); note **no `store()` method** though resource references it [VERIFIED_FROM_SOURCE] — [`VERIFIED_FACTS.md:44-46,52-56`](../audit/VERIFIED_FACTS.md#L44-L46,L52-L56)
- **Delivery persons:** `DeliveryPersonController` [VERIFIED_FROM_SOURCE] — [web.php 110-113](../../palVarnanDashboard/routes/web.php#L110-L113)
- **Top customers:** `TopCustomerController` (index only) [VERIFIED_FROM_SOURCE] — [web.php 367-369](../../palVarnanDashboard/routes/web.php#L367-L369)

## Geography

- **Countries/states/districts/areas:** `CountryController`, `AreaController`, area assignments via `AreaAssignController` [VERIFIED_FROM_SOURCE] — [web.php 127-131,151-159](../../palVarnanDashboard/routes/web.php#L127-L131), [web.php 151-159](../../palVarnanDashboard/routes/web.php#L151-L159)
- **Country scoping:** filters by `session('country_id')` in many screens [VERIFIED_FROM_SOURCE] — [`01-project-overview.md:45-47`](../01-project-overview.md#L45-L47)

## Coupons, gifts, offers, banners

- **Coupons:** `CouponController` [VERIFIED_FROM_SOURCE] — [web.php 322-326](../../palVarnanDashboard/routes/web.php#L322-L326)
- **Offer images/banners:** `OfferImageController`, `ScrollerController` (routes exist) [VERIFIED_FROM_SOURCE] — [web.php 330-333](../../palVarnanDashboard/routes/web.php#L330-L333), see [08-routing.md](../08-routing.md)
- **Gifts:** gift management controllers/views with `is_gift` separation [VERIFIED_FROM_SOURCE] — [`01-project-overview.md:36-44`](../01-project-overview.md#L36-L44), gift routes around 399+ [INFERRED] — [web.php 399+](../../palVarnanDashboard/routes/web.php#L399-L450) (gift section)

## Reports

- **Productwise reports:** `ProductwiseController` — methods `productWiseReport`, `filterProductWiseReport`, `exportExcel`, `exportPDF` (resource route commented) [VERIFIED_FROM_SOURCE] — [web.php 373-378](../../palVarnanDashboard/routes/web.php#L373-L378), [`VERIFIED_FACTS.md:47-50`](../audit/VERIFIED_FACTS.md#L47-L50)
- **Orderwise reports:** `OrderwiseController` — `orderwisereport`, `filterorderWiseReport`, `exportExcel`, `exportPDF` [VERIFIED_FROM_SOURCE] — [web.php 381-385](../../palVarnanDashboard/routes/web.php#L381-L385)
- **Gift productwise:** `GiftProductwiseController` [VERIFIED_FROM_SOURCE] — [`01-project-overview.md:36-39`](../01-project-overview.md#L36-L39)
- **Inventory reports:** `InventoryReportController`, `GiftInventoryReportController` [VERIFIED_FROM_SOURCE] — [`01-project-overview.md:52-55`](../01-project-overview.md#L52-L55)
- **Others:** purchase reports, supplier reports etc [INFERRED] — see [12-business-logic.md](../12-business-logic.md), [09-controllers.md](../09-controllers.md)

## Packing/dispatch, cancel/return

- **Packing/dispatch flows:** handled via `ProductSlotController`, `ProductOrdersController` [VERIFIED_FROM_SOURCE] — [`09-controllers.md`](../09-controllers.md), [web.php 392-399](../../palVarnanDashboard/routes/web.php#L392-L399)
- **Cancel requests:** `cancelrequests`, `approverequest` (ProductSlotController) [VERIFIED_FROM_SOURCE] — [web.php 393-394](../../palVarnanDashboard/routes/web.php#L393-L394)
- **Return requests:** `returnrequests`, `rejectReturnRequests` (ProductSlotController) [VERIFIED_FROM_SOURCE] — [web.php 396-398](../../palVarnanDashboard/routes/web.php#L396-L398)

## Shiprocket sync/tracking

- **Sync command:** `shiprocket:sync` every 30 min updates statuses and `product_slots.delivery_status`; emails customer if mailable exists [VERIFIED_FROM_SOURCE] — [`SyncShiprocketStatus.php:43-120`](../../palVarnanDashboard/app/Console/Commands/SyncShiprocketStatus.php#L43-L120), [`Kernel.php:18`](../../palVarnanDashboard/app/Console/Kernel.php#L18)
- **Service:** `ShiprocketService.php` for token, tracking, labels, pickup, invoices [VERIFIED_FROM_SOURCE] — [`ShiprocketService.php:9-108`](../../palVarnanDashboard/app/Services/ShiprocketService.php#L9-L108)
- **Controllers:** also push/create/cancel via inline cURL (with hardcoded creds) [VERIFIED_FROM_SOURCE] — [README reconciliations](./README.md#reconciliation-headlines-vs-existing-docs)

## Notifications

- **Notifications:** `NotificationController` (resource + custom routes) [VERIFIED_FROM_SOURCE] — [web.php 343-348](../../palVarnanDashboard/routes/web.php#L343-L348)

## Test/debug endpoints

- **API test (unauthenticated write):** `GET /api/test` → `TestController::test()` creates `milk_order_user_addresses` row with no middleware [VERIFIED_FROM_SOURCE] — [`api.php:25`](../../palVarnanDashboard/routes/api.php#L25), [`VERIFIED_FACTS.md:64-67`](../audit/VERIFIED_FACTS.md#L64-L67)
- **Telescope/Debugbar:** dev tools present [VERIFIED_FROM_SOURCE] — [`composer.json:12,28`](../../palVarnanDashboard/composer.json#L12), [`composer.json:28`](../../palVarnanDashboard/composer.json#L28)

## Roles/permissions UI

- `RoleController`, `DashboardUserController` provide management UI [VERIFIED_FROM_SOURCE] — [`01-project-overview.md:48-50`](../01-project-overview.md#L48-L50) (note enforcement gap)

## Purchase orders & suppliers

- **Purchase orders:** `PurchaseOrder`, `GiftPurchaseOrder`, controllers `PurchaseOrderController`, `GiftPurchaseOrderController` [VERIFIED_FROM_SOURCE] — [`01-project-overview.md:28-31`](../01-project-overview.md#L28-L31)
- **Suppliers:** `SupplierDetail`, `GiftSupplierController` [VERIFIED_FROM_SOURCE] — [`01-project-overview.md:28-31,36-39`](../01-project-overview.md#L28-L31,L36-L39)

See [12-business-logic.md](../12-business-logic.md), [09-controllers.md](../09-controllers.md), [05-database-schema.md](../05-database-schema.md) for full details.