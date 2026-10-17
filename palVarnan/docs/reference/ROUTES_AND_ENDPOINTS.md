# ROUTES_AND_ENDPOINTS.md

**Purpose:** The 45 routes, middleware coverage, and request/response shapes.

---

## 1. Route inventory (45 entries) — `routes/web.php`

Total `Route::` entries: **45** `VERIFIED_FROM_SOURCE` ([`routes/web.php`](../../routes/web.php)).

| Method | URI | Name/Action | Middleware | Notes |
|---|---|---|---|---|
| GET | `/select-country` | `CountryController@index` | none | Country selection view (`routes/web.php:14`) |
| POST | `/select-country` | `CountryController@store` | none | Store selection (`routes/web.php:15`) |
| GET | `/login` | view `pages.login` (`name('login')`) | none | View only (`routes/web.php:17`) |
| GET | `/register` | view `pages.register` (`name('register')`) | none | View only (`routes/web.php:18`) |
| GET | `/about` | view `pages.about` (`name('about')`) | none | Static (`routes/web.php:19`) |
| GET | `/contact` | view `pages.contact` (`name('contact')`) | none | Static (`routes/web.php:20`) |
| GET | `/product` | view `pages.product-detail` | none | No controller data; no slug (`routes/web.php:21`) |
| GET | `/privacy-policy` | view `pages.privacy-policy` (`name('privacy.policy')`) | none | Static (`routes/web.php:24`) |
| GET | `/terms-conditions` | view `pages.terms-conditions` (`name('terms.conditions')`) | none | Static (`routes/web.php:25`) |
| GET | `/return-refund-policy` | view `pages.return-refund-policy` (`name('refund.policy')`) | none | Static (`routes/web.php:26`) |
| GET | `/shipping-policy` | view `pages.shipping-policy` (`name('shipping.policy')`) | none | Static (`routes/web.php:27`) |
| GET | `/` | `HomeController@index` | none | Country-gated redirect if unset (`routes/web.php:30`) |
| GET | `/search` | `SearchController@search` | none | Returns JSON (`routes/web.php:32`) |
| GET | `/products` | `ProductController@index` | none | Catalog listing (`routes/web.php:33`) |
| POST | `/register-store` | `AuthController@registerStore` (`name('register.store')`) | none | Registration (`routes/web.php:35`) |
| POST | `/login-store` | `AuthController@login` (`name('login.store')`) | none | Login (JSON) (`routes/web.php:36`) |
| POST | `/forgot-password` | `AuthController@forgotPassword` (`name('password.email')`) | none | Send reset link (JSON) (`routes/web.php:37`) |
| POST | `/reset-password-direct` | `AuthController@resetPassword` (`name('password.reset.direct')`) | none | **No token** (critical) (`routes/web.php:38`) |
| PUT | `/profile-update` | `ProfileController@update` (`name('profile.update')`) | none* | Updates profile; auth checked in controller (`routes/web.php:40-41`) |
| POST | `/logout` | `ProfileController@logout` (`name('logout')`) | none | Logs out; preserves country_id (`routes/web.php:43`) |
| GET | `/cart` | `CartController@index` | none | Cart view (`routes/web.php:45`) |
| POST | `/cart-update` | `CartController@update` (`name('cart.update')`) | none | Bulk update (IDOR risk) (`routes/web.php:47-48`) |
| GET | `/cart-remove/{id}` | `CartController@remove` (`name('cart.remove')`) | none | Remove by id (IDOR, GET) (`routes/web.php:50-51`) |
| POST | `/cart/store` | `CartController@store` (`name('cart.store')`) | none | Add to cart (`routes/web.php:54-55`) |
| POST | `/cart/ajax-update` | `CartController@ajaxUpdate` (`name('cart.ajaxUpdate')`) | none | AJAX scoped update (`routes/web.php:57`) |
| POST | `/cart/ajax-remove` | `CartController@ajaxRemove` (`name('cart.ajaxRemove')`) | none | AJAX scoped remove (`routes/web.php:58`) |
| POST | `/wishlist/toggle` | `WishlistController@toggle` (`name('wishlist.toggle')`) | none | Toggle (`routes/web.php:60`) |
| GET | `/wishlist` | `WishlistController@index` (`name('wishlist.index')`) | none | Wishlist view (`routes/web.php:61`) |
| DELETE | `/wishlist/{id}/remove` | `WishlistController@remove` (`name('wishlist.remove')`) | none | Remove (scoped to user_id) (`routes/web.php:62`) |
| GET | `/wishlist/check` | `WishlistController@check` | `auth` | Check existence (`routes/web.php:63`) |
| GET | `/wishlist/check-product` | `WishlistController@checkProduct` | `auth` | Variant check (`routes/web.php:64`) |
| GET | `/product/{slug}` | `ProductController@show` | none | Product detail (`routes/web.php:65`) |
| GET | `/cart/subtotal` | `CartController@subtotal` | none | AJAX subtotal (`routes/web.php:66`) |
| GET | `/myaccount` | `ProfileController@index` (`name('account.index')`) | none | Redirects if not auth (controller) (`routes/web.php:68`) |

**Auth middleware group** ([`routes/web.php:71-83`](../../routes/web.php#L71-L83)):

| Method | URI | Action/Name | Notes |
|---|---|---|---|
| GET | `/checkout` | `CheckoutController@index` (`name('checkout.index')`) | Build cart/address/state (`routes/web.php:72`) |
| POST | `/checkout/prepare-payment` | `CheckoutController@preparePayment` (`name('checkout.preparePayment')`) | Create Razorpay order (`routes/web.php:73`) |
| POST | `/checkout/get-couriers` | `CheckoutController@getShiprocketCouriers` (`name('checkout.getCouriers')`) | Serviceability (`routes/web.php:74`) |
| POST | `/checkout/get-shipping-charge` | `CheckoutController@getShippingCharge` (`name('checkout.getShippingCharge')`) | State shipping (`routes/web.php:75`) |
| POST | `/checkout/get-cities` | `CheckoutController@getCitiesByState` (`name('checkout.getCities')`) | Districts by state (`routes/web.php:76`) |
| POST | `/checkout/place-order` | `CheckoutController@placeOrder` (`name('checkout.placeOrder')`) | Place order + Shiprocket (`routes/web.php:77`) |
| GET | `/checkout/thankyou/{order}` | `CheckoutController@thankyou` (`name('checkout.thankyou')`) | Confirmation (scoped) (`routes/web.php:78`) |
| POST | `/checkout/address` | `CheckoutController@storeAddress` (`name('address.store')`) | Store address (`routes/web.php:80`) |
| PUT | `/checkout/address/{address}` | `CheckoutController@updateAddress` (`name('address.update')`) | Update (ownership enforced) (`routes/web.php:81`) |
| DELETE | `/checkout/address/{address}` | `CheckoutController@destroyAddress` (`name('address.destroy')`) | Delete (ownership enforced) (`routes/web.php:82`) |

**Middleware notes:** Laravel 12 bootstraps middleware via [`bootstrap/app.php`](../../bootstrap/app.php) (sets `trustProxies`). No global `auth` middleware. Route-level `auth` applied only to wishlist/check, wishlist/check-product, and the entire checkout group. `VERIFIED_FROM_SOURCE`.

**Request/response shapes (typical):**
- Search returns JSON array. Login/register/password flows return JSON (see [`AuthController`](../../app/Http/Controllers/AuthController.php)). Checkout AJAX endpoints return JSON. `VERIFIED_FROM_SOURCE`.

---

## 2. Related

- [`../06-api-and-routes.md`](../06-api-and-routes.md)
- [`CONTROLLERS.md`](./CONTROLLERS.md)
- [`FEATURE_CATALOG.md`](./FEATURE_CATALOG.md)