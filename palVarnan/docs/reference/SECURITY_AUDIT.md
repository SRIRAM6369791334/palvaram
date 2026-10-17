# Security Audit (Source-Verified Register) — Project B (`palVarnan`)

> A source-verified register of security findings against the canonical source. Evidence
> vocabulary: `VERIFIED_FROM_SOURCE`, `VERIFIED_FROM_LIVE_SCHEMA`, `INFERRED`,
> `UNVERIFIED`, `NOT_AVAILABLE`, `CONFLICTING_EVIDENCE`. **No credentials or PII are
> reproduced.** The register below explicitly covers the items requested, plus additional
> findings.
>
> This file **reconciles** historical claims:
> - Wishlist IDOR (`WishlistController::remove`) is **FIXED** (scoped by `user_id`) — R-1.
> - "Checkout writes no order / transaction rolls back after payment" is **OUTDATED** —
>   `placeOrder()` writes across multiple tables inside `DB::beginTransaction()` and commits
>   on success; a known historical issue is the **missing columns / runtime DB mismatches**
>   that cause certain builds to throw SQL errors — R-2.
>
> Siblings: [`./MIDDLEWARE_AND_AUTHORIZATION.md`](./MIDDLEWARE_AND_AUTHORIZATION.md),
> [`./ROUTES_AND_API_REFERENCE.md`](./ROUTES_AND_API_REFERENCE.md),
> [`./CONTROLLER_REFERENCE.md`](./CONTROLLER_REFERENCE.md),
> [`./INTEGRATION_MAP.md`](./INTEGRATION_MAP.md),
> [`./FRONTEND_REFERENCE.md`](./FRONTEND_REFERENCE.md).
> Existing narrative: [`../13-security-audit.md`](../13-security-audit.md),
> [`../20-known-issues.md`](../20-known-issues.md).

---

## 1. REQUIRED FINDINGS

The following six required items are verified against source.

### SEC-B-01 — CRITICAL: unauthenticated password reset via `/reset-password-direct`

- **Severity:** CRITICAL
- **ID:** SEC-B-01
- **Location:** `app/Http/Controllers/AuthController.php:100-126` (`resetPassword()`);
  `routes/web.php:38` (`POST /reset-password-direct`, name `password.reset.direct`)
- **Evidence:** `VERIFIED_FROM_SOURCE`
- **Description:** `resetPassword()` validates only `email` and `password`, looks up the user
  by email, sets a new `Hash::make($request->password)`, saves, and calls `Auth::logout()`.
  **No token, no expiry, no verification step.**
- **Impact:** Any unauthenticated caller who knows (or enumerates) an email address can reset
  that account's password and effectively take over the account.
- **Recommendation:**
  - Delete or replace `resetPassword()` with token-based flow using
    `Illuminate\Support\Facades\Password::reset()` and a signed `GET /reset-password/{token}`
    view + `password_confirmation`.
  - Return a generic response regardless of whether the email exists (user-enumeration hardening).
  - Add `throttle` to `/reset-password-direct` and to the legitimate forgot/reset endpoints.
  - Ensure mailer is configured (currently `MAIL_MAILER=log`) so reset links can actually reach users.

### SEC-B-02 — HIGH: client-supplied `shipping_charge`/`coupon_discount` in `placeOrder`

- **Severity:** HIGH
- **ID:** SEC-B-02
- **Location:** `app/Http/Controllers/CheckoutController.php:191-193` (`preparePayment()`),
  `:286-288` (`placeOrder()`); `routes/web.php:73`, `:77`
- **Evidence:** `VERIFIED_FROM_SOURCE`
- **Description:** `preparePayment()` and `placeOrder()` do **not** take subtotal/GST from the
  request (those are recomputed from cart). However, `shipping_charge` and `coupon_discount`
  are `floatval($request->shipping_charge)` and `floatval($request->coupon_discount)` with no
  server-side validation, no coupon lookup, and no binding to the selected courier/state.
- **Impact:** Client can submit arbitrary values. `preparePayment()` sets
  `$total = max((subtotal+gst+shipping)-discount, 1)` (`:193`) — creates a Razorpay order
  for as little as ₹1 (price drop). `placeOrder()` uses `(subtotal+gst+shipping)-discount`
  with **no floor** (`:288`) — can persist negative/zero `total_amount`. `shipping_charge`
  is also echoed into Shiprocket payload (`:486`).
- **Recommendation:**
  - Derive `shipping_charge` server-side from state/courier quote (or state table), never from
    request.
  - Validate a coupon code against `coupons` table and apply computed discount server-side.
  - Extract a single `PricingService::quote(...)` used by both `preparePayment()` and
    `placeOrder()` with identical arithmetic.
  - Treat money fields as read-only server-computed outputs; remove them from the accepted
    request contract.

### SEC-B-03 — HIGH: IDOR cart update (`CartController::update`)

- **Severity:** HIGH
- **ID:** SEC-B-03
- **Location:** `app/Http/Controllers/CartController.php:33-44`;
  `routes/web.php:47-48` (`POST /cart-update`)
- **Evidence:** `VERIFIED_FROM_SOURCE`
- **Description:** `update()` iterates `$request->quantities` (`cartId => qty`) and does
  `Cart::where('id', $cartId)->update(['product_quantity' => $qty])` with **no ownership
  check** (no `user_id` and no `ip_addresscart`/guest token filtering). `$qty` is written
  raw.
- **Impact:** Any unauthenticated/any user can edit quantity of any cart row by ID; can also
  attempt mass writes (unbounded array). Negative/large values are accepted.
- **Recommendation:**
  - Scope updates to the owner (current user or current guest identity). Validate each entry
    (`cart_id` exists and belongs to owner).
  - Use a Form Request or inline validation: `integer`, `min:0` or `min:1`, `max:20`.
  - Prefer a bulk scoped `UPDATE ... WHERE id IN (...) AND (user_id=... OR guest ...)` to avoid
    N unscoped queries; reject any ids not owned by the caller.
- **Notes:** `CartController::ajaxUpdate()` **does** scope by `user_id`/`ip` (`:95-113`) —
  the two update paths differ on both authorization and response shape.

### SEC-B-04 — HIGH: IDOR cart remove (`CartController::remove`, GET)

- **Severity:** HIGH
- **ID:** SEC-B-04
- **Location:** `app/Http/Controllers/CartController.php:46-62`;
  `routes/web.php:50` (`GET /cart-remove/{id}`)
- **Evidence:** `VERIFIED_FROM_SOURCE`
- **Description:** `remove($id)` calls `Cart::findOrFail($id)->delete()` with **no ownership
  check**. The route is a **GET** request.
- **Impact:** Any visitor can delete any cart line by ID. Because it is a state-changing
  `GET`, CSRF middleware does not protect it; link prefetching/crawlers can trigger deletion.
- **Recommendation:**
  - Change route to `DELETE /cart/{id}` (or `POST` with CSRF) and scope deletion to the owner.
  - Apply `abort_if`/`abort_unless` to ensure the row belongs to `Auth::id()` or current guest.
  - Return consistent JSON for AJAX and proper redirect otherwise.

### SEC-B-05 — HIGH: no rate limiting

- **Severity:** HIGH
- **ID:** SEC-B-05
- **Location:** `routes/web.php` (all routes); `bootstrap/app.php:13-15` (empty middleware
  body); **no `RateLimiter::for(...)` definitions** in `app/`
- **Evidence:** `VERIFIED_FROM_SOURCE` (grep over `app/` for `RateLimiter`, `throttle` returned
  zero matches; `bootstrap/app.php` has only `trustProxies`)
- **Description:** Zero throttle rules. Laravel 12 default web group applies session/cookies/CSRF,
  but **no rate-limiting middleware or named limiters** are registered.
- **High-value targets:** `POST /login-store`, `POST /register-store`,
  `POST /forgot-password`, `POST /reset-password-direct`, `POST /checkout/get-couriers`,
  `GET /search`, all cart/wishlist write endpoints, `POST /checkout/place-order`,
  `POST /checkout/prepare-payment`.
- **Impact:** Credential stuffing, account enumeration/abuse, mass password reset attempts,
  quota burn on Shiprocket (couriers endpoint calls live external API), cheap DoS on the
  `LIKE '%q%'` search.
- **Recommendation:**
  - Define named limiters in `bootstrap/app.php` via `RateLimiter::for('login', ...)`,
    `api`, `shiprocket`, `search`, etc.
  - Apply `->middleware('throttle:login')`/sensible per-minute caps (e.g. login 5/min,
    register/forgot/reset 3/min, search 30/min, couriers 15/min, place-order 10/min).
  - Use per-IP keying; consider per-user when authenticated where appropriate.

### SEC-B-06 — MEDIUM: `env('MAIN_URL')` direct call in `SearchController.php:23`

- **Severity:** MEDIUM
- **ID:** SEC-B-06
- **Location:** `app/Http/Controllers/SearchController.php:23`
- **Evidence:** `VERIFIED_FROM_SOURCE`
- **Description:** `$baseImageUrl = rtrim(env('MAIN_URL'), '/') . '/images/';` calls `env()`
  directly in application code (not in `config/`).
- **Impact:** After running `php artisan config:cache`, `env()` returns `null` outside
  configuration files; `MAIN_URL` becomes empty and image URLs collapse to `/images/...`,
  breaking search-result images in production (and any cached-config deployment).
- **Recommendation:**
  - Move `MAIN_URL` to `config/services.php` (or `config/app.php`) and read via
    `config('services.main_url')` (or equivalent). Add to `.env.example`.
  - Never call `env()` from controllers/services; only from `config/*` files.
  - If images are served by this app, consider using `asset('images/...')` instead of a
    hard-coded base host.

---

## 2. RESOLVED / RECONCILIATION ITEMS (required to state explicitly)

### R-1 — RESOLVED: wishlist IDOR (`WishlistController::remove` scopes by `user_id`)

- **Status:** RESOLVED (previously reported as HIGH IDOR; **FIXED**)
- **ID:** R-1
- **Location:** `app/Http/Controllers/WishlistController.php:50-54`;
  `routes/web.php:62` (`DELETE /wishlist/{id}/remove`)
- **Evidence:** `VERIFIED_FROM_SOURCE`
- **Description:** `remove()` executes
  `Wishlist::where('id', $id)->where('user_id', Auth::id())->delete();` — it **scopes** to
  the authenticated user. The route is `DELETE` with CSRF (web group).
- **Residual caveat:** The route has **no `auth` middleware** (`routes/web.php:62`), so for an
  anonymous caller `Auth::id()` is `null` and the query becomes `WHERE id=$id AND user_id IS
  NULL`. That means an anonymous request can delete any wishlist row whose `user_id` is `NULL`
  (orphan rows). It does **not** allow deleting another authenticated user's wishlist row by
  ID.
- **Conclusion:** The "wishlist remove IDOR" is **FIXED** in terms of cross-user access; the
  missing `auth` middleware is a **separate operational/abuse issue** (orphan-row deletion),
  not an IDOR across different user accounts. Label: **RESOLVED / CONFLICTING_EVIDENCE** (the
  prior broad claim is outdated; scope is correct when `auth` is present; anonymous
  exposure is a different defect).

### R-2 — RECONCILIATION: "checkout writes no order" → **OUTDATED**

- **Status:** CONFLICTING_EVIDENCE (historical claim **OUTDATED**; correctness depends on
  schema)
- **ID:** R-2
- **Location:** `app/Http/Controllers/CheckoutController.php:225-535`
- **Evidence:** `VERIFIED_FROM_SOURCE` (full `placeOrder()` implementation read; transaction
  block `:292-421` verified)
- **Description:** `placeOrder()` does **create an order**. Inside `DB::beginTransaction()` it
  writes:
  1. `product_orders` (`:295`) — includes `billing_state` (`:306`), `shipping_state`,
     `courier_name`, `subtotal/gst/shipping/total`, payment fields, `order_number`,
     `order_id` same as `order_number`, `status='Order Placed'`, `payment_status`
  2. `product_order_user_addresses` (`:330`)
  3. `product_transaction_logs` (`:348`)
  4. `product_tracking` (`:359`)
  5. `product_order_items` (`:373`) — includes `product_image`
  6. `product_slots` (`:388`)
  7. stock deductions in `productstocks` (`:403-408`) and `product_varients` (`:411-416`)
  8. clears `carts` for the user (`:419`)
- Commit (`:421`); after commit Shiprocket `createOrder()` (`:498`) and optional
  `assignAWB()` (`:510`), with failures caught and logged (`:525-527`).
- **Historical caveat:** The repository's live schema may lack some columns that the
  controller/model expect (e.g. historical migration drift). The code **does write** orders;
  runtime SQL errors can occur if the DB is out of sync — that is a **schema/integration**
  mismatch, not that the code path "writes no order". The earlier doc saying "checkout writes
  no order (`billing_state`, `product_image` in no migration)" conflated code behaviour with
  schema state.
- **Conclusion:** The claim "checkout writes no order" is **OUTDATED**. The "no migration"
  observation remains true for domain migrations (stock Laravel only) and may explain
  historical rollbacks, but the code unambiguously persists an order. **CONFLICTING_EVIDENCE**
  → **OUTDATED**.

---

## 3. ADDITIONAL SOURCE-VERIFIED FINDINGS

These are distinct from the six required and reinforce the register.

### SEC-B-07 — MEDIUM: root diagnostic scripts with credentials and TLS-off (do not reproduce)

- **Severity:** MEDIUM (operational/exposure)
- **ID:** SEC-B-07
- **Location:** `test_apis.php` (5,523 B), `test_services.php` (7,028 B) — sit in web root
  `C:\xampp\htdocs\pauls\palVarnan/`
- **Evidence:** `VERIFIED_FROM_SOURCE` (file reads; contents contain hard-coded credentials and
  `CURLOPT_SSL_VERIFYPEER => false`, `CURLOPT_SSL_VERIFYHOST => 0`)
- **Description:** Both scripts read `.env` or fall back to hard-coded values and make
  outbound HTTPS calls with TLS verification disabled. They are web-reachable in a typical
  XAMPP document-root layout.
- **Impact:** Credential exposure if served publicly; MITM risk when run; accidental execution.
- **Recommendation:** Delete both files from the repository/public tree, add to `.gitignore`,
  rotate any credentials they reference (Razorpay/Shiprocket/DB). Never commit diagnostic
  scripts containing secrets.

### SEC-B-08 — HIGH: cart remove is state-changing GET (no CSRF)

- **Severity:** HIGH
- **ID:** SEC-B-08
- **Location:** `routes/web.php:50` (`GET /cart-remove/{id}`); `CartController::remove()`
- **Evidence:** `VERIFIED_FROM_SOURCE`
- **Description:** Combined with **SEC-B-04** (no ownership), the `GET` verb means CSRF
  middleware ignores it and any `<a href>` or prefetch can trigger deletion.
- **Impact:** Cross-site request + prefetch-based deletion; trivial to trigger.
- **Recommendation:** Convert to `DELETE /cart/{id}`, add `auth`/ownership scope, include
  CSRF token.

### SEC-B-09 — MEDIUM: `SearchController` env bypass (duplicate of SEC-B-06)

- **Severity:** MEDIUM
- **ID:** SEC-B-09 (alias of SEC-B-06)
- **Evidence:** `VERIFIED_FROM_SOURCE`

(See SEC-B-06.)

### SEC-B-10 — HIGH: CSRF/unauth gaps on write endpoints

- **Severity:** HIGH
- **ID:** SEC-B-10
- **Location:** `routes/web.php:19 PUT /profile-update` (no `auth`), `:27 POST /wishlist/toggle`
  (no `auth`), `:28 GET /wishlist` (no `auth`), `:22 POST /cart-update` (no owner), `:23 GET
  /cart-remove/{id}` (no owner, GET)
- **Evidence:** `VERIFIED_FROM_SOURCE`
- **Description:** Several write/read endpoints lack `auth` middleware or ownership. `fetch()`
  calls: 29 of 32 include `X-CSRF-TOKEN`; 3 missing; only `checkout.blade.php` handles 419.
- **Impact:** CSRF gaps + IDOR + unauthenticated writes.
- **Recommendation:** Enforce `auth` where appropriate; fix ownership; add a shared `apiFetch()`
  with CSRF and 419 handling.

### SEC-B-11 — MEDIUM: no session regeneration on login

- **Severity:** MEDIUM
- **ID:** SEC-B-11
- **Location:** `AuthController::login()` `:66` does `Auth::login($user)` without
  `$request->session()->regenerate()`
- **Evidence:** `VERIFIED_FROM_SOURCE`
- **Description:** Session ID is not rotated after successful authentication.
- **Impact:** Session fixation risk in certain scenarios.
- **Recommendation:** Call `$request->session()->regenerate()` immediately after
  `Auth::login($user)`.

### SEC-B-12 — MEDIUM: PII/operational data in logs

- **Severity:** MEDIUM
- **ID:** SEC-B-12
- **Location:** `CheckoutController.php:497` logs full Shiprocket order payload (includes name,
  address, phone, email, pincode); `ShiprocketService.php` logs login failures and serviceability
  errors; `laravel.log` historically contains SQL/stack traces from failed inserts
- **Evidence:** `VERIFIED_FROM_SOURCE` (payload logged at `info` level)
- **Description:** Customer address/phone/email written to disk at info level on every order.
- **Impact:** PII leakage to logs; compliance/retention concerns.
- **Recommendation:** Redact PII from payload logs; log only shipment/order IDs and dimensions;
  never log full request bodies containing PII; set log retention.

### SEC-B-13 — MEDIUM: Razorpay amount/receipt + formula divergence

- **Severity:** MEDIUM
- **ID:** SEC-B-13
- **Location:** `CheckoutController.php:193` vs `:288` (floor differs); `:201` receipt is
  `'rcpt_' . uniqid()` (not the internal `order_number`)
- **Evidence:** `VERIFIED_FROM_SOURCE`
- **Description:** Two different total formulas (with/without `max(...,1)` floor). Receipt does
  not carry `order_number`, hindering reconciliation. No idempotency key.
- **Impact:** Reconciliation difficulty; order-retry risk; possible total mismatch in edge cases.
- **Recommendation:** Unify pricing (single service); use `order_number` in receipt/notes where
  feasible; add idempotency keys for `placeOrder()`/`preparePayment()`; verify amount against
  Razorpay order on callback if applicable.

### SEC-B-14 — LOW: mass-assignment posture (acceptable given explicit creates)

- **Severity:** LOW
- **ID:** SEC-B-14
- **Evidence:** `VERIFIED_FROM_SOURCE` — grep for `->create($request)`/`->update($request->all())`
  returned **zero** matches. Controllers use explicit arrays.
- **Models:** `ProductOrder::$fillable` is broad (includes operational fields like
  `delivery_person_id`, `pickup_scheduled`, `awb_code`, etc.) — none are populated directly
  from raw request in controllers. No direct mass assignment observed.
- **Recommendation:** Keep using explicit field lists (current practice is safe). If any future
  code starts using `$request->all()` on models, tighten `$fillable`/$guarded`.

---

## 4. RESOLVED/OUTDATED SUMMARY (explicit statement)

| Historical claim | Current status | Evidence |
|---|---|---|
| Wishlist IDOR (remove) | **FIXED** — `where('user_id', Auth::id())` scoped; only anonymous exposure to orphan rows remains | `WishlistController.php:52` (`VERIFIED_FROM_SOURCE`) |
| Checkout writes no order / rolls back after payment | **OUTDATED** — `placeOrder()` writes 6 tables + stock + cart clear inside transaction and commits; runtime rollbacks can occur if DB schema mismatches (missing columns). Code persists orders. | `CheckoutController.php:292-421` (`VERIFIED_FROM_SOURCE`) |
| Razorpay absent from composer.lock | **OUTDATED** — present in `composer.json:12` and `composer.lock` | `VERIFIED_FROM_SOURCE` |
| "One commit; checkout untracked" | **OUTDATED** — checkout feature tracked; untracked are `docs/audit/` etc. | `git` state (`VERIFIED_FROM_SOURCE`) |
| Pickup never scheduled (`createPickup`) | **STILL TRUE for `generatePickup`** — `generatePickup()` has no callers; method name differs (`createPickup` vs `generatePickup`) | `ShiprocketService.php:119-124`; grep callers (`VERIFIED_FROM_SOURCE`) |

---

## 5. MITIGATION PRIORITY

| Priority | Items | Rationale |
|---|---|---|
| **P0** | SEC-B-01, SEC-B-02, SEC-B-03, SEC-B-04, SEC-B-05, SEC-B-08 | Direct account-takeover + IDOR + price manipulation + abuse surface |
| **P1** | SEC-B-06 (SEC-B-09), SEC-B-07, SEC-B-10, SEC-B-11, SEC-B-12, SEC-B-13 | Config correctness + exposure + session/CSRF + logging + reconciliation |
| **P2** | SEC-B-14 | Hardening |

Cross-links: [`../29-remediation-plan.md`](../29-remediation-plan.md) (sequenced fixes),
[`../13-security-audit.md`](../13-security-audit.md) (broader register),
[`../20-known-issues.md`](../20-known-issues.md).
