# 04 — Directory Structure

All paths relative to `palVarnanDashboard/`.

```
.
├── app/
│   ├── Console/
│   │   ├── Commands/SyncShiprocketStatus.php
│   │   └── Kernel.php
│   ├── Exceptions/Handler.php
│   ├── Helpers/validation_helpers.php
│   ├── Http/
│   │   ├── Controllers/        84 files
│   │   ├── Kernel.php
│   │   └── Middleware/         9 files
│   ├── Models/                 63 files
│   ├── Policies/               (empty — no policies)
│   ├── Providers/              6 files
│   └── Services/ShiprocketService.php
├── bootstrap/                  app.php, cache/
├── config/                     19 files
├── database/
│   ├── factories/              1 file
│   ├── migrations/             60 files
│   └── seeders/                2 files
├── docs/                       this documentation
├── public/                     229 MB — document root
├── resources/
│   ├── css/  fonts/  images/  images1/
│   ├── js/    app.js + 36 pages/*.init.js
│   ├── lang/  libs/
│   ├── scss/  bootstrap.scss, icons.scss, app.scss
│   └── views/                  197 files
├── routes/                     web.php, api.php, channels.php
├── storage/                    6 MB — includes framework cache
├── stubs/
├── tests/                      4 files (scaffold)
├── vendor/                     56.89 MB
├── yesbe_dash/                 empty
├── .env  .env.example  .htaccess
├── check_db_raw.php  check_db_schema.php  debug_query.php
├── dump_order.php  seed_promotions.php  test.php  tmp_schema_check.php
├── schema_output.txt
└── composer.json  package.json  webpack.mix.js  phpunit.xml
```

## `app/Http/Controllers/` — 84 files

`VERIFIED` — the controller layer is the widest part of the codebase and is organised by feature
rather than by technical role. Notable entries:

**Catalog & pricing**
`ProductController` (1031 lines, largest file in the project), `ProductManagementController`,
`ProductVarientControllet` *(sic — misspelling preserved in the filename)*, `ProductThumController`,
`CategoryController`, `SubCategoryController`, `BrandController`, `UnitController`,
`NewcouponController`, `CouponController`, `TodayDealsController`, `HomePromotionController`,
`OfferImageController`, `ScrollingBarController`, `BannerImagesController`.

**Orders & fulfilment**
`ProductOrdersController`, `ProductSlotController`, `PackingOrderController`,
`PackingCompleteController`, `PackingDispatchController`, `PackingDeliveryController`,
`MilkOrdersController`, `MilkSlotController`, `CancelProductController`, `BulkOrderController`,
`OrderwiseController`, `OrderSummeryController` *(sic)*, `ProductwiseController`,
`TopCustomerController`, `ShippingController`, `NotificationController`.

**Inventory**
`StockController` (723 lines), `StockDistributionController`, `StockTrackingController`,
`InventoryReportController`, `CompoStockController`, `PurchaseOrderController`,
`PurchaseReportController`, `SupplierDetailController`.

**Gifts** (parallel retail stack)
`GiftController` (557), `GiftVariantController` (329), `GiftColorController`,
`GiftSizeController`, `GiftPurchaseOrderController` (259), `GiftInventoryReportController` (236),
`GiftPurchaseReportController`, `GiftProductwiseController`, `GiftSupplierController`.

**Customers & access**
`UserController` (340), `DashboardUserController`, `DeliveryPersonController`, `RoleController`,
`AreaController`, `AreaAssignController`, `LocationController` / `LocationsController` *(both
referenced — see [08-routing.md](08-routing.md))*.

**Shipping**
`ShiprocketController`.

**Debug**
`TestController` — reachable unauthenticated via `routes/api.php`. See
[13-security-findings.md](13-security-findings.md).

## `app/Models/` — 63 files

`VERIFIED` — three implement `Illuminate\Contracts\Auth\Authenticatable`: `User`,
`DashboardUser`, `DeliveryPerson`. The remainder are plain Eloquent models spanning catalogue,
inventory, orders, slots, refunds, returns, suppliers, warehouses, branches, areas, coupons,
promotions, notifications, and permissions.

Naming is inconsistent: singular/plural mix (`Product`, `Areas` vs `Area`), irregular plurals
(`Category` vs `SubCategory`), and snake-to-camel without separation (`ProductVarient`,
`ProductThum`, `OrderSummery`, `WareHouse`, `StockTransfer`). See
[06-data-model.md](06-data-model.md).

## `app/Providers/`

`VERIFIED`

| File | Note |
| --- | --- |
| `AppServiceProvider.php` | Telescope gate; sidebar `View::composer` running count queries on every request. |
| `AuthServiceProvider.php` | **Empty policy map; no gates defined.** |
| `BroadcastServiceProvider.php` | Stock scaffold. |
| `EventServiceProvider.php` | Stock scaffold. |
| `RouteServiceProvider.php` | `HOME = '/'`; API rate limit 60/min per user or IP. |
| `TelescopeServiceProvider.php` | Present while Telescope is in `dont-discover`. |

## `app/Middleware/` — 9 files

`VERIFIED` — all are Laravel 8 defaults except:

- `Localization.php` — sets `App::setLocale()` from `session('lang')`. Registered in the `web`
  group (`app/Http/Kernel.php:44`).
- `TrustHosts.php` exists but is **commented out** of the global stack (`app/Http/Kernel.php:19`).
- `AuthenticateSession` is commented out of the `web` group (`app/Http/Kernel.php:39`), so session
  invalidation on password change does not occur. See [10-authentication.md](10-authentication.md).

## `resources/views/`

`VERIFIED`

| Subdirectory | Contents |
| --- | --- |
| `layouts/` | 16 files — `master.blade.php` is the primary admin shell. |
| `pages/` | 75 files — one per admin screen. |
| `pages/gifts/` | 16 files — gift equivalents. |
| `ajaxPages/` | 8 partials returned as HTML fragments. |
| `invoicePages/` | 1 — `product_orders_invoice.blade.php`. |
| `exports/` | 2 — `orderwise`, `productwise` Excel/PDF templates. |
| `emails/` | 1 — `order_status_updated.blade.php`. |
| `components/` | 1 — `breadcrumb.blade.php`. |
| *(root)* | ~86 files of Symox demo pages (`ecommerce-*`, `charts-*`, `ui-*`, `pages-*`, `forms-*`). |

The ~86 demo pages at the view root are template showreel content, not application screens. See
[19-frontend-architecture.md](19-frontend-architecture.md).

## `routes/`

`VERIFIED` — three files. `web.php` (~635 lines) holds essentially the entire application; `api.php`
has two routes; `channels.php` is the untouched Laravel scaffold. Details in
[08-routing.md](08-routing.md).

## `storage/` — 6 MB

`VERIFIED` — contains populated `framework/cache`, `framework/sessions`, `framework/views`, and
`logs`. Session and view caches are **present in the tree**, meaning either the deploy is not
excluding them or the directory was committed. `VERIFIED` for presence; commit status is
`UNVERIFIED` because `.gitignore` does not ignore `storage/` contents by default beyond
`/storage/*.key`.

## Notable absences

`VERIFIED` — no `docker-compose.yml`, no `.github/` workflows, no CI configuration, no
`Dockerfile`, no `.gitlab-ci.yml`, no `phpstan.neon`/`psalm.xml`, no `.eslintrc`, no Prettier
config, no `.env.testing`.