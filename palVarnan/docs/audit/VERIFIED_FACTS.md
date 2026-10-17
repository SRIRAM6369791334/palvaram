# VERIFIED FACTS — Project B (`palVarnan`)

> Working notes for the canonical reference library under `docs/reference/`.
> Every fact here was checked directly against source on the audit date.
> Evidence vocabulary: `VERIFIED_FROM_SOURCE`, `VERIFIED_FROM_LIVE_SCHEMA`, `INFERRED`,
> `UNVERIFIED`, `NOT_AVAILABLE`, `CONFLICTING_EVIDENCE`.
> **Never copy secret values into documentation.**

## Identity & stack

- Root: `C:\xampp\htdocs\pauls\palVarnan`
- Framework: **Laravel `^12.0`** — `VERIFIED_FROM_SOURCE` (`composer.json`)
- PHP: **`^8.2`** — `VERIFIED_FROM_SOURCE` (`composer.json`)
- Payment SDK: **`razorpay/razorpay: ^2.9`** — present in **`composer.json` AND `composer.lock`**
  (`composer.lock` line ~3298). `VERIFIED_FROM_SOURCE`
  → The pre-existing doc claim "Razorpay is absent from composer.lock" is **OUTDATED**.
- Frontend: **Vite 7 + Tailwind 4 + laravel-vite-plugin 2** — `VERIFIED_FROM_SOURCE` (`package.json`).
- DB: shared MySQL database **`palvarnan_db`** (see cross-project notes).

## Counts (VERIFIED_FROM_SOURCE)

| Artefact | Count | Note |
|---|---|---|
| `Route::` entries in `routes/web.php` | **45** | README previously said 38 → OUTDATED |
| Controllers in `app/Http/Controllers` | **10** files | 9 domain + base `Controller.php` |
| Models in `app/Models` | **25** | README previously said 24 → OUTDATED |
| Domain migrations | **0** | only 3 stock Laravel migrations |
| Stock migrations | **3** | `0001_01_01_000000_create_users_table`, `..._create_cache_table`, `..._create_jobs_table` |
| Tables in SQL dump `palvarnan_db (2).sql` | **93** | `VERIFIED_FROM_LIVE_SCHEMA` (dump dated 2026-07-31) |
| Blade views | **23** files | |
| Services | **1** | `app/Services/ShiprocketService.php` |
| `app/Http/Middleware` | **absent** | Laravel 12 uses `bootstrap/app.php` |
| Tests | 3 files | `TestCase.php` + 2 scaffold `ExampleTest.php`; no domain tests |

## Controllers (10)

`AuthController`, `CartController`, `CheckoutController` (701 lines), `Controller` (base),
`CountryController`, `HomeController`, `ProductController`, `ProfileController`,
`SearchController`, `WishlistController`.

## Models (25)

`Area`, `AreaAssign`, `Brand`, `Cart`, `Category`, `Country`, `Coupon`, `District`,
`HomePromotion`, `Product`, `ProductChildImage`, `ProductOrder`, `ProductOrderItem`,
`ProductOrderUserAddress`, `ProductSlot`, `ProductStock`, `ProductTracking`,
`ProductTransactionLog`, `ProductVarient`, `State`, `SubCategory`, `Unit`, `User`,
`UserAddress`, `Wishlist`.
- `Unit` and `HomePromotion` are **empty models** (no relations/fillable) — `VERIFIED_FROM_SOURCE`.

## Routes (45) — `VERIFIED_FROM_SOURCE` (`routes/web.php`)

- Public: `/select-country` (GET/POST), `/` (home), `/search`, `/products`, `/product/{slug}`,
  `/about`, `/contact`, `/login`, `/register`, `/product` (view), privacy/terms/refund/shipping views.
- Auth POST endpoints are public: `/register-store`, `/login-store`, `/forgot-password`,
  `/reset-password-direct` (named `password.reset.direct`).
- Auth-protected (`middleware('auth')` group):
  `/checkout` + checkout AJAX endpoints (`prepare-payment`, `get-couriers`, `get-shipping-charge`,
  `get-cities`, `place-order`, `thankyou/{order}`), address CRUD.
- `auth` middleware on: `/profile-update` (PUT), `/logout` (POST), `/myaccount`, wishlist check
  endpoints. Cart/wishlist toggle/index routes are **not** all behind `auth`.
- Only ~10 routes sit behind `auth`.

## Checkout — CRITICAL CORRECTION (`CheckoutController.php`, 701 lines)

- `index()` builds cart/address/state view data.
- `getShiprocketCouriers()` calls `ShiprocketService::checkServiceability()`.
- `preparePayment()` creates a Razorpay order via `new Api(key, secret)`; returns
  `razorpay_order_id`, amount, key.
- `placeOrder()` **DOES create an order** and writes across multiple tables inside a
  `DB::beginTransaction()`:
  1. `product_orders` (`ProductOrder::create`) — includes `billing_state` (line 306),
     `order_number`, `order_id` = same value, status `Order Placed`, payment status.
  2. `product_order_user_addresses`
  3. `product_transaction_logs`
  4. `product_tracking`
  5. `product_order_items` (per line; includes `product_image`)
  6. `product_slots` (dashboard packing table)
  7. stock deduction in `productstocks` and `product_varients`
  8. clears `carts` rows for the user
- For `Online Payment`, it verifies the Razorpay signature via
  `$api->utility->verifyPaymentSignature($attributes)` (lines 252-268). `VERIFIED_FROM_SOURCE`.
  → The pre-existing claim "checkout writes no order / transaction rolls back after payment" is
  **CONTRADICTED BY SOURCE / OUTDATED**.
- Client-supplied money fields still present: `shipping_charge` and `coupon_discount` are read from
  the request (lines 286-288) → **INFERRED** price-tampering risk remains. VERIFIED the code reads them.
- After commit, creates a Shiprocket order (`ShiprocketService::createOrder`) and optionally
  assigns AWB; failures are caught and logged (non-fatal).
- `thankyou()` scopes by `user_id` (ownership enforced).

## Address ownership

- `updateAddress()` and `destroyAddress()` call `abort_unless($address->user_id === Auth::id(), 403)`
  → ownership enforced. `VERIFIED_FROM_SOURCE`.

## Auth (`AuthController.php`) — CRITICAL

- `registerStore()` creates a `users` row (country_id from session).
- `login()` looks up by email OR phone_number, `Hash::check`, `Auth::login`.
- `forgotPassword()` uses `Password::sendResetLink` (Laravel password broker).
- `resetPassword()` (route `/reset-password-direct`, **public/unauthenticated**):
  validates only `email` + `password`, finds the user by email, and sets a new password with
  `Hash::make` — **NO token, NO signed link, NO expiry**. → **CRITICAL account-takeover**
  (`VERIFIED_FROM_SOURCE`).

## Cart / Wishlist — IDOR reconciliation

- `CartController::update()` iterates `$request->quantities` and does
  `Cart::where('id',$cartId)->update(...)` with **no ownership check** → IDOR. `VERIFIED_FROM_SOURCE`.
- `CartController::remove($id)` uses `Cart::findOrFail($id)->delete()` with **no ownership check**
  (and it is a GET route) → IDOR. `VERIFIED_FROM_SOURCE`.
- `CartController::ajaxUpdate()` / `ajaxRemove()` DO scope by `user_id`/ip → safe.
- `CartController::store()`, `index()`, `subtotal()` scope by user/ip.
- `WishlistController::remove($id)` now DOES `->where('user_id', Auth::id())` → the previously
  reported wishlist IDOR is **FIXED / OUTDATED**.
- `WishlistController::toggle/index/check/checkProduct` scope by `user_id`.

## Search — config bypass

- `SearchController.php` **line 23** uses `env('MAIN_URL')` directly
  (`$baseImageUrl = rtrim(env('MAIN_URL'), '/') . '/images/';`). `VERIFIED_FROM_SOURCE`.
  → Pre-existing quick-check `grep -rn "env(" app/` expecting 0 matches is **OUTDATED** for this line.

## Shiprocket service (`app/Services/ShiprocketService.php`)

- Methods: `getToken`, `checkServiceability`, `createOrder`, `assignAWB`, `generatePickup`,
  `generateManifest`, `printManifest`, `generateLabel`, `printInvoice`, `trackAWB`,
  `getPickupLocations`.
- Credentials via `config('services.shiprocket.*')` (env keys `SHIPROCKET_EMAIL`,
  `SHIPROCKET_PASSWORD`, `SHIPROCKET_PICKUP_LOCATION`, `SHIPROCKET_PICKUP_PINCODE`).
- **`generatePickup` (the pickup-scheduling method) has NO caller anywhere in `app/`**
  → pickup is never scheduled. `VERIFIED_FROM_SOURCE`.
  (Pre-existing doc called this method `createPickup()`, which does not exist — naming is off; the
  behavioural claim "pickup is never scheduled" still holds for `generatePickup`.)
- `generateLabel` / `generateManifest` also have no callers.

## Security headline reconciliation vs `docs/README.md`

| Pre-existing headline | Current status |
|---|---|
| Password reset requires no token | **STILL CRITICAL** — confirmed in `AuthController::resetPassword` |
| Checkout writes no order (`billing_state`, `product_image` in no migration) | **OUTDATED** — order IS written; the two columns ARE used by controller + model `$fillable`. The "no migration" part remains true (0 domain migrations). |
| Razorpay absent from composer.lock | **OUTDATED** — present in `composer.json` and `composer.lock` |
| Live creds in web-reachable `test_apis.php`/`test_services.php` | root scripts still exist — see `SECURITY_AUDIT` (do not reproduce values) |
| Client controls amount | **STILL PRESENT** for `shipping_charge`/`coupon_discount` |
| Three IDORs (cart update/remove, wishlist remove) | cart update + cart remove **STILL PRESENT**; wishlist remove **FIXED** |
| Zero domain migrations | **STILL TRUE** (3 stock only) |
| Pickup never scheduled (`createPickup`) | **STILL TRUE** for `generatePickup` (name corrected) |
| No rate limiting / policies / form requests | **STILL TRUE** |
| `APP_DEBUG=true`, root DB user, empty password | env-dependent — verify `.env.example`, do not print secrets |

## Git / repository state

- Repo root `C:\xampp\htdocs\pauls`, 4 commits; latest `a864d15` "Update palVarnan composer.lock to
  include razorpay…". Checkout feature **is tracked** now → pre-existing "one commit; checkout
  untracked" claim is **OUTDATED**.
- Only untracked change: `palVarnan/docs/audit/` (this audit).

## Rules for writers

1. Read the actual source before asserting anything. Cite `file:line`.
2. Reuse and **link** the existing numbered docs (`../01-…` through `../30-…`) rather than copying.
3. Never print credential values; refer to env keys only.
4. Label every non-obvious claim with an evidence tag.
5. Do not modify anything outside `docs/`.
