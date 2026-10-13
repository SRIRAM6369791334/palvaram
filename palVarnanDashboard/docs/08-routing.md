# 08 — Routing

## Files

`VERIFIED`

| File | Lines | Contents |
| --- | --- | --- |
| `routes/web.php` | 635 (39,695 bytes) | The entire application |
| `routes/api.php` | 26 | Two routes |
| `routes/channels.php` | 18 | Untouched Laravel scaffold |

## Route volume

`VERIFIED` — parsed from `routes/web.php`, excluding comments:

| Metric | Count |
| --- | --- |
| Live `Route::` definitions | 322 |
| Explicit `[Controller::class, 'method']` pairs | 271 |
| `Route::resource(...)` declarations | 31 (each `->only(["index","store"])` or similar) |
| `Route::view(...)` declarations | 5 |
| `Route::prefix(...)` groups | 2 |
| `Route::get` | 108 |
| `Route::post` | 172 |
| `Route::put` | 1 |
| `Route::delete` | 2 |
| Commented-out route definitions | 8 |

`UNVERIFIED` — the exact count `php artisan route:list` would report. It is larger than 322 because
each `Route::resource` expands to multiple routes and `Auth::routes()` (`routes/web.php:89`)
contributes 8.

The POST/GET ratio (172/108) reflects the code style: almost every mutation, including reads used
to populate filters, is a POST returning JSON. Route verbs are not used semantically.

## Middleware application

`VERIFIED`

```
Route::middleware(['auth'])->group(function () { ... });   // routes/web.php:91
```

- `Auth::routes()` at line 89 sits **outside** the auth group — this is the only unauthenticated
  web surface.
- The `auth` group runs lines 91 through the end of the file (~line 617).
- Line 92 additionally applies `->middleware("auth")` to the `/` route — redundant.
- **No route uses `can`, `role`, `permission`, `verified`, `throttle`, or `signed`.**
  `app/Http/Kernel.php` registers the `can` alias, but nothing applies it.

`VERIFIED` — `Localization` middleware is applied globally to the `web` group
(`app/Http/Kernel.php:44`), which sets the locale from `session('lang')`.

`VERIFIED` — API rate limiting is defined (60/minute per user or IP) but `routes/api.php` never
applies the `throttle:api`... actually it does: the `api` middleware group in `app/Http/Kernel.php:48`
includes `throttle:api`, and `RouteServiceProvider::boot()` mounts `routes/api.php` under the `api`
group. So the one unauthenticated API route **is** rate-limited at 60/min.

## Duplicate route paths — `BROKEN`

`VERIFIED` — these paths are each registered more than once. In Laravel, **the first registration
wins**, so the later handler is unreachable.

### Different handlers, same path — silent shadowing

| Method + path | First registration | Second registration (dead) |
| --- | --- | --- |
| `GET /get-warehouses` | line 500 → `InventoryReportController::getWarehouses` | line 528 → `StockDistributionController::getWarehouses` |
| `GET /get-branches` | line 501 → `InventoryReportController::getBranches` | line 529 → `StockDistributionController::getBranches` |

Two different features both wanted warehouse/branch dropdowns. The stock-distribution screen's
dropdowns are served by the inventory-report controller instead. Any query-parameter convention
difference between the two handlers is silently ignored.

### Identical duplicates

| Method + path | Lines |
| --- | --- |
| `POST /purchase-orders/update-status` → `PurchaseOrderController::updateStatus` | 417 and 524 |
| `GET /orderwisereport` → `OrderwiseController::orderwisereport` | 282 and 525 |
| `GET /order-wise-report/filter` → `OrderwiseController::filterorderWiseReport` | 283 and 526 |
| `GET /oreport/export/excel` → `OrderwiseController::exportExcel` | 284 and 527 |
| `GET /oreport/export/pdf` → `OrderwiseController::exportPDF` | 285 and 528 |
| `POST /validateSubCategoryName` → `SubCategoryController::validateSubCategoryName` | 136 and 290 |
| `GET /getsubcategory/{id}` → `ProductController::getProductsByCategory` | 146 (commented) and 147 |

The order-wise report block is **copied wholesale** into the gift section region and left in place.
Lines 282-285 and 525-528 are byte-identical including route names.

## Duplicate route names — `BROKEN`

`VERIFIED` —

| Name | Registrations |
| --- | --- |
| `products.getProductDetail` | line 144 (`getProductDetail`) and line 145 (`getproductfilter`) |

Both `Route::post("getProductDetail", ...)` and `Route::post("getproductfilter", ...)` are named
`products.getProductDetail`. Laravel's name lookup resolves to the last registration, so
`route('products.getProductDetail')` points at the **filter** endpoint. Any Blade view using
`route('products.getProductDetail')` expecting order/product detail data reaches the wrong handler.

Note that `filter.data` (lines 492 and 497) and `index` (lines 485 and 496) look like duplicates but
are not — both sit inside `->name('...')` prefix groups that disambiguate them.

## Routes pointing at missing controller methods — `BROKEN`

Verified by parsing every `[Controller::class, 'method']` pair and every
`Route::resource(...)->only([...])` declaration against the controller files.

| Route | Missing method | Impact |
| --- | --- | --- |
| `POST /validateSubCategoryName` (lines 136, 290) | `SubCategoryController::validateSubCategoryName` | **HTTP 500** `Call to undefined method` |
| `GET /productwisereport` (resource `productwisereport`) | `ProductwiseController::index` | **HTTP 500** — the resource declares `->only(["index"])` but the controller implements `productWiseReport` (routed separately at line 275) and no `index` |

## Resource routes declaring a `store()` that does not exist — `BROKEN`

`VERIFIED` — 9 resources are declared `->only(["index","store"])`, but the controller has no `store()`
method. `POST` to the collection URL returns HTTP 500.

| Resource path | Controller | Actual method that handles creation |
| --- | --- | --- |
| `cancelproduct` | `CancelProductController` | — (none found) |
| `coupons` | `CouponController` | — (POST `/updatecoupon/{id}` at line 325; `/admin/coupons/{id}/update` at line 188) |
| `users` | `DashboardUserController` | `userss()` |
| `milkOrders` | `MilkOrdersController` | — |
| `milkRefunds` | `MilkRefundController` | `refundMilkSlot()` |
| `review` | `NotificationController` | — |
| `offerImages` | `OfferImageController` | — |
| `productRefunds` | `ProductRefundController` | `refundProductSlot()` |
| `productthump` | `ProductThumController` | — |
| `productvarient` | `ProductVarientControllet` | `addproductvarient()` |
| `productwisereport` | `ProductwiseController` | `productWiseReport()` |

For `users` this is not merely dead — `DashboardUserController::userss()` (the real create-user
handler) is registered separately, so the working path and the broken resource path coexist. The
same pattern holds for `productvarient`, `milkRefunds`, and `productRefunds`.

## Dead imports — `INCOMPLETE`

`VERIFIED` — `routes/web.php` imports classes that are never used by any route:

| Import | Line | File exists? |
| --- | --- | --- |
| `App\Http\Controllers\LocationController` | 3 | **No** — only `LocationsController` exists and is used |
| `App\Http\Controllers\RefundController` | 35 | **No** — only `MilkRefundController` / `ProductRefundController` exist and are used |
| `App\Http\Controllers\IndexContrller` | 17 | Yes (triple-`l` typo), never referenced by a route |
| `App\Http\Controllers\Category` (model, line 70) | 70 | Yes, never referenced |

**Correction to earlier audit notes:** `LocationController` and `RefundController` are **not** wired
to routes. Because PHP does not autoload unused `use` statements, these imports are harmless at
runtime — they do not produce errors. They are leftovers that will confuse the next developer.

## Catch-all route — not present

`VERIFIED` — the wildcard `Route::get('{any}', [HomeController::class, 'index'])->name('index')`
is **commented out** at line 632. This is fortunate: with it active, every unmatched URL would reach
`HomeController::index()`, which calls `view($request->path())` and renders any file in
`resources/views` matching the request path — including the ~86 template demo pages. See
[13-security-findings.md](13-security-findings.md).

`INCOMPLETE` — `HomeController::index()` remains implemented and reachable if the route is ever
re-enabled.

## `routes/api.php`

`VERIFIED`

| Method + path | Middleware | Notes |
| --- | --- | --- |
| `GET /api/user` | `auth:sanctum` | Laravel scaffold; returns `$request->user()`. |
| `GET /api/test` | **`api` group only — no auth** | `TestController::test()`. Writes a DB row and `dd()`-dumps customer delivery data. |

Because `config/auth.php` has no `sanctum` guard user provider configured for `DashboardUser`,
`/api/user` may not be usable as intended. `UNVERIFIED`.

`/api/test` is covered in detail in [13-security-findings.md](13-security-findings.md).

## `routes/channels.php`

`VERIFIED` — untouched scaffold. `Broadcast::channel('App.Models.User.{id}', ...)` authorises
against `User`, but broadcasting is driven to the `log` driver, so the channel is never exercised.

## Route conventions

`VERIFIED` — the file mixes styles freely:

- `Route::resource` + separate `updateX/{id}` / `destroyX/{id}` POSTs instead of `PUT`/`DELETE`
  (e.g. `categories`, `subcategories`, `brands`, `customers`).
- Bare verbs without names (`Route::post("getProductsOptions", ...)`) and named verbs elsewhere.
- Inconsistent casing and plurals: `subcategories` vs `SubCategories`, `productthump`,
  `productvarient`, `milkOrders` vs `milk_orders` table names.
- GET used for data mutations: `Route::get("Getcitys/{custid}")`,
  `Route::get("getsubcategory/{id}")`.

`BROKEN` — because update/delete are POST rather than PUT/DELETE, the browser cannot rely on
idempotency, and the CSRF token requirement applies to an unbounded set of state-changing URLs.