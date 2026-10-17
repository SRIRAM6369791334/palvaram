# PROJECT_OVERVIEW.md

**Purpose:** What the `palVarnan` storefront is, its stack, integrations, user roles, and highest-level flow.

---

## 1. What this application is

`palVarnan` is a **server-rendered Laravel 12 e-commerce storefront** (customer-facing web shop). It allows
a visitor to browse products, search, maintain a cart and wishlist, register/login, manage an address
book, and place orders via **Cash on Delivery (COD)** or **Online Payment (Razorpay)**. Orders created
in the storefront are forwarded to **Shiprocket** for fulfilment.

- Root: [`C:\xampp\htdocs\pauls\palVarnan`](../audit/VERIFIED_FACTS.md:11) — `VERIFIED_FROM_SOURCE`
- Related index: [`../01-project-inventory.md`](../01-project-inventory.md) — link to inventory
- Flow detail: [`../05-user-flows.md`](../05-user-flows.md)

---

## 2. Stack

| Component | Version/Description | Evidence |
|---|---|---|
| Framework | Laravel **^12.0** | `VERIFIED_FROM_SOURCE` — [`composer.json:10`](../../composer.json#L10) |
| PHP | **^8.2** | `VERIFIED_FROM_SOURCE` — [`composer.json:9`](../../composer.json#L9) |
| Database | MySQL (`DB_CONNECTION=mysql`, `DB_DATABASE=palvarnan_db`) | `VERIFIED_FROM_SOURCE` — [`config/database.php:19`](../../config/database.php#L19), [`config/database.php:46`](../../config/database.php#L46); [`.env`](../../.env) keys `DB_*` |
| Templating | Blade | `VERIFIED_FROM_SOURCE` — `resources/views/**/*.blade.php` |
| Frontend | Vendored **MarketPro** HTML theme; jQuery + inline `fetch()` calls; **Vite 7 + Tailwind CSS 4** configured but largely unused | `VERIFIED_FROM_SOURCE` — [`package.json:10,14,15`](../../package.json); [`vite.config.js`](../../vite.config.js); [`resources/views/layouts/app.blade.php`](../../resources/views/layouts/app.blade.php); [`../01-project-inventory.md:17-18`](../01-project-inventory.md#1-what-kind-of-project-is-this) |
| Auth | Laravel session auth (cookies) | `VERIFIED_FROM_SOURCE` — [`config/auth.php`](../../config/auth.php); [`AuthController.php`](../../app/Http/Controllers/AuthController.php) |
| Session/Cache/Queue | Session `file` in current `.env`; Cache `file`; Queue **`sync`** in current `.env` (matches `VERIFIED_FACTS`) | `VERIFIED_FROM_SOURCE` — [`.env`](../../.env): `SESSION_DRIVER=file`, `CACHE_STORE=file`, `QUEUE_CONNECTION=sync` |
| Container/runtime | Laravel 12 app bootstrap | `VERIFIED_FROM_SOURCE` — [`bootstrap/app.php`](../../bootstrap/app.php) |

> Existing audit states cache/session/queue are all `database` driver in some `.env` snapshot
> ([`../01-project-inventory.md:20`](../01-project-inventory.md#1-what-kind-of-project-is-this)). **This is `CONFLICTING_EVIDENCE`**:
> current `.env` has `SESSION_DRIVER=file`, `CACHE_STORE=file`, `QUEUE_CONNECTION=sync`. The reference adopts
> current `.env` truth (`VERIFIED_FROM_SOURCE`).

---

## 3. Integrations

| Integration | Purpose | How used | Evidence |
|---|---|---|---|
| **Razorpay** | Online payments | `razorpay/razorpay: ^2.9` in `composer.json:12` and `composer.lock` (`VERIFIED_FROM_SOURCE`). `CheckoutController::preparePayment()` creates order via `new Api(key,secret)` ([`CheckoutController.php:172-220`](../../app/Http/Controllers/CheckoutController.php#L172-L220)); `placeOrder()` verifies signature ([`CheckoutController.php:252-268`](../../app/Http/Controllers/CheckoutController.php#L252-L268)). Also includes a conditional `require_once` for autoload fallback ([`CheckoutController.php:26-31`](../../app/Http/Controllers/CheckoutController.php#L26-L31)). | `VERIFIED_FROM_SOURCE` |
| **Shiprocket** | Fulfilment (courier serviceability, order creation, AWB assignment) | `App\Services\ShiprocketService` ([`app/Services/ShiprocketService.php`](../../app/Services/ShiprocketService.php)) called from checkout: serviceability ([`CheckoutController.php:89-167`](../../app/Http/Controllers/CheckoutController.php#L89-L167)), create order + optional AWB ([`CheckoutController.php:433-527`](../../app/Http/Controllers/CheckoutController.php#L433-L527)). Credentials via `config/services.php:43-48` ([`config/services.php`](../../config/services.php)). | `VERIFIED_FROM_SOURCE` |

> Correction to earlier audit: Razorpay **is** present in `composer.lock`. `CONFLICTING_EVIDENCE` vs
> [`../README.md:17`](../README.md#1-headline-findings).

---

## 4. User roles

The application supports two main customer roles in practice:

| Role | Description | Evidence |
|---|---|---|
| **Guest** | Unauthenticated visitor. Can browse, search, add to cart/wishlist by identity (IP-based: `ip_addresscart`, and `guest_token` cookie used in view composer). No server-side ownership checks on some cart endpoints. | `VERIFIED_FROM_SOURCE` — [`CartController.php:11-31`](../../app/Http/Controllers/CartController.php#L11-L31), [`AppServiceProvider.php:54-72`](../../app/Providers/AppServiceProvider.php#L54-L72); [`../04-feature-inventory.md`](../04-feature-inventory.md) |
| **Registered customer (authenticated user)** | Authenticated via session. Can update profile, manage address book, access `myaccount`, place orders. Routes behind `auth` middleware include checkout and address endpoints ([`routes/web.php:71-83`](../../routes/web.php#L71-L83)). | `VERIFIED_FROM_SOURCE` — [`AuthController.php:46-72`](../../app/Http/Controllers/AuthController.php#L46-L72), [`routes/web.php`](../../routes/web.php) |

**No other roles** are defined in the storefront codebase. `INFERRED` from code.

---

## 5. Highest-level flow

The canonical flow (as implemented):

1. **Select country** — [`CountryController@index/store`](../../app/Http/Controllers/CountryController.php) sets `session('country_id')`. Home forces redirect if unset ([`HomeController.php:13-15`](../../app/Http/Controllers/HomeController.php#L13-L15)).
2. **Browse** — [`HomeController@index`](../../app/Http/Controllers/HomeController.php#L11-L27) loads banners and categories; [`ProductController@index`](../../app/Http/Controllers/ProductController.php#L16-L108) lists products with filters.
3. **Search** — type-ahead via [`/search`](../../routes/web.php#L32) ([`SearchController@search`](../../app/Http/Controllers/SearchController.php#L13-L91)).
4. **Product detail** — [`/product/{slug}`](../../routes/web.php#L65) ([`ProductController@show`](../../app/Http/Controllers/ProductController.php#L110-L178)).
5. **Cart** — add/update/remove via [`CartController`](../../app/Http/Controllers/CartController.php). Identity is user_id (auth) or `ip_addresscart`/guest token (guest).
6. **Wishlist** — toggle/check via [`WishlistController`](../../app/Http/Controllers/WishlistController.php); remove scoped by `user_id` ([`WishlistController.php:50-54`](../../app/Http/Controllers/WishlistController.php#L50-L54)).
7. **Authentication** — [`/register`](../../routes/web.php#L18), [`/login`](../../routes/web.php#L17), register/login POSTs ([`AuthController`](../../app/Http/Controllers/AuthController.php)). **Password reset direct**: [`/reset-password-direct`](../../routes/web.php#L38) — **no token** (critical) ([`AuthController::resetPassword:100-126`](../../app/Http/Controllers/AuthController.php#L100-L126)).
8. **Checkout (auth required)** — [`/checkout`](../../routes/web.php#L72). Builds cart/address/state ([`CheckoutController@index`](../../app/Http/Controllers/CheckoutController.php#L38-L84)). For address management, ownership enforced ([`updateAddress/destroyAddress`](../../app/Http/Controllers/CheckoutController.php#L594-L633)).
9. **Courier selection** — AJAX [`/checkout/get-couriers`](../../routes/web.php#L74) calls Shiprocket serviceability ([`CheckoutController@getShiprocketCouriers`](../../app/Http/Controllers/CheckoutController.php#L89-L167)).
10. **Payment preparation** — AJAX [`/checkout/prepare-payment`](../../routes/web.php#L73) creates Razorpay order ([`CheckoutController::preparePayment`](../../app/Http/Controllers/CheckoutController.php#L172-L220)).
11. **Place order** — POST [`/checkout/place-order`](../../routes/web.php#L77). Verifies Razorpay signature (online), writes multi-table order (transaction `DB::beginTransaction()` → commit at [`421`](../../app/Http/Controllers/CheckoutController.php#L421)), reduces stock, clears cart for user, then creates Shiprocket order and may assign AWB ([`CheckoutController::placeOrder`](../../app/Http/Controllers/CheckoutController.php#L225-L535)). **CRITICAL:** `shipping_charge` and `coupon_discount` are client-supplied ([`CheckoutController.php:286-288`](../../app/Http/Controllers/CheckoutController.php#L286-L288)) → price-tampering risk.
12. **Thank you** — [`/checkout/thankyou/{order}`](../../routes/web.php#L78) scoped to `user_id` ([`CheckoutController::thankyou`](../../app/Http/Controllers/CheckoutController.php#L540-L548)).

**Controllers backing the flow:** `CountryController`, `HomeController`, `ProductController`, `SearchController`, `CartController`, `WishlistController`, `AuthController`, `ProfileController`, `CheckoutController`. (`Controller` is base only.) `VERIFIED_FROM_SOURCE`.

---

## 6. Related

- [`../04-feature-inventory.md`](../04-feature-inventory.md) — feature statuses
- [`../05-user-flows.md`](../05-user-flows.md) — detailed flows
- [`../11-models-and-relationships.md`](../11-models-and-relationships.md)
- [`../12-services-and-integrations.md`](../12-services-and-integrations.md)