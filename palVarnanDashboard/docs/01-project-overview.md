# 01 — Project Overview

## What this system is

`palVarnanDashboard` is a **multi-country dairy and retail administration dashboard** built on
Laravel 8. It is the back-office application for a milk-subscription and packaged-goods business.

It is **not** a customer-facing storefront. A separate project (`palVarnan/`) in the repository
parent serves the customer/mobile side. `VERIFIED` — the dashboard has no public product browsing
routes; the only unauthenticated web surface is Laravel's `Auth::routes()` at
`routes/web.php:89`.

## What the dashboard does

Two product families run through parallel, largely duplicated code paths.

**Dairy / subscription business**
- Milk orders with delivery slots (`MilkOrder`, `MilkSlot`, controllers `MilkOrdersController`,
  `MilkSlotController`, `MilkRefundController`).
- Delivery-person assignment per area (`Area`, `AreaAssign`, `DeliveryPersonController`).
- Customer default addresses drive slot delivery (`user_default_address_id`, see
  `app/Http/Controllers/HomeController.php:110-113`).

**Retail / packaged goods business**
- Catalog with variants, units, brands, categories, subcategories, and thumbnail images
  (`Product`, `ProductVarient`, `ProductThum`, `ProductVarientControllet`).
- Per-warehouse, per-branch stock with distribution to stores
  (`ProductStock`, `StockDistribution`, `StockTransfer` — `app/Http/Controllers/StockController.php`).
- Purchase orders with supplier records and inventory receipt
  (`PurchaseOrder`, `SupplierDetail`, `GiftPurchaseOrder`).
- Order lifecycle: pending → processing → packed → dispatched → shipped → out_for_delivery →
  delivered, with separate `ProductOrder` and `ProductSlot` tables. `VERIFIED` — status strings are
  hardcoded string literals in queries, e.g. `app/Http/Controllers/HomeController.php:78-88`.

**Gift business**
A third, near-duplicate variant of the retail flow with its own controllers and views
(`GiftController`, `GiftVariantController`, `GiftSizeController`, `GiftColorController`,
`GiftPurchaseOrderController`, `GiftInventoryReportController`, `GiftProductwiseController`,
`GiftSupplierController`) and its own views under `resources/views/pages/gifts/`.
Separation is enforced by an `is_gift` boolean flag on `products`, `categories`, and
`supplier_details`. `VERIFIED` — migration `2026_02_12_155843_add_is_git_to_gift_tables.php`
and the `is_gift` filters in `StockController::index()` / `giftIndex()`.

**Cross-cutting features**
- Multi-country operation: a `country_id` column was added to most core tables in April 2026, and
  most admin screens filter by `session('country_id')`. `VERIFIED` — e.g.
  `app/Http/Controllers/StockController.php:33-37`.
- Spatie role/permission data with a user-management UI. `VERIFIED` — migration
  `2026_04_06_124404_create_permission_tables.php`, `RoleController`, `DashboardUserController`.
- Shiprocket shipping integration for order push and status sync. `VERIFIED` —
  `app/Services/ShiprocketService.php`, `app/Console/Commands/SyncShiprocketStatus.php`.
- Inventory, order-wise, product-wise, and purchase reporting with Excel/PDF export.
  `VERIFIED` — `ReportsController`, `InventoryReportController`, `barryvdh/laravel-dompdf`,
  `maatwebsite/excel`.

## Who logs in

`DashboardUser` is the only authenticatable model wired into the `web` guard, even though `User`
and `DeliveryPerson` also implement `Authenticatable`. `VERIFIED` — `config/auth.php`.

The dashboard's home page is a KPI tile dashboard hitting ten `COUNT(*)` subqueries plus twelve
per-status order counts in a single request. `VERIFIED` — `app/Http/Controllers/HomeController.php:44-88`.

## Health summary

| Area | Status | Note |
| --- | --- | --- |
| Framework baseline | `VERIFIED` | Laravel 8.65, EOL upstream. |
| Routing integrity | `BROKEN` | Live routes call missing methods; duplicate paths and names. |
| Authorization | `BROKEN` | Roles defined, never enforced on routes. |
| Credential hygiene | `BROKEN` | Hardcoded API credentials in source. |
| Schema management | `BROKEN` | Migrations cover a fraction of live tables. |
| Test coverage | `INCOMPLETE` | Laravel scaffold only. |
| Business logic | `INCOMPLETE` | Works, but heavily duplicated and inconsistently validated. |
| Frontend | `INCOMPLETE` | Template works; large demo surface shipped alongside it. |

## Where to start reading

1. [08-routing.md](08-routing.md) — the request surface.
2. [12-business-logic.md](12-business-logic.md) — what actually happens on an order.
3. [13-security-findings.md](13-security-findings.md) — what to fix first.
4. [26-refactoring-roadmap.md](26-refactoring-roadmap.md) — the ordered plan.