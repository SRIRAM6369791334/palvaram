# 02 — Architecture (reverse engineered from code)

> All statements below are derived from reading the source. Where a claim cannot be proven from code it is marked **UNVERIFIED**.

---

## 1. Architectural style — what it actually is

**It is a monolithic server-rendered Laravel application, not a layered/Clean/DDD architecture.**

There is **no Service layer for business logic** (the only class in `app/Services/` is a third-party HTTP wrapper), **no Repository layer**, **no Form Request classes**, **no API Resources**, and **no Policy/Gate authorisation**. Business rules live directly inside controller method bodies.

The only architectural "layering" that exists is Laravel's mandatory request lifecycle plus one cross-cutting View Composer.

```
                  ┌──────────────────────────────────────┐
  Browser  ──────▶│  public/index.php  (Laravel front    │
  (HTML + fetch)  │  controller — every request enters)   │
                  └──────────────────┬───────────────────┘
                                     │
                  ┌──────────────────▼───────────────────┐
                  │  routes/web.php  — 40 routes        │
                  │  web middleware group:               │
                  │    • StartSession                    │
                  │    • AddQueuedCookiesToResponse      │
                  │    • ShareErrorsFromSession          │
                  │    • ValidateCsrfToken               │
                  │    • SubstituteBindings              │
                  │  NO auth middleware on public routes │
                  └──────────────────┬───────────────────┘
                                     │
         ┌───────────────────────────┼───────────────────────────┐
         │                           │                           │
  ┌──────▼───────┐          ┌────────▼────────┐         ┌────────▼────────┐
  │ 9 Controllers│          │ View Composer    │         │ Auth middleware│
  │ (business    │          │ AppServiceProv.  │         │ group (12 routes)│
  │  logic inline)│          │ runs on EVERY    │         │                │
  └──────┬───────┘          │ Blade view       │         └────────┬────────┘
         │                  └────────┬────────┘                  │
         │                           │                           │
  ┌──────▼───────────────────────────▼───────────────────────────▼──────┐
  │  Eloquent Models (23)  ──►  MySQL  `palvarnan_db`  (93 tables)     │
  └──────────────────────────────┬──────────────────────────────────────┘
                                 │
              ┌──────────────────┴──────────────────┐
              │                                     │
    ┌─────────▼──────────┐              ┌───────────▼────────────┐
    │ ShiprocketService   │              │ Razorpay PHP SDK       │
    │ (app/Services)      │              │ (vendor/razorpay)      │
    │ → apiv2.shiprocket  │              │ → api.razorpay.com     │
    │   .in/v1/external   │              │                        │
    └─────────────────────┘              └────────────────────────┘
```

---

## 2. The real request lifecycle (verified step by step)

### 2.1 Entry

**Source:** `bootstrap/app.php:8-13`

```php
->withRouting(
    web: __DIR__.'/../routes/web.php',
    commands: __DIR__.'/../routes/console.php',
    health: '/up',
)
```

- Only `routes/web.php` is loaded. **There is no `routes/api.php`** and no API route file at all. Every "API" endpoint in this project is a `web` route returning JSON.
- Because they are `web` routes, **all** of them have CSRF protection and session cookies.
- `/up` is Laravel's built-in health endpoint.

**Source:** `bootstrap/app.php:14-18` — `withMiddleware()` and `withExceptions()` are **empty closures**. No custom middleware, no custom exception handler. Every global behaviour is Laravel 12 default.

### 2.2 View Composer — runs on every single render

**Source:** `app/Providers/AppServiceProvider.php:25-57`

```php
View::composer('*', function ($view) {
    ...
    if (Auth::check()) {
        $cartItems = Cart::with('variant')->where('user_id', Auth::id())->whereHas('variant')->get();
        $cartCount = $cartItems->unique(...)->count();
        $cartSubtotal = $cartItems->sum(...);
        $wishlistCount = Wishlist::where('user_id', Auth::id())->count();
    } else {
        $guestToken = request()->cookie('guest_token') ?? request()->ip();
        $cartItems = Cart::with('variant')->where('ip_addresscart', $guestToken)->whereNull('user_id')->whereHas('variant')->get();
        ...
    }
    $view->with('cartCount', $cartCount);
    $view->with('wishlistCount', $wishlistCount);
    $view->with('cartSubtotal', $cartSubtotal);
});
```

Key consequences:
1. `View::composer('*')` fires for **every** view, including partials. So the cart query runs on **every page**, including `/login`, `/register`, `/select-country`, `/search`.
2. It loads the **entire** cart into PHP memory and computes the subtotal with a `Collection::sum()` in PHP — no SQL aggregate. For a large cart this is O(n) memory on every page.
3. `carts` has **no index on `user_id` or `ip_addresscart`** (verified in the SQL dump — only a `PRIMARY KEY`). Every page load is a **full table scan** of `carts`.
4. `wishlists` likewise has **no index on `user_id`**.
5. `request()->cookie('guest_token')` — no view anywhere sets a `guest_token` cookie. Grep confirms it is only *read*, never written. So the guest path silently falls back to `request()->ip()`.

**Verified runtime consequence:** `storage/logs/laravel.log` records
`Maximum execution time of 60 seconds exceeded` (2026-08-17) — consistent with an unindexed full scan plus remote product images.

### 2.3 Request → response, per controller

Every controller method follows one of three shapes:

| Shape | Example | Behaviour |
|---|---|---|
| **Query → View** | `HomeController::index()`, `ProductController::index()`, `CartController::index()` | Load models, `return view(...)` |
| **Validate → Mutate → Redirect/JSON** | `AuthController::registerStore()`, `CartController::store()` | `$request->validate()`, Eloquent write, `redirect()` or `response()->json()` |
| **Validate → Mutate → View composer → Redirect** | `ProfileController::update()` | `back()->with('success', …)` → session flash → SweetAlert2 in `app.blade.php:117-129` |

There is **no service/repository indirection**. To change how an order total is computed you must edit `CheckoutController` directly.

---

## 3. Cross-application architecture (verified, critical)

```
┌────────────────────────────────────────────────────────────────────┐
│                        MySQL: palvarnan_db                          │
│                        93 tables, 34 FKs                           │
│                                                                    │
│  products · product_varient · carts · wishlists · product_orders    │
│  product_order_items · product_slots · productstocks · categories   │
│  brands · countries · states · districts · units · user_addresses   │
│  + 77 more tables used ONLY by the dashboard                        │
└──────────▲──────────────────────────────────────────▲──────────────┘
           │                                          │
  ┌────────┴───────────────────┐        ┌─────────────┴────────────────────┐
  │  palVarnan  (STOREFRONT)   │        │  palVarnanDashboard  (ADMIN)     │
  │  Laravel 12                │        │  Laravel 8-era                    │
  │  9 controllers             │        │  70 controllers                   │
  │  23 models                 │        │  57 migrations ← OWNS THE SCHEMA │
  │  3 Laravel-default migs    │        │  ShiprocketService (duplicate)   │
  │  40 routes                 │        │  artisan shiprocket:sync (30 min)│
  │  Razorpay checkout         │        │  Spatie permissions              │
  │  Shiprocket order creation │───────▶│  order status / packing /        │
  └────────────────────────────┘  writes└──dispatch / reports / inventory ┘
```

### Verified coupling points

| # | Coupling | Evidence |
|---|---|---|
| 1 | **Shared schema.** The storefront's 3 migrations cover only `users`, `cache`, `cache_locks`, `jobs`, `failed_jobs`, `job_batches`, `sessions`, `password_reset_tokens`. Every domain table is created by `palVarnanDashboard/database/migrations/*`. | `database/migrations/` listing vs `palVarnanDashboard/database/migrations/` listing (57 files) |
| 2 | **Status-vocabulary mismatch (CONFIRMED BUG).** The storefront writes `product_orders.status = 'Order Placed'` (`CheckoutController.php:322`). The dashboard's sync command filters `whereIn('status', ['processing','ready_for_pickup','shipped','out_for_delivery'])` (`SyncShiprocketStatus.php:38-39`) — `'Order Placed'` is **not in that list**. The dashboard's own mapping (`ProductOrder::getDeliveryStatusAttribute()`) also lacks `'Order Placed'`. | Both files read directly |
| 3 | **Missing columns are added by the dashboard, not the storefront.** `razorpay_order_id`, `shiprocket_order_id`, `awb_code`, `courier_name`, `shiprocket_status`, `label_url`, … are added by `palVarnanDashboard/database/migrations/2026_08_17_000001_add_shiprocket_and_razorpay_columns_to_product_orders_table.php` (dated **2026-08-17**, i.e. *after* the 2026-07-31 SQL dump). | Both files read directly |
| 4 | **Duplicated `ShiprocketService`.** Byte-for-byte the same class exists in both apps with the same `config('services.shiprocket.*')` contract. A fix to one is not applied to the other. | `palVarnan/app/Services/ShiprocketService.php` ≡ `palVarnanDashboard/app/Services/ShiprocketService.php` |
| 5 | **Order id has two meanings.** `product_order_items.order_id` stores `product_orders.id` (numeric PK). `product_slots.order_id`, `product_tracking.order_id`, `product_transaction_logs.order_id` and `product_order_user_addresses.order_id` all store the **string** `order_number` (`'ORD-…'`). | `CheckoutController.php:374` vs `:331, :350, :361, :389` |

---

## 4. Data flow of the most complex path — placing an order

**Source:** `CheckoutController::placeOrder()` (lines 225-535)

```
Browser: click "Place Order"
  │  checkout.blade.php:733 handlePlaceOrderClick()
  ├─ IF payment_method == 'Online Payment':
  │    POST /checkout/prepare-payment          → CheckoutController::preparePayment (:172)
  │      ├─ getCartItems()                      (:688)
  │      ├─ recompute subtotal + GST in PHP     (:181-189)
  │      ├─ $shipping  = floatval($request->shipping_charge)   ← FROM THE BROWSER
  │      ├─ $discount  = floatval($request->coupon_discount)   ← FROM THE BROWSER
  │      ├─ Razorpay Api->order->create(amount = round($total*100))
  │      └─ returns razorpay_order_id + razorpay_key (public) to browser
  │
  ├─ Razorpay modal opens  → user pays in browser
  │    checkout.razorpay.com/v1/checkout.js
  │
  ├─ Razorpay handler() → appends razorpay_payment_id / order_id / signature
  │
  └─ submitFinalOrder(formData)  → POST /checkout/place-order   → placeOrder() (:225)
       ├─ Validator::make(...) 9 shipping fields + payment_method enum  (:230-249)
       ├─ IF Online Payment → Razorpay Api->utility->verifyPaymentSignature()  (:252-268)
       │        ⚠ MONEY IS ALREADY CAPTURED AT THIS POINT
       ├─ getCartItems()                                                            (:270)
       ├─ recompute subtotal + GST in PHP                                           (:276-284)
       ├─ $total = (subtotal + gst + shipping) - discount
       │        ⚠ shipping & discount re-read from the POST body, not the DB
       ├─ DB::beginTransaction()                                                    (:292)
       │    ├─ 1. ProductOrder::create()          → product_orders
       │    │        ✖ FAILS: unknown column 'billing_state'  (VERIFIED IN LOG)
       │    ├─ 2. ProductOrderUserAddress::create() → product_order_user_addresses
       │    ├─ 3. ProductTransactionLog::create()   → product_transaction_logs
       │    ├─ 4. ProductTracking::create()         → product_tracking
       │    ├─ 5. foreach cart item:
       │    │      ├─ ProductOrderItem::create()    → product_order_items
       │    │      │        ✖ FAILS: unknown column 'product_image' (VERIFIED IN LOG)
       │    │      ├─ ProductSlot::create()         → product_slots
       │    │      ├─ ProductStock: availablestock -= qty ; salestock += qty
       │    │      └─ ProductVarient: product_qty   -= qty
       │    └─ 6. Cart::where('user_id',$userId)->delete()
       │    DB::commit()                                                          (:421)
       │  catch(\Throwable) → DB::rollBack() + Log::error + HTTP 500               (:423-430)
       │
       └─ 7. Shiprocket (OUTSIDE the transaction)                                 (:432-527)
            ├─ new ShiprocketService()
            │     └─ getToken() → Cache::remember('shiprocket_token_<md5>', 8h)
            │          └─ POST https://apiv2.shiprocket.in/v1/external/auth/login
            ├─ createOrder($shiprocketOrderData)   → /orders/create/adhoc
            │     └─ on success: order->update(shiprocket_order_id, shipment_id, status)
            ├─ if selected_courier_id:
            │     └─ assignAWB()                   → /courier/assign/awb
            │          └─ on success: order->update(awb_code, courier_name, 'AWB Assigned')
            └─ catch(\Exception) → Log::error only. Order still reported as SUCCESS.
```

### Critical architectural weaknesses visible in this flow

| # | Weakness | Location |
|---|---|---|
| A | **Payment is captured before the order exists.** Razorpay verification happens *before* `DB::beginTransaction()`. If any insert fails, the transaction rolls back but the customer's money is already captured. **VERIFIED**: `storage/logs/laravel.log` 2026-08-17 05:12:55 shows a successful `razorpay_payment_id`, `payment_status = 'Paid'`, `payment_method = 'Online Payment'` rolled back with `Unknown column 'billing_state'`. No compensating refund exists anywhere in the code. | `CheckoutController.php:252-268` vs `:292-430` |
| B | **Shipping charge and discount are client-supplied.** `$request->shipping_charge` and `$request->coupon_discount` are `floatval()`-ed with **no server-side recomputation**. `coupon_discount` is currently always the literal `0` from `checkout.blade.php:473`, but nothing prevents a client from POSTing a negative or arbitrary discount. | `:286-288`, `:191-193` |
| C | **Razorpay amount rounding differs between the two calls.** `preparePayment` uses `max($total, 1)`; `placeOrder` uses no floor. A zero/negative total after a discount would create a Razorpay order for ₹1 but record a ₹0 order. | `:193` vs `:288` |
| D | **Shiprocket failure is swallowed.** The order is reported `"Order placed successfully!"` even when the shipment was never created. The order row exists with `shiprocket_status = NULL` and no AWB, and nothing retries. | `:525-527` then `:529-534` |
| E | **No idempotency.** Double-clicking "Place Order" creates two Razorpay orders and, if the DB write succeeds, two orders. The button is only disabled client-side. | `checkout.blade.php:744` |
| F | **No queue.** Order placement, email and Shiprocket calls all run synchronously inside the HTTP request. `QUEUE_CONNECTION=database` is configured but no job is ever dispatched. | `.env`, no `dispatch(` in `app/` |

---

## 5. Authentication & authorisation architecture

**Source:** `config/auth.php` (Laravel default), `routes/web.php:60-75`

```
Authentication : Laravel session guard ("web"), users table, bcrypt hashes
Session storage: database driver → sessions table
Password hash  : bcrypt, BCRYPT_ROUNDS=12
Token guard    : NOT configured (config/auth.php has no 'api' guard)
```

**Authorisation: there is none.** Concretely:

| Concern | Reality | Evidence |
|---|---|---|
| Route protection | Only the 12 checkout routes use `middleware('auth')` | `routes/web.php:60-75` |
| Ownership checks | **Only** `CheckoutController::updateAddress()` and `::destroyAddress()` call `abort_unless($address->user_id === Auth::id(), 403)` | `CheckoutController.php:596, 629` |
| `carts` | `update()`, `remove()`, `ajaxUpdate()`, `ajaxRemove()` scope by variant only, **not** by user. `update()` and `remove()` have **no scope at all**. | `CartController.php:35-52, 90-113` |
| `wishlists` | `toggle()`, `index()`, `remove()`, `check()`, `checkProduct()` all use `Auth::id()` but the routes have **no** `auth` middleware → `Auth::id()` is `null` for guests → operates on `user_id IS NULL` rows. `remove($id)` has no ownership filter. | `routes/web.php:56-59`, `WishlistController.php` |
| Roles / permissions | `roles`, `permissions`, `model_has_roles`, `dashboard_users` tables exist in the DB, and Spatie-style pivot tables are present — but **the storefront does not reference them at all**. | Table list vs `app/` grep |
| Admin surface | **None.** No admin routes, no `is_admin` column, no Gate definitions. | `routes/web.php` |

---

## 6. Caching, logging, queues, events

| Concern | Configuration | Reality in code |
|---|---|---|
| **Cache** | `CACHE_STORE=database` → `cache` + `cache_locks` tables | Used in exactly **one** place: `ShiprocketService::getToken()` → `Cache::remember($key, 8*60*60, …)`. Everything else is uncached. |
| **Logging** | `LOG_CHANNEL=stack`, `LOG_STACK=single`, `LOG_LEVEL=debug` → `storage/logs/laravel.log` | `Log::info`/`Log::error` in `CheckoutController` (5 sites) and `ShiprocketService` (3 sites). **Logs contain full Shiprocket order payloads, Razorpay IDs and customer PII.** |
| **Queue** | `QUEUE_CONNECTION=database` → `jobs` table | **Never used.** No `dispatch(`, no `ShouldQueue`, no jobs, no listeners, no events. The `composer dev` script starts `queue:listen` for nothing. |
| **Events / Broadcasting** | `BROADCAST_CONNECTION=log` | Not used. |
| **Sessions** | `SESSION_DRIVER=database`, `SESSION_LIFETIME=120` (minutes) | Used heavily — `country_id` is the tenant key. |

---

## 7. Multi-tenancy architecture (unusual — document this)

The app supports multiple countries, but **not** with one-row-per-tenant. Instead `country_id` is a **comma-separated string** on catalogue tables:

**Source:** `HomeController.php:18`, `ProductController.php:26,29,32`

```php
$categories = Category::whereRaw("FIND_IN_SET(?, country_id)", [$countryId])->get();
```

| Table | `country_id` declared type | Reality |
|---|---|---|
| `categories` | `varchar(255)` | comma list, e.g. `"3,5,243"` |
| `brands` | `varchar(255)` | comma list |
| `sub_categories` | `bigint UNSIGNED` (from migration) but `Category`/`SubCategory` models cast to `string` | comma list |
| `products` | `varchar(255)` | **single** value or `NULL` |
| `products.country_id` in the listing query | — | `where('country_id', $countryId)->orWhereNull('country_id')` (`ProductController.php:37-40`) — i.e. global products are those with `NULL` |

`products` is therefore **half** multi-valued, while categories are fully multi-valued. This is why `Country` casts to `string` in `Category` and `Brand`.

**Reality check:** the dump contains exactly **one** country — `(243, 'India', …, 'GST')`. The whole `FIND_IN_SET` mechanism currently has one operand.

**The tenant key lives in the session**, not in the URL or host:

```
GET /select-country  → CountryController::index()   (all countries, ordered by name)
POST /select-country → CountryController::store()   → session(['country_id' => $country->id])
                        then redirect('/')
```

`HomeController::index()` and `ProductController::index()/show()` hard-redirect to `/select-country` when `session('country_id')` is missing. `CountryController::store()` preserves the selection across logout by saving and restoring it (`ProfileController.php:54, 63`).

---

## 8. Middleware pipeline — actual

**Source:** `bootstrap/app.php` (empty `withMiddleware`) + `routes/web.php`

| Layer | Middleware | Note |
|---|---|---|
| Global | Laravel 12 defaults (TrustProxies/Hosts not configured) | — |
| `web` group | `EncryptCookies`, `AddQueuedCookiesToResponse`, `StartSession`, `ShareErrorsFromSession`, `ValidateCsrfToken`, `SubstituteBindings` | Applied to **all 40 routes** |
| `auth` group | `Illuminate\Auth\Middleware\Authenticate` + `EncryptCookies`, `StartSession` | Only `routes/web.php:60-75` |
| Custom middleware | **NONE** | No throttle/rate-limit on login, register, forgot-password, search, or any AJAX endpoint |
| CORS | **NONE** | `config/cors.php` not present in project |
| Guest-only middleware | **NONE** | `/login`, `/register` are reachable while logged in |

**Consequence:** `POST /login-store` (AuthController::login) has **no rate limiting and no failed-attempt lockout**. An attacker can brute-force credentials at unlimited rate.

---

## 9. Related documents

- [03-module-map.md](03-module-map.md) — module-by-module breakdown
- [05-user-flows.md](05-user-flows.md) — step-by-step user journeys
- [08-business-logic.md](08-business-logic.md) — every rule extracted
- [10-backend.md](10-file-structure.md — layer-by-layer reference
- [22-dependency-map.md](09-dependencies.md — verified import/call graphs
