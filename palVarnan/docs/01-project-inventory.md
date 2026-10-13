# 01 — Project Inventory

> **Scope of this audit:** `C:\xampp\htdocs\pauls\palVarnan`
> **Audit date:** 2026-10-01
> **Method:** Static read of all source files + read of the 27 MB database dump `../palvarnan_db (2).sql` + read of the runtime log `storage/logs/laravel.log`.

---

## 1. What kind of project is this?

| Property | Value | Evidence |
|---|---|---|
| Project type | E-commerce **storefront** (customer-facing web shop) | `routes/web.php`, `resources/views/pages/*` |
| Framework | **Laravel 12** | `composer.json` → `"laravel/framework": "^12.0"` |
| Language | **PHP ^8.2** (dev machine runs PHP 8.2.12 ZTS x64) | `composer.json`, `php -v` |
| Template engine | **Blade** | `resources/views/**/*.blade.php` |
| Frontend | **Server-rendered Blade + jQuery**, *not* a SPA | `layouts/app.blade.php:78,112` |
| Asset bundler | **Vite 7 + Tailwind CSS 4** declared, but effectively unused | `vite.config.js`, `package.json` |
| Database | **MySQL** (`palvarnan_db`) on `127.0.0.1` | `.env` `DB_CONNECTION=mysql`, `test_services.php` |
| Cache / Session / Queue | All set to `database` driver | `.env` |
| Payments | **Razorpay** (India) | `razorpay/razorpay` in `composer.json`, `CheckoutController` |
| Shipping | **Shiprocket** API v2 | `app/Services/ShiprocketService.php` |
| Auth | Laravel **session/cookie** auth, no API tokens | `config/auth.php` (default), `AuthController` |

### The single most important structural fact

This project is **NOT self-contained**. It is one half of a two-application system:

```
C:\xampp\htdocs\pauls\
├── palVarnanDashboard/   ← ADMIN app. Owns ALL 57 schema migrations.
├── palVarnan/            ← THIS PROJECT. Storefront. Owns 3 Laravel default migrations only.
└── palvarnan_db (2).sql  ← 27 MB MySQL dump of the SHARED database
```

**Both applications connect to the same MySQL database** (`DB_DATABASE` length 12 = `palvarnan_db`, `DB_HOST=127.0.0.1`, `DB_USERNAME=root`, empty password in both `.env` files).

The storefront has **no migrations for its own domain tables**. Every `products`, `product_orders`, `carts`, `wishlists`, `productstocks` … table is created by `palVarnanDashboard/database/migrations/*`. See [07-database.md](07-database-schema.md and [17-devops-deployment.md](17-devops-deployment.md).

---

## 2. Directory map (non-vendor)

```
palVarnan/
├── app/
│   ├── Http/Controllers/          9 controllers (8 concrete + 1 abstract)
│   ├── Models/                    23 Eloquent models
│   ├── Providers/AppServiceProvider.php
│   └── Services/ShiprocketService.php
├── bootstrap/
│   ├── app.php                    Laravel 12 bootstrap (routing/middleware/exception config)
│   ├── providers.php
│   └── cache/                     GENERATED (packages.php, services.php)
├── config/                        13 Laravel default config files — 1 modified
├── database/
│   ├── migrations/                3 Laravel default migrations ONLY
│   ├── factories/UserFactory.php  Laravel default
│   ├── seeders/DatabaseSeeder.php Laravel default
│   └── database.sqlite            0 bytes, unused
├── public/
│   ├── index.php                  Laravel front controller
│   ├── .htaccess                  Laravel Apache rewrite
│   ├── favicon.ico                0 bytes (BROKEN)
│   ├── robots.txt                 24 bytes
│   └── assets/                    MarketPro HTML template assets (css/js/images/webfonts/sass)
├── resources/
│   ├── css/app.css, js/app.js, js/bootstrap.js   Laravel default, UNUSED
│   └── views/
│       ├── layouts/               app, header, footer, country-modal
│       ├── pages/                 11 page templates
│       ├── country-selection.blade.php
│       └── welcome.blade.php      DEAD — Laravel default welcome page
├── routes/web.php                 40 route definitions
├── routes/console.php             Laravel default `inspire` command
├── storage/                       sessions + cache + views + logs (all GENERATED)
├── tests/                         2 default example tests, 0 real tests
├── test_apis.php                  ⚠ ROOT-LEVEL SCRIPT WITH LIVE CREDENTIALS
├── test_services.php              ⚠ ROOT-LEVEL SCRIPT WITH LIVE CREDENTIALS
├── README.md                      Laravel default README — NOT project documentation
├── composer.json / composer.lock
├── package.json / vite.config.js
├── phpunit.xml
├── .env / .env.example
└── artisan
```

**Notably absent (this is a storefront, not a monorepo):**
No `app/Http/Middleware/` (folder does not exist), no `app/Http/Requests/`, no `app/Http/Resources/`, no `app/Policies/`, no `app/Jobs/`, no `app/Events/`, no `app/Console/Commands/`, no `app/Mail/`, no `app/Notifications/`, no `app/Exceptions/`, no `app/Http/Kernel.php` (Laravel 12 style), no `resources/js/components/`, no `resources/views/components/`, no `.github/`, no `Dockerfile`, no `docker-compose.yml`, no CI config, no `nginx.conf`, no cron config.

---

## 3. Complete PHP file catalog

Every non-vendor, non-generated PHP file, with lines of code.

### 3.1 Controllers — `app/Http/Controllers/`

| File | LOC | Type | Purpose | Status | Risk |
|---|---|---|---|---|---|
| `Controller.php` | 6 | abstract base | Empty abstract class; all controllers extend it. Adds nothing. | Complete (trivial) | Low |
| `HomeController.php` | 21 | Controller | Loads home banners + categories for the selected country. | Complete | Low |
| `CountryController.php` | 22 | Controller | Country picker; writes `country_id` to session. | Complete | Low |
| `AuthController.php` | 98 | Controller | Register, login, forgot-password, reset-password. | **Partially complete** — email reset link is sent but there is **no reset-form page**; `resetPassword()` accepts a bare email + new password with **no token verification**. | **Critical** |
| `ProductController.php` | 105 | Controller | Product listing (filters + pagination) and product detail page. | Complete | Medium |
| `CartController.php` | 114 | Controller | Cart page, add/update/remove, AJAX variants, subtotal. | Complete | **Critical** (IDOR) |
| `WishlistController.php` | 71 | Controller | Wishlist toggle/index/remove/check. | Partially complete | **High** (IDOR + no auth) |
| `ProfileController.php` | 69 | Controller | Update profile/password, logout, my-account page. | Complete | High |
| `CheckoutController.php` | 608 | Controller | Address CRUD, courier lookup, Razorpay prep, **order placement**, Shiprocket push. | **BROKEN** — verified failure, see [20-known-issues.md](20-known-issues.md) | **Critical** |

### 3.2 Models — `app/Models/`

| File | LOC | Table | Status | Risk |
|---|---|---|---|---|
| `User.php` | 38 | `users` | Complete | Low |
| `Product.php` | 27 | `products` | Complete (SoftDeletes enabled) | Low |
| `ProductVarient.php` | 21 | `product_varient` (sic) | Complete | Medium — misspelling is load-bearing |
| `Category.php` | 25 | `categories` | Complete | Low |
| `SubCategory.php` | 11 | `sub_categories` | **BROKEN** — `belongsTo(Category::class,'category_id')` and `Category::subCategories()` reference a **non-existent** `category_id` column | High |
| `Brand.php` | 20 | `brands` | Complete | Low |
| `Cart.php` | 21 | `carts` | Complete | Medium — no ownership scoping |
| `Wishlist.php` | 19 | `wishlists` | Complete | Medium — no ownership scoping |
| `Country.php` | 8 | `countries` | Complete | Low |
| `State.php` | 18 | `states` | Complete | Low |
| `District.php` | 14 | `districts` | Complete | Low |
| `Unit.php` | 7 | `units` | Complete (empty model, works by convention) | Low |
| `Area.php` | 14 | `areas` | Complete | Low |
| `AreaAssign.php` | 14 | `area_assigns` | **Dead code** — 0 references outside itself | Low |
| `UserAddress.php` | 43 | `user_addresses` | **BROKEN relationship** — `country()` does `belongsTo(Country::class,'area_id')` (points at the wrong FK) | Medium |
| `Coupon.php` | 18 | `coupons` | **Dead code** — never queried; UI buttons have no handler | Medium |
| `HomePromotion.php` | 7 | `home_promotions` | Complete (empty model) | Low |
| `ProductOrder.php` | 87 | `product_orders` | Partially complete — `$fillable` lists columns that no migration creates | **Critical** |
| `ProductOrderItem.php` | 30 | `product_order_items` | **BROKEN** — `$fillable` includes `product_image`, which no migration creates | **Critical** |
| `ProductOrderUserAddress.php` | 34 | `product_order_user_addresses` | Complete | Low |
| `ProductSlot.php` | 43 | `product_slots` | Complete | Low |
| `ProductStock.php` | 32 | `productstocks` | Complete | Low |
| `ProductTracking.php` | 15 | `product_tracking` | Complete | Low |
| `ProductTransactionLog.php` | 18 | `product_transaction_logs` | Complete | Low |

### 3.3 Services / Providers

| File | LOC | Purpose | Status | Risk |
|---|---|---|---|---|
| `app/Services/ShiprocketService.php` | 181 | 11 HTTP wrappers for the Shiprocket external API. Token cached 8 h via `Cache::remember`. | Complete (7 of 11 methods unused) | Medium |
| `app/Providers/AppServiceProvider.php` | 58 | `View::composer('*')` injects `cartCount`, `wishlistCount`, `cartSubtotal` into **every** view. | Complete but **expensive** | **High** (perf) |

### 3.4 Routes

| File | LOC | Purpose | Status | Risk |
|---|---|---|---|---|
| `routes/web.php` | 55 | 40 routes: 13 GET pages, 8 POST/PUT/DELETE mutating, 12 AJAX JSON endpoints, 1 health check. | Complete | High — see [06-api-and-routes.md](06-api-and-routes.md) |
| `routes/console.php` | 6 | Default `inspire` command. | Complete (unused) | Low |

### 3.5 Config — `config/`

| File | Modified? | Notes |
|---|---|---|
| `services.php` | **YES** | Added `razorpay` and `shiprocket` blocks. `shiprocket.base_url` is **missing** even though `ShiprocketService` reads it. |
| `app.php`, `auth.php`, `cache.php`, `database.php`, `filesystems.php`, `logging.php`, `mail.php`, `queue.php`, `session.php` | No | Unmodified Laravel 12 defaults |
| Others (`broadcasting.php`, `cors.php`, `hashing.php`, `view.php`, …) | — | Present in `vendor/laravel/framework` defaults |

### 3.6 Database / Tests / Root scripts

| File | LOC | Purpose | Status | Risk |
|---|---|---|---|---|
| `database/migrations/0001_01_01_000000_create_users_table.php` | 44 | Default Laravel `users` + `password_reset_tokens` + `sessions` | Complete | Medium — `users` here ≠ the real `users` table |
| `database/migrations/0001_01_01_000001_create_cache_table.php` | 31 | Default | Complete | Low |
| `database/migrations/0001_01_01_000002_create_jobs_table.php` | 52 | Default | Complete | Low |
| `database/seeders/DatabaseSeeder.php` | 20 | Creates one `Test User` | Default | Low |
| `database/factories/UserFactory.php` | 39 | Default; **references `State::class`** | Default but coupled | Medium |
| `tests/Unit/ExampleTest.php` | 13 | `assertTrue(true)` | **Placeholder** | Low |
| `tests/Feature/ExampleTest.php` | 15 | `GET /` expects 200 | **BROKEN by design** — `/` 302-redirects to `/select-country` without a session | Medium |
| `test_apis.php` | 131 | Manual Razorpay + Shiprocket connectivity script | **BROKEN / DANGEROUS** — contains live credentials, disables TLS verification, **creates real orders** | **Critical** |
| `test_services.php` | 164 | Manual DB `ALTER TABLE` + API script | **BROKEN / DANGEROUS** — hard-codes MySQL root with blank password, contains live credentials, **mutates schema** | **Critical** |

### 3.7 Frontend views — `resources/views/`

| File | LOC | Purpose | Status | Risk |
|---|---|---|---|---|
| `layouts/app.blade.php` | 212 | HTML shell: assets, header, `@yield('main')`, footer, floating cart bar. | Complete | **High** — loads jQuery twice (L78 + L112) |
| `layouts/header.blade.php` | 832 | Site header, mega-menu, search box, cart/wishlist badges. | **BROKEN** — **85 links → `shop.html`** + 13 other `.html` links that do not exist | **High** |
| `layouts/footer.blade.php` | 140 | Footer, category links. | Partially complete — contains **US demo contact data** | Medium |
| `layouts/country-modal.blade.php` | 37 | Country-switch modal. | Complete | Low |
| `country-selection.blade.php` | 70 | Standalone country picker page. | Complete | Low |
| `pages/home.blade.php` | 1896 | Home page. | **INCOMPLETE** — only 4 Blade directives in 1896 lines; 50 Unsplash demo images, 29 template-author links, hard-coded demo prices `₹14.99 / ₹28.99 / ₹60.99` | **Critical** (product credibility) |
| `pages/products.blade.php` | 536 | Listing page with filters, pagination, AJAX add-to-cart, wishlist. | Complete | High |
| `pages/product-detail.blade.php` | 620 | Detail page: variants, AJAX cart/wishlist, related products. | Complete | High |
| `pages/cart.blade.php` | 251 | Cart page, quantity update, remove, **non-functional coupon UI**. | Partially complete | Medium |
| `pages/checkout.blade.php` | 793 | Address CRUD, courier picker, Razorpay modal, place order. | Partially complete — order submit reaches a **broken** backend | **Critical** |
| `pages/myaccount.blade.php` | 820 | Tabs: profile, addresses, wishlist, cart, orders. | Partially complete — `$variant->unit->short_name` null-derefs (verified in log) | High |
| `pages/login.blade.php` | 203 | Login + forgot/reset password forms (all AJAX). | Partially complete | **Critical** (see below) |
| `pages/register.blade.php` | 190 | Registration form. | Complete | Medium |
| `pages/wishlist.blade.php` | 174 | Wishlist page. | Complete | Medium |
| `pages/thankyou.blade.php` | 68 | Post-order confirmation. | Complete but **unreachable in practice** | High |
| `welcome.blade.php` | 270 | **DEAD** — Laravel default welcome page, referenced by no route | Low |

---

## 4. File status / risk roll-up

| Status | Count | Notable items |
|---|---|---|
| **BROKEN** | 6 files | `CheckoutController.php`, `ProductOrder.php`, `ProductOrderItem.php`, `SubCategory.php`, `UserAddress.php`, `header.blade.php` |
| **INCOMPLETE** | 8 | `home.blade.php`, `cart.blade.php`, `login.blade.php`, `myaccount.blade.php`, `checkout.blade.php`, `AuthController.php`, `examples tests`, coupon UI |
| **Dead code** | 4 | `Coupon.php`, `AreaAssign.php`, `welcome.blade.php`, 7 unused `ShiprocketService` methods |
| **Configuration** | 13 | `config/*.php` |
| **Generated** | 6 | `storage/**`, `bootstrap/cache/**`, `public/assets/**` (built SASS) |
| **Complete** | 21 | Most models, most pages, routes, most config |

### Risk roll-up

| Risk | Count | Highest-risk item |
|---|---|---|
| **Critical** | 6 | Hard-coded live Razorpay + Shiprocket credentials in `test_apis.php` / `test_services.php` (committed to git) |
| **High** | 11 | Order placement fails at `product_orders.billing_state`; `carts`/`wishlists` IDOR; `carts` unindexed |
| **Medium** | 12 | Password reset with no token; `env()` in Blade; double jQuery |
| **Low** | 8 | Dead models; unused config keys |

---

## 5. What is NOT in this repository (verified absent)

| Expected for a complete app | Present? | Consequence |
|---|---|---|
| Migrations for domain tables | **No** | Storefront cannot provision its own schema |
| Any real automated test | **No** | Zero regression safety |
| `.github/workflows/*` | **No** | No CI |
| `Dockerfile` / `docker-compose.yml` | **No** | No containerised environment |
| Deployment script | **No** | Deployment is manual |
| `AGENTS.md` / `CONTRIBUTING.md` | **No** | No onboarding docs (this `docs/` set is the first) |
| `app/Http/Middleware/` | **No** | No custom auth/rate-limit/throttle logic |
| `app/Policies/` | **No** | No authorisation layer at all |
| `resources/js/*.js` (custom) | **No** | All JS is inline `<script>` in Blade |
| Custom CSS | **No** | Only compiled MarketPro template CSS |

---

## 6. Related documents

- [02-architecture.md](02-architecture.md) — how requests actually flow
- [07-database.md](07-database-schema.md — the 93-table schema
- [11-dependencies.md](09-dependencies.md — Composer/npm analysis
- [13-security-audit.md](13-security-audit.md) — SEC-001 … SEC-0xx
- [19-incomplete-work.md](19-incomplete-work.md) — every unfinished item
- [30-project-status.md](30-project-status.md) — overall state
