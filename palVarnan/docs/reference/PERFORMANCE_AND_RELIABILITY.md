# Performance and Reliability — `palVarnan`

> Scope: runtime performance and reliability risks in the `palVarnan` storefront.
> This is the source-level companion to [`../16-performance.md`](../16-performance.md). Where a fact
> already exists there it is **linked, not repeated**. This document adds code citations, the
> request-path sequencing, and the reliability (not just speed) consequences of synchronous I/O.
>
> Evidence vocabulary: `VERIFIED_FROM_SOURCE`, `VERIFIED_FROM_LIVE_SCHEMA`, `INFERRED`,
> `UNVERIFIED`, `NOT_AVAILABLE`, `CONFLICTING_EVIDENCE`.
>
> **No profiler was run and no load test was executed.** Every "query per request" figure is a
> static count from source, not a measurement — see [`../16-performance.md`](../16-performance.md) §8.

---

## 1. Headline reliability risks

| # | Risk | Where | Class | Evidence |
|---|---|---|---|---|
| P-1 | Global `View::composer('*')` runs on **every** rendered view and loads the full cart into memory | `AppServiceProvider.php:33-76` | Per-request load | `VERIFIED_FROM_SOURCE` |
| P-2 | Shiprocket courier quote is a synchronous HTTP call inside the request | `CheckoutController.php:112-166` → `ShiprocketService.php:63-77` | Sync I/O | `VERIFIED_FROM_SOURCE` |
| P-3 | Shiprocket order creation + AWB assignment run **after commit but inside the web request** | `CheckoutController.php:432-527` | Sync I/O | `VERIFIED_FROM_SOURCE` |
| P-4 | No HTTP timeout or retry on any Shiprocket call | `ShiprocketService.php:54,66,88,109,...` | Reliability | `VERIFIED_FROM_SOURCE` |
| P-5 | `QUEUE_CONNECTION=sync`, no `app/Jobs/` — nothing runs asynchronously | `.env`; `app/Jobs/` absent | Architecture | `VERIFIED_FROM_SOURCE` |
| P-6 | Only caching in the app is the Shiprocket login token | `ShiprocketService.php:30` | Caching | `VERIFIED_FROM_SOURCE` |
| P-7 | Unbounded `->get()` on listing/config queries | Home/Product/Profile/Wishlist controllers | Scalability | `VERIFIED_FROM_SOURCE` |
| P-8 | Unindexable `FIND_IN_SET` and leading-wildcard `LIKE` scans | `HomeController.php:24`; `ProductController.php:24,32,34`; `SearchController.php:26,42,61,76` | Query cost | `VERIFIED_FROM_SOURCE` |
| P-9 | Price-sort subquery targets a **non-existent table** `product_varients` | `ProductController.php:84,87` | Correctness/500 | `VERIFIED_FROM_SOURCE` + `VERIFIED_FROM_LIVE_SCHEMA` |

---

## 2. N+1 and per-request query risks

### 2.1 The global per-view composer (the dominant cost)

`AppServiceProvider::boot()` registers `View::composer('*', ...)` — `AppServiceProvider.php:33`.
The `'*'` wildcard matches **every** view, so the closure runs on every page render, including
pages that never display a cart badge. `VERIFIED_FROM_SOURCE`.

| Branch | Lines | Queries / behaviour |
|---|---|---|
| Authenticated | `:38-52` | `Cart::with('variant')->where('user_id', Auth::id())->whereHas('variant')->get()` **+** `Wishlist::where('user_id', Auth::id())->count()` |
| Guest | `:54-70` | `Cart::with('variant')->where('ip_addresscart', $guestToken)->whereNull('user_id')->whereHas('variant')->get()` |

- `->get()` loads **every cart line** into a PHP collection on every request, then
  `->unique(...)` and `->sum(...)` are computed in PHP (`:44-50`, `:64-70`). `VERIFIED_FROM_SOURCE`.
- `whereHas('variant')` adds a correlated `EXISTS` subquery. `VERIFIED_FROM_SOURCE`.
- Cost breakdown and the missing `carts.user_id` / `carts.ip_addresscart` indexes are documented in
  [`../16-performance.md`](../16-performance.md) §1 and §4 — **not repeated here**.

**Reliability consequence:** because the closure swallows nothing but also performs real queries on
every view, a DB hiccup degrades *every* page, not just cart pages. `INFERRED`.

### 2.2 Checkout cart loading

`CheckoutController::getCartItems()` — `CheckoutController.php:688-700`:

```php
$query = Cart::with(['product', 'variant']);        // :690
if (Auth::check()) { $query->where('user_id', Auth::id()); }        // :692-693
else { $query->where('ip_addresscart', request()->cookie('guest_token') ?? request()->ip())
            ->whereNull('user_id'); }                               // :694-696
return $query->whereHas('variant')->get();                          // :699
```

Eager loading of `product` **and** `variant` is correct, so the `placeOrder()` line-item loop
(`:366-416`) does **not** re-query per line. **No N+1 in the checkout loop.** `VERIFIED_FROM_SOURCE`.
(This is a change from older notes — see §7.)

### 2.3 Cart page

`CartController::index()` — `CartController.php:13` uses `Cart::with(['product', 'variant'])`;
the subtotal loop `:26-28` dereferences `$item->variant->offer_price` **without a `??` guard**
(`:27`), so a variant with `NULL` `offer_price` silently contributes `0`. `VERIFIED_FROM_SOURCE`.
`CartController::subtotal()` `:141-142` *does* use `?? 0` — the two paths differ (tracked as
`TD-B-03` in [`./KNOWN_ISSUES_AND_TECHNICAL_DEBT.md`](./KNOWN_ISSUES_AND_TECHNICAL_DEBT.md)).

### 2.4 Product listing

`ProductController::index()` — `ProductController.php:42` uses `Product::with('variants.unit')`
(correct, 2 eager queries). `VERIFIED_FROM_SOURCE`. The listing itself is paginated at `:105`
(`paginate($perPage)`, page sizes restricted to 6/12/24/48 at `:100-103`), so it is **bounded** —
unlike the lookup queries in §6.

### 2.5 Wishlist

`WishlistController::index()` — `WishlistController.php:42-45` eager-loads
`product.variants.unit` and `variant.unit`. No N+1, but it loads **every variant of every
wishlisted product** just to render a name. `VERIFIED_FROM_SOURCE`.

### 2.6 My Account

`ProfileController::index()` — `ProfileController.php:75-96`. The cart eager load at `:82` **does**
include `'product'`:

```php
$cartItems = Cart::where('user_id', $userId)
    ->whereHas('product')
    ->with(['product', 'variant.unit'])      // :82 — 'product' IS present
    ->get();
```

So the specific "N+1 on cart `product`" claim in [`../16-performance.md`](../16-performance.md) §2.3
is **`CONTRADICTED_BY_SOURCE` against the current tree** — the eager load is present. What remains
true: `orders` (`:85-88`), `addresses` (`:90-92`) and `State::all()` (`:94`) are all `->get()`
without a limit (see §6). Recorded in
[`./SOURCE_COVERAGE_AND_VERIFICATION.md`](./SOURCE_COVERAGE_AND_VERIFICATION.md).

### 2.7 Per-request lazy-load risk (general)

Laravel lazy-loading is **not** prevented anywhere (`Model::preventLazyLoading()` absent;
`bootstrap/app.php` `withExceptions` body is an empty comment — `bootstrap/app.php:16-18`).
Any Blade view that dereferences a relation not declared in a controller's `with()` will emit one
query per row at render time. Views are large monoliths
([`../16-performance.md`](../16-performance.md) §6) and were **not** exhaustively traced here, so
the residual per-view lazy-load surface is `INFERRED` / `UNVERIFIED`, not enumerated.

---

## 3. Synchronous external HTTP on the request path

Both Shiprocket interactions are blocking calls made **while the user waits**.

### 3.1 Courier quote (pre-payment)

`CheckoutController::getShiprocketCouriers()` — `CheckoutController.php:89-167`:

- Builds weight and calls `$shiprocket->checkServiceability($params)` at `:124`.
- `ShiprocketService::checkServiceability()` — `ShiprocketService.php:63-77` issues
  `$this->client()->get("{$this->baseUrl}/courier/serviceability", $params)`.
- `ShiprocketService::client()` — `:52-55` is `Http::withToken($this->getToken())->acceptJson()`
  — **no `->timeout()`**. First call in the 8-hour window also performs a Shiprocket login
  (`getToken()` `:25-47`). `VERIFIED_FROM_SOURCE`.
- The route is unthrottled (`routes/web.php:74`) — see [`./CHANGE_IMPACT_GUIDE.md`](./CHANGE_IMPACT_GUIDE.md).

### 3.2 Order placement (post-commit)

`CheckoutController::placeOrder()` — `CheckoutController.php:225-535`:

1. `DB::beginTransaction()` `:292` → writes 6 tables → `DB::commit()` `:421`.
2. **After commit**, still inside the same HTTP request: `new ShiprocketService()` `:434`,
   `ShiprocketService::createOrder()` `:498`, and optionally `assignAWB()` `:510`.
3. Failures are swallowed — `catch (\Exception $e) { Log::error(...) }` `:525-527` — and the
   method still returns `{'status':'success'}` `:529-534`.

`VERIFIED_FROM_SOURCE`. **Reliability consequence:** the customer waits for two extra network round
trips *after* their order is already durably saved; a Shiprocket outage makes the request slow but
still "succeeds", so the failure is invisible to both buyer and staff (cross-ref
[`../14-error-handling.md`](../14-error-handling.md) §3 and `KI-B-06` in
[`./KNOWN_ISSUES_AND_TECHNICAL_DEBT.md`](./KNOWN_ISSUES_AND_TECHNICAL_DEBT.md)).

### 3.3 No timeout, no retry, no re-auth

`ShiprocketService` uses `Http::withToken(...)->acceptJson()` for every call
(`:54, :66, :88, :109, :122, :135, :148, :161, :174, :187, :197`) with **no `->timeout()` and no
`->retry()`**. A grep of `app/` for `->timeout`/`->retry` returns **zero matches**.
`VERIFIED_FROM_SOURCE`. The application log records `Maximum execution time of 60 seconds exceeded`
(2 occurrences) rather than a clean connection timeout. The absence of a 401 re-authentication path
is tracked as `KI-B-07`.

---

## 4. Queue = sync — nothing is asynchronous

| Fact | Evidence |
|---|---|
| `QUEUE_CONNECTION=sync` in the live `.env` | read from `.env` (`VERIFIED_FROM_SOURCE`) |
| `.env.example` ships `QUEUE_CONNECTION=database` | `.env.example:38` (mismatch, see [`./DEPLOYMENT_AND_CONFIGURATION.md`](./DEPLOYMENT_AND_CONFIGURATION.md)) |
| `app/Jobs/` does not exist | directory listing of `app/` returns only `Http`, `Models`, `Providers`, `Services` (`VERIFIED_FROM_SOURCE`) |
| `app/Console/` does not exist; `routes/console.php` is stock | [`../18-configuration-and-env.md`](../18-configuration-and-env.md) §7 |
| `composer.json` `dev` script runs `queue:listen` | `composer.json:45-48` — there is nothing to listen for |

The direct reliability impact: the correct remedy for the swallowed Shiprocket failure (§3.2) is to
**dispatch fulfilment to a queue**. That is impossible in the current configuration — there is no
job class, no worker, and `sync` would run the job inline anyway. `VERIFIED_FROM_SOURCE`.

---

## 5. Caching posture

A grep for `Cache::` across `app/` returns **exactly one** call site:

```
app/Services/ShiprocketService.php:30: return Cache::remember($cacheKey, 8 * 60 * 60, function () {
```

`VERIFIED_FROM_SOURCE`. There is **no** caching of:

- country-scoped category / subcategory / brand lists (the `FIND_IN_SET` results), although they
  change rarely — see [`../16-performance.md`](../16-performance.md) §3.1;
- courier quotes (same `(pincode, weight, cod)` re-quotes Shiprocket every time);
- product listing fragments.

The store is `CACHE_STORE=file` (live `.env`), so even the single Shiprocket token is cached
**per node** and every web node logs into Shiprocket independently under a multi-server deploy.
`VERIFIED_FROM_SOURCE`. See [`../18-configuration-and-env.md`](../18-configuration-and-env.md) §6.

---

## 6. Unbounded queries

Queries that have **no `limit`/`paginate`** and therefore grow with table size. All
`VERIFIED_FROM_SOURCE`.

| Site | Line(s) | Query | Growth driver |
|---|---|---|---|
| `AppServiceProvider` (auth branch) | `:39-42` | `Cart::...->get()` | every cart line, every page |
| `AppServiceProvider` (guest branch) | `:58-62` | `Cart::...->get()` | every cart line, every page |
| `HomeController::index` | `:19-22` | `HomePromotion::...->get()` | banners |
| `HomeController::index` | `:24` | `Category::whereRaw(FIND_IN_SET)->get()` | categories (full scan) |
| `ProductController::index` | `:24-40` | `Category/SubCategory/Brand ...->get()` ×3 | lookup tables (full scans) |
| `ProductController::show` | `:133-144` | extra-products `->get()` | bounded by `take(8)` — OK |
| `ProfileController::index` | `:75-94` | wishlist / cart / orders / addresses / `State::all()` | per-user history |
| `WishlistController::index` | `:42-45` | `Wishlist::...->get()` | wishlist size |
| `CartController::index` | `:22` | `Cart::...->get()` | cart size |

---

## 7. Reconciliation with `../16-performance.md`

The following `../16-performance.md` statements no longer match the current tree
(detail in [`./SOURCE_COVERAGE_AND_VERIFICATION.md`](./SOURCE_COVERAGE_AND_VERIFICATION.md)):

| `../16-performance.md` claim | Status vs current source |
|---|---|
| §2.3 "N+1 on cart products" at `/myaccount` — `'product'` missing from `with()` | **`CONTRADICTED_BY_SOURCE`** — `ProfileController.php:82` includes `'product'` |
| §3.1 `FIND_IN_SET` sites `HomeController.php:21`, `ProductController.php:26,29,32` | **`OUTDATED`** line refs — current sites are `HomeController.php:24`, `ProductController.php:24,32,34` |
| §3.2 `SearchController.php:31, 38, 49, 64` `LIKE` sites | **`OUTDATED`** line refs — current sites are `SearchController.php:26,42,61,76` |
| §5.1 "`cURL error 28` on Shiprocket calls" | **`CONFLICTING_EVIDENCE`** — grep of `laravel.log` finds `cURL error` = 0; it finds `Maximum execution time of 60 seconds exceeded` ×2 |
| §3.1 `FIND_IN_SET` count "4 call sites" | **`VERIFIED_CURRENT`** (4 sites, different lines) |

---

## 8. Additional source-level reliability defect: price sort 500

`ProductController::index()` — `ProductController.php:84,87`:

```php
$products->orderByRaw('(SELECT MIN(offer_price) FROM product_varients WHERE product_varients.product_id = products.id) ASC');
$products->orderByRaw('(SELECT MAX(offer_price) FROM product_varients WHERE product_varients.product_id = products.id) DESC');
```

The model `ProductVarient` declares `protected $table = 'product_varient'` (singular) —
`app/Models/ProductVarient.php:8`. The SQL dump contains a `product_varient` table and **no
`product_varients` table** (`CREATE TABLE \`product_varients\`` → 0 matches;
`CREATE TABLE \`product_varient\`` → 1 match in `palvarnan_db (2).sql`, dated 2026-07-31).
Therefore selecting sort = `price_low_high` or `price_high_low` compiles to a subquery against a
non-existent table and raises a SQL error. `VERIFIED_FROM_SOURCE` + `VERIFIED_FROM_LIVE_SCHEMA`.
Tracked as `KI-B-09`.

---

## 9. Priority summary

| Priority | Action | Removes |
|---|---|---|
| P1 | Add indexes + rewrite the global composer to aggregates (see `../16-performance.md` §9) | P-1 |
| P1 | `->timeout(10)->retry(2, 200)` on every Shiprocket call; add a 401 re-auth path | P-4 |
| P1 | Move `createOrder`/`assignAWB` to a queued job; requires `QUEUE_CONNECTION=database\|redis` **and** a worker | P-3, P-5 |
| P2 | Cache courier quotes and country-scoped lookups | P-6 |
| P2 | Add `->limit()`/pagination to the unbounded lookups in §6 | P-7 |
| P2 | Throttle `/checkout/get-couriers` (unthrottled today) | P-2 |
| P3 | Fix the `product_varients` table name in the price sort | P-9 |

---

## 10. Related documents

- [`../16-performance.md`](../16-performance.md) — query-count analysis, index audit, render cost
- [`../18-configuration-and-env.md`](../18-configuration-and-env.md) — queue/cache/session settings
- [`../14-error-handling.md`](../14-error-handling.md) — swallowed Shiprocket failure, log cost
- [`../12-services-and-integrations.md`](../12-services-and-integrations.md) — method-by-method integration detail
- [`../23-external-services.md`](../23-external-services.md) — IR-numbered integration risks
- [`./KNOWN_ISSUES_AND_TECHNICAL_DEBT.md`](./KNOWN_ISSUES_AND_TECHNICAL_DEBT.md) — `KI-B-*` / `TD-B-*` register
- [`./CHANGE_IMPACT_GUIDE.md`](./CHANGE_IMPACT_GUIDE.md) — blast radius if you touch these paths
