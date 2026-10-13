# 20 — Known Issues

The consolidated bug register. Every entry is verified against source, the `palvarnan_db (2).sql` DDL, or `storage/logs/laravel.log`.

Severity: **S1 Critical** (money loss, data loss, or account compromise) · **S2 High** · **S3 Medium** · **S4 Low**

Corrections applied during this pass are called out explicitly where an earlier draft was wrong.

---

## 1. Register

### S1 — Critical

| ID | Issue | Location | Evidence |
|---|---|---|---|
| BUG-01 | Password reset needs no token ⇒ account takeover | `AuthController::resetPassword()` | Source: no `token` reference anywhere |
| BUG-02 | Live Razorpay and Shiprocket credentials hard-coded in web-reachable scripts | `test_apis.php`, `test_services.php` | Untracked files in the document root |
| BUG-03 | TLS verification disabled on live payment calls | both test scripts | `CURLOPT_SSL_VERIFYPEER => false` |
| BUG-04 | **Checkout persists nothing — payment captured, order lost** | `CheckoutController.php:292-430` | Log: `Unknown column 'billing_state'`, then `'product_image'` |
| BUG-05 | Client controls the shipping charge and discount | `CheckoutController.php:191-193, 286-288` | Source: `floatval($request->… ?? 0)`, no validation, no coupon lookup |
| BUG-06 | Cart quantity update has no ownership check | `CartController.php:35-44` | `Cart::where('id', $cartId)->update(…)` |
| BUG-07 | Cart delete has no ownership check **and is a `GET`** | `CartController.php:46-60`, `routes/web.php:42` | `Cart::findOrFail($id)` |

### S2 — High

| ID | Issue | Location | Evidence |
|---|---|---|---|
| BUG-08 | Razorpay missing from `composer.lock` despite being in `composer.json` | `composer.json:10` | 0 matches in `composer.lock`, `installed.json`, `autoload_psr4.php`; the `vendor/razorpay` directory exists unmanaged |
| BUG-09 | `myaccount` reachable unauthenticated | `routes/web.php:60` | No `auth` middleware; guest sees `user_id IS NULL` rows |
| BUG-10 | `profile.update` has no `auth` ⇒ null dereference 500 | `routes/web.php:32`, `ProfileController.php:16-22` | `auth()->user()` is null before `$user->id` |
| BUG-11 | Wishlist `toggle`/`index` write and read `user_id = NULL` rows | `routes/web.php:52-53` | No `auth` middleware |
| BUG-12 | Wishlist `remove` deletes `user_id IS NULL` rows when called anonymously | `WishlistController.php:45-49` | **Correction:** the query *is* scoped; with `Auth::id()` null it becomes `WHERE user_id IS NULL AND id = ?` |
| BUG-13 | No rate limiting on any of the 38 routes | `bootstrap/app.php:13-15` | `withMiddleware()` body is an empty comment |
| BUG-14 | User enumeration on reset and register | `AuthController::resetPassword()`, `registerStore()` | Explicit `'User not found'`; `unique` 422 |
| BUG-15 | SQL and gateway error text leaked to clients | `CheckoutController.php:429, 218` | `'Order failed to save: ' . $e->getMessage()` |
| BUG-16 | `APP_DEBUG=true` against a database with customer data | `.env` | Env read |
| BUG-17 | MySQL `root` with an empty password | `.env` | `DB_USERNAME=root`, `DB_PASSWORD=` |
| BUG-18 | Pickup never scheduled — orders exist but are never collected | `ShiprocketService::generatePickup()` `:119` | No caller |
| BUG-19 | The status the storefront writes is excluded by the dashboard's sync | `CheckoutController.php:322` vs `SyncShiprocketStatus` | `'Order Placed'` is not in the dashboard's status list |
| BUG-20 | Shiprocket failures swallowed; the customer still sees success | `CheckoutController.php:525-527` | `catch (\Exception)` logs only |
| BUG-21 | No idempotency on order placement | `CheckoutController::placeOrder()` | No key; `razorpay_order_id` has no unique index |
| BUG-22 | No refund path if the order fails after payment | whole codebase | No refund code anywhere |
| BUG-23 | Session not regenerated on login | `AuthController::login()` | `Auth::login($user)` with no `regenerate()` |
| BUG-24 | Five columns are written but do not exist in the schema | `product_orders`, `product_order_items` | `billing_state`, `shipping_state`, `courier_name`, `discount`, `product_image` |

### S3 — Medium

| ID | Issue | Location | Evidence |
|---|---|---|---|
| BUG-25 | Variant search can never match | `SearchController.php:38` | `product_varient.varient` is `int(11)` |
| BUG-26 | Brand search URLs resolve to `/brand/` | `SearchController.php` | `brands` has `brand_slug`, not `slug` |
| BUG-27 | Category search URLs resolve to `/category/` | `SearchController.php` | `categories` has no slug column at all |
| BUG-28 | `env('MAIN_URL')` breaks under `config:cache` | `SearchController.php:29` | `env()` outside config |
| BUG-29 | `SubCategory::category()` FK does not exist | `SubCategory.php` | Log: `Unknown column 'sub_categories.category_id'` (3×) |
| BUG-30 | `ProductVarient::category()` infers `category_id`; the column is `categoryid` | `ProductVarient.php` | Source |
| BUG-31 | `UserAddress::country()` uses `area_id` as the country FK | `UserAddress.php` | Source |
| BUG-32 | Soft-deleted products leave dangling cart and wishlist rows | `Product` uses `SoftDeletes`; nothing else does | Log: `Attempt to read property "offer_price" on null` (6×), `"slug" on null` (3×) |
| BUG-33 | Null `unit` relation crashes the account page | `myaccount.blade.php` | Log: `Attempt to read property "name" on null` (2×) |
| BUG-34 | `products.product_price` read at 7 sites; the column does not exist | `CheckoutController.php:60,184,279,367,447`, `ProfileController.php:90`, `checkout.blade.php:431` | `?? 0` chain falls through to zero |
| BUG-35 | `CartController::index()` computes the subtotal with no null guard | `CartController.php:26-28` | `$item->variant->offer_price * …`, unlike `subtotal()` which uses `?? 0` |
| BUG-36 | Oversell silently floors stock at zero | `CheckoutController.php:405, 413` | `max(…, 0)`, no backorder flag |
| BUG-37 | No `lockForUpdate()` on stock | `CheckoutController.php:403, 411` | Source |
| BUG-38 | **Variant weight and dimension columns do not exist** | `product_varient` DDL vs `CheckoutController.php:453-463, 104` | Every parcel ships as 0.5 kg at 10×10×10 cm |
| BUG-39 | Two different weight heuristics for the same cart | `CheckoutController.php:110` (`0.1` floor) vs `:460` (`0.5` floor), plus a `/1000` branch at `:107-109` | Log confirms `weight: 1.0` sent for a two-item order |
| BUG-40 | No HTTP timeout or retry on any Shiprocket call | `ShiprocketService.php` | Log: `Maximum execution time of 60 seconds exceeded` |
| BUG-41 | No 401 re-authentication path | `ShiprocketService::client()` `:52-55` | Source |
| BUG-42 | `SHIPROCKET_BASE_URL` in `.env` is never read | `config/services.php` | No `base_url` key; the service hard-codes a default |
| BUG-43 | Guest identity differs across 6 code sites | `CartController::index/subtotal/store/ajaxUpdate/ajaxRemove`, `AppServiceProvider` | Reads use `cookie('guest_token') ?? ip()`; writes use `ip()` only — and the cookie is never set |
| BUG-44 | No guest → user cart or wishlist merge on login | `AuthController::login()` | Source |
| BUG-45 | Cart quantity on an existing line is hard-coded to `+1` | `CartController::store()` | `$existing->increment('product_quantity', 1)`; response `quantity` also hard-coded to `1` |
| BUG-46 | `carts.pre_order` is stripped by `$fillable` | `Cart.php` vs `CartController.php` | Column stays `NULL` forever |
| BUG-47 | `ajaxUpdate`/`ajaxRemove` key on `product_varient_id`, not the cart line id | `CartController.php:92-113` | Two variants of one product share a key |
| BUG-48 | Order number from `uniqid()` — **Correction:** `product_orders_order_number_unique` exists, so collisions fail loudly rather than silently | `CheckoutController.php:290` | DDL confirms the unique index |
| BUG-49 | Two server-side total formulas with different floors, plus a third in Blade | `:193` (`max(…,1)`), `:288` (no floor), `checkout.blade.php:626` (`max(…,0)`) | Source |
| BUG-50 | `coupon_discount` is an unchecked client number; the `coupons` table is never queried | `:192`, `:287`; `Coupon` imported at `:6` | Source |
| BUG-51 | `courier_name` is written from an unvalidated request field | `:321` | No rule covers `selected_courier_name` |
| BUG-52 | `payment_status = 'Paid'` is set inside the transaction that rolls back | `:324` | Source |
| BUG-53 | The applied discount is never persisted | `ProductOrder::$fillable` has `discount`; no code writes it | The order total cannot be reconciled |
| BUG-54 | `country-selection` never renders `session('error')` | `CountryController::store()` | Grep: no `session('error')` in the view |
| BUG-55 | 85 dead `shop.html` links in the header | `layouts/header.blade.php` | Verified count |
| BUG-56 | ~700 lines of template demo on the homepage | `pages/home.blade.php` (1 991 lines) | 50 Unsplash images, 29 `wowtheme7` links |
| BUG-57 | US demo contacts in the footer | `layouts/footer.blade.php` | Vendor Gmail addresses and a placeholder phone number |
| BUG-58 | jQuery loaded twice — 3.7.1 local plus 3.6.0 CDN | `layouts/app.blade.php` | Two `<script>` tags |
| BUG-59 | No shared fetch helper; the CSRF header is hand-rolled per call | Blade layer | 32 `fetch()` calls, 3 files send no CSRF token at all |
| BUG-60 | Only 1 of 32 calls handles HTTP 419 | `checkout.blade.php:572` | Verified |
| BUG-61 | Cart and wishlist counts run on **every** view | `AppServiceProvider.php` `View::composer('*')` | Two queries + `->get()` per render |
| BUG-62 | No index on `carts.user_id`, `carts.ip_addresscart`, or `wishlists.user_id` | DDL | Only a primary key on each |
| BUG-63 | `FIND_IN_SET(country_id)` is unindexable | `HomeController`, `ProductController` | Function applied to a column |
| BUG-64 | Search fires on every keystroke, four `LIKE '%…%'` per call | `layouts/header.blade.php` | Source |
| BUG-65 | App timezone is UTC; the business operates in IST | `config/app.php` | `'timezone' => 'UTC'` |
| BUG-66 | `MAIL_MAILER=log` ⇒ password reset is non-functional | `.env` | Log-only transport |
| BUG-67 | `SESSION_SECURE_COOKIE` unset; `SESSION_ENCRYPT=false` | `config/session.php`, `.env` | No `secure` flag |
| BUG-68 | Duplicate relationship pairs | `Product`, `ProductOrder` | `variants`/`productvari`, `user`/`customer` |
| BUG-69 | `created_at`/`updated_at` in `$fillable` on the two audit-critical tables | `ProductOrder`, `ProductSlot` | Timestamp forging |
| BUG-70 | `pickup_scheduled` is in `$fillable` but never written | `ProductOrder` | Source |
| BUG-71 | `product_varient.created_at` has `ON UPDATE current_timestamp()` | DDL | Creation time silently changes |
| BUG-72 | Four spellings of the variant key across six tables | `carts`, `wishlists`, `product_slots`, `product_order_items`, `productstocks` | `product_varient_id` / `product_variant_id` / `pro_ver_id` |
| BUG-73 | `orders` carry two identifiers — `id` and `order_id` — and no FK enforces either | `product_orders` and 4 child tables | Items use `order->id`, slots/logs/tracking/address use `ORD-…` |
| BUG-74 | No payment reconciliation is possible | `product_transaction_logs` | Records amounts but **no** `razorpay_payment_id` |
| BUG-75 | Money columns are `int` or `bigint`, and no model casts to `decimal:2` | `product_varient.offer_price`, `mrp_price`; `products.product_mrp_price` | Float arithmetic throughout |
| BUG-76 | `product_gst` stores category codes (`1-as5`, `2-as12`, `3-as18`, `4-as28`) treated as percentages | `product_varient.product_gst` vs `:188`, `:283` | GST is computed as `gst / 100` on a code value |
| BUG-77 | No `$hidden` on any order or address model | 20 of 24 models | PII in any `->toArray()` |
| BUG-78 | The full Shiprocket payload is logged at info level on every order | `CheckoutController.php:497` | Log confirms name, address, pincode, phone, email |
| BUG-79 | Cart and wishlist `pre_order` / flash-sale / low-stock features have no storefront path | `product_varient`, `carts` | Columns exist, no UI or logic |
| BUG-80 | The whole Contact feature is missing while four links point at it | header and footer `contact.html` | No controller, route, or view |

### S4 — Low

| ID | Issue | Location |
|---|---|---|
| BUG-81 | `GET /product` renders the detail template with no data | `routes/web.php:19` |
| BUG-82 | `welcome.blade.php` is unreachable | `resources/views/` |
| BUG-83 | Vite/Tailwind pipeline entirely unused | no `@vite` anywhere |
| BUG-84 | `node_modules` and `public/build` missing | root |
| BUG-85 | `composer.lock` stale against `composer.json` | lock `content-hash` |
| BUG-86 | `favicon.ico` is 0 bytes | `public/favicon.ico` |
| BUG-87 | `boostrap.bundle.min.js` misspelled | `public/assets/js/`, `app.blade.php` |
| BUG-88 | No per-page SEO metadata | one static `<title>` |
| BUG-89 | Password policy `min:6`, no confirmation on register or reset | `AuthController` |
| BUG-90 | `bcrypt()` used where the model declares a `hashed` cast | `ProfileController::update()` |
| BUG-91 | No email verification; `MustVerifyEmail` commented out | `User.php` |
| BUG-92 | **Correction:** `users.phone_number` **is** unique. The defect is that `registerStore()` has no `unique` rule, so a duplicate surfaces as a raw SQL 500 | `AuthController::registerStore()` |
| BUG-93 | `state` and `pincode` are free text | `placeOrder()` rules, `validateAddress()` |
| BUG-94 | `getCitiesByState()` and `getShippingCharge()` are dead code | `CheckoutController.php:638-670` |
| BUG-95 | No custom 404/500 pages | `bootstrap/app.php:16-17` |
| BUG-96 | No scheduled tasks; the `sessions` table grows unbounded | `routes/console.php` is stock |
| BUG-97 | No CI | no `.github/workflows/` |
| BUG-98 | `composer run setup` implies a migration that does not exist for 93 tables | `composer.json` |
| BUG-99 | `palVarnan1/` duplicate app directory | sibling folder, untracked |
| BUG-100 | Dashboard commits `storage/framework/sessions` and compiled views | sibling repo |
| BUG-101 | `₹` and `'INR'` hard-coded despite a country selector | Blade, `preparePayment()` |
| BUG-102 | `robots.txt` is stock and disallows nothing | `public/robots.txt` |
| BUG-103 | 9.0 MB of theme assets committed (472 files) plus a 13.3 MB extracted template directory | `public/assets/`, repo root |
| BUG-104 | The entire checkout feature is uncommitted — `app/Services/`, 10 models, `thankyou.blade.php`, both `test_*.php` | `git status` |
| BUG-105 | Mojibake in source comments | `WishlistController.php`, `myaccount.blade.php` |

---

## 2. By severity

| Severity | Count | IDs |
|---|---|---|
| **S1 Critical** | 7 | BUG-01…07 |
| **S2 High** | 17 | BUG-08…24 |
| **S3 Medium** | 56 | BUG-25…80 |
| **S4 Low** | 26 | BUG-81…105 |
| **Total** | **106** | |

S3 is large because it includes template debt, frontend duplication, and schema drift, not just behavioural defects.

---

## 3. By area

| Area | IDs | Count |
|---|---|---|
| Checkout / orders | 04, 05, 18, 19, 20, 21, 22, 24, 36, 37, 38, 39, 48, 49, 50, 51, 52, 53, 66, 74, 76, 93 | 22 |
| Auth / session | 01, 10, 11, 12, 13, 14, 23, 66, 67, 89, 91, 92 | 12 |
| Cart / wishlist | 06, 07, 35, 43, 44, 45, 46, 47, 59, 61, 62, 79 | 12 |
| Integrations | 08, 18, 19, 20, 38, 39, 40, 41, 42, 78 | 10 |
| Data model / schema | 24, 29, 30, 31, 32, 33, 34, 68, 69, 70, 71, 72, 73, 74, 75, 77, 98 | 17 |
| Search | 25, 26, 27, 28, 64 | 5 |
| Secrets / config | 02, 03, 15, 16, 17, 66, 67 | 7 |
| Frontend / template | 54, 55, 56, 57, 58, 59, 60, 61, 80, 81, 82, 86, 87, 88, 101, 102, 103 | 17 |
| Performance | 61, 62, 63 | 3 |
| DevOps | 83, 84, 85, 96, 97, 99, 100, 103, 104 | 9 |

---

## 4. Verified in log vs static analysis

| Evidence class | IDs |
|---|---|
| **Confirmed in `laravel.log`** | BUG-04 (`billing_state`, `product_image`), BUG-08 (`Class "Razorpay\Api\Api" not found`), BUG-29 (`sub_categories.category_id`), BUG-32 (`offer_price`/`slug` on null), BUG-33 (`name` on null), BUG-39 (`Maximum execution time of 60 seconds exceeded`), BUG-40, BUG-78 (payload logged with PII) |
| **Confirmed against the DDL** | BUG-24, BUG-26, BUG-27, BUG-29, BUG-30, BUG-34, BUG-38, BUG-48, BUG-62, BUG-71, BUG-73, BUG-75, BUG-92 |
| **Confirmed by reading source** | All others |

**Caveat on the log.** It spans 2026-04-27 to 2026-08-17 and mixes two machines: entries up to 2026-06-04 come from `C:\Users\AISH\Documents\Xampp-Backup\htdocs\palVarnan`, while the checkout errors are from `C:\xampp\htdocs\pauls\palVarnan`. Several historical errors (`products.sku`, `products.name`, `wishlists.variant_id`, `product_variants` table, `Class "App\Models\ProductVariant"`, `Call to undefined method CartController::ajaxUpdate()`) are from **earlier code states and are fixed in current source**. They are documented as history, not as current defects.

The one conclusion the log supports strongly: **later `2026-08-17` entries show a successful Shiprocket order creation**, which means someone applied `ALTER TABLE` statements in a database that no migration in this repository records.

---

## 5. Root causes

106 issues reduce to 9 root causes:

| # | Root cause | Issues |
|---|---|---|
| RC-1 | **Schema changed by hand, never migrated** — 93 tables, 0 domain migrations | BUG-04, 08, 24, 29, 34, 38, 48, 62, 71, 73, 74, 75, 76, 98 |
| RC-2 | **No middleware layer** — every authorization decision is ad hoc | BUG-06, 07, 09, 10, 11, 12, 13, 23 |
| RC-3 | **Business logic duplicated instead of centralised** | BUG-34, 35, 39, 43, 45, 49, 61 |
| RC-4 | **Trusting the client for anything** | BUG-05, 06, 07, 11, 13, 36, 50, 51 |
| RC-5 | **Template never pruned** | BUG-55, 56, 57, 80, 81, 82, 87, 88, 101, 103 |
| RC-6 | **No tests, so regressions were invisible** | produces most of RC-1, RC-2, RC-3 |
| RC-7 | **Dependencies unmanaged** | BUG-08, 83, 84, 85, 98 |
| RC-8 | **Integration errors swallowed or leaked** | BUG-15, 18, 19, 20, 22, 40, 41, 42 |
| RC-9 | **Secrets handled carelessly** | BUG-02, 03, 16, 17, 66, 67 |

**Fixing RC-1, RC-2, and RC-4 resolves roughly half the register.** RC-1 alone unblocks checkout.

---

## 6. Corrections from the earlier draft

| Earlier claim | Reality |
|---|---|
| `shiprocket_shipment_id` receives the order id | **False.** `:501` reads `$shipResponse['shipment_id']` and `:511` passes it to `assignAWB` |
| Address update/delete are IDORs | **False.** `abort_unless($address->user_id === Auth::id(), 403)` on both |
| The thank-you page is not ownership-scoped | **False.** `:543-545` scopes by `user_id` with `firstOrFail()` |
| Shipping dimensions are sent as `0` | **False.** The fallbacks are `10`, not `0` — but the underlying columns are missing entirely, so the real defect is BUG-38 |
| `order_number` has no uniqueness guarantee | **False.** `product_orders_order_number_unique` exists |
| `users.phone_number` has no unique constraint | **False.** `users_phone_number_unique` exists |
| `product_orders.user_id` has no FK | **False.** `product_orders_user_id_foreign` exists with `ON DELETE CASCADE` |
| Razorpay receives PII in `notes` | **False.** `:200-204` sends only `receipt`, `amount`, `currency` |
| The client controls the order subtotal | **False.** Subtotal and GST are recomputed from the cart at `:181-189` and `:276-284` |
| Log contains `cURL error 28` | **Not found.** The log shows `Maximum execution time of 60 seconds exceeded` instead |

---

## 7. Related documents

- [13-security-audit.md](13-security-audit.md) — SEC-numbered view of the S1 and S2 items
- [04-feature-inventory.md](04-feature-inventory.md) — F-numbered view
- [19-incomplete-work.md](19-incomplete-work.md) — what is missing rather than broken
- [21-technical-debt.md](21-technical-debt.md) — structural cost
- [22-database-integrity.md](22-database-integrity.md) — the corrective migrations for RC-1
- [29-remediation-plan.md](29-remediation-plan.md) — the sequenced fix order