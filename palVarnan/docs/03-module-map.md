# 03 — Module Map

Nine functional modules were identified. Each is documented with its files, routes, tables, dependencies, business rules, status and known issues.

Legend for **Status**:
`COMPLETE` · `PARTIAL` · `PLACEHOLDER` · `BROKEN` · `DEAD` · `NOT IMPLEMENTED`

---

## Module index

| # | Module | Status | Risk |
|---|---|---|---|
| M1 | Country / Region Selection | COMPLETE | Low |
| M2 | Authentication | **PARTIAL — CRITICAL** | Critical |
| M3 | Home / Promotional Content | **PLACEHOLDER** | Critical |
| M4 | Catalogue & Product Browsing | COMPLETE | High |
| M5 | Search | **BROKEN (2 of 4 entity types)** | High |
| M6 | Cart | COMPLETE (functionally) / **INSECURE** | Critical |
| M7 | Wishlist | PARTIAL / **INSECURE** | High |
| M8 | User Account & Addresses | COMPLETE | High |
| M9 | Checkout, Payment & Shipping | **BROKEN** | **Critical** |

---

## M1 — Country / Region Selection

| Aspect | Detail |
|---|---|
| **Purpose** | Establishes which country's catalogue, currency and shipping rules to use. Stored as `session('country_id')`. |
| **Files** | `app/Http/Controllers/CountryController.php`, `app/Models/Country.php`, `resources/views/country-selection.blade.php`, `resources/views/layouts/country-modal.blade.php` |
| **Routes** | `GET /select-country` → `CountryController::index`<br>`POST /select-country` → `CountryController::store` |
| **Controller methods** | `index()`, `store()` |
| **Model** | `Country` → table `countries` |
| **Tables** | `countries` (columns: `id`, `name`, `iso_code`, `phone_code`, `ship_charge`, `currency`, timestamps) |
| **Business rules** | BR-M1-01: selection is matched case-insensitively via `LOWER(name) = ?` (`CountryController.php:19`).<br>BR-M1-02: only the `country_id` session key is stored — the whole rest of the app reads it.<br>BR-M1-03: selection survives logout — saved before `Auth::logout()` and restored after (`ProfileController.php:54, 63`). |
| **Status** | **COMPLETE** |
| **Known issues** | `GET /select-country` is **not** in the `auth` group, so it is reachable at any time — intentional. No validation that `countries` is non-empty. `iso_code`, `phone_code`, `ship_charge` columns are never read by any code. |
| **Missing** | Country-specific currency formatting is hard-coded to `₹` (INR) throughout the Blade templates regardless of `countries.currency`. The dump shows `currency = 'GST'` which is a tax name, not a currency code. |

---

## M2 — Authentication

| Aspect | Detail |
|---|---|
| **Purpose** | Register, log in, log out, "forgot" and "reset" password. |
| **Files** | `app/Http/Controllers/AuthController.php`, `app/Http/Controllers/ProfileController.php` (logout), `app/Models/User.php`, `resources/views/pages/login.blade.php`, `resources/views/pages/register.blade.php` |
| **Routes** | `GET /login` → `Route::view('pages.login')` (no controller)<br>`GET /register` → `Route::view('pages.register')` (no controller)<br>`POST /register-store` → `AuthController::registerStore` **named `register.store`**<br>`POST /login-store` → `AuthController::login` **named `login.store`**<br>`POST /forgot-password` → `AuthController::forgotPassword` **named `password.email`**<br>`POST /reset-password-direct` → `AuthController::resetPassword` **named `password.reset.direct`**<br>`POST /logout` → `ProfileController::logout` |
| **Tables** | `users`, `sessions`, `password_reset_tokens` |
| **Business rules** | See [08-business-logic.md](08-business-logic.md) BL-AUTH-01 … BL-AUTH-06 |
| **Status** | **PARTIAL — CRITICAL** |
| **BROKEN** | `AuthController::resetPassword()` (lines 100-120) accepts `email` + `password` only. **There is no `token` field, no `password_reset_tokens` lookup, no expiry check.** Anyone who knows a registered email address can set that account's password to anything. Confirmed by reading `AuthController.php:100-120`. |
| **BROKEN** | `forgotPassword()` calls `Password::sendResetLink()` (a real, token-based Laravel flow), but **no route or view renders a reset form** and `resetPassword()` never verifies the token that was emailed. So the emailed link is unusable, and the direct endpoint is an open account-takeover. |
| **Known issues** | 1. **No rate limiting / lockout** on `POST /login-store`.<br>2. Login accepts either email **or** phone number in a field named `username` (`AuthController.php:56-58`); no normalisation.<br>3. `User::$fillable` includes `first_name`, which is never set by `registerStore()` — dead field.<br>4. Password minimum length is **6** (`:21`, `:107`).<br>5. `User::casts()` includes `'password' => 'hashed'` **and** `registerStore()` calls `Hash::make()` — double hashing is avoided only because Laravel's `hashed` cast detects an already-hashed string. `ProfileController.php:42` uses `bcrypt()` explicitly instead — inconsistent.<br>6. Registration is not gated to the selected country beyond writing `country_id`; `users.country_id` is nullable. |
| **Missing** | Email verification (`MustVerifyEmail` is commented out in `User.php:5`). Social login. CAPTCHA. Account deletion. Password-strength rules. Login "remember me". |

---

## M3 — Home / Promotional Content

| Aspect | Detail |
|---|---|
| **Purpose** | Render the storefront landing page: hero banners from `home_promotions` plus a category grid. |
| **Files** | `app/Http/Controllers/HomeController.php`, `app/Models/HomePromotion.php`, `resources/views/pages/home.blade.php` (1896 lines) |
| **Routes** | `GET /` → `HomeController::index` |
| **Tables** | `home_promotions`, `categories` |
| **Business rules** | BR-HOME-01: redirect to `/select-country` if `session('country_id')` is empty (`HomeController.php:12-14`).<br>BR-HOME-02: banners filtered `country_id = session AND status = 1`, ordered by `sort_order` (`:16-19`).<br>BR-HOME-03: categories filtered with `FIND_IN_SET(session_country_id, country_id)` (`:21`). |
| **Status** | **PLACEHOLDER — the single largest incomplete area** |
| **Evidence** | `resources/views/pages/home.blade.php` is 1896 lines but contains only **4 Blade control directives** in total. It contains:<br>• **50** `images.unsplash.com` URLs (stock photos of unrelated products)<br>• **29** `wowtheme7.com` links (the template vendor's demo URLs)<br>• Hard-coded demo prices `₹14.99`, `₹28.99`, `₹60.99`<br>Only lines 45 (banner images) and 97 (category images) are data-driven. |
| **Missing** | Real product carousels (new arrivals, best sellers, trending, flash sale, deals, popular, top brands, vendors, testimonials, blog, newsletter). The `home_promotions` table only drives the hero slider. Tables that exist for these sections and are never queried: `offer_images`, `today_deals`, `scrolling_bars`, `banner_images`, `web_images`, `testimonials`, `reviews`, `newcoupons`. |
| **Related dead assets** | `public/assets/images/{products,thumbs,icon,bg,shape,flags,cart}/*` — ~400 template demo images that no dynamic page uses. |

---

## M4 — Catalogue & Product Browsing

| Aspect | Detail |
|---|---|
| **Purpose** | Paginated product listing with category / subcategory / brand filters, and a product detail page with variant selection, related products, cart and wishlist actions. |
| **Files** | `app/Http/Controllers/ProductController.php`, `app/Models/Product.php`, `app/Models/ProductVarient.php`, `app/Models/Category.php`, `app/Models/SubCategory.php`, `app/Models/Brand.php`, `app/Models/Unit.php`, `resources/views/pages/products.blade.php`, `resources/views/pages/product-detail.blade.php` |
| **Routes** | `GET /products` → `ProductController::index`<br>`GET /product/{slug}` → `ProductController::show`<br>`GET /product` → `Route::view('pages.product-detail')` **← broken: no controller, no `$product`, will throw** |
| **Tables** | `products`, `product_varient`, `categories`, `sub_categories`, `brands`, `units` |
| **Business rules** | BR-CAT-01: listing shows `country_id = current OR country_id IS NULL` (`ProductController.php:37-40`).<br>BR-CAT-02: filters are simple equality: `category_id`, `subcategory_id`, `brand_id` from query string — **no validation, no allow-list**.<br>BR-CAT-03: `latest()->paginate(12)` (`:65`).<br>BR-CAT-04: related products = same `category_id`, different `id`, country-visible, `latest()->take(8)` (`:78-89`).<br>BR-CAT-05: cart/wishlist membership is computed by plucking **all** of the user's `product_varient_id` / `product_id` values into a PHP array and testing membership in Blade (`:44-64`, `:91-104`).<br>BR-CAT-06: soft-deleted products are excluded automatically via `Product`'s `SoftDeletes` trait. |
| **Status** | **COMPLETE** (functionally correct) |
| **Known issues** | 1. `GET /product` route (`routes/web.php:13`) renders `pages.product-detail` with **zero** controller data → the view dereferences `$product` → fatal error. `storage/logs/laravel.log` contains `View [pages.product-details` and `View [home` errors from earlier iterations of this.<br>2. `SubCategory::category()` and `Category::subCategories()` reference a **non-existent** `sub_categories.category_id` column. Verified failure in log: `Unknown column 'sub_categories.category_id' in 'where clause'`.<br>3. Filters are unvalidated — `?category=abc` produces a MySQL-coerced comparison rather than a 422.<br>4. `find_or_fail` on `show()` means any unknown slug → 404. Acceptable, but there is no redirect to alternatives.<br>5. `ProductController::show()` has a **large commented-out block** (lines 106-113) computing `$wishlistVariantIds`, and then passes `collect()` to the view anyway (`:96, :109-111`).<br>6. No faceted counts, no sort options, no price filter — only the three ID filters exist. |
| **Missing** | Reviews/ratings UI (`reviews` table unused). Product Q&A. Stock availability display. Size/chart guides. Compare. Product enquiry. |

---

## M5 — Search

| Aspect | Detail |
|---|---|
| **Purpose** | Type-ahead search box in the header returning a JSON array of suggestions. |
| **Files** | `app/Http/Controllers/SearchController.php`, `resources/views/layouts/header.blade.php:835` |
| **Routes** | `GET /search?q=` → `SearchController::search` |
| **Tables** | `products`, `product_varient`, `brands`, `categories` |
| **Business rules** | BR-SEARCH-01: searches 4 entity types, 5 results each, maximum 20 suggestions (`:29-104`).<br>BR-SEARCH-02: image base URL is `rtrim(env('MAIN_URL'),'/') . '/images/'` (`:29`).<br>BR-SEARCH-03: results sorted by type order: Products → Variants → Brands → Categories (not by relevance). |
| **Status** | **BROKEN (2 of 4 entity types return unusable results)** |
| **BROKEN 1** | `ProductVarient.varient` is declared **`int(11)`** in the SQL dump, but `SearchController.php:41` searches `->where('varient', 'LIKE', "%{$q}%')`. MySQL casts any string against an `int` column to `0`, so `LIKE '%abc%'` can never match. **Variant search can never return a row.** |
| **BROKEN 2** | `SearchController.php:83` builds `url('/brand/' . $item->slug)`. The `brands` table column is **`brand_slug`**, and `Brand` has no `slug` attribute → URL is `/brand/` for every brand. **No route matches `/brand/{…}` anyway.** |
| **BROKEN 3** | `SearchController.php:103` builds `url('/category/' . $item->slug)`. The `categories` table has **no `slug` column at all** → `/category/`. No route matches. |
| **Known issues** | 1. `env('MAIN_URL')` is called at runtime — returns `null` if `php artisan config:cache` has ever been run. See [12-configuration.md](18-configuration-and-env.md.<br>2. No debounce on the client, no minimum query length, no rate limiting.<br>3. `trim($request->q)` on a **missing** `q` parameter → PHP warning + treated as `null`; `if (!$q)` then returns `[]`, so it survives, but only by luck.<br>4. `LIKE '%…%'` with a user-supplied value cannot use an index — full scan of 4 tables per keystroke (only 5 rows fetched, but the scan is complete). |
| **Missing** | No results page (`/search` returns JSON only; the header search box form has `action="#"`). No typo tolerance, no relevance scoring, no highlighting. |

---

## M6 — Cart

| Aspect | Detail |
|---|---|
| **Purpose** | Persist a shopping basket for both logged-in users and guests, render it, update quantities, remove lines, and expose the subtotal to the header. |
| **Files** | `app/Http/Controllers/CartController.php`, `app/Models/Cart.php`, `app/Providers/AppServiceProvider.php`, `resources/views/pages/cart.blade.php` |
| **Routes** | `GET /cart` → `CartController::index`<br>`POST /cart-update` → `CartController::update` (`cart.update`)<br>`GET /cart-remove/{id}` → `CartController::remove` (`cart.remove`)<br>`POST /cart/store` → `CartController::store` (`cart.store`)<br>`POST /cart/ajax-update` → `CartController::ajaxUpdate`<br>`POST /cart/ajax-remove` → `CartController::ajaxRemove`<br>`GET /cart/subtotal` → `CartController::subtotal` |
| **Tables** | `carts`, `products`, `product_varient` |
| **Business rules** | See [08-business-logic.md](08-business-logic.md) BL-CART-01 … BL-CART-09 |
| **Status** | **COMPLETE functionally / INSECURE (IDOR) / UNINDEXED** |
| **BROKEN (security)** | `CartController::update()` (`:35-44`) — `Cart::where('id', $cartId)->update(['product_quantity' => $qty])`. **No user scoping whatsoever.** Any user who guesses a cart row ID can change any cart's quantity.<br>`CartController::remove()` (`:46-60`) — `Cart::findOrFail($id); $cart->delete();`. **No ownership check.** Any user can delete any cart line by ID.<br>`CartController::ajaxUpdate()` / `ajaxRemove()` scope by `user_id`/`ip_addresscart` — **safe**.<br>`CartController::store()` scopes by user — **safe**. |
| **BROKEN (correctness)** | **Guest-cart identity is inconsistent across the app.** `store()` stores `$request->ip()` (`:82`). `index()` reads `request()->cookie('guest_token') ?? request()->ip()` (`:16`). `subtotal()` same (`:101`). `AppServiceProvider` same (`:43`). But `ProductController::index()/show()` read `request()->ip()` only (`:47, :96`). Behind a proxy/CDN/load-balancer, `request()->ip()` returns the proxy IP → **all guests share one cart**. |
| **BROKEN (correctness)** | `CartController::update()` reads `$request->quantities` as an array keyed by cart ID with **no validation at all** — no integer check, no minimum, no maximum. A client can submit `{"quantities":{"1":"999999999"}}` or a negative value. |
| **BROKEN (correctness)** | `CartController::remove()` is a **`GET`** route with a **destructive side effect** (`DELETE FROM carts`). It is CSRF-unprotected-by-design (GETs skip `ValidateCsrfToken`) and is prefetchable/prerenderable by browsers and crawlers. |
| **BROKEN (correctness)** | `Cart::$fillable` does **not** include `pre_order`, yet `CartController::store()` writes `'pre_order' => 0` (`:86`). Mass-assignment silently drops it. |
| **Missing** | No stock check when adding. No minimum-order / free-shipping rule (a `free_shipping_settings` table exists, unused). Coupon application is UI-only (see M9). Gift wrapping. Cart merge on login — a guest's cart is **never** migrated into their user cart. Stock reservation. |

---

## M7 — Wishlist

| Aspect | Detail |
|---|---|
| **Purpose** | Toggle a product-variant pair in a wishlist, render the wishlist page, remove entries, and check membership. |
| **Files** | `app/Http/Controllers/WishlistController.php`, `app/Models/Wishlist.php`, `resources/views/pages/wishlist.blade.php`, plus toggle JS in `product-detail.blade.php`, `products.blade.php` |
| **Routes** | `POST /wishlist/toggle` → `WishlistController::toggle` (`wishlist.toggle`) — **no auth middleware**<br>`GET /wishlist` → `WishlistController::index` (`wishlist.index`) — **no auth middleware**<br>`DELETE /wishlist/{id}/remove` → `WishlistController::remove` (`wishlist.remove`) — **no auth middleware**<br>`GET /wishlist/check` → `WishlistController::check` — `middleware('auth')`<br>`GET /wishlist/check-product` → `WishlistController::checkProduct` — `middleware('auth')` |
| **Tables** | `wishlists`, `products`, `product_varient`, `units` |
| **Business rules** | BR-WISH-01: uniqueness is (user_id, product_id, product_varient_id) enforced only by application logic, no DB unique index.<br>BR-WISH-02: a wishlist entry is per **variant**, so the same product can appear once per variant.<br>BR-WISH-03: `checkProduct()` reports existing entries for **other** variants of the same product so the UI can warn the user. |
| **Status** | **PARTIAL / INSECURE** |
| **BROKEN (security)** | The first three routes have **no `auth` middleware**. For a guest `Auth::id()` is `null`, so:<br>• `toggle()` creates a row with `user_id = NULL` — pollutes the table and is invisible to the real owner.<br>• `index()` queries `where('user_id', null)` which SQL-translates to `user_id IS NULL` → **every guest sees every other guest's orphan wishlist rows**.<br>• `remove($id)` deletes **any** wishlist row by ID, regardless of owner. |
| **BROKEN** | `WishlistController.php:56, 70` contain **mojibake comments** (`//+? correct column name`). The `//+?` bytes are a corrupted em-dash, confirming the file was edited in a non-UTF-8 editor. |
| **BROKEN (UI)** | `wishlist.blade.php:61` renders `env('MAIN_URL') . 'images/' . $item->product->product_image`. If `product` is `null` (soft-deleted or orphaned row), `Attempt to read property "product_image" on null` — **verified in `storage/logs/laravel.log`** (3 occurrences). `Wishlist::index()` eager-loads `product.variants.unit` but never filters out rows whose product is gone. |
| **Known issues** | `checkProduct()` uses `->first()` and `->with('variant.unit')`; if `unit` is `null` the `?? ''` guards it, so that path is safe. `check()` was previously querying a `variant_id` column that does not exist (verified in log, 2026-06-04) — since fixed to `product_varient_id`, but **unverified since**. |
| **Missing** | Move-to-cart from wishlist. Move-to-cart *is* wired from the wishlist page (`wishlist.blade.php:150`) but only adds one unit. Wishlist sharing. Guest wishlist persistence by cookie (the `ip_address` column is written but never read). |

---

## M8 — User Account & Addresses

| Aspect | Detail |
|---|---|
| **Purpose** | "My Account" page combining profile, saved addresses, wishlist, cart and order history; plus profile/password update and logout. |
| **Files** | `app/Http/Controllers/ProfileController.php`, `app/Http/Controllers/CheckoutController.php` (address CRUD only), `app/Models/UserAddress.php`, `app/Models/User.php`, `resources/views/pages/myaccount.blade.php` |
| **Routes** | `GET /myaccount` → `ProfileController::index` (`account.index`) — **no auth middleware**<br>`PUT /profile-update` → `ProfileController::update` (`profile.update`) — **no auth middleware**<br>`POST /logout` → `ProfileController::logout` (`logout`) — **no auth middleware**<br>`POST /checkout/address` → `CheckoutController::storeAddress` (auth)<br>`PUT /checkout/address/{address}` → `CheckoutController::updateAddress` (auth)<br>`DELETE /checkout/address/{address}` → `CheckoutController::destroyAddress` (auth) |
| **Tables** | `users`, `user_addresses`, `wishlists`, `carts`, `product_orders`, `states` |
| **Business rules** | BL-ACCT-01 … BL-ACCT-06 in [08-business-logic.md](08-business-logic.md) |
| **Status** | **COMPLETE** |
| **Good practice present** | `updateAddress()` and `destroyAddress()` correctly do `abort_unless($address->user_id === Auth::id(), 403)` (`CheckoutController.php:596, 629`). This is the **only** ownership check in the entire codebase. |
| **Known issues** | 1. `GET /myaccount` has no `auth` middleware. For a guest, `Auth::id()` is `null`, so the page queries `where('user_id', null)` → shows **all orphan rows** and renders an empty shell rather than redirecting to login.<br>2. `PUT /profile-update` and `POST /logout` have no `auth` middleware. `update()` calls `auth()->user()` and dereferences `$user->id` → for a guest this is `null->id` → error.<br>3. `ProfileController::index()` loads `State::all()` on every visit (`:82`) — the full Indian states table — for a `<select>`; fine, but uncached.<br>4. `$cartSubtotal` in `ProfileController.php:88` uses a **different price fallback chain** than `AppServiceProvider` and `CartController`: `($item->variant->offer_price ?? $item->product->product_price ?? 0)`. `products.product_price` **does not exist** in the schema (verified). All three price paths are therefore `offer_price ?? null ?? 0`.<br>5. `UserAddress::country()` (`app/Models/UserAddress.php:40-43`) is `belongsTo(Country::class, 'area_id')` — it joins a country by an **area** ID. Broken relationship; no code currently calls it.<br>6. **Verified null-dereference:** `storage/logs/laravel.log` (2026-08-17, 2 occurrences) `Attempt to read property "name" on null (View: …\pages\myaccount.blade.php)`.<br>7. Email uniqueness is enforced by validation using the **current** user's ID (`ProfileController.php:20`) — correct. |
| **Missing** | No "change password" email confirmation. No account deletion. No profile avatar upload (`users.profile_image` column exists, unused). No order cancellation from the account page. No invoice download. |

---

## M9 — Checkout, Payment & Shipping — **THE BROKEN MODULE**

| Aspect | Detail |
|---|---|
| **Purpose** | Address selection/CRUD, live courier-rate lookup, Razorpay payment, order persistence across 6 tables, and Shiprocket fulfilment. |
| **Files** | `app/Http/Controllers/CheckoutController.php` (701 lines), `app/Services/ShiprocketService.php` (181 lines), `config/services.php`, `resources/views/pages/checkout.blade.php` (872 lines), `resources/views/pages/thankyou.blade.php` |
| **Routes (all inside `middleware('auth')`, `routes/web.php:60-75`)** | `GET /checkout` → `index`<br>`POST /checkout/prepare-payment` → `preparePayment`<br>`POST /checkout/get-couriers` → `getShiprocketCouriers`<br>`POST /checkout/get-shipping-charge` → `getShippingCharge` — **never called by any view**<br>`POST /checkout/get-cities` → `getCitiesByState` — **never called by any view**<br>`POST /checkout/place-order` → `placeOrder`<br>`GET /checkout/thankyou/{order}` → `thankyou`<br>`POST /checkout/address` → `storeAddress`<br>`PUT /checkout/address/{address}` → `updateAddress`<br>`DELETE /checkout/address/{address}` → `destroyAddress` |
| **Tables written** | `product_orders`, `product_order_user_addresses`, `product_transaction_logs`, `product_tracking`, `product_order_items`, `product_slots`, `productstocks`, `product_varient`, `carts` (deleted), `user_addresses` |
| **Tables read** | `carts`, `products`, `product_varient`, `user_addresses`, `states`, `districts` |
| **Business rules** | BL-CHK-01 … BL-CHK-14 in [08-business-logic.md](08-business-logic.md) |
| **Status** | **BROKEN — VERIFIED BY RUNTIME LOG** |

### Confirmed failures

| Evidence | Detail |
|---|---|
| `storage/logs/laravel.log` 2026-08-17 05:12:55 | `Order creation failed in DB transaction {"error":"SQLSTATE[42S22]: Column not found: 1054 Unknown column 'billing_state' in 'field list' (… SQL: insert into `product_orders` (…, `billing_state`, …)"}` — the **entire order insert fails and rolls back**. |
| `storage/logs/laravel.log` 2026-08-17 | `Order creation failed in DB transaction {"error":"SQLSTATE[42S22]: Column not found: 1054 Unknown column 'product_image' in 'field list' …}` — the **second** missing column, on `product_order_items`. Even after `billing_state` was fixed, order placement still fails. |
| Both errors are logged **after** `razorpay_payment_id` / `razorpay_order_id` were accepted and `payment_status = 'Paid'` | ⇒ **The customer was charged and no order was created.** See SEC-002 / BUG-001. |
| `palVarnanDashboard/database/migrations/*.php` (57 files, full grep) | **No migration anywhere creates `product_orders.billing_state`, `product_orders.shipping_state`, or `product_order_items.product_image`.** |
| `../palvarnan_db (2).sql` (2026-07-31 dump, 93 tables) | `product_orders` has no `billing_state` / `shipping_state`; `product_order_items` has no `product_image`. |
| `storage/logs/laravel.log` 2026-08-17 05:18 | `Creating Shiprocket order {"order_id":"ORD-6A82999B299AB", …}` — a Shiprocket order **was** created later the same day, proving the developer manually `ALTER`ed the live DB in between. Those manual changes exist **only on that one machine** and are not reproducible from the repository. |

### Other confirmed issues in this module

| # | Issue | Location |
|---|---|---|
| 1 | **Client-supplied totals.** `shipping_charge` and `coupon_discount` are taken from the POST body with `floatval()` and never recomputed server-side. | `:286-288` |
| 2 | **Razorpay SDK autoload hack.** Lines 26-31 `require_once base_path('vendor/razorpay/razorpay/Razorpay.php')` if the class is missing. Log shows `Class "Razorpay\Api\Api" not found` twice on 2026-08-17, so this was added reactively. It is a workaround for a broken Composer install, not a fix. | `:26-31` |
| 3 | **Raw exception message leaked to the browser.** `'Shiprocket Service Error: ' . $e->getMessage()` (`:164`) and `'Payment initiation failed: ' . $e->getMessage()` (`:218`) and `'Order failed to save: ' . $e->getMessage()` (`:429`) — all three disclose internals. | `:164, :218, :429` |
| 4 | **Full order payload logged** including customer name, email, phone, address. | `:497` |
| 5 | **First/last name splitting is naive.** `explode(' ', trim($shipping_name))` + `array_pop` means "Sriram P" → first="Sriram", last="P", but "Paul's International Kitchen" → first="Paul's International", last="Kitchen". | `:465-467` |
| 6 | **HSN code hard-coded to `441122`** for every product. | `:450` |
| 7 | **Weight unit heuristic is wrong.** `if ($totalWeight > 10) { $totalWeight /= 1000; }` (`getShiprocketCouriers` `:107-110`) and a separate `max($totalWeight, 0.5)` in `placeOrder` (`:460`). Two different floors (0.1 vs 0.5) and inconsistent unit conversion between the quote and the order. | `:107-110, :460` |
| 8 | **Coupon is not implemented.** `Coupon` is imported (`:6`) but never queried. `hidden_coupon_discount` is hard-coded `0` (`checkout.blade.php:473`). | `:6`, blade `:473` |
| 9 | **`getShippingCharge` / `getCitiesByState` are dead endpoints** — implemented and routed but no view calls them. | `:638-670` |
| 10 | **`thankyou` is unreachable in practice** because `placeOrder` fails. | `:540` |
| 11 | **No order cancellation, refund, or return flow** in the storefront, though `product_refunds`, `cancel_requests`, `product_slots.is_cancelled` and `product_tracking.return_requested` all exist in the schema. | — |
| 12 | **No stock check before order placement.** Stock is decremented with `max($x - qty, 0)`, so an oversell silently records 0 stock rather than failing. | `:403-408` |

---

## Cross-module dependencies (verified)

```
Country selection (M1)  ──► gates ──►  Home (M3), Product listing (M4), Product detail (M4)
Authentication (M2)    ──► gates ──►  Cart subtotal source, Checkout (M9), Account (M8)
Cart (M6)              ──► feeds ──►  Checkout (M9) totals, header badge, Account (M8) tab
Products (M4)          ──► feeds ──►  Cart (M6), Wishlist (M7), Checkout (M9)
ShiprocketService      ──► feeds ──►  Checkout (M9) courier rates + order creation
palVarnanDashboard     ──► owns  ──►  ALL tables; reads product_orders / product_slots
```

**The single most fragile coupling:** M9 → `palVarnanDashboard`'s schema. The storefront writes 6 tables that the dashboard reads, using an order-ID convention the dashboard also depends on, with a `status` vocabulary the dashboard does not recognise.

---

## Related documents

- [04-feature-inventory.md](04-feature-inventory.md) — every feature, verified not assumed
- [05-user-flows.md](05-user-flows.md) — end-to-end journeys
- [06-api-and-routes.md](06-api-and-routes.md) — endpoint reference
- [08-business-logic.md](08-business-logic.md) — all business rules (BL-*)
