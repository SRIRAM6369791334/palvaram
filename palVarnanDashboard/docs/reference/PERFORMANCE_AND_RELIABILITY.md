# PERFORMANCE AND RELIABILITY — Project A (`palVarnanDashboard`)

> Scope: runtime performance, query behaviour, network I/O, and reliability failure modes of the
> Laravel 8 admin dashboard. Documentation only; no source/config/DB was changed.
> Evidence vocabulary: `VERIFIED_FROM_SOURCE`, `VERIFIED_FROM_LIVE_SCHEMA`, `INFERRED`,
> `UNVERIFIED`, `NOT_AVAILABLE`, `CONFLICTING_EVIDENCE`. Secrets are never reproduced.

- **Home:** [README.md](./README.md) · **Facts:** [../audit/VERIFIED_FACTS.md](../audit/VERIFIED_FACTS.md)
- **Related:** [ARCHITECTURE.md](./ARCHITECTURE.md) · [BUSINESS_LOGIC.md](./BUSINESS_LOGIC.md) · [MIGRATION_AND_SQL_AUDIT.md](./MIGRATION_AND_SQL_AUDIT.md) · [DATABASE_SCHEMA.md](./DATABASE_SCHEMA.md) · [DEVELOPER_SETUP.md](./DEVELOPER_SETUP.md)
- **Existing numbered docs:** [../13-security-findings.md](../13-security-findings.md) · [../16-external-integrations.md](../16-external-integrations.md) · [../19-frontend-architecture.md](../19-frontend-architecture.md) · [../20-scheduling-and-jobs.md](../20-scheduling-and-jobs.md)

## 1. Headline risks

| # | Risk | Evidence | Tag |
|---|---|---|---|
| P1 | `/api/test` runs a write + `dd()` on every unauthenticated GET | `routes/api.php:25`, `TestController.php:24-44` | `VERIFIED_FROM_SOURCE` |
| P2 | Every page render fires ~11+ `COUNT` queries in a sidebar view composer | `AppServiceProvider.php:43-71` | `VERIFIED_FROM_SOURCE` |
| P3 | Every page render fires extra aggregate queries (topbar composer) | `AppServiceProvider.php:73-149` | `VERIFIED_FROM_SOURCE` |
| P4 | Inline Shiprocket cURL with `CURLOPT_TIMEOUT => 0` can hang a PHP worker forever | `ProductController:1006,1028`; `ProductOrdersController:106,129`; `ProductSlotController:127,154,198,225,283,346` | `VERIFIED_FROM_SOURCE` |
| P5 | Report endpoints materialise whole result sets (`.get()`, no pagination) | `ProductwiseController:125`; `OrderwiseController:131` | `VERIFIED_FROM_SOURCE` |
| P6 | N+1: one `ProductVarient` lookup per report row | `ProductwiseController:125-133` | `VERIFIED_FROM_SOURCE` |
| P7 | Scheduler sync every 30 min with no `withoutOverlapping()` / `onOneServer()` | `Kernel.php:18`; [20-scheduling-and-jobs.md §Missing scheduling guards](../20-scheduling-and-jobs.md) | `VERIFIED_FROM_SOURCE` |
| P8 | Telescope registered in `local` despite `dont-discover`; debugbar middleware in the request stack | `AppServiceProvider.php:26-29`; debugbar frame in `storage/logs/laravel.log` | `VERIFIED_FROM_SOURCE` (config), `VERIFIED_FROM_SOURCE` (log trace) |

## 2. Controller surface

`VERIFIED_FROM_SOURCE` — **76 controller files** (recursive) under `app/Http/Controllers`
(6 are `Auth/` scaffold). Largest files (lines):

| Controller | Lines |
|---|---:|
| `ProductController.php` | 1031 |
| `StockController.php` | 723 |
| `GiftController.php` | 557 |
| `ProductVarientControllet.php` | 373 |
| `UserController.php` | 340 |
| `ProductOrdersController.php` | 317 |
| `ProductSlotController.php` | 305 |
| `PurchaseOrderController.php` | 277 |
| `InventoryReportController.php` | 263 |
| `StockTrackingController.php` | 262 |

`INFERRED` — the largest files combine HTTP handling, validation, persistence, external HTTP calls,
and reporting in single classes; `ProductController` alone mixes product CRUD, bulk Excel import,
Shiprocket login/pickup, milk/product subscription creation, and invoice rendering. This monolithic
shape makes targeted optimisation and testing harder. See [../09-controllers.md](../09-controllers.md).

## 3. N+1 and missing eager loading

### 3.1 Report row loop does a query per row — `VERIFIED_FROM_SOURCE`

`ProductwiseController::getFilteredReportDataFromDates()` (`ProductwiseController.php:125-133`):

```php
return $query->orderByDesc('total_quantity')->get()->map(function ($item) {
    $variant = ProductVarient::with('unit')->find($item->product_varient_id); // 1 query per row
    ...
});
```

The aggregate query already returns one row per `(product_id, product_varient_id)` group; the
`map()` then re-queries `product_varient` (plus its `unit`) for every row. For a month of sales this
is `1 + N` queries where `N` = distinct product/variant groups. `INFERRED` — fix by joining
`product_varient`/`units` into the aggregate or by `whereIn(...)->get()->keyBy('id')` once.

### 3.2 Correlated sub-selects per row — `VERIFIED_FROM_SOURCE`

`OrderwiseController.php:77-79` selects three correlated subqueries **per result row**:

```php
DB::raw('(SELECT COUNT(*) FROM product_slots WHERE product_slots.order_id = product_orders.order_id) as total_items'),
DB::raw('(SELECT SUM(quantity) FROM product_slots WHERE product_slots.order_id = product_orders.order_id) as total_quantity'),
DB::raw('(SELECT name FROM countries WHERE countries.id = product_orders.country_id) as country_name')
```

`ProductwiseController.php:74` uses the same `countries` correlated sub-select. `INFERRED` — these
are `LEFT JOIN`/`GROUP BY` candidates; the `countries` lookup is static reference data that could be
cached.

### 3.3 Sidebar/topbar view composers run on every render — `VERIFIED_FROM_SOURCE`

`AppServiceProvider::boot()` registers two `View::composer` callbacks:

- `layouts.sidebar` (`AppServiceProvider.php:43-71`) executes **11** `count()` queries per render
  (`milk_orders`, `product_orders` ×7, `milk_refunds`, `product_refunds`, `product_tracking`).
- `layouts.topbar` (`AppServiceProvider.php:73-149`) executes: one multi-join `count()`, a
  `Country::all()`, an expiring-batches query with `JOIN` + `DATEDIFF`, and an overdue-distributions
  query with 3 joins.

`INFERRED` — these run for **every** authenticated page because both layouts are shared. No caching
layer (`Cache::remember`) is applied. Combined, a single dashboard page can emit **15+ aggregate
queries** before the controller runs any of its own. `UNVERIFIED` — actual query counts on a
running instance; not measured here.

### 3.4 Scheduler materialises the whole in-flight order set — `VERIFIED_FROM_SOURCE`

`SyncShiprocketStatus` uses `->get()` with no `chunk()` and eager-loads an unused `orderAddress`
relationship. Full analysis in
[20-scheduling-and-jobs.md §Selection query](../20-scheduling-and-jobs.md).

## 4. Query-heavy reports and exports

`VERIFIED_FROM_SOURCE`

| Feature | Location | Concern |
|---|---|---|
| Product-wise report | `ProductwiseController.php:60-135` | Full `.get()`; per-row N+1; correlated `countries` sub-select |
| Order-wise report | `OrderwiseController.php:60-142` | Full `.get()`; 3 correlated sub-selects per row |
| Excel export | `ProductwiseController.php:37-41`, `OrderwiseController.php:37-41` | Re-runs the same heavy query inside the request |
| PDF export (Dompdf) | `ProductwiseController.php:43-48`, `OrderwiseController.php:43-48` | CPU-bound render inside the HTTP request |
| Excel bulk import (ZIP) | `ProductController::bulkUpload()` | Minutes-long parse inside the request |
| Invoice render | `viewProductInvoice` | Dompdf render inside the request |

`INFERRED` — exports run synchronously (`QUEUE_CONNECTION=sync`, [20-scheduling-and-jobs.md](../20-scheduling-and-jobs.md#what-runs-inline-instead-of-in-a-queue)),
so a large month makes the browser wait and occupies a worker for the duration. `UNVERIFIED` — no
query timings were captured.

### 4.1 Only-FULL-GROUP-BY failures observed — `VERIFIED_FROM_SOURCE` (historic log)

`storage/logs/laravel.log` records repeated `SQLSTATE[42000] ... 1055 '<col>' isn't in GROUP BY`
errors for the product-slot/country aggregate, e.g.:

```
SQLSTATE[42000]: ... 1055 'palvarnan_db.product_orders.country_id' isn't in GROUP BY
(SQL: select ... (SELECT name FROM countries WHERE countries.id = product_orders.country_id) as country_name
 from `product_slots` inner join `product_orders` ... group by `product_slots`.`product_id`, ...)
```

`config/database.php:59` sets `'strict' => true`, so `ONLY_FULL_GROUP_BY` is enforced.
`VERIFIED_FROM_SOURCE` — the current `ProductwiseController.php:78` **does** include
`product_orders.country_id` in `groupBy`, so this specific log entry appears **OUTDATED** relative
to current source (see [SOURCE_COVERAGE_AND_VERIFICATION.md](./SOURCE_COVERAGE_AND_VERIFICATION.md)).
The log path (`C:\Users\AISH\...`) indicates the entries originate from another machine/historic run
`[INFERRED]`.

## 5. Network I/O — external integrations

### 5.1 Shiprocket sync every 30 minutes — `VERIFIED_FROM_SOURCE`

`app/Console/Kernel.php:18` schedules `shiprocket:sync` `->everyThirtyMinutes()`. The command makes
one tracking HTTP call per eligible order. Both applications share the Shiprocket account
([../16-external-integrations.md](../16-external-integrations.md), [../audit/VERIFIED_FACTS.md](../audit/VERIFIED_FACTS.md#L80-L85)).

Reliability gaps (`VERIFIED_FROM_SOURCE`; full table in
[20-scheduling-and-jobs.md](../20-scheduling-and-jobs.md)):

- No `->withoutOverlapping()` → slow/n hung runs overlap and duplicate customer emails.
- No `->onOneServer()` → single-host today; unsafe if scaled.
- No `->emailOutputOnFailure()` → **silent** failure.
- Command always `return 0` even when all orders fail → monitoring sees "healthy".
- `UNVERIFIED` — whether a cron entry invoking `php artisan schedule:run` exists on the host.

### 5.2 Inline cURL with no timeout — `VERIFIED_FROM_SOURCE`

Active Shiprocket cURL blocks set `CURLOPT_TIMEOUT => 0` (no timeout):

| File | Lines |
|---|---|
| `ProductController.php` | 1006, 1028 |
| `ProductOrdersController.php` | 106, 129 |
| `ProductSlotController.php` | 127, 154, 198, 225, 283, 346 |

`INFERRED` — under Apache/`mod_php` with a fixed worker pool, enough concurrent hung requests
exhaust the pool and take the whole app offline. The safe alternative
(`app/Services/ShiprocketService.php`) uses `Illuminate\Support\Facades\Http` with the shared config
and an 8-hour cached token (`ShiprocketService.php:21-42`), but the controllers bypass it.

## 6. Synchronous work (`QUEUE_CONNECTION=sync`)

`VERIFIED_FROM_SOURCE` — `.env`/`.env.example` set `QUEUE_CONNECTION=sync`; there are **no**
`app/Jobs`, `app/Events`, `app/Listeners`, or `app/Notifications` directories
([../20-scheduling-and-jobs.md §Directory inventory](../20-scheduling-and-jobs.md#directory-inventory--verified)).
Deferred work therefore executes inside the triggering HTTP request: Shiprocket login + calls,
Excel import/export, Dompdf renders, and order-status email. `INFERRED` — this couples request
latency to upstream network/CPU cost and cancels the benefit of the cached token
([ARCHITECTURE.md](./ARCHITECTURE.md)).

## 7. Debug tooling in dev

- `VERIFIED_FROM_SOURCE` — `AppServiceProvider::register()` (`AppServiceProvider.php:26-29`)
  registers `\Laravel\Telescope\TelescopeServiceProvider` when `app()->environment('local')`, despite
  `composer.json:63-69` disabling Telescope auto-discovery. `VERIFIED_FROM_LIVE_SCHEMA` — the shared
  DB contains `telescope_entries` (657 rows), `telescope_entries_tags`, `telescope_monitoring`.
- `VERIFIED_FROM_SOURCE` — `barryvdh/laravel-debugbar: "*"` is in production `require`
  (`composer.json:12`); `.env` sets `DEBUGBAR_ENABLED=false`. `storage/logs/laravel.log` frames
  include `vendor\barryvdh\laravel-debugbar\src\Middleware\InjectDebugbar.php(59)`, confirming the
  middleware was active in the captured request stack. See [../18-dependencies.md §debugbar](../18-dependencies.md).
- `VERIFIED_FROM_SOURCE` — `ProductSlotController.php:239` contains a live `dd($channel_id);` (not
  commented). `TestController.php:44` contains `dd($deliveryPerson);` on the unauthenticated
  `/api/test` route.

`INFERRED` — Telescope/debugbar add per-request query logging and response injection overhead; they
must never be enabled on the Render production service (see
[DEPLOYMENT_AND_CONFIGURATION.md](./DEPLOYMENT_AND_CONFIGURATION.md)).

## 8. Reliability observations from `storage/logs/laravel.log`

`VERIFIED_FROM_SOURCE` — the log is ~5.05 MB. Grouped distinct messages (values redacted; only
message text retained). These are **historic** (paths reference `C:\Users\AISH\...`) `[INFERRED]`.

| Count | Level | Message (truncated, no secrets) |
|---:|---|---|
| 12 | ERROR | `SQLSTATE[HY000] 1364 Field 'supplier_name' doesn't have a default value` (insert `purchase_orders`) |
| 5 | ERROR | `SQLSTATE[42S22] 1054 Unknown column 'batch_no' in 'field list'` (insert `productstocks`) |
| 4 | ERROR | `Undefined variable $allowedCountryIds` (`pages/supplier_details.blade.php`) |
| 4 | ERROR | `Undefined array key 0` (`invoicePages/product_orders_invoice.blade.php`) |
| 4 | ERROR | `There is no permission named 'Customers.index' for guard 'web'` |
| 4 | ERROR | `SQLSTATE[42000] 1055 ... isn't in GROUP BY` (product-slots report) |
| 3 | ERROR | `SQLSTATE[42S02] 1146 Table 'palvarnan_db.stocks' doesn't exist` |
| 3 | ERROR | `Call to undefined method App\Models\StockTransfer::productStock()` |
| 3 | ERROR | `Route [inventory.report.filter.data] not defined` (inventory report view) |
| 3 | ERROR | `Undefined array key "quantity"` |
| 3 | ERROR | `Too few arguments to function Spatie PermissionRegistrar::registerPermissions()` |
| 2 | ERROR | `SQLSTATE[42S02] 1146 Table 'palvarnan_db.suppliers' doesn't exist` |

`INFERRED` — these indicate: (a) a legacy table name (`stocks`) referenced somewhere while the live
table is `productstocks`; (b) Spatie permission names seeded/passed with the wrong guard/name
(`Customers.index`); (c) UI-level undefined variable/array-key defects. Cross-reference
[KNOWN_ISSUES_AND_TECHNICAL_DEBT.md](./KNOWN_ISSUES_AND_TECHNICAL_DEBT.md) §KI-A-10..KI-A-13.

## 9. Recommendations (performance & reliability)

1. **Remove `/api/test`** or gate it behind `auth:sanctum` + a non-production env check; delete the
   `dd()`. (P1)
2. Cache the sidebar/topbar badge counts (e.g. `Cache::remember(..., 30-60s)`) or move them to a
   lightweight polled endpoint. (P2, P3)
3. Replace `get()` with `chunkById()` in `SyncShiprocketStatus`; drop the unused `orderAddress`
   eager load. ([20-...](../20-scheduling-and-jobs.md))
4. Add `->withoutOverlapping()->onOneServer()->emailOutputOnFailure()` to the schedule; return
   non-zero on failure; log via `Log::` not `->error()`. (P7)
5. Rewrite report aggregates as joins and pre-load reference data once instead of per-row
   sub-selects/lookups. (P4, P5, P6)
6. Route all Shiprocket calls through `ShiprocketService` and set `timeout(15)` + retries. (P4)
7. Move imports/exports/emails/Shiprocket calls to a real queue (`redis`/`database`). (P6)
8. Ensure Telescope/debugbar are disabled outside `local`; never set `APP_DEBUG=true` in production.
9. Delete the `dd()` at `ProductSlotController.php:239` before release.
10. Add a missing-table/legacy-name cleanup pass (see log table). (KI-A-10)

## 10. Verification status

- Counts (76/63/60/330/2) re-derived from source on the audit date — `VERIFIED_FROM_SOURCE`.
- Query-count claims are static `INFERRED`; no profiler run — `UNVERIFIED`.
- Log entries are `VERIFIED_FROM_SOURCE` as text, but `INFERRED` as to current relevance.
- See [SOURCE_COVERAGE_AND_VERIFICATION.md](./SOURCE_COVERAGE_AND_VERIFICATION.md) for the
  per-area VERIFIED_CURRENT / OUTDATED / CONTRADICTED_BY_SOURCE matrix.
