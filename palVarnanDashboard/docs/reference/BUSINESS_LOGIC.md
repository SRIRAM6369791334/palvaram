# BUSINESS_LOGIC

Order workflows, stock tracking, slots/packing, Shiprocket flows, country scoping, permissions/role checks. Link to existing detailed analysis: [12-business-logic.md](../12-business-logic.md), [07-data-integrity.md](../07-data-integrity.md), [11-authorization-rbac.md](../11-authorization-rbac.md).

## Order workflows

### Retail (Product orders)

- **Lifecycle:** pending → processing → packed → dispatched → shipped → out_for_delivery → delivered (with variations like cancelled/return) [VERIFIED_FROM_SOURCE] — [`01-project-overview.md:32-35`](../01-project-overview.md#L32-L35), [`HomeController.php:78-88`](../../palVarnanDashboard/app/Http/Controllers/HomeController.php#L78-L88)
- **Tables:** `ProductOrder`, `ProductOrderItems`, `ProductSlot`, `product_tracking` etc [INFERRED] — see [05-database-schema.md](../05-database-schema.md), [12-business-logic.md](../12-business-logic.md)
- **Controllers:** `ProductOrdersController`, `ProductSlotController` manage order processing/packing/dispatch flows [VERIFIED_FROM_SOURCE] — [`09-controllers.md`](../09-controllers.md)

### Dairy (Milk orders)

- **Flow:** `MilkOrder`, `MilkSlot`, `MilkRefundController` with delivery slots and area assignments [VERIFIED_FROM_SOURCE] — [`01-project-overview.md:18-23`](../01-project-overview.md#L18-L23)

### Gift orders

- Separate controllers/models with `is_gift` separation [VERIFIED_FROM_SOURCE] — [`01-project-overview.md:36-44`](../01-project-overview.md#L36-L44)

## Stock tracking

- **Stock updates:** handled via stock controllers (`StockController`, `CompoStockController`) with distribution/transfers [VERIFIED_FROM_SOURCE] — [`01-project-overview.md:26-29`](../01-project-overview.md#L26-L29)
- **Country scoping:** many stock queries filter by `session('country_id')` [VERIFIED_FROM_SOURCE] — [`StockController.php:33-37`](../../palVarnanDashboard/app/Http/Controllers/StockController.php#L33-L37)
- **Gift separation:** `is_gift` filters in `StockController::index()` / `giftIndex()` [VERIFIED_FROM_SOURCE] — [`01-project-overview.md:40-44`](../01-project-overview.md#L40-L44)
- **Data integrity:** stock/quantity consistency noted as area of concern in existing docs [VERIFIED_FROM_SOURCE] — [`07-data-integrity.md`](../07-data-integrity.md), [`01-project-overview.md:71-74`](../01-project-overview.md#L71-L74)

## Slots/packing

- **ProductSlot:** manages slot-based delivery/packing; `ProductSlotController` handles cancel/return requests and approval flows [VERIFIED_FROM_SOURCE] — [web.php 392-399](../../palVarnanDashboard/routes/web.php#L392-L399)
- **Shiprocket sync updates:** `SyncShiprocketStatus` updates `product_slots.delivery_status` via `getStatusNum()` when SR status changes [VERIFIED_FROM_SOURCE] — [`SyncShiprocketStatus.php:99-102`](../../palVarnanDashboard/app/Console/Commands/SyncShiprocketStatus.php#L99-L102)

## Shiprocket integration flows

Two integration styles coexist:

1. **Service-based (safe):** `ShiprocketService` authenticates via config (`email/password/base_url`), caches token (8h), provides `trackAWB`, `getShipmentDetails`, `generateLabel`, `printInvoice`, `getPickupLocations`, `generatePickup` [VERIFIED_FROM_SOURCE] — [`ShiprocketService.php:9-108`](../../palVarnanDashboard/app/Services/ShiprocketService.php#L9-L108)
2. **Inline cURL (unsafe):** controllers perform direct Shiprocket API calls with **hardcoded credentials** in JSON payloads [VERIFIED_FROM_SOURCE] — [`VERIFIED_FACTS.md:71-79`](../audit/VERIFIED_FACTS.md#L71-L79), locations: `ProductController.php:869,1010`; `ProductOrdersController.php:110`; `ProductSlotController.php:131,202`

**Sync flow (scheduled):**
- Runs every 30 min: finds orders with `shiprocket_order_id`, non-terminal statuses (`processing`, `ready_for_pickup`, `shipped`, `out_for_delivery`) and `awb_code` [VERIFIED_FROM_SOURCE] — [`SyncShiprocketStatus.php:48-60`](../../palVarnanDashboard/app/Console/Commands/SyncShiprocketStatus.php#L48-L60)
- Maps SR `current_status_id` → local status; updates order and `product_slots.delivery_status`; emails customer if mailable exists [VERIFIED_FROM_SOURCE] — [`SyncShiprocketStatus.php:66-112`](../../palVarnanDashboard/app/Console/Commands/SyncShiprocketStatus.php#L66-L112)

See [16-external-integrations.md](../16-external-integrations.md), [20-scheduling-and-jobs.md](../20-scheduling-and-jobs.md).

## Country scoping

- `country_id` column added to most core tables (April 2026) [VERIFIED_FROM_SOURCE] — [`01-project-overview.md:45-47`](../01-project-overview.md#L45-L47)
- Many admin screens filter by `session('country_id')` (e.g. StockController) [VERIFIED_FROM_SOURCE] — [`StockController.php:33-37`](../../palVarnanDashboard/app/Http/Controllers/StockController.php#L33-L37)
- Multi-country operation affects queries across catalog/stock/orders [INFERRED] — see [05-database-schema.md](../05-database-schema.md), [12-business-logic.md](../12-business-logic.md)

## Permissions/role checks

- **Route layer:** no enforcement (only `auth`) [VERIFIED_FROM_SOURCE] — [`VERIFIED_FACTS.md:38-40`](../audit/VERIFIED_FACTS.md#L38-L40)
- **Controller layer:** Spatie roles/permissions UI exists; specific permission checks inside controllers may be present in places but not systematically enforced at routes [INFERRED] — [`11-authorization-rbac.md`](../11-authorization-rbac.md)
- **Known privilege issue:** password reset endpoints lack ownership/role checks — any authenticated dashboard user can reset another user’s password [VERIFIED_FROM_SOURCE] — [`VERIFIED_FACTS.md:58-60`](../audit/VERIFIED_FACTS.md#L58-L60)
  - `updatepass/{id}` → `DashboardUserController::update1` (line 340 in web.php) [VERIFIED_FROM_SOURCE] — [`web.php:340`](../../palVarnanDashboard/routes/web.php#L340)
  - `updatePasss/{userId}` → `UserController::updatePass` (line 370) [VERIFIED_FROM_SOURCE] — [`web.php:370`](../../palVarnanDashboard/routes/web.php#L370)
  - `DashboardUserController::update1` uses `Hash::make` at/around line ~171 [VERIFIED_FROM_SOURCE] — [`VERIFIED_FACTS.md:60`](../audit/VERIFIED_FACTS.md#L60)

## Route/method consistency (affects business flows)

- `validateSubCategoryName` called at `web.php:136,390` but **method does not exist** in `SubCategoryController` [VERIFIED_FROM_SOURCE] — [`VERIFIED_FACTS.md:41-44`](../audit/VERIFIED_FACTS.md#L41-L44), [`web.php:136,390`](../../palVarnanDashboard/routes/web.php#L136), [`web.php:390`](../../palVarnanDashboard/routes/web.php#L390)
- `DashboardUserController` resource `only(["index","store"])` references `store()` but controller has no `store()` method (has `index`, `userss`, `update`, `destroy`, `update1`) [VERIFIED_FROM_SOURCE] — [`VERIFIED_FACTS.md:44-46`](../audit/VERIFIED_FACTS.md#L44-L46), [`web.php:336`](../../palVarnanDashboard/routes/web.php#L336)
- `ProductwiseController` index route commented; explicit report routes used instead [CONFLICTING_EVIDENCE vs some docs] [VERIFIED_FROM_SOURCE] — [`VERIFIED_FACTS.md:47-50`](../audit/VERIFIED_FACTS.md#L47-L50), [`web.php:373-378`](../../palVarnanDashboard/routes/web.php#L373-L378)

## Unauthenticated write (test)

`GET /api/test` (no middleware) calls `TestController::test()` which writes `milk_order_user_addresses` row [VERIFIED_FROM_SOURCE] — [`api.php:25`](../../palVarnanDashboard/routes/api.php#L25), [`VERIFIED_FACTS.md:64-67`](../audit/VERIFIED_FACTS.md#L64-L67). This is a data-modifying unauthenticated endpoint (should be restricted/removed) [INFERRED].

## References

- [12-business-logic.md](../12-business-logic.md) — comprehensive business logic
- [07-data-integrity.md](../07-data-integrity.md) — stock/quantity consistency
- [13-security-findings.md](../13-security-findings.md) — prioritized security issues
- [08-routing.md](../08-routing.md) — routing surface
- [16-external-integrations.md](../16-external-integrations.md) — Shiprocket details