# 13 — Security Audit

Severity: **CRITICAL** (exploitable for data or money loss) · **HIGH** (significant impact) · **MEDIUM** (needs conditions or is defence-in-depth) · **LOW** (hardening)

Every finding is verified against source. **7 CRITICAL, 10 HIGH, 8 MEDIUM, 6 LOW — 31 total.**

---

## 1. Summary table

| ID | Finding | Severity | Location |
|---|---|---|---|
| SEC-01 | Live credentials hard-coded in a web-reachable PHP script | **CRITICAL** | `test_apis.php`, `test_services.php` |
| SEC-02 | TLS verification disabled | **CRITICAL** | both test scripts |
| SEC-03 | Password reset requires no token | **CRITICAL** | `AuthController.php:95-120` |
| SEC-04 | Checkout writes nothing — payment captured, order lost | **CRITICAL** | `CheckoutController.php:290-421` |
| SEC-05 | Client-controlled shipping charge and discount | **CRITICAL** | `CheckoutController.php:191-193, 286-288` |
| SEC-05b | Three total formulas, two different floors | **HIGH** | `:193`, `:288`, `checkout.blade.php:626` |
| SEC-06 | Cart update — no ownership check | **CRITICAL** | `CartController.php:35-44` |
| SEC-06b | Cart quantity accepts any value | **HIGH** | `CartController.php:35-44, 108` |
| SEC-07 | Cart remove — no ownership check, on GET | **CRITICAL** | `CartController.php:46-60` |
| SEC-08 | Wishlist remove — IDOR | **HIGH** | `WishlistController.php:45-49` |
| SEC-09 | Wishlist writes with `user_id = NULL` | **HIGH** | `WishlistController.php:12-33` |
| SEC-10 | `myaccount` readable unauthenticated | **HIGH** | `routes/web.php:60`, `ProfileController.php:69` |
| SEC-11 | `profile.update` has no `auth` | **HIGH** | `routes/web.php:32-33` |
| SEC-12 | No rate limiting anywhere | **HIGH** | `routes/web.php`, `bootstrap/app.php:13-15` |
| SEC-13 | User enumeration on reset and registration | **HIGH** | `AuthController.php:108, 21` |
| SEC-14 | `APP_DEBUG=true` in the deployed `.env` | **HIGH** | `.env` |
| SEC-15 | SQL, stack trace and payment error text leaked to the client | **HIGH** | `CheckoutController.php:429, 214` |
| SEC-16 | Session fixation — no `regenerate()` on login | **MEDIUM** | `AuthController.php:44-70` |
| SEC-17 | No session invalidation outside `logout` | **MEDIUM** | same |
| SEC-18 | PII written to logs | **MEDIUM** | `laravel.log`, `ShiprocketService.php:41-44`, `CheckoutController.php:497` |
| SEC-19 | No CSRF handling on 20+ `fetch()` calls | **MEDIUM** | all Blade |
| SEC-20 | `root` DB user with an empty password | **MEDIUM** | `.env` |
| SEC-21 | SQL injection surface — `whereRaw` with `FIND_IN_SET` | **MEDIUM** | `ProductController.php:26,29,32` |
| SEC-22 | No idempotency on order placement | **MEDIUM** | `CheckoutController.php:225` |
| SEC-23 | Session cookie not marked secure | **MEDIUM** | `config/session.php`, `.env` |
| SEC-24 | Address fields accept free-text pincode/state | **LOW** | `CheckoutController.php:553-633` |
| SEC-25 | Minimum password length 6 | **LOW** | `AuthController.php:20` |
| SEC-26 | Registration does not validate `phone_number` uniqueness | **LOW** | `AuthController.php:14` |
| SEC-27 | Inconsistent password hashing (`bcrypt` vs cast) | **LOW** | `ProfileController.php:29-48` |
| SEC-28 | No email verification | **LOW** | `User.php:5` |
| SEC-29 | Secrets in `.env` sitting beside a public web root | **LOW** | project layout |

---

## 2. CRITICAL findings

### SEC-01 — Live credentials hard-coded in a web-reachable script

`test_apis.php` (5 523 B) and `test_services.php` (7 028 B) sit in `C:\xampp\htdocs\pauls\palVarnan\` — **the document root**.

Both files contain literal Razorpay and Shiprocket **live** credentials. Values are deliberately not reproduced in this document.

Under XAMPP's default configuration these are reachable at `http://localhost/palVarnan/test_apis.php`. Any other user/process on the machine can read them, and **if the folder is ever exposed to a public host, the credentials are public.**

```
Impact:     full control of the Razorpay account (create orders, capture, refund)
            full control of the Shiprocket account (create shipments, print labels)
            direct unrestricted MySQL access if the same creds are reused
Mitigation: DELETE BOTH FILES IMMEDIATELY, then ROTATE every credential
            (Razorpay key+secret, Shiprocket email+password, DB password).
            Add them to .gitignore so they cannot be re-committed.
```

### SEC-02 — TLS verification disabled

Both scripts set:
```php
CURLOPT_SSL_VERIFYPEER   => false,
CURLOPT_SSL_VERIFYHOST   => 0,
```
Any network attacker can transparently intercept the Razorpay/Shiprocket traffic **and capture the credentials themselves**, plus all payment data.

```
Fix: remove both lines. If a certificate chain problem exists, install the CA bundle
     — never disable verification. (The main app's Laravel Http client does verify
     TLS correctly; only these scripts bypass it.)
```

### SEC-03 — Password reset requires no token — **account takeover**

```php
// AuthController.php:95-120
public function resetPassword(Request $request)
{
    $request->validate(['email' => 'required|email', 'password' => 'required|min:6']);
    $user = User::where('email', $request->email)->first();
    if (!$user) {
        return response()->json(['errors' => ['email' => ['User not found']]], 422);
    }
    $user->password = Hash::make($request->password);
    $user->save();
    Auth::logout();
    return response()->json(['success' => true, 'message' => 'Password updated successfully']);
}
```

**No token check. No expiry. No confirmation. No notification to the account owner.**

```
Attack:  POST /reset-password-direct
         email=<victim@example.com>&password=<attacker-chosen>
         → instant takeover of any account whose email is known.

The real implementation exists at AuthController::forgotPassword():72-93 and calls
Password::sendResetLink(), but NO VIEW CALLS IT and NO ROUTE ACCEPTS A TOKEN.
It is entirely dead code.

Fix: delete resetPassword() and password.reset.direct. Restore the standard flow:
  POST /forgot-password → sendResetLink()
  GET  /reset-password/{token} → view   (new route)
  POST /reset-password → Password::reset()
Also: set MAIL_MAILER to a real transport — currently 'log', so the legitimate
link only ever reaches storage/logs/laravel.log.
```

### SEC-04 — Checkout persists nothing; payment is captured and lost

**VERIFIED in `storage/logs/laravel.log`:**
```
Unknown column 'billing_state' in 'field list'      (2026-08-17 05:12:55)
Unknown column 'product_image' in 'field list'     (2026-08-17)
```
No migration anywhere creates these columns.

```
Sequence:
  1. Razorpay order created
  2. Customer pays — money leaves the account, payment_status 'Paid' recorded
     in the attempted INSERT (log shows pay_TQiWawBEhaCveJ)
  3. INSERT fails on billing_state
  4. Transaction rolls back → no order, cart intact
  5. HTTP 500 to the browser with the raw SQL error
  6. No refund logic exists anywhere in the codebase

Missing columns, all present in `ProductOrder::$fillable` but absent from the
`palvarnan_db (2).sql` DDL:

| Column | Table | Written at | Value source |
|---|---|---|---|
| `billing_state` | `product_orders` | `:306` | `$request->shipping_state` — **billing copies shipping** |
| `shipping_state` | `product_orders` | `:315` | `$request->shipping_state` |
| `courier_name` | `product_orders` | `:321` | `$request->selected_courier_name` — **client-supplied, unvalidated** |
| `product_image` | `product_order_items` | `:378` | derived |
| `discount` | `product_orders` | — | in `$fillable`, **never written** — the applied discount is unrecoverable |

The two log-confirmed errors are the first row and `product_image`. `courier_name` at `:321` sits **inside the same `ProductOrder::create()` call** as `billing_state`, so it fails identically on the same statement — the customer never reaches the Shiprocket stage. MySQL reports one unknown column per statement, which is why only `billing_state` appears in the log.

`courier_name` is a second trust problem independent of the missing column: it is written from a request field that no validation rule covers, so even after the migration lands, the stored courier name is whatever the client sent.

Fix: add the migrations (see 22-database-integrity.md). Then wrap order creation
in a durable saga: persist the intent FIRST, then charge, then reconcile — and
add a refund-on-failure path. Also set `payment_status = 'Paid'` only after the
transaction commits; it is currently written at `:324`, inside the block that rolls back.
```

### SEC-05 — Client controls the shipping charge and discount

**Correction to an earlier reading of this codebase:** `preparePayment()` does **not** take `subtotal` from the request. `CheckoutController.php:181-189` iterates `$this->getCartItems()` and rebuilds `subtotal` and `gstAmount` from `product_varient.offer_price` × cart quantity. That part is correct.

The defect is narrower and still critical — **the two adjustable components come from the request** and are never validated:

```php
// preparePayment() :191-193
$shipping = floatval($request->shipping_charge ?? 0);
$discount = floatval($request->coupon_discount ?? 0);
$total    = max(($subtotal + $gstAmount + $shipping) - $discount, 1);

$api->order->create(['amount' => (int) round($total * 100), 'currency' => 'INR']);
```

```php
// placeOrder() :286-288 — same source, and NO floor
$shipping = floatval($request->shipping_charge ?? 0);
$discount = floatval($request->coupon_discount ?? 0);
$total    = ($subtotal + $gstAmount + $shipping) - $discount;
```

No coupon table is consulted. No rate is checked. No coupon code is validated.

```
Attack A:  POST /checkout/prepare-payment  coupon_discount=100000
           → max(floored)  → Razorpay order created for ₹1.00

Attack B:  POST /checkout/place-order  coupon_discount=-5000
           → no floor     → negative total_amount persisted to product_orders

Attack C:  POST /checkout/place-order  shipping_charge=0
           → free shipping on every order
```

`shipping_charge` is also echoed straight into the Shiprocket payload (`:486`), so the courier invoice carries the manipulated figure.

**The 1-rupee floor in `preparePayment` makes Attack A possible; the missing floor in `placeOrder` makes Attack B possible.** Two different formulas for the same money value — see SEC-05b.

**Fix:** derive the discount from a coupon lookup keyed by a code, and derive the shipping charge from the courier actually selected (or a server-side state rate table). Never read either value from the request body.

### SEC-05b — Three divergent total formulas

| Site | Formula | Floor |
|---|---|---|
| `checkout.blade.php:626` | `Math.max((baseSubtotalWithGst + currentShippingCharge) - currentDiscount, 0)` | `0` (client-side display only) |
| `preparePayment()` `:193` | `max((subtotal + gst + shipping) - discount, 1)` | `1` |
| `placeOrder()` `:288` | `(subtotal + gst + shipping) - discount` | **none** |

**Two server-side formulas, two different floors.** The Blade copy drives the hidden `shipping_charge` / `coupon_discount` inputs (`:472-473`, `:629`) that the two server endpoints then read back — so the client is supplying its own display result as input to server arithmetic.

Consequence: Razorpay is charged one amount and `product_orders.total_amount` records another whenever the intermediate value is between 0 and 1, or negative. `placeOrder()` writes whatever the unfloored formula produces.

**Fix:** one `PricingService::quote(Cart $cart, ?string $couponCode)` used by all three sites, with no client-supplied money fields in the request contract.

### SEC-06 — Cart quantity update: no ownership check

```php
// CartController.php:33-44
public function update(Request $request)
{
    foreach ($request->quantities as $cartId => $qty) {
        Cart::where('id', $cartId)
            ->update(['product_quantity' => $qty]);
    }

    return back()->with('success', 'Cart updated successfully');
}
```

**Worse than a single-line IDOR.** `quantities` is an *associative array of cart-line ids*, so a single request rewrites **an unbounded number of rows in one statement**, and `$cartId` is never bound to the caller.

```php
// Attacker rewrites every cart line in the table
POST /cart-update
quantities[1]=-9999&quantities[2]=-9999&quantities[3]=-9999&…
```

No validation at all: `$qty` is written raw, so a negative quantity is accepted (SEC-06b). Compare `ajaxUpdate()`, which *does* scope by `user_id`/`ip_addresscart` and returns a bare `{success: bool}` — **the two paths disagree on both security and response shape.** Note also that `ajaxUpdate()` takes `product_varient_id` + `quantity` while `update()` takes a `cartId => qty` map, so the two routes are not interchangeable.

### SEC-06b — Cart quantity accepts any value

`$request->quantities` is never validated in `update()`; `$request->quantity` in `ajaxUpdate()` is likewise unvalidated, as is `$request->quantity` in `store()`.

| Input | Result |
|---|---|
| `-9999` | negative quantity in `carts`; subtotals go negative |
| `0` | free line item |
| `999999` | no stock check anywhere in the cart path |
| `"5abc"` | stored into an `int` column — MySQL coerces to `5` in non-strict mode, errors in strict mode |

**Fix:** `UpdateCartRequest` with `'quantity' => ['required','integer','min:1','max:20']`, and scope the update to the authenticated user or the guest's `ip_addresscart`. Note that `carts` has **no `guest_token` column** — the `cookie('guest_token')` read in `index()`/`subtotal()`/`getCartItems()` is never set by any code in this repository, so the fallback to `request()->ip()` always wins. Ownership by IP is weak but it is what exists.

### SEC-07 — Cart removal: IDOR **plus** an unprotected GET

```php
// CartController.php:46-60
public function remove($id) {
    $cart = Cart::findOrFail($id);
    $cart->delete();
    ...
}
// routes/web.php:42
Route::get('/cart-remove/{id}', [CartController::class, 'remove']);
```

| Problem | Consequence |
|---|---|
| No ownership check | Any visitor deletes any cart line by ID |
| **`GET` method** | Laravel's CSRF middleware only inspects state-changing verbs ⇒ **no CSRF protection** |
| GET with side effects | `<img src="/cart-remove/5">` on any page destroys a cart; browser link prefetching can trigger it silently |

```
Fix:  Route::delete('/cart/{id}', …) and scope by user_id/ip_addresscart.
```

---

## 3. HIGH findings

### SEC-08 — Wishlist remove IDOR
```php
// WishlistController.php:45-49
Wishlist::where('id', $id)->where('user_id', Auth::id())->delete();
```
The scoping is correct, **but `routes/web.php:54` has no `auth` middleware** ⇒ `Auth::id()` is `null` for anonymous requests ⇒ the query becomes `WHERE user_id IS NULL AND id = $id` ⇒ **any wishlist row can be deleted by ID.** Fix: move the route into the `auth` group.

### SEC-09 — Wishlist writes orphan rows
`routes/web.php:52-53` (`toggle`, `index`) also lack `auth`. Guests create `user_id = NULL` rows, and `/wishlist` then returns **every** guest's orphan rows to any anonymous visitor.

### SEC-10 — `myaccount` readable without authentication
`routes/web.php:60` — `ProfileController::index()` queries `where('user_id', Auth::id())`. For a guest `Auth::id()` is `null`, so it returns `user_id IS NULL` rows: **orphan wishlist and cart lines belonging to other anonymous users, plus their product names and prices.**

### SEC-11 — `profile.update` has no `auth`
`routes/web.php:32-33` → `ProfileController::update()` calls `auth()->user()` then `$user->id` ⇒ null dereference ⇒ 500. Not an info leak (the request dies), but a trivially-triggerable error and a sign the middleware was forgotten.

### SEC-12 — No rate limiting anywhere

`bootstrap/app.php:13-15` — `withMiddleware()` body is an empty comment. **Zero throttle rules.**

| Endpoint | Risk |
|---|---|
| `/login-store` | Credential stuffing. **No lockout, no CAPTCHA, no delay** |
| `/reset-password-direct` | Automated mass takeover (SEC-03) |
| `/register-store` | Automated accounts; email enumeration via the `unique` 422 |
| `/search` | 4 unindexed `LIKE '%…%'` scans per keystroke ⇒ cheap DoS |
| `/checkout/get-couriers` | **Triggers a live outbound Shiprocket call** ⇒ burns quota, risks rate-limiting the account |
| `/cart/*`, `/wishlist/*` | DB write flood |

```
Fix (bootstrap/app.php):
    RateLimiter::for('login',  fn (Request $r) => Limit::perMinute(5)->by($r->ip()));
    RateLimiter::for('api',   fn (Request $r) => Limit::perMinute(60)->by($r->user()?->id ?: $r->ip()));
  then ->middleware('throttle:login') on the login route, etc.
```

### SEC-13 — User enumeration
| Endpoint | Signal |
|---|---|
| `POST /reset-password-direct` | `{"errors":{"email":["User not found"]}}` — **explicit confirmation an email is registered** |
| `POST /register-store` | `unique:users,email` returns a 422 for registered addresses |

Fix: return an identical generic response either way.

### SEC-14 — `APP_DEBUG=true` on a live database
`.env` → `APP_DEBUG=true`, `APP_ENV=local`, `DB_DATABASE=palvarnan_db` with real customer data.

With debug on, any uncaught error renders **the full stack trace, file paths, environment values, and the SQL query** to the browser. Combined with SEC-04 and SEC-15 this is an active information-disclosure channel.

Fix: `APP_DEBUG=false`, `APP_ENV=production` in any shared environment. `.env` is gitignored — **but `test_apis.php` and `test_services.php` are not, and contain the same secrets.**

### SEC-15 — Internal errors returned to the client
```php
// CheckoutController.php:429
return response()->json(['status' => 'error',
    'message' => 'Order failed to save: ' . $e->getMessage()], 500);
```
`$e->getMessage()` on a `QueryException` includes the **SQL statement and bound values** — customer emails, phone numbers, addresses, Razorpay payment ids. The same pattern appears at `:214` (payment preparation).

Fix: log the detail server-side; return a generic message plus a correlation id.

---

## 4. MEDIUM findings

### SEC-16 — Session fixation: no `regenerate()` on login

`AuthController::login()` calls `Auth::login($user)` with **no** `$request->session()->regenerate()`. The session id issued to an anonymous visitor survives authentication unchanged.

An attacker who can fix a victim's session id — a permissive `SESSION_DOMAIN` (`null` in `.env`, so host-only by default, which limits exposure), or a session id injected via any same-site XSS — retains authenticated access after the victim logs in.

Fix: `$request->session()->regenerate()` immediately before `Auth::login($user)`.

### SEC-17 — Session invalidation only exists in one place

`ProfileController::logout()` is **correct**: `Auth::logout()`, then `invalidate()`, then `regenerateToken()`, preserving `country_id` across the boundary. No other endpoint invalidates a session — there is no "log out everywhere", and `password.reset.direct` calls `Auth::logout()` **without** `invalidate()`, so the old session id and CSRF token survive a password change.

### SEC-18 — PII in logs
`storage/logs/laravel.log` contains, in plaintext:
- Full failed INSERT statements with **emails, phone numbers, door numbers, street addresses**
- `razorpay_payment_id` values
- Shiprocket `order_id`s
- `ShiprocketService.php:41-44` logs the raw authentication-failure body
- **`CheckoutController.php:497` logs the entire Shiprocket order payload at `info` level on every order** — customer name, full address, pincode, phone, and email, written unconditionally

No retention policy, no rotation beyond `LOG_STACK=single`.

Fix: redact PII from log context; drop the payload log or reduce it to ids and dimensions; never log raw exception messages that may contain bound values; set a retention policy.

### SEC-19 — CSRF gaps in the AJAX layer

`app.blade.php` emits a CSRF meta tag, but most `fetch()` calls never send the `X-CSRF-TOKEN` header. Laravel's CSRF middleware inspects every non-GET verb, so those calls return **419** rather than succeeding.

Evidence of awareness: `checkout.blade.php:572-575` handles HTTP 419 — for the address form only. The remaining calls throughout the Blade layer do not.

Interaction with SEC-07: because cart removal sits on a `GET` route, it is the one write path with **no** CSRF exposure at all — a different failure mode (zero protection rather than a broken header).

Fix: a single `apiFetch()` helper that attaches the header and handles 419 uniformly, applied project-wide.

### SEC-20 — `root` database user, empty password
`.env` → `DB_USERNAME=root`, `DB_PASSWORD=` (empty), `DB_HOST=127.0.0.1`.

Full unrestricted MySQL access with no credential. Combined with SEC-01 (where `test_services.php` can execute DDL), any local file-read becomes full database control.

Fix: a dedicated least-privilege MySQL user scoped to `palvarnan_db` with a strong password.

### SEC-21 — `whereRaw` / `FIND_IN_SET` surface
```php
Category::whereRaw("FIND_IN_SET(?, country_id)", [$countryId])->get();
```
The country id comes from the session (`CountryController::store()` looks it up in the DB, so it is an integer) — **not directly injectable**. The residual risk is low, but the pattern is fragile and there is **no prepared-parameter discipline enforced**; a future change to pass raw input would become injectable.

### SEC-22 — No idempotency on order placement
`placeOrder()` has no idempotency key. A double-clicked button, a retry after a timeout, or a duplicated request creates **two orders and charges twice.** No unique constraint on `razorpay_order_id`.

### SEC-23 — Session cookie hardening
`.env` → `SESSION_ENCRYPT=false`, and `SESSION_SECURE_COOKIE` is **unset** (config default `null` ⇒ the `secure` flag is not set). Over plain HTTP the session id travels in cleartext.

Fix: `SESSION_SECURE_COOKIE=true` behind TLS, `SESSION_ENCRYPT=true`, `SESSION_SAME_SITE=lax` (already the default).

---

## 5. LOW findings

| ID | Finding | Fix |
|---|---|---|
| SEC-24 | `state`, `city`, `pincode` are free text in the checkout form and in `UserAddress` | Add `exists:states,name`, `size:6` on `pincode`, a phone regex |
| SEC-25 | `min:6` password rule, no complexity, no confirmation field | `min:8`, `confirmed`, a breach-list check |
| SEC-26 | **Correction:** `users.phone_number` **does** have `users_phone_number_unique` in the schema. The defect is that `registerStore()` has **no `unique` rule**, so a duplicate phone surfaces as a raw SQL 500 rather than a field error | Add `unique:users,phone_number` to the rules |
| SEC-27 | `ProfileController::update()` uses `bcrypt()` while `User` casts `password => hashed` | Use the cast; drop the explicit call |
| SEC-28 | `MustVerifyEmail` is commented out in `User.php:5`; `MAIL_MAILER=log` | Configure a real mailer and enable verification |
| SEC-29 | `.env` sits beside a document root | Serve the app from `public/`; keep secrets outside the web root |

---

## 6. What is done correctly

Worth preserving as the house pattern:

| Location | Pattern |
|---|---|
| `CheckoutController::updateAddress` / `destroyAddress` `:594-633` | `abort_unless($address->user_id === Auth::id(), 403)` — **correct, 2/2** |
| `CartController::ajaxUpdate` / `ajaxRemove` `:92-113` | Correctly scoped by `user_id` or `ip_addresscart` |
| `WishlistController::remove()` `:45-49` | `where('id',$id)->where('user_id', Auth::id())` — scoped; `DELETE` verb; only the missing `auth` middleware makes it exploitable (SEC-08) |
| `CheckoutController::thankyou()` `:540-548` | `where('order_number',…)->where('user_id', Auth::id())->firstOrFail()` — **correct**, returns 404 not 403 |
| `CheckoutController::placeOrder()` `:252-268` | Razorpay signature verification via the official SDK helper |
| `CheckoutController::validateAddress()` `:672-686` | 10 typed rules with length caps — the best-validated form in the app |
| `ProfileController::update()` password path | `current_password` + `confirmed` + `Hash::check` before writing |
| `ProfileController::logout()` | `invalidate()` + `regenerateToken()`, `country_id` preserved |
| `AuthController::login()` error | Single generic message — does not disclose account existence |
| `CheckoutController::storeAddress()` error path | Generic message to the client, real exception logged |
| `CheckoutController::createOrder` call site `:500-522` | Checks `order_id` **and** `shipment_id` present; uses `shipment_id` for `assignAWB` |
| Laravel `Http` client | TLS verification **on** by default |
| `User::$hidden` | `password`, `remember_token` hidden |
| `config/session.php` | `same_site => 'lax'` |

**The address book (`abort_unless`) is the pattern every write endpoint should copy.**

Corrections to earlier drafts of this document: the thank-you page **is** ownership-scoped, the address update/delete routes **are** ownership-checked, and `shiprocket_shipment_id` **is** assigned from the response's `shipment_id`.

---

## 7. Remediation order

### Immediately (do before anything else)
1. **Delete `test_apis.php` and `test_services.php`.**
2. **Rotate Razorpay key+secret, Shiprocket email+password, and the MySQL password.**
3. **Set `APP_DEBUG=false`.**
4. Create a least-privilege MySQL user; stop using `root` with no password.

### This week
5. Delete `resetPassword()`; restore the token flow (SEC-03).
6. Scope `cart-update` and `cart-remove` to the owner; validate quantities (SEC-06, 06b, 07).
7. Move `myaccount`, `profile.update`, `wishlist.toggle`, `wishlist` into the `auth` group (SEC-08, 09, 10, 11).
8. Add the five missing columns via guarded migrations — `billing_state`, `shipping_state`, `courier_name`, `discount` on `product_orders` and `product_image` on `product_order_items` (SEC-04) — plus a refund-on-failure path.
9. Stop reading `shipping_charge` and `coupon_discount` from the request; unify the two total formulas (SEC-05, 05b).
10. Add rate limiting and remove error detail from responses (SEC-12, 15).

### This month
11. `session()->regenerate()` on login; `invalidate()` on password reset (SEC-16, 17).
12. Project-wide `apiFetch()` CSRF helper (SEC-19).
13. Idempotency keys on order placement plus a unique index on `razorpay_order_id` (SEC-22).
14. Log redaction and retention; drop the Shiprocket payload log (SEC-18).
15. Cookie hardening (SEC-23).
16. Password policy, email verification, `phone_number` uniqueness rule (SEC-25, 26, 28).

---

## 8. Related documents

- [04-feature-inventory.md](04-feature-inventory.md) — affected features (F-06, F-23, F-24, F-29, F-36)
- [05-user-flows.md](05-user-flows.md) — exploitation paths in context
- [08-business-logic.md](08-business-logic.md) — BL-CHK pricing rules
- [14-error-handling.md](14-error-handling.md) — SEC-15 in detail
- [22-database-integrity.md](22-database-integrity.md) — SEC-04 migrations
- [29-remediation-plan.md](29-remediation-plan.md) — sequenced fix plan