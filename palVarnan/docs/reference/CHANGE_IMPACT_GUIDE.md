# Change Impact Guide — `palVarnan`

> Practical "if you change X, also check Y" map for a developer working on the storefront. Each
> entry cites code/schema evidence and flags cross-app/shared-DB coupling. This is designed to
> answer: *"If I touch this, what else can I break?"*
>
> Evidence vocabulary: `VERIFIED_FROM_SOURCE`, `VERIFIED_FROM_LIVE_SCHEMA`, `INFERRED`,
> `UNVERIFIED`, `NOT_AVAILABLE`, `CONFLICTING_EVIDENCE`.

---

## 1. Pricing / GST / money

| If you change | Also check | Why | Evidence |
|---|---|---|---|
| `product_varient.offer_price` or `product_varient.product_gst` | `AppServiceProvider.php:48-50` (cartSubtotal), `CartController.php:27,141-142`, `CheckoutController.php:60,184,279,367,447`, `ProfileController.php:96` | Pricing is recomputed in at least 7 places with 3 shapes (missing guard at `CartController.php:27`) | `VERIFIED_FROM_SOURCE` |
| Shipping logic or state rates | `CheckoutController.php:191,286` (`shipping_charge`), `getShippingCharge()` `:638-654` (dead), `State` model/table, `config/services.php` pickup | Client supplies shipping; server recomputes but also trusts the request field | `VERIFIED_FROM_SOURCE` |
| Coupon logic | `Coupon` model, `CheckoutController.php:6,192,287` (reads `coupon_discount` from request, never queries table), `coupons` / `newcoupons` tables | Coupon engine absent — discount is unvalidated | `VERIFIED_FROM_SOURCE` + `VERIFIED_FROM_LIVE_SCHEMA` |
| Money formatting/display | Hard-coded `₹` appears across Blade; also `preparePayment()` sets `currency: 'INR'` (`CheckoutController.php:204`) | Currency is hard-coded (no multi-currency) | `VERIFIED_FROM_SOURCE` |
| The price-sort subquery | `ProductController.php:84,87` — correct table name (`product_varient` vs `product_varients`) | Currently references a non-existent table → SQL error | `VERIFIED_FROM_SOURCE` + `VERIFIED_FROM_LIVE_SCHEMA` |

---

## 2. Cart schema and behaviour

| If you change | Also check | Why | Evidence |
|---|---|---|---|
| `carts` table columns (e.g. `pre_order`, `ip_addresscart`) | `Cart` model `$fillable` (`Cart.php:9-17`), `AppServiceProvider.php:33-76`, `CartController.php:1-147`, `CheckoutController.php:688-700`, `ProfileController.php:80-83` | 6 different identity paths; `carts.pre_order` is in `$fillable` but never written | `VERIFIED_FROM_SOURCE` |
| Guest identity | `AppServiceProvider.php:56,59`, `CartController.php:18-19,72,102,122,137`, `CheckoutController.php:695-696` | Uses `cookie('guest_token') ?? ip()` on reads; writes use `ip()` only; cookie never set | `VERIFIED_FROM_SOURCE` |
| Cart ownership / scoping | `CartController.php:33-44` (no scope), `CartController.php:46-62` (no scope, GET), `CartController.php:95-127` (scoped by user/ip) | Critical IDOR in update/remove paths | `VERIFIED_FROM_SOURCE` |
| Stock columns (`productstocks`, `product_varient.product_qty`) | `CheckoutController.php:403-415` (no `lockForUpdate()`), oversell flooring at `:405,413` | Risk of oversell on concurrent orders | `VERIFIED_FROM_SOURCE` |

---

## 3. Orders, order tables, and fulfilment

| If you change | Also check | Why | Evidence |
|---|---|---|---|
| `product_orders` columns | `ProductOrder` `$fillable` (includes `created_at`,`updated_at`,`pickup_scheduled`), `CheckoutController.php:295-327`, `ProductOrderItem`, `ProductOrderUserAddress`, `ProductSlot`, `ProductTracking`, `ProductTransactionLog` | Writes to 6 tables in one transaction (`CheckoutController.php:292-421`) | `VERIFIED_FROM_SOURCE` |
| `product_order_items` schema/columns | `CheckoutController.php:373-385` (writes `product_image`, `product_variant_id`), `ProductOrderItem::$fillable`, `ProductOrder::items()` relation | Column names differ across tables (`product_variant_id` in items vs `product_varient_id` elsewhere) | `VERIFIED_FROM_SOURCE` |
| Order number / id | `CheckoutController.php:290,298` (`order_number` and `order_id` both set), child tables join on `order_id` (string) vs `id` (int) | Two identifiers; slots/tracking/address join on `order_id` (ORD-…), items on `order->id` | `VERIFIED_FROM_SOURCE` |
| Razorpay payment fields | `CheckoutController.php:196-197,258,324-326`, `ProductOrder::$fillable` includes `razorpay_*` | Signature verification only; no amount re-verification; payment status set inside the transaction | `VERIFIED_FROM_SOURCE` |
| Shiprocket payload | `CheckoutController.php:432-527`, `ShiprocketService.php` (methods, no timeout/retry), `product_orders.shiprocket_*`, `awb_code`, `courier_name` | External call after commit; failures swallowed (`:525-527`); `generatePickup()` has no caller | `VERIFIED_FROM_SOURCE` |

---

## 4. Products, variants, product schema

| If you change | Also check | Why | Evidence |
|---|---|---|---|
| `ProductVarient` table/name | `ProductVarient::$table = 'product_varient'` (singular), `ProductController.php:84,87` references `product_varients` (plural) → error | SQL error on price sort | `VERIFIED_FROM_SOURCE` + `VERIFIED_FROM_LIVE_SCHEMA` |
| Variant identifier columns | `Cart::$fillable` uses `product_varient_id`, `ProductOrderItem::$fillable` uses `product_variant_id` (missing 'r'), `ProductStock` uses `pro_ver_id`, `Wishlist` uses `product_varient_id` | Four different spellings across tables; increases breakage surface | `VERIFIED_FROM_SOURCE` + `VERIFIED_FROM_LIVE_SCHEMA` |
| Product slug / images | `SearchController.php:23,36,54,72,83` builds image URLs using `env('MAIN_URL')` and URLs from slugs; brand/category slugs use different columns (`brand_slug` vs `slug`) | Breaks under `config:cache` and produces bad search URLs | `VERIFIED_FROM_SOURCE` |
| Category/brand country filtering | `HomeController.php:24`, `ProductController.php:24,32,34` use `FIND_IN_SET(country_id, ...)` (unindexable); types differ (`products.country_id` int vs CSV on categories/brands) | Full scans per listing; filter logic differs by model | `VERIFIED_FROM_SOURCE` + `VERIFIED_FROM_LIVE_SCHEMA` |
| Variant weight/dimensions | `CheckoutController.php:104-110,453-463` send weight/dims to Shiprocket (fallbacks 0.5/10) but `product_varient` may lack those columns | Shiprocket parcels defaulted; affects courier quotes | `VERIFIED_FROM_SOURCE` + `INFERRED` |

---

## 5. Auth, password flows, and sessions

| If you change | Also check | Why | Evidence |
|---|---|---|---|
| `AuthController::resetPassword()` | `AuthController.php:100-126`, route `POST /reset-password-direct` (`routes/web.php:38`), no token route | **No token required** → account takeover. Must switch to broker-based reset | `VERIFIED_FROM_SOURCE` |
| `AuthController::login()` | `AuthController.php:46-72` — uses `Auth::login($user)` with **no** `regenerate()` | Session fixation risk | `VERIFIED_FROM_SOURCE` |
| `AuthController::registerStore()` | `User` model, `users` unique constraints (email/phone), country from session | `registerStore()` has no unique rule for phone in validation | `VERIFIED_FROM_SOURCE` |
| `ProfileController::update()` | `ProfileController.php:32-46` updates password with `bcrypt($request->password)` but `User` casts `password` as `hashed` | Double-hashing risk; inconsistent with other code paths | `VERIFIED_FROM_SOURCE` |
| Session config | `SESSION_DRIVER=file` live (`.env`), `config/session.php`, logout calls `invalidate()` + `regenerateToken()` (`ProfileController.php:58-60`) | No shared sessions across nodes; `config:cache` interacts poorly with env access elsewhere | `VERIFIED_FROM_SOURCE` |

---

## 6. The shared DB — cross-app coupling

| If you change | Also check | Why | Evidence |
|---|---|---|---|
| Any domain table schema | `palVarnanDashboard` (sibling) migrations and schema; `palvarnan_db (2).sql` dump; 0 domain migrations here | **Schema is owned by the other app.** This storefront has no domain migrations; hand-applied columns exist | `VERIFIED_FROM_SOURCE` + `VERIFIED_FROM_LIVE_SCHEMA` |
| Writing to `product_orders`/items/slots/tracking/logs/address | The dashboard's read path — e.g. dashboard expects `order_id` string on slots/tracking/address vs `id` int on items | Cross-app identifier semantics differ | `VERIFIED_FROM_SOURCE` |
| `product_orders.status` value | Dashboard sync excludes `'Order Placed'` per [`../20-known-issues.md`](../20-known-issues.md) | Status vocabulary mismatch | `VERIFIED_FROM_SOURCE` |
| Deleting/dropping tables or running `migrate:fresh` | Never run destructive commands against the shared `palvarnan_db` | **Production data loss.** Use a separate `palvarnan_test` DB for tests | `INFERRED` |
| Test DB setup | [`./TESTING_GUIDE.md`](./TESTING_GUIDE.md) §5 — use MySQL test DB (not SQLite in-memory) | Schema uses MySQL-specific DDL | `VERIFIED_FROM_SOURCE` |

---

## 7. Integrations (Razorpay / Shiprocket)

| If you change | Also check | Why | Evidence |
|---|---|---|---|
| `config/services.php` | `ShiprocketService.php:15` — no `base_url` key exists, so `SHIPROCKET_BASE_URL` from `.env` is ignored | Sandbox switching silently has no effect | `VERIFIED_FROM_SOURCE` |
| Shiprocket base URL / credentials | `ShiprocketService.php:54-55` uses `Http::withToken()` with **no `->timeout()`/`->retry()`**; `getToken()` caches 8h | 30s hangs; no 401 re-auth | `VERIFIED_FROM_SOURCE` |
| `placeOrder()` Shiprocket flow | `CheckoutController.php:525-527` — catch logs only, returns success | Failures invisible to customer/staff | `VERIFIED_FROM_SOURCE` |
| Pickup | `ShiprocketService::generatePickup()` has **no caller** | Orders never collected | `VERIFIED_FROM_SOURCE` |
| Razorpay SDK setup | `CheckoutController.php:26-31` does inline `require_once` if class missing; `composer.lock` now includes it | `vendor/razorpay` could be removed by `composer update` in older states | `VERIFIED_FROM_SOURCE` |

---

## 8. Routes, middleware, authz

| If you change | Also check | Why | Evidence |
|---|---|---|---|
| Route methods/middleware | `routes/web.php`: cart removal is `GET` (`:50`); many routes unauthenticated (`/myaccount` `:68`, wishlist `:60-61`, profile update `:40-41`); checkout group is `auth` (`:71-83`) | CSRF/prefetch risk for GET deletes; access control gaps | `VERIFIED_FROM_SOURCE` |
| Global composer | `AppServiceProvider.php:33-76` runs on **every** view | A route/view change can suddenly hit more pages and amplify the global query cost | `VERIFIED_FROM_SOURCE` |
| `bootstrap/app.php` | Only `trustProxies('*')`; no rate limiting, no exception rendering for JSON; no `Model::preventLazyLoading()` | No throttles on auth/quote endpoints | `VERIFIED_FROM_SOURCE` |

---

## 9. Views / frontend / assets

| If you change | Also check | Why | Evidence |
|---|---|---|---|
| Blade templates referencing cart | `AppServiceProvider.php` shares `cartCount/wishlistCount/cartSubtotal` to `'*'` but its `$cartItems` is not passed; views may iterate the same relations | Eager loads in composer don't guarantee no lazy loads in views | `INFERRED` |
| Search form | `SearchController.php:23` uses `env('MAIN_URL')`; header fires AJAX on every keystroke; 4 `LIKE '%...%'` queries per request | Config cache break + performance | `VERIFIED_FROM_SOURCE` |
| Vite/Tailwind | `package.json` has Vite; `@vite` only in `welcome.blade.php:15` (unreachable); live views use `public/assets/` | Pipeline is half-configured | `VERIFIED_FROM_SOURCE` |
| JavaScript | 32 inline `fetch()` calls; only the address form handles HTTP 419; no global CSRF wrapper | Hard to maintain and test | [`../15-frontend-and-assets.md`](../15-frontend-and-assets.md) (referenced) |

---

## 10. Environment/config cache safety checklist

| When deploying | Check | Notes |
|---|---|---|
| Before `php artisan config:cache` | `grep -rn "env(" app/` → **0** matches? | Currently has `SearchController.php:23`. Fix to `config()` first. |
| Before `php artisan migrate:fresh` | Is the target DB `palvarnan_test`? Never run against `palvarnan_db` | Shared DB with dashboard. |
| Before committing `.env.example` changes | Ensure commerce keys are added (`MAIN_URL`, `RAZORPAY_*`, `SHIPROCKET_*`, `DB_*`) | 13 missing currently. |
| When enabling queues | Add job classes + worker + `QUEUE_CONNECTION=database\|redis` | Current is `sync` with no jobs. |

---

## 11. Related documents

- [`./PERFORMANCE_AND_RELIABILITY.md`](./PERFORMANCE_AND_RELIABILITY.md) — per-request paths and I/O
- [`./TESTING_GUIDE.md`](./TESTING_GUIDE.md) — tests that prevent regressions here
- [`./DEPLOYMENT_AND_CONFIGURATION.md`](./DEPLOYMENT_AND_CONFIGURATION.md) — env/config/deploy traps
- [`./KNOWN_ISSUES_AND_TECHNICAL_DEBT.md`](./KNOWN_ISSUES_AND_TECHNICAL_DEBT.md) — KI-B/TD-B registry
- [`../08-business-logic.md`](../08-business-logic.md) — the pricing/cart/checkout rules
- [`../22-database-integrity.md`](../22-database-integrity.md) — schema bootstrap / missing columns
- [`../13-security-audit.md`](../13-security-audit.md) — authz and IDOR details
- [`../25-forms-and-validation.md`](../25-forms-and-validation.md) — validation rules per form
