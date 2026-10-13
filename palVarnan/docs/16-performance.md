# 16 — Performance

No profiler was run and no load test was executed, so every figure below is **derived from query-count analysis of the source**, not measured latency. Statements are marked accordingly.

---

## 1. The global cost — one query per page view

`AppServiceProvider.php:26-69` registers:

```php
View::composer('*', function ($view) { … });
```

**`*` matches every view.** This closure runs on every single rendered view and issues **1–3 database queries**:

| Branch | Queries |
|---|---|
| Authenticated | `Cart::with('variant')->where('user_id',…)->whereHas('variant')->get()` **+** `Wishlist::where('user_id',…)->count()` |
| Guest | `Cart::with('user_id IS NULL')->where('ip_addresscart',…)->whereHas('variant')->get()` |

| Aspect | Assessment |
|---|---|
| Applies to | **Every page**, including `country-selection.blade.php` and `welcome.blade.php` |
| Data volume | `->get()` loads **every cart line** with its variant into PHP memory on every request |
| `whereHas('variant')` | Adds a correlated `EXISTS` subquery |
| Indexing | `carts` has **no index** on `user_id` or `ip_addresscart` ⇒ full table scan |
| `with('variant')` | One extra query (eager load) |
| Correctness | The cart badge excludes rows whose variant is missing, but does not exclude rows whose **product** is soft-deleted |

**Fix:** replace `->get()` with two `sum()`/`count()` aggregates, cache per request, and add indexes. Estimated 3 queries + N rows → 1 query + 1 row.

---

## 2. N+1 queries

### 2.1 Product listing — `ProductController::index()`

```php
$products = Product::with('variants.unit')          // :34  eager load — correct
    ->where(fn($q) => $q->where('country_id',$c)->orWhereNull('country_id'))
    ->latest()->paginate(12);                        // :65
```
Eager loading is used correctly here — `variants.unit` is 2 queries, not 12×N. **This is the one place the project gets it right.**

### 2.2 Product detail — `ProductController::show()`
```php
Product::with('variants.unit','category','brand')->where('slug',$slug)->firstOrFail();
$relatedProducts = Product::with('variants.unit')->…->take(8)->get();
```
Also correct. **4 queries total.**

### 2.3 My Account — **the worst offender**

```php
$wishlistItems = Wishlist::where('user_id',$userId)->with(['product.variants','variant.unit'])->get();
$cartItems     = Cart::where('user_id',$userId)->with(['product','variant.unit'])->get();
$orders        = ProductOrder::where('user_id',$userId)->with(['items.product','items.variant'])->latest()->get();
$addresses     = UserAddress::where('user_id',$userId)->latest()->get();
$states        = State::all();
```
Eager loads are declared, **but the Blade template iterates relationships that are not eager-loaded**:

| Blade access | Load path | Query count |
|---|---|---|
| `$item->variant->unit->short_name` | declared `variant.unit` | eager ✅ |
| `$item->product->product_image` | `product` **not** in `with()` for carts | **N+1** |
| `$orders->items->product` | declared `items.product` | eager ✅ |
| `$orders->items->variant` | declared `items.variant` | eager ✅ |

**Plus** `AppServiceProvider` adds its 2 queries. So `/myaccount` issues roughly:

```
2 (AppServiceProvider) + 5 (top level) + 1 (states) + 1 (wishlist variants) + N (cart products, lazy)
```
with **N lazy queries for cart products**. On a 10-line cart that is ~20 queries. Verified by the log: `Attempt to read property "name" on null` at `pages\myaccount.blade.php` proves the eager load returns a null relation that Blade then dereferences.

**Fix:** add `'product'` to the cart `with()` array, and constrain `State::all()` to the columns needed.

### 2.4 Wishlist page — `WishlistController::index()`
```php
Wishlist::where('user_id', Auth::id())->with(['product.variants.unit','variant.unit'])->get();
```
Eager loading is correct. But `with('product.variants.unit')` loads **every variant and unit of every product** just to show a name — 3 joined queries returning far more data than needed.

### 2.5 Cart page — `CartController::index()`
```php
Cart::with(['product','variant'])->…->whereHas('variant')->get();
```
Correct eager loading. **But the subtotal loop dereferences `$item->variant->offer_price` without a guard (`:27`)** — and if `offer_price` is `NULL`, `$subtotal` becomes `0` silently.

---

## 3. Unindexable queries

### 3.1 `FIND_IN_SET` — 4 call sites

```php
Category::whereRaw("FIND_IN_SET(?, country_id)", [$countryId])->get();
SubCategory::whereRaw("FIND_IN_SET(?, country_id)", [$countryId])->get();
Brand::whereRaw("FIND_IN_SET(?, country_id)", [$countryId])->get();
```
(`HomeController.php:21`, `ProductController.php:26, 29, 32`)

**This cannot be indexed.** `FIND_IN_SET(x, column_id)` requires a full table scan because the function's output depends on the whole column value. There is no index strategy that helps.

**Root cause:** `country_id` on `categories`/`brands`/`sub_categories` is a **comma-separated varchar** (multi-country), while on `products` it is a **single integer**. One column name, two incompatible designs.

**Fixes, in order of preference:**

| Option | Effort | Result |
|---|---|---|
| **Normalise to a join table** (`countryables`) | High | Proper indexes, unlimited countries |
| Split to `primary_country_id` + `other_country_ids`, index the first | Low | Fast for the common single-country case |
| **Cache the `FIND_IN_SET` result per country** | Low | Best effort/benefit ratio — these lists change rarely |

### 3.2 `LIKE '%…%'` — 4 searches per keystroke

`SearchController.php:31, 38, 49, 64` all use leading-wildcard `LIKE`. A `LIKE '%x%'` scan **cannot use a BTREE index** on any of the four tables. Every keystroke triggers 4 full scans.

`header.blade.php:835` fires the request **on every keystroke** with no debounce and no minimum length.

**Fix:** `FULLTEXT` indexes + `MATCH…AGAINST` (works with `innodb_ft_min_token_size=3`), plus a 250 ms debounce and a 3-character minimum.

---

## 4. Missing indexes

| Table | Query | Column(s) | Index present? |
|---|---|---|---|
| `carts` | **every page view** (`AppServiceProvider`) | `user_id` | **No** |
| `carts` | **every guest page view** | `ip_addresscart` | **No** |
| `carts` | ajax update/remove (×6 sites) | `product_varient_id` | **No** |
| `wishlists` | toggle, index, check ×5 | `user_id`, `product_id`, `product_varient_id` | **No** |
| `wishlists` | `index` | `user_id` | **No** |
| `products` | listing | `country_id`, `category_id`, `subcategory_id`, `brand_id`, `created_at` | Partial |
| `products` | show | `slug` | **UNVERIFIED** |
| `categories`/`brands`/`sub_categories` | `FIND_IN_SET(country_id)` | — | **Impossible** |
| `product_varient` | `LIKE '%q%'` | `varient` | **Impossible** |
| `states` | `State::all()` | — | no filter needed |
| `product_orders` | thankyou | `user_id`, `id` | `id` is PK |
| `sessions` | every request | `id` | PK ✅ |

**Highest-value index set (5 indexes, fixes the global hot path):**
```sql
ALTER TABLE carts       ADD INDEX (user_id);
ALTER TABLE carts       ADD INDEX (ip_addresscart);
ALTER TABLE carts       ADD INDEX (product_varient_id);
ALTER TABLE wishlists   ADD INDEX (user_id, product_id, product_varient_id);
ALTER TABLE products    ADD INDEX (country_id, category_id, created_at);
```

---

## 5. External HTTP latency — on the critical path

### 5.1 Courier quoting

`CheckoutController::getShiprocketCouriers()` → `checkServiceability()` is a **synchronous HTTP call inside the page's critical path**, triggered whenever the user changes address or payment method (`checkout.blade.php:652-731`).

| Issue | Detail |
|---|---|
| No timeout | Laravel default 30 s. Log contains `cURL error 28: Operation timed out` |
| No retry | A single 502 fails the quote |
| Token cache lookup | First call in an 8 h window also performs a Shiprocket login |
| Rate limit | **None on this route** ⇒ unbounded calls can get the Shiprocket account rate-limited |

**Fix:** `->timeout(10)->retry(2, 200)`, add `throttle:15,1`, and cache quotes per `(pincode, weight, cod)` for a few minutes.

### 5.2 Order placement

`placeOrder()` calls Shiprocket **after** the DB commit (`:432`). It is outside the transaction — correct, because a slow HTTP call must not hold a DB lock — but it makes the user wait, and **its failure is swallowed** (`:525-527`) while the response still reports success.

**Fix:** keep it outside the transaction, but dispatch it to a queue and add a reconciliation job.

---

## 6. Rendering cost

| View | Size | Lines | Notes |
|---|---|---|---|
| `pages/home.blade.php` | 162 868 B | 1 991 | **50 Unsplash images**, 91 hard-coded prices, 29 vendor links |
| `pages/myaccount.blade.php` | 59 340 B | — | 5 tabs, all rendered server-side on every visit |
| `pages/checkout.blade.php` | 48 752 B | 872 | 4 `fetch()` calls |
| `layouts/header.blade.php` | 41 981 B | 889 | 85 dead `shop.html` links |
| `pages/product-detail.blade.php` | 35 901 B | — | 10 `fetch()` call sites |

Every view is a monolith with inline JavaScript — unminifiable, uncacheable, duplicated across pages. See [15-frontend-and-assets.md](15-frontend-and-assets.md).

**Asset weight:** `public/assets/` is 9.4 MB / 472 files, committed to git. **jQuery is loaded twice** (3.7.1 local + 3.6.0 from cdnjs) — the CDN copy is render-blocking and overwrites the local one.

---

## 7. Configuration-level performance settings

| Setting | Value | Assessment |
|---|---|---|
| `APP_DEBUG=true` | `.env` | **Severe** — every exception renders a full stack trace; SQL is logged at `debug` |
| `LOG_LEVEL=debug` | `.env` | Every query logged. Slow I/O, huge files |
| `LOG_CHANNEL=single` | `.env` | One unbounded file, no rotation |
| `SESSION_DRIVER=file` | `.env` | Session read/write on **every request** — no shared state across servers |
| `CACHE_STORE=file` | `.env` | Shiprocket token cached **per node** ⇒ N logins across N servers |
| `QUEUE_CONNECTION=sync` | `.env` | **Synchronous** — any queued job would block the request. No workers defined |
| `BROADCAST_CONNECTION=log` | `.env` | fine |
| `config:cache` / `route:cache` / `view:cache` | unknown | `bootstrap/cache` inspected; no reliable evidence of cached config |
| `opcache` | unknown | **UNVERIFIED** — XAMPP default config varies |

**Note:** `config:cache` would break `SearchController.php:29`'s `env('MAIN_URL')` call, returning `null`. Fix the code before enabling it.

---

## 8. Per-request query estimate

Derived by static analysis. **Not measured.**

| Page | Auth | Guest | Dominant cost |
|---|---|---|---|
| `/select-country` | ~2 | ~2 | `Country::orderBy('name')->get()` |
| `/` | ~6 | ~5 | 1 banner query + `FIND_IN_SET` scan + 2 AppServiceProvider |
| `/products` | ~9 | ~7 | `FIND_IN_SET` ×3 + cart + wishlist + eager loads + **2 AppServiceProvider** |
| `/product/{slug}` | ~9 | ~7 | as above, plus related products |
| `/search` (per keystroke) | ~5 | ~5 | **4 unindexable full scans** |
| `/cart` | ~7 | ~6 | cart + variants + products + 2 AppServiceProvider |
| `/wishlist` | ~8 | **~4** | returns other users' rows when `Auth::id()` is `null` |
| `/myaccount` | **~20** | **~6** | **N+1 on cart products** |
| `/checkout` | ~10 | — | cart + addresses + `State::all()` + 2 AppServiceProvider |
| `/checkout/get-couriers` | ~4 + **HTTP** | — | **Shiprocket round trip, no timeout** |
| `/checkout/place-order` | **~25 + 2 HTTP** | — | transaction writes + Razorpay + Shiprocket |

---

## 9. Recommendations, in impact order

| # | Action | Expected effect |
|---|---|---|
| **P1** | Add the 5 indexes in §4 | Removes the full table scan from the **global** cart path |
| **P1** | Rewrite `AppServiceProvider`'s closure to use aggregate queries instead of `->get()` | −2 queries and far less memory on every page |
| **P1** | Fix the My Account N+1 — add `'product'` to the cart eager load | −N queries on `/myaccount` |
| **P1** | Add `->timeout(10)->retry(2, 200)` to all Shiprocket calls | Removes 30 s hangs |
| **P2** | Debounce search (250 ms) and require 3+ characters | Cuts search scans by ~90 % |
| **P2** | Add `FULLTEXT` indexes and switch to `MATCH…AGAINST` | Search becomes index-backed |
| **P2** | Cache `FIND_IN_SET` category/brand/subcategory lists per country | Removes 3 full scans per listing page |
| **P2** | Set `LOG_LEVEL=info`, `LOG_CHANNEL=daily`, `APP_DEBUG=false` | Removes debug I/O on every error |
| **P2** | Remove the duplicate CDN jQuery | One fewer render-blocking request per page |
| P3 | Prune `home.blade.php`'s 50 Unsplash images | ~500 KB off the homepage |
| P3 | Add `loading="lazy"` to all images | Faster first paint |
| P3 | Extract Blade components; move JS to `resources/js` | Caching and minification become possible |
| P3 | Move session and cache to a shared store (Redis) | Enables multi-node deployment |
| P3 | Normalise `country_id` to a join table | Makes country filtering indexable |

---

## 10. Related documents

- [07-database-schema.md](07-database-schema.md) — D-S9 index audit
- [11-models-and-relationships.md](11-models-and-relationships.md) — relationship-driven N+1
- [14-error-handling.md](14-error-handling.md) — debug logging cost
- [15-frontend-and-assets.md](15-frontend-and-assets.md) — asset and render cost
- [18-configuration-and-env.md](18-configuration-and-env.md) — cache/session/queue settings