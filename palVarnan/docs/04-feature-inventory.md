# 04 — Feature Inventory

Every feature was verified by reading its implementation. **A feature is only marked "Fully implemented" if the code path is complete, the schema supports it, and no logged error contradicts it.**

Status vocabulary:
`FULL` Fully implemented · `PARTIAL` Partially implemented · `PLACEHOLDER` UI exists, no backend · `BROKEN` Implemented but fails · `NOT IMPLEMENTED` · `UNKNOWN`

---

## 1. Feature table

| # | Feature | Location | Status | Dependencies | Business logic | Missing / broken parts |
|---|---|---|---|---|---|---|
| F-01 | Country selection gate | `CountryController.php`, `country-selection.blade.php` | **FULL** | `countries` table, session | BR-M1-01…03 | `iso_code`/`ship_charge` unused; currency always `₹` |
| F-02 | Country switch modal | `layouts/country-modal.blade.php` | **PARTIAL** | `countries` | — | Modal posts to `/select-country`; country list is re-fetched, not cached |
| F-03 | User registration | `AuthController::registerStore()` `:14-40` | **FULL** | `users`, session | BL-AUTH-01 | min password 6; no email verification; no CAPTCHA; no rate limit |
| F-04 | Login (email **or** phone) | `AuthController::login()` `:44-70` | **FULL** | `users`, `sessions` | BL-AUTH-02 | **no rate limit / lockout**; field misnamed `username` |
| F-05 | Forgot-password (send link) | `AuthController::forgotPassword()` `:72-93` | **PARTIAL** | `password_reset_tokens`, mail | BL-AUTH-03 | `MAIL_MAILER=log` ⇒ link only lands in `storage/logs/laravel.log`; no UI shows it |
| F-06 | **Reset password (direct)** | `AuthController::resetPassword()` `:95-120` | **BROKEN — CRITICAL** | `users` | — | **No token check. Anyone knowing an email can set that account's password.** Account takeover. |
| F-07 | Logout | `ProfileController::logout()` `:52-67` | **FULL** | `sessions` | BL-M1-03 | Route has no `auth` middleware (harmless here) |
| F-08 | Profile update | `ProfileController::update()` `:14-50` | **FULL** | `users` | BL-ACCT-01 | Route has no `auth` middleware ⇒ guest causes a null deref |
| F-09 | Change password | `ProfileController::update()` `:29-48` | **FULL** | `users` | BL-ACCT-02 | Uses `bcrypt()` while `User` declares `password => hashed` cast — inconsistent with registration |
| F-10 | **My Account page** | `ProfileController::index()` `:69-96` | **PARTIAL** | 6 tables | BL-ACCT-03 | Route unauthenticated ⇒ guest sees `user_id IS NULL` rows. Verified null-deref on `$variant->unit` / `->name` in log. |
| F-11 | Address book CRUD | `CheckoutController::storeAddress/updateAddress/destroyAddress` `:553-633` | **FULL** | `user_addresses` | BL-CHK-10 | **Best-secured code in the project** (`abort_unless(...user_id === Auth::id(), 403)`). `state`/`pincode` are free-text, not FK-validated |
| F-12 | Home hero banners | `HomeController.php:16-19`, `home.blade.php:32-70` | **FULL** | `home_promotions` | BR-HOME-02 | Only the slider is dynamic |
| F-13 | Home category grid | `HomeController.php:21`, `home.blade.php:97` | **FULL** | `categories` | BR-HOME-03 | — |
| F-14 | **Home product sections** | `home.blade.php:121-900` | **PLACEHOLDER** | — | — | **~700 lines of MarketPro template demo content**: 50 Unsplash images, 29 wowtheme7.com links, hard-coded `₹14.99/₹28.99/₹60.99`. Zero database involvement. |
| F-15 | Product listing + filters | `ProductController::index()`, `products.blade.php` | **FULL** | `products`, `categories`, `sub_categories`, `brands` | BR-CAT-01…03 | Filters unvalidated; `sub_categories` relationship broken; no sort/price filters |
| F-16 | Product detail | `ProductController::show()`, `product-detail.blade.php` | **FULL** | `products`, `product_varient`, `units` | BR-CAT-04 | `GET /product` route renders this view with no controller data ⇒ fatal |
| F-17 | Related products | `ProductController.php:78-89` | **FULL** | `products` | BR-CAT-04 | Fixed `take(8)`, no relevance |
| F-18 | Type-ahead search | `SearchController.php`, `header.blade.php:835` | **PARTIAL (2 of 4 broken)** | 4 tables | BR-SEARCH-01…03 | Product search works. **Variant search impossible** (`varient` is `int`). **Brand + Category URLs are `/brand/` and `/category/`** (`slug` missing) and match no route. |
| F-19 | **Search results page** | — | **NOT IMPLEMENTED** | — | — | `/search` returns JSON only; header form is `action="#"` |
| F-20 | Add to cart (logged in) | `CartController::store()` `:62-90` | **FULL** | `carts` | BL-CART-01…03 | `pre_order` silently dropped by `$fillable` |
| F-21 | Add to cart (guest) | `CartController::store()` `:64-70` | **PARTIAL** | `carts` | BL-CART-02 | Identity = `request()->ip()`; **no guest→user merge on login** |
| F-22 | View cart | `CartController::index()` `:11-33` | **FULL** | `carts` | BL-CART-04 | Loads all lines into PHP memory |
| F-23 | **Update cart quantities** | `CartController::update()` `:35-44` | **BROKEN — IDOR + CRITICAL** | `carts` | — | **No ownership filter, no validation.** Any user can rewrite any cart's quantity. |
| F-24 | **Remove cart line** | `CartController::remove()` `:46-60` | **BROKEN — IDOR + CRITICAL** | `carts` | — | **`Cart::findOrFail($id)->delete()` with no ownership check**, and exposed as a **`GET`** (CSRF- and prefetch-unsafe) |
| F-25 | AJAX cart update/remove | `CartController::ajaxUpdate/ajaxRemove()` `:92-113` | **FULL** | `carts` | BL-CART-05 | Correctly scoped. Keyed by `product_varient_id`, not cart id — a product with 2 cart lines of the same variant updates/deletes only the first |
| F-26 | Cart subtotal badge (global) | `AppServiceProvider.php:25-57` + `app.blade.php:223-232` | **PARTIAL** | `carts` | BL-CART-06 | Runs `Cart::with('variant')->get()` **on every view**; `carts` is unindexed ⇒ full scan per page |
| F-27 | Wishlist toggle | `WishlistController::toggle()` `:12-33` | **PARTIAL** | `wishlists` | BR-WISH-01 | Reads `$request->variant_id`, whereas the sibling cart endpoints use `product_varient_id` — inconsistent naming. Route unauthenticated ⇒ guest writes `user_id = NULL` |
| F-28 | Wishlist page | `WishlistController::index()`, `wishlist.blade.php` | **PARTIAL** | `wishlists`, `products` | — | Route unauthenticated ⇒ guest sees **all** `user_id IS NULL` rows. Verified `Attempt to read property "product_image" on null` in log |
| F-29 | **Wishlist remove** | `WishlistController::remove($id)` `:45-49` | **BROKEN — IDOR** | `wishlists` | — | `where('id',$id)->where('user_id', Auth::id())->delete()` — route unauthenticated, so `Auth::id()` is `null` ⇒ deletes any row by ID |
| F-30 | Wishlist pre-check APIs | `WishlistController::check/checkProduct()` | **FULL** | `wishlists` | BR-WISH-03 | Correctly `middleware('auth')`. Previously queried non-existent `variant_id` (log 2026-06-04); fixed, unverified since |
| F-31 | Checkout page | `CheckoutController::index()`, `checkout.blade.php` | **FULL** | 6 tables | BL-CHK-01 | Redirects to `/cart` if empty |
| F-32 | **Live courier rates** | `CheckoutController::getShiprocketCouriers()`, `ShiprocketService::checkServiceability()` | **FULL** | Shiprocket API | BL-CHK-05…08 | Works (log: `Shiprocket Login Successful`, 8 successful quotes). Weight heuristic `:107-110` is inconsistent with `:460` |
| F-33 | Courier selection | `checkout.blade.php:652-731` | **FULL** | — | BL-CHK-09 | Cheapest auto-selected (`usort` then index 0) |
| F-34 | **Razorpay order creation** | `CheckoutController::preparePayment()` `:172-220` | **PARTIAL** | `services.razorpay` | BL-CHK-11 | Works, but `shipping_charge`/`coupon_discount` are client-supplied. Leaks `$e->getMessage()` |
| F-35 | **Razorpay signature verification** | `CheckoutController::placeOrder()` `:252-268` | **FULL** | Razorpay | BL-CHK-12 | Correctly calls `verifyPaymentSignature`. Log shows `Razorpay\Api\Api not found` twice ⇒ a manual `require_once` hack (`:26-31`) was added |
| F-36 | **Order persistence** | `CheckoutController::placeOrder()` `:290-421` | **BROKEN — CRITICAL** | 6 tables | BL-CHK-13 | **`Unknown column 'billing_state'` then `Unknown column 'product_image'` — VERIFIED in log. Transaction rolls back.** No migration creates these columns. |
| F-37 | Stock deduction | `CheckoutController.php:402-415` | **FULL** | `productstocks`, `product_varient` | BL-CHK-14 | Two tables updated. Uses `max(...,0)` ⇒ oversell silently zeroes stock |
| F-38 | Cart clear after order | `CheckoutController.php:419` | **FULL** | `carts` | — | Inside the transaction, so it rolls back correctly |
| F-39 | **Shiprocket order creation** | `CheckoutController.php:432-527` | **PARTIAL** | Shiprocket API | BL-CHK-15 | Runs **outside** the DB transaction. Failures are swallowed (`:525-527`) and the order is still reported successful. Log confirms it worked once |
| F-40 | AWB assignment | `CheckoutController.php:508-523` | **FULL** | Shiprocket API | — | Only if `selected_courier_id` is present. `awb_code` / `courier_name` columns come from the dashboard's migration |
| F-41 | Thank-you page | `CheckoutController::thankyou()`, `thankyou.blade.php` | **FULL but UNREACHABLE** | `product_orders` | — | Correctly scopes `where('user_id', Auth::id())`. Never reached because F-36 fails |
| F-42 | **Coupon application** | UI in `cart.blade.php:153-154`, `checkout.blade.php:94` | **PLACEHOLDER** | `coupons` table exists | — | "Apply Coupon" button has **no event handler**. `hidden_coupon_discount` hard-coded `0`. `Coupon` model imported in `CheckoutController.php:6` but **never queried**. 3 branches on the homepage are dead `<button>`s. |
| F-43 | State-based shipping charge | `CheckoutController::getShippingCharge()` `:638-654` | **NOT IMPLEMENTED (dead code)** | `states.shipping_charge` | — | Route exists, method implemented, **no view ever calls it**. Overridden in practice by live Shiprocket rates |
| F-44 | District/city cascade | `CheckoutController::getCitiesByState()` `:659-670` | **NOT IMPLEMENTED (dead code)** | `districts` | — | Route exists, **no view calls it** |
| F-45 | Order tracking | `product_tracking` written (`:359-363`); `ShiprocketService::trackAWB()` exists | **PARTIAL** | — | — | Nothing ever **reads** tracking in the storefront. `SyncShiprocketStatus` runs in the *dashboard* app and **excludes `status = 'Order Placed'`** ⇒ storefront orders are never synced |
| F-46 | Order cancellation / return | — | **NOT IMPLEMENTED** | `cancel_requests`, `product_refunds`, `product_slots.is_cancelled` exist | — | Tables present, zero storefront code |
| F-47 | Reviews & ratings | — | **NOT IMPLEMENTED** | `reviews` table exists | — | No model, no route, no UI |
| F-48 | Product enquiry / bulk order | — | **NOT IMPLEMENTED** | `bulkorders` table exists | — | — |
| F-49 | Blog / news | — | **NOT IMPLEMENTED** | — | — | `header.blade.php` links `blog.html` ×1, `blog-details.html` ×1 ⇒ 404 |
| F-50 | Contact page | — | **NOT IMPLEMENTED** | — | — | `header.blade.php` links `contact.html` ×3 ⇒ 404 |
| F-51 | Vendor / seller pages | — | **NOT IMPLEMENTED** | — | — | `vendor.html`, `vendor-details.html`, `vendor-two.html`, `vendor-two-details.html` ⇒ 404. Note: the purchased template is named "multivendor" but **no multi-vendor logic exists at all** |
| F-52 | Become a seller | — | **NOT IMPLEMENTED** | — | — | `become-seller.html` ⇒ 404 |
| F-53 | `index-two` / `index-three` home variants | — | **NOT IMPLEMENTED** | — | — | `header.blade.php` links both ⇒ 404 |
| F-54 | Guest→user cart merge | — | **NOT IMPLEMENTED** | — | — | Login does not migrate guest cart rows to `user_id` |
| F-55 | Email order confirmation | — | **NOT IMPLEMENTED** | — | — | `MAIL_MAILER=log`; no mailable in this app. The dashboard has `App\Mail\OrderStatusUpdated` |
| F-56 | Admin panel | — | **NOT IMPLEMENTED in this app** | `roles`, `permissions`, `dashboard_users` exist | — | Lives entirely in `palVarnanDashboard` |
| F-57 | SEO / meta / sitemap | — | **NOT IMPLEMENTED** | — | — | Only `<title> Pal Varnan</title>` (`app.blade.php:9`); no meta description, no OpenGraph, no canonical, no per-product title. `public/robots.txt` is 24 bytes |
| F-58 | Analytics / tracking pixels | — | **NOT IMPLEMENTED** | — | — | No GA/GTM/Pixel in any view |
| F-59 | 404 / error pages | — | **PARTIAL** | — | — | Only Laravel's default error pages; `withExceptions()` is empty (`bootstrap/app.php:16-18`) |
| F-60 | Favicon | `public/favicon.ico` | **BROKEN** | — | — | File is **0 bytes**. `app.blade.php:11` points at `/assets/images/logo/logo.png` instead |
| F-61 | Health check | `bootstrap/app.php:12` | **FULL** | — | — | `GET /up` |

---

## 2. Features by status

| Status | Count | Feature IDs |
|---|---|---|
| **FULL** | 25 | F-01, F-03, F-04, F-07, F-08, F-09, F-11, F-12, F-13, F-15, F-16, F-17, F-20, F-22, F-25, F-27*, F-31, F-32, F-33, F-35, F-37, F-38, F-40, F-61 |
| **PARTIAL** | 15 | F-02, F-05, F-10, F-18, F-21, F-26, F-27, F-28, F-30*, F-34, F-39, F-41, F-45, F-59, *F-30 |
| **PLACEHOLDER** | 3 | F-14 (home sections), F-42 (coupons) |
| **BROKEN** | 6 | **F-06 (critical)**, F-16 (partial: `/product` route), F-18 (partial), F-23 (critical), F-24 (critical), **F-36 (critical)** |
| **NOT IMPLEMENTED** | 15 | F-19, F-43, F-44, F-46, F-47, F-48, F-49, F-50, F-51, F-52, F-53, F-54, F-55, F-56, F-57, F-58 |
| **BROKEN (asset)** | 1 | F-60 |

\* F-27/F-30 appear in both lists because `toggle` is complete for authenticated users but the route lacks `auth` middleware.

---

## 3. The dead-link problem — VERIFIED

**Source:** `resources/views/layouts/header.blade.php`, link census.

| Target | Occurrences in header | File exists in `public/`? | Result |
|---|---|---|---|
| `shop.html` | **85** | **No** | 404 |
| `contact.html` | 3 | No | 404 |
| `blog.html` | 1 | No | 404 |
| `blog-details.html` | 1 | No | 404 |
| `vendor.html` | 1 | No | 404 |
| `vendor-details.html` | 1 | No | 404 |
| `vendor-two.html` | 1 | No | 404 |
| `vendor-two-details.html` | 1 | No | 404 |
| `product-details.html` | 1 | No | 404 |
| `product-details-two.html` | 1 | No | 404 |
| `index-two.html` | 1 | No | 404 |
| `index-three.html` | 1 | No | 404 |
| `checkout.html` | 1 | No | 404 |
| `become-seller.html` | 1 | No | 404 |
| `account.html` | 1 | No | 404 |
| **Total dead links** | **101** | — | — |

`resources/views/layouts/footer.blade.php` is better (`/products` ×23) but contains leftover **US demo contact data**: `mailto:apnstores369@gmail.com`, `mailto:apaulskitchen@gmail.com`, `tel:+(406)555-0120` (a Montana, USA area code on an India-only store).

---

## 4. Duplicated / conflicting implementations

| # | Duplication | Where | Impact |
|---|---|---|---|
| D-1 | **Cart subtotal computed in 4 places** with different code | `AppServiceProvider.php:47-49`, `CartController.php:26-28`, `CartController.php:107`, `CheckoutController.php:59-69`, `ProfileController.php:88` | 5 divergent implementations of one number |
| D-2 | **Guest identity resolved 3 different ways** | `$request->ip()` (`CartController::store():82`, `ProductController:47,96`) vs `cookie('guest_token') ?? ip()` (`CartController::index():16`, `subtotal():101`, `AppServiceProvider:43`, `CheckoutController:695`) | Guest carts split across identities; behind a proxy all guests collide |
| D-3 | **Price fallback chain written 3 ways** | `variant->offer_price ?? product->product_price ?? 0` — `product_price` **does not exist** in `products` | All paths degrade to `offer_price ?? 0` |
| D-4 | **`ShiprocketService` duplicated verbatim** | `palVarnan/app/Services/` and `palVarnanDashboard/app/Services/` | Fixes diverge |
| D-5 | **`config/services.php` razorpay/shiprocket blocks duplicated** | both apps | Divergence risk |
| D-6 | **Two different total formulas** | `placeOrder():288` `($subtotal + $gst + $shipping) - $discount` vs `checkout.blade.php:626` `max((baseSubtotalWithGst + shipping) - discount, 0)` | Backend can store a negative `total_amount` |
| D-7 | **Two different weight floors** | `getShiprocketCouriers():110` `max(...,0.1)` vs `placeOrder():460` `max(...,0.5)` | Quote ≠ actual charge |
| D-8 | **Cart "in cart" detection computed twice** with different semantics | `AppServiceProvider` uses `unique(product_id-product_varient_id)`; `ProductController` uses raw `pluck('product_varient_id')` | Header badge and product-page buttons can disagree |

---

## 5. Related documents

- [03-module-map.md](03-module-map.md) — module grouping
- [19-incomplete-work.md](19-incomplete-work.md) — remediation plan for every NOT IMPLEMENTED / PLACEHOLDER item
- [20-known-issues.md](20-known-issues.md) — the bug register
- [21-technical-debt.md](21-technical-debt.md) — duplication cost analysis
