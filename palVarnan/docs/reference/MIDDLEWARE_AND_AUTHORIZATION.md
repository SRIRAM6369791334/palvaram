# Middleware and Authorization — Project B (`palVarnan`)

> Canonical register of middleware registration, `auth` usage, and every authorization
> check in the application. Evidence vocabulary: `VERIFIED_FROM_SOURCE`,
> `VERIFIED_FROM_LIVE_SCHEMA`, `INFERRED`, `UNVERIFIED`, `NOT_AVAILABLE`,
> `CONFLICTING_EVIDENCE`. **No credentials or PII are reproduced.**
>
> Siblings: [`./ROUTES_AND_API_REFERENCE.md`](./ROUTES_AND_API_REFERENCE.md),
> [`./CONTROLLER_REFERENCE.md`](./CONTROLLER_REFERENCE.md),
> [`./SECURITY_AUDIT.md`](./SECURITY_AUDIT.md).
> Existing narrative: [`../13-security-audit.md`](../13-security-audit.md).

---

## 1. Framework middleware model (Laravel 12)

This project uses Laravel 12's slim application bootstrap. All middleware configuration
lives in `bootstrap/app.php`; the legacy `app/Http/Kernel.php` does **not** exist in this
application. `VERIFIED_FROM_SOURCE`.

```php
// bootstrap/app.php:7-18
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
```

| Observation | Evidence |
|---|---|
| `withMiddleware()` registers **only** `trustProxies(at: '*')` | `bootstrap/app.php:13-15` |
| The `web` group (session, cookies, CSRF, bindings) is applied by the framework default to `routes/web.php` | Laravel 12 default; all routes appear in the `web` group |
| **No custom middleware alias, no global middleware, no `throttle` registration** | `bootstrap/app.php` |
| **No `RateLimiter::for(...)` anywhere** | `bootstrap/app.php`; no `RateLimiter` reference in `app/` (`VERIFIED_FROM_SOURCE`, grep over `app/`) |
| `withExceptions()` body is empty — no custom rendering/handling | `bootstrap/app.php:16-18` |
| `trustProxies(at: '*')` trusts **all** proxies — see §6 | `bootstrap/app.php:14` |

---

## 2. `app/Http/Middleware` is absent

`VERIFIED_FROM_SOURCE` (directory check):

```
Test-Path app/Http/Middleware   → False
Test-Path app/Http/Requests     → False
Test-Path app/Policies          → False
```

The full `app/` tree is:

```
app/
├── Http/
│   └── Controllers/        (10 files — see CONTROLLER_REFERENCE.md)
├── Models/                 (25 models)
├── Providers/
│   └── AppServiceProvider.php
└── Services/
    └── ShiprocketService.php
```

There is **no** `app/Http/Middleware/`, no `app/Http/Requests/` (no Form Requests), and no
`app/Policies/`. `VERIFIED_FROM_SOURCE`.

---

## 3. `auth` middleware usage in `routes/web.php`

`auth` is referenced in exactly two places. `VERIFIED_FROM_SOURCE`.

### 3.1 The `auth` group — `routes/web.php:71-83`

```php
Route::middleware('auth')->group(function () {
    Route::get('/checkout', ...)->name('checkout.index');
    Route::post('/checkout/prepare-payment', ...)->name('checkout.preparePayment');
    Route::post('/checkout/get-couriers', ...)->name('checkout.getCouriers');
    Route::post('/checkout/get-shipping-charge', ...)->name('checkout.getShippingCharge');
    Route::post('/checkout/get-cities', ...)->name('checkout.getCities');
    Route::post('/checkout/place-order', ...)->name('checkout.placeOrder');
    Route::get('/checkout/thankyou/{order}', ...)->name('checkout.thankyou');
    Route::post('/checkout/address', ...)->name('address.store');
    Route::put('/checkout/address/{address}', ...)->name('address.update');
    Route::delete('/checkout/address/{address}', ...)->name('address.destroy');
});
```

**10 routes** are protected by this group (7 checkout + 3 address). `VERIFIED_FROM_SOURCE`.

### 3.2 Per-route `auth` — `routes/web.php:63-64`

```php
Route::get('/wishlist/check', [WishlistController::class, 'check'])->middleware('auth');
Route::get('/wishlist/check-product', [WishlistController::class, 'checkProduct'])->middleware('auth');
```

**2 routes** carry an inline `auth`. `VERIFIED_FROM_SOURCE`.

### 3.3 Count reconciliation — "~10 routes"

| Statement | Verified value |
|---|---|
| Routes carrying `auth` middleware **total** | **12** (10 group + 2 inline) `VERIFIED_FROM_SOURCE` |
| Routes carrying `auth` **inside the checkout group** | 10 |
| `auth` "protects only ~10 routes" (task framing / `VERIFIED_FACTS.md`) | refers to the group; the strict total is **12** |

> `CONFLICTING_EVIDENCE` (minor): the shorthand "~10" under-counts by the two
> wishlist-check routes. The verified total is **12 of 44** concrete web routes ≈ 27%.
> `VERIFIED_FROM_SOURCE`.

So **32 of 44** concrete routes are reachable **without authentication**. `VERIFIED_FROM_SOURCE`.

---

## 4. Routes that arguably need `auth` but do not have it

The following routes are **outside** any `auth` middleware, yet their controllers read or
write user-scoped data. `VERIFIED_FROM_SOURCE`.

| Route | Method | Controller@method | Why it is exposed |
|---|---|---|---|
| `/profile-update` | PUT | `ProfileController@update` | Dereferences `auth()->user()` → **500 when anonymous** (`ProfileController.php:16`) |
| `/myaccount` | GET | `ProfileController@index` | Self-guards in-controller (`:69-71`), but the route itself is public |
| `/wishlist/toggle` | POST | `WishlistController@toggle` | Writes `user_id = Auth::id()` = `null` for guests |
| `/wishlist` | GET | `WishlistController@index` | Reads `WHERE user_id IS NULL` for guests |
| `/wishlist/{id}/remove` | DELETE | `WishlistController@remove` | Scoped by `user_id`, but `Auth::id()` is `null` for guests |
| `/cart-update` | POST | `CartController@update` | No ownership scope at all |
| `/cart-remove/{id}` | GET | `CartController@remove` | No ownership scope at all |
| `/cart/store`, `/cart`, `/cart/subtotal` | mixed | `CartController@*` | User/guest-IP scoped; guest identity is weak |
| `POST /select-country` | POST | `CountryController@store` | No validation; sets session only (low risk) |

The **auth POST endpoints are public by design**: `/register-store`, `/login-store`,
`/forgot-password`, `/reset-password-direct`. Of these, `/reset-password-direct` is the
critical one (unauthenticated password overwrite — see `./SECURITY_AUDIT.md` SEC-B-01).

---

## 5. Authorization is entirely ad-hoc

There are **no** policies, **no** gates, and **no** form-request `authorize()` methods.
`VERIFIED_FROM_SOURCE` (greps over `app/` for `Gate::`, `->can(`, `authorize(`, `policy`
returned zero matches; `app/Policies` and `app/Http/Requests` do not exist).

Authorization is enforced (when at all) with two hand-rolled patterns:

### 5.1 `abort_unless(... === Auth::id(), 403)` — the correct house pattern (2 call sites)

```php
// CheckoutController.php:596  (updateAddress)
abort_unless($address->user_id === Auth::id(), 403);

// CheckoutController.php:629  (destroyAddress)
abort_unless($address->user_id === Auth::id(), 403);
```

`VERIFIED_FROM_SOURCE`. This is the only explicit "throw on mismatch" authorization in the
codebase.

### 5.2 `->where('user_id', Auth::id())` scoping — 27 call sites

All ownership "checks" elsewhere are query scopes. Verified call sites
(`VERIFIED_FROM_SOURCE`, grep over `app/`):

| File | Lines | Context |
|---|---|---|
| `app/Providers/AppServiceProvider.php` | `:40`, `:52` | Global view composer: cart items + wishlist count |
| `app/Http/Controllers/CheckoutController.php` | `:544`, `:559`, `:693` | `thankyou` scope; `storeAddress` user id; `getCartItems` |
| `app/Http/Controllers/CartController.php` | `:16`, `:67`, `:84`, `:100`, `:120`, `:135` | index/update-dedupe/store/ajaxUpdate/ajaxRemove/subtotal |
| `app/Http/Controllers/ProductController.php` | `:65`, `:76`, `:151`, `:162` | cart/wishlist id lookups (2 methods) |
| `app/Http/Controllers/WishlistController.php` | `:17`, `:28`, `:42`, `:52`, `:58`, `:83` | toggle/index/remove/checkProduct/check |
| `app/Http/Controllers/ProfileController.php` | `:73` | index |

> Note: `CartController::update()` (`:33-44`) and `CartController::remove()` (`:46-62`) are
> **absent** from this list — they apply **no** owner scope. See §5.4.

### 5.3 Guest fallback scoping

When `Auth::check()` is false, three cart paths scope to
`ip_addresscart = request()->cookie('guest_token') ?? request()->ip()` **plus**
`whereNull('user_id')` (`CartController.php:18-19`, `:137-138`; `AppServiceProvider.php:56-61`).
`VERIFIED_FROM_SOURCE`. `carts` has no `guest_token` column and no code sets that cookie, so
the IP branch always wins — IP-based ownership is weak (shared NATs). See
[`../25-forms-and-validation.md`](../25-forms-and-validation.md) §2.6.

### 5.4 Endpoints with NO ownership check

| Endpoint | Evidence | Impact |
|---|---|---|
| `POST /cart-update` | `CartController.php:35-40` — `Cart::where('id',$cartId)->update(...)`, no `user_id`/`ip` clause | Any visitor edits any cart line by id (IDOR). **SEC-B-03** |
| `GET /cart-remove/{id}` | `CartController.php:48-50` — `Cart::findOrFail($id)->delete()`, no owner clause | Any visitor deletes any cart line; state-changing GET (no CSRF). **SEC-B-04** |

`VERIFIED_FROM_SOURCE`.

### 5.5 Correctly scoped (preserve as reference)

| Endpoint / method | Evidence | Pattern |
|---|---|---|
| `updateAddress` / `destroyAddress` | `CheckoutController.php:596`, `:629` | `abort_unless($address->user_id === Auth::id(), 403)` |
| `thankyou` | `CheckoutController.php:544` | `where('user_id', Auth::id())->firstOrFail()` (404, not 403) |
| `CartController::ajaxUpdate/ajaxRemove` | `:100`, `:120` | `user_id` / `ip` + `whereNull('user_id')` |
| `WishlistController::remove` | `:52` | `where('id',$id)->where('user_id',Auth::id())` — **wishlist IDOR FIXED** |
| `WishlistController::check/checkProduct` | `:58`, `:83` | `user_id` scope + route behind `auth` |

---

## 6. Other middleware-relevant facts

| Topic | Finding | Evidence |
|---|---|---|
| CSRF | Applies to `POST`/`PUT`/`DELETE` via the framework `web` group; **not disabled** anywhere in `bootstrap/app.php`. `GET /cart-remove/{id}` therefore has **no** CSRF coverage because it is a GET. | `bootstrap/app.php:13-15`; `routes/web.php:50` |
| `throttle` | **Zero** occurrences on any route; no `RateLimiter` definition. **SEC-B-05.** | `routes/web.php`; `bootstrap/app.php` |
| `trustProxies(at: '*')` | Trusts all proxies unconditionally. With a directly reachable origin this lets a client spoof `X-Forwarded-*` headers (IP, proto). `INFERRED` residual risk; not exploitable behind a correctly configured proxy. | `bootstrap/app.php:14` |
| HTTPS forcing | `AppServiceProvider::boot()` forces `https` when `app.env === 'production'` or `X-Forwarded-Proto: https` — dependent on the trusted-proxy header above. | `AppServiceProvider.php:27-29` |
| Session | `login()` does **not** call `session()->regenerate()`; only `logout()` calls `invalidate()`/`regenerateToken()`. | `AuthController.php:66`; `ProfileController.php:56-60` |
| `verified` middleware | **Not used anywhere** — no email verification. | `routes/web.php` |
| `signed` middleware | **Not used anywhere** — the password-reset route uses no signed URL/token. | `routes/web.php:38` |
| `can` middleware | **Not used** — no policies/gates to bind. | `routes/web.php` |

---

## 7. Summary

| Property | Value | Evidence |
|---|---|---|
| `app/Http/Middleware` present? | **No** | `VERIFIED_FROM_SOURCE` |
| Custom middleware count | **0** | `bootstrap/app.php` |
| Policies / Gates | **0 / 0** | no `app/Policies`, no `Gate::` refs |
| Form Requests | **0** | no `app/Http/Requests` |
| Routes behind `auth` | **12 of 44** | `routes/web.php:63-64`, `:71-83` |
| Routes with `throttle` | **0** | `routes/web.php` |
| Authorization mechanisms | Ad-hoc `abort_unless` (×2) + `where('user_id',…)` scopes (×27) | grep over `app/` |
| Ownership gaps | `CartController::update`, `CartController::remove` | `CartController.php:33-62` |

Cross-links: [`./SECURITY_AUDIT.md`](./SECURITY_AUDIT.md) (SEC-B-03/04/05),
[`./ROUTES_AND_API_REFERENCE.md`](./ROUTES_AND_API_REFERENCE.md),
[`../13-security-audit.md`](../13-security-audit.md),
[`../29-remediation-plan.md`](../29-remediation-plan.md).
