# 10 — File Structure

Complete inventory of the repository. Counts and sizes verified on disk.

---

## 1. Root

| Path | Size | Purpose | Status |
|---|---|---|---|
| `artisan` | 425 B | Laravel CLI entry | Stock |
| `composer.json` | 2 886 B | PHP dependencies | **Modified** — Razorpay added without re-locking |
| `composer.lock` | 309 949 B | Lock file | **STALE** — missing `razorpay/razorpay` |
| `package.json` | 414 B | Frontend deps | Declared, **unused** |
| `vite.config.js` | 436 B | Vite config | **Unused** — no `@vite` in views |
| `phpunit.xml` | 1 284 B | Test config | Stock |
| `.env` | 1 450 B | Local environment | **`APP_DEBUG=true`**, `DB_USERNAME=root`, empty `DB_PASSWORD`, live payment creds. Gitignored |
| `.env.example` | 1 086 B | Env template | **Missing `MAIN_URL`, `RAZORPAY_*`, `SHIPROCKET_*`** |
| `README.md` | 3 911 B | Docs | **Stock Laravel README** — says nothing about this project |
| `.gitignore` | 283 B | Ignore rules | `/vendor`, `.env`, `*.log`, `/node_modules`, `/public/build` — reasonable |
| `test_apis.php` | 5 523 B | Live Razorpay/Shiprocket test script | **DANGEROUS — delete, rotate creds** |
| `test_services.php` | 7 028 B | Live DB DDL test script | **DANGEROUS — delete, rotate creds** |

---

## 2. `app/` — 39 files

```
app/
├── Http/Controllers/          10 files
│   ├── AuthController.php        3 085 B
│   ├── CartController.php        4 319 B
│   ├── CheckoutController.php   28 275 B   ← 25% of all app code
│   ├── Controller.php               77 B   (empty base class)
│   ├── CountryController.php        669 B
│   ├── HomeController.php           671 B
│   ├── ProductController.php      4 346 B
│   ├── ProfileController.php      2 637 B
│   ├── SearchController.php       2 509 B
│   └── WishlistController.php     2 774 B
├── Models/                    24 files
│   ├── Area.php                   391 B   ← no storefront code
│   ├── AreaAssign.php             410 B   ← no storefront code
│   ├── Brand.php                  522 B
│   ├── Cart.php                   486 B
│   ├── Category.php               661 B   ← broken relationship
│   ├── Country.php                190 B
│   ├── Coupon.php                 396 B   ← imported but never queried
│   ├── District.php               309 B   ← no storefront code
│   ├── HomePromotion.php          116 B
│   ├── Product.php              1 223 B
│   ├── ProductOrder.php         2 475 B
│   ├── ProductOrderItem.php       737 B   ← untracked
│   ├── ProductOrderUserAddress.php 928 B  ← untracked
│   ├── ProductSlot.php          1 097 B   ← untracked
│   ├── ProductStock.php           785 B   ← untracked
│   ├── ProductTracking.php        335 B   ← untracked
│   ├── ProductTransactionLog.php  436 B   ← untracked
│   ├── ProductVarient.php         934 B
│   ├── State.php                  419 B
│   ├── SubCategory.php            349 B   ← broken relationship
│   ├── Unit.php                   107 B
│   ├── User.php                 1 038 B
│   ├── UserAddress.php          1 104 B
│   └── Wishlist.php               487 B
├── Providers/
│   └── AppServiceProvider.php   2 096 B   ← View::composer('*') global cart query
└── Services/
    └── ShiprocketService.php    6 004 B   ← entire app/Services/ is untracked
```

### Structural observations

| Finding | Detail |
|---|---|
| **`app/Services/` is untracked** (`?? app/Services/`) | The entire service layer is not in version control |
| **6 order-related models are untracked** | `ProductOrder`, `ProductOrderItem`, `ProductOrderUserAddress`, `ProductSlot`, `ProductStock`, `ProductTracking`, `ProductTransactionLog` — **the entire checkout write path is uncommitted** |
| **No `app/Http/Middleware/`** | Laravel 12 allows this, but it means **zero custom middleware** |
| **No `app/Http/Requests/`** | All validation is inline in controllers |
| **No `app/Http/Resources/`, no `app/Policies/`, no `app/Repositories/`** | — |
| **No `app/Console/`, no `app/Jobs/`, no `app/Events/`, no `app/Listeners/`, no `app/Notifications/`, no `app/Mail/`** | No scheduled tasks, no queued work, no notifications |
| **`CheckoutController.php` is 28 KB** | One class handling page render, courier quoting, payment init, order persistence, stock mutation, address CRUD, and Shiprocket fulfilment. **The single largest maintainability risk in the codebase.** |
| **No interfaces, no DTOs, no value objects** | Everything is `array` + `floatval()` + `??` |

---

## 3. `resources/` — 17 files

```
resources/
├── css/app.css                   390 B   ← unused (no @vite)
├── js/
│   ├── app.js                     22 B   ← unused
│   └── bootstrap.js              127 B   ← unused
└── views/
    ├── country-selection.blade.php    3 397 B
    ├── welcome.blade.php             82 568 B   ← Laravel default, UNUSED (no route renders it)
    ├── layouts/
    │   ├── app.blade.php              9 090 B
    │   ├── country-modal.blade.php    2 307 B
    │   ├── footer.blade.php           7 853 B
    │   └── header.blade.php          41 981 B   ← 85 dead `shop.html` links
    └── pages/
        ├── cart.blade.php            13 676 B
        ├── checkout.blade.php        48 752 B   ← all checkout JS inline
        ├── home.blade.php           162 868 B   ← largest file in the project
        ├── login.blade.php            9 169 B
        ├── myaccount.blade.php       59 340 B
        ├── product-detail.blade.php  35 901 B
        ├── products.blade.php        30 089 B
        ├── register.blade.php         7 819 B
        ├── thankyou.blade.php         3 676 B   ← untracked
        └── wishlist.blade.php         9 118 B
```

### View findings

| Finding | Detail |
|---|---|
| **`home.blade.php` is 163 KB** | ~700 lines are MarketPro template demo: 50 Unsplash images, 29 `wowtheme7.com` links, hard-coded `₹14.99` / `₹28.99` / `₹60.99` |
| **`welcome.blade.php` is 83 KB of unused Laravel marketing page** | No route renders it — dead weight in git |
| **`header.blade.php` is 42 KB** | 101 dead links; inline jQuery for search, cart and country modal |
| **`checkout.blade.php` is 49 KB** | Razorpay SDK loaded from CDN, all checkout logic inline, `hidden_coupon_discount` hard-coded `0` |
| **No `@vite`, no component classes, no `@include`/`@extends` beyond `layouts.app`** | Every page is one giant monolith |
| **No partials/components** | `resources/views/components/` **does not exist** — no Blade components at all |
| **Zero `<x-` component usage** | — |

---

## 4. `public/` — 4 root files + 472 assets

| Path | Size | Finding |
|---|---|---|
| `public/.htaccess` | 740 B | Stock Laravel rewrite |
| `public/index.php` | 543 B | Stock |
| `public/robots.txt` | 24 B | Stock — no sitemap reference, no disallow rules |
| **`public/favicon.ico`** | **0 B** | **Empty file.** `app.blade.php:11` points at `/assets/images/logo/logo.png` instead |
| `public/assets/` | **9.4 MB, 472 files** | `css/`, `images/`, `js/`, `sass/`, `webfonts/` — the entire MarketPro template, committed to git |
| **`public/build/`** | **MISSING** | Vite output absent |
| **`public/storage`** | gitignored | No `storage:link` artefacts committed |

**Repo weight:** `public/assets` alone (9.4 MB) plus `home.blade.php` (163 KB) plus `welcome.blade.php` (83 KB) dominate the working tree. There is **no `node_modules`** and **no `public/build`**.

---

## 5. `config/` — 10 files, all stock

```
config/app.php         4 272 B   stock (timezone UTC, no custom providers)
config/auth.php        4 029 B   stock (guards via providers, no Sanctum)
config/cache.php       3 683 B   default store: env('CACHE_STORE', 'database')
config/database.php    6 979 B   stock
config/filesystems.php 2 532 B   stock (local disk)
config/logging.php     4 327 B   stock (single channel)
config/mail.php        3 614 B   stock (default: env('MAIL_MAILER', 'log'))
config/queue.php       4 199 B   stock (default: env('QUEUE_CONNECTION', 'database'))
config/services.php    1 418 B   ← razorpay + shiprocket blocks added
config/session.php     7 848 B   stock (default: env('SESSION_DRIVER', 'database'))
```

### `config/services.php` — the only customised config

```php
'razorpay' => [
    'key'    => env('RAZORPAY_KEY'),
    'secret' => env('RAZORPAY_SECRET'),
],
'shiprocket' => [
    'email'           => env('SHIPROCKET_EMAIL'),
    'password'        => env('SHIPROCKET_PASSWORD'),
    'pickup_location' => env('SHIPROCKET_PICKUP_LOCATION', 'Home'),
    'pickup_pincode'  => env('SHIPROCKET_PICKUP_PINCODE', '641006'),
],
```

**Defect — `base_url` is missing.** `ShiprocketService.php:15` reads `config('services.shiprocket.base_url', 'https://apiv2.shiprocket.in/v1/external')`, but no `base_url` key exists in the config. The default is used. `.env` defines `SHIPROCKET_BASE_URL=https://apiv2.shiprocket.in/v1/external` — **which is read by nothing.** Changing it in `.env` has no effect. (Value currently matches the hard-coded default, so nothing is broken today — it is a silent trap.)

**Also missing:** no `main_url` entry, yet `SearchController.php:29` calls `env('MAIN_URL')` directly.

---

## 6. `database/` — the missing foundation

```
database/
├── factories/          (1 default file — UserFactory)
├── migrations/         3 files, ALL Laravel defaults
│   ├── 0001_01_01_000000_create_users_table.php
│   ├── 0001_01_01_000001_create_cache_table.php
│   └── 0001_01_01_000002_create_jobs_table.php
└── seeders/            (1 default file — DatabaseSeeder)
```

**Zero domain migrations.** See [07-database-schema.md](07-database-schema.md) §1 and §6.

---

## 7. `bootstrap/`, `routes/`, `storage/`

| Path | Size | Note |
|---|---|---|
| `bootstrap/app.php` | 525 B | 18 lines; `withMiddleware` and `withExceptions` bodies are **empty comments** |
| `bootstrap/providers.php` | 64 B | `App\Providers\AppServiceProvider::class` |
| `bootstrap/cache/` | — | `.gitignore` present |
| `routes/web.php` | 3 149 B | 75 lines, 44 routes |
| `routes/console.php` | stock | **No scheduled tasks** — no `session:prune`, no `shiprocket:sync`, no `telescope:*` |
| `routes/channels.php` | — | absent |
| `storage/logs/laravel.log` | large | Contains live PII, SQL fragments, API payloads |
| `storage/framework/` | — | `views/` cache **committed to git** in the dashboard; check `.gitignore` |

---

## 8. Git state

| Fact | Value |
|---|---|
| Branch | `main` |
| Commits | **1** — `b156a59 Initial commit of palvaram project` |
| Tracked modified files (this app) | 7: `CheckoutController.php`, `ProfileController.php`, `composer.json`, `config/services.php`, `pages/checkout.blade.php`, `pages/myaccount.blade.php`, `routes/web.php` |
| Untracked in this app | `app/Models/{Coupon,District,ProductOrder,ProductOrderItem,ProductOrderUserAddress,ProductSlot,ProductStock,ProductTracking,ProductTransactionLog,State}.php`, `app/Services/`, `resources/views/pages/thankyou.blade.php`, `test_apis.php`, `test_services.php`, `docs/` |
| Untracked outside this app | `../RSD_Paul's International.pdf`, `../marketpro-ecommerce-multivendor-html-template-…/` (the purchased template), `../palVarnan1/` (an older copy of the app), `../palVarnanDashboard/app/Console/Commands/`, `../palVarnanDashboard/app/Services/`, `../palVarnanDashboard/app/Http/Controllers/ShiprocketController.php`, `../palVarnanDashboard/check_db_raw.php`, `../palVarnanDashboard/check_db_schema.php`, the dashboard's 2026-08-17 migration |
| Deleted session/view cache files tracked in the dashboard | 8 deletions — **`storage/framework/sessions` and `views` are committed in `palVarnanDashboard`** |

### Two findings from git state

1. **The entire Shiprocket + checkout feature set is uncommitted.** A fresh clone of this repository **cannot process an order**: no `app/Services/`, no `ProductOrder` models, no `thankyou.blade.php`, no `checkout.blade.php` changes, no `routes/web.php` additions, no Razorpay in `composer.lock`.
2. **`palVarnan1/` sits next to the working app** and is untracked — an older duplicate. High risk of editing the wrong copy.

---

## 9. Missing directories (expected in a Laravel app, absent here)

| Directory | Status |
|---|---|
| `app/Http/Middleware/` | **absent** |
| `app/Http/Requests/` | **absent** |
| `app/Http/Resources/` | **absent** |
| `app/Policies/` | **absent** |
| `app/Providers/*ServiceProvider` beyond `AppServiceProvider` | **absent** |
| `app/Console/` | **absent** |
| `app/Jobs/` | **absent** |
| `app/Events/` `app/Listeners/` | **absent** |
| `app/Mail/` `app/Notifications/` | **absent** |
| `app/Exceptions/` | **absent** |
| `resources/views/components/` | **absent** — no Blade components |
| `resources/views/partials/` | **absent** |
| `resources/lang/` | **absent** — no i18n (the app is English + hard-coded `₹`) |
| `database/seeders` (domain) | **only default** |
| `.github/workflows/` | **absent** — no CI |
| `docker/`, `Dockerfile`, `docker-compose.yml` | **absent** |
| `deploy/`, `.deploy/`, `Procfile` | **absent** |
| `tests/Feature/` (domain) | **only `ExampleTest.php`** |
| `docs/` | **created by this audit** |

---

## 10. Where the code actually is

| Concern | Location | Size |
|---|---|---|
| Business logic | 1 controller | `CheckoutController.php` — 28 275 B / 701 lines |
| All client-side behaviour | Inline `<script>` in Blade | No JS modules, no `resources/js` usage |
| All styling | Vendored template CSS | `public/assets/css` |
| Real domain schema | **The sibling dashboard app** | ~57 migrations |
| Fulfilment status sync | **The sibling dashboard app** | `SyncShiprocketStatus` command |

**Summary:** a Laravel app whose application logic is 39 files, whose schema is owned by a sibling repository, and whose JavaScript is inlined into 163 KB of Blade template markup.

---

## 11. Related documents

- [01-project-inventory.md](01-project-inventory.md) — feature and asset catalog
- [03-module-map.md](03-module-map.md) — what each module does
- [12-configuration.md](18-configuration-and-env.md — env/config reference
- [21-technical-debt.md](21-technical-debt.md) — refactoring plan keyed to these files
- [30-project-status.md](30-project-status.md) — commit-state risk