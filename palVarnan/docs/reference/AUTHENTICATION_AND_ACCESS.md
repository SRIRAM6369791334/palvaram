# AUTHENTICATION_AND_ACCESS.md

**Purpose:** Session auth, register/login/logout, password reset defects, authorization gaps.

---

## 1. Auth implementation

- Driver: Laravel session auth (cookies). `config/auth.php` defaults. `VERIFIED_FROM_SOURCE`.
- Login: [`AuthController@login`](../../app/Http/Controllers/AuthController.php#L46-L72) looks up by `email` **or** `phone_number`; verifies with `Hash::check`; calls `Auth::login($user)`. Returns JSON.
- Register: [`AuthController@registerStore`](../../app/Http/Controllers/AuthController.php#L13-L43) validates min password 6; creates user with `country_id` from session.
- Logout: [`ProfileController@logout`](../../app/Http/Controllers/ProfileController.php#L51-L65) logs out, invalidates session, regenerates token; preserves `country_id`.

---

## 2. Password reset (defects)

- **Forgot password:** [`AuthController@forgotPassword`](../../app/Http/Controllers/AuthController.php#L75-L97) uses `Password::sendResetLink` (Laravel password broker). Returns JSON; mailer `log` in `.env` so link goes to `storage/logs/laravel.log`. `WORKING` in flow but mail not delivered.
- **Reset password (direct):** [`AuthController@resetPassword`](../../app/Http/Controllers/AuthController.php#L100-L126) — **CRITICAL SECURITY RISK**
  - Validates only `email` + `password` (min 6)
  - Finds user by `email`
  - Sets new password with `Hash::make`, saves
  - Logs out
  - **NO token**, **NO signed URL**, **NO expiry**
  - Public/unauthenticated route [`POST /reset-password-direct`](../../routes/web.php#L38) named `password.reset.direct`
  - **Anyone knowing a valid email can take over the account.**

`VERIFIED_FROM_SOURCE`. `CONFLICTING_EVIDENCE` vs any claim it requires token.

---

## 3. Authorization gaps

- Middleware: only `auth` applied to wishlist/check routes and full checkout group ([`routes/web.php:63-83`](../../routes/web.php#L63-L83)). Many routes have **no `auth` middleware** (cart, wishlist toggle/index/remove, search, product, etc.) and rely on controller checks.
- Cart IDOR: [`CartController@update`](../../app/Http/Controllers/CartController.php#L33-L44) updates by cart `id` with no ownership check. [`CartController@remove`](../../app/Http/Controllers/CartController.php#L46-L62) deletes by `id` with no ownership check; exposed as **GET** route.
- Wishlist remove: **fixed** — now scopes by `user_id` ([`WishlistController.php:50-54`](../../app/Http/Controllers/WishlistController.php#L50-L54)).
- Address CRUD: update/destroy enforce ownership ([`CheckoutController.php:596,629`](../../app/Http/Controllers/CheckoutController.php#L596-L629)).
- Thank-you: scoped by `order_number` and `user_id` ([`CheckoutController.php:544`](../../app/Http/Controllers/CheckoutController.php#L544)).
- MyAccount redirects if not auth ([`ProfileController.php:69-71`](../../app/Http/Controllers/ProfileController.php#L69-L71)) but route not behind `auth` middleware.

No Policies/Gates, no Form Requests. `VERIFIED_FROM_SOURCE`.

---

## 4. Related

- [`../13-security-audit.md`](../13-security-audit.md)
- [`SECURITY_POSTURE.md`](./SECURITY_POSTURE.md) (to be created by siblings; referenced conceptually)
- [`ROUTES_AND_ENDPOINTS.md`](./ROUTES_AND_ENDPOINTS.md)