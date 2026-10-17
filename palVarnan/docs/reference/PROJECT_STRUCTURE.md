# PROJECT_STRUCTURE.md

**Purpose:** Directory map of `app/`, `routes/`, `resources/views` (23 files), `config/`, `database/migrations`
(3 stock), `public/`. Explain there is no `app/Http/Middleware` (Laravel 12 bootstraps via `bootstrap/app.php`).

---

## 1. Top-level layout

Root: [`C:\xampp\htdocs\pauls\palVarnan`](../audit/VERIFIED_FACTS.md:11). Non-vendor structure (reconciled):

```
palVarnan/
├── app/                       # Application core
├── bootstrap/                 # Laravel 12 bootstrap
├── config/                    # Configuration files
├── database/                  # Migrations/factories/seeders
├── docs/                      # Audit + this reference set
├── public/                    # Web root (index, assets)
├── resources/                 # Views, JS/CSS sources
├── routes/                    # Web/console routes
├── storage/                   # Logs/cache (generated)
├── tests/                     # Feature/Unit tests
├── vendor/                    # Composer dependencies
├── .env / .env.example        # Environment
├── composer.json / composer.lock
├── package.json / vite.config.js
├── artisan
└── (root test scripts)         # test_apis.php, test_services.php (web-reachable artefacts)
```

Evidence: [`VERIFIED_FROM_SOURCE`](../audit/VERIFIED_FACTS.md:11-33), [`../01-project-inventory.md:42-85`](../01-project-inventory.md#2-directory-map-non-vendor).

---

## 2. `app/`

```
app/
├── Http/
│   ├── Controllers/           # 10 files (9 domain + Controller base)
│   │   ├── AuthController.php
│   │   ├── CartController.php
│   │   ├── CheckoutController.php
│   │   ├── Controller.php
│   │   ├── CountryController.php
│   │   ├── HomeController.php
│   │   ├── ProductController.php
│   │   ├── ProfileController.php
│   │   ├── SearchController.php
│   │   └── WishlistController.php
│   └── (no Middleware/)       # Laravel 12: no app/Http/Middleware directory
├── Models/                    # 25 Eloquent models (see §3)
├── Providers/
│   └── AppServiceProvider.php # View composer for cart/wishlist counts/subtotal
└── Services/
    └── ShiprocketService.php  # Shiprocket HTTP wrapper (lone service)
```

**Key point:** `app/Http/Middleware` is **absent**. In Laravel 12, middleware is configured in
[`bootstrap/app.php`](../../bootstrap/app.php) (lines 13-15 show `withMiddleware` registering `trustProxies`).
This is `VERIFIED_FROM_SOURCE`. Compare to numbered audit: [`../01-project-inventory.md`](../01-project-inventory.md)
records the absence; the numbered text elsewhere sometimes implies a middleware dir — this reference states the
source truth. `CONFLICTING_EVIDENCE` resolved in favour of source.

Other absent layers: `Policies/`, `Requests/` (Form Requests), `Repositories/`. `VERIFIED_FROM_SOURCE`.

---

## 3. `app/Models` (25)

List (by file):

```
Area.php, AreaAssign.php, Brand.php, Cart.php, Category.php, Country.php,
Coupon.php, District.php, HomePromotion.php, Product.php, ProductChildImage.php,
ProductOrder.php, ProductOrderItem.php, ProductOrderUserAddress.php, ProductSlot.php,
ProductStock.php, ProductTracking.php, ProductTransactionLog.php, ProductVarient.php,
State.php, SubCategory.php, Unit.php, User.php, UserAddress.php, Wishlist.php
```

Notes: `Unit` and `HomePromotion` are **empty models** (no `$fillable` content declared/used significantly)
`VERIFIED_FROM_SOURCE`; see [`MODELS_AND_RELATIONSHIPS.md`](./MODELS_AND_RELATIONSHIPS.md) and
[`../11-models-and-relationships.md`](../11-models-and-relationships.md).

---

## 4. `routes/`

```
routes/
├── web.php      # 45 Route:: entries (VERIFIED_FROM_SOURCE)
└── console.php  # Artisan::command('inspire') only; no scheduler (VERIFIED_FROM_SOURCE)
```

`web.php` lines: see [`ROUTES_AND_ENDPOINTS.md`](./ROUTES_AND_ENDPOINTS.md) and source
[`routes/web.php:1-83`](../../routes/web.php). `console.php` has only the default inspire command
([`console.php:1-8`](../../routes/console.php)); **no `Schedule::` calls**.

---

## 5. `resources/` — 23 Blade views

```
resources/
├── css/                # app.css (exists; referenced by vite.config.js)
├── js/                 # app.js, bootstrap.js
└── views/
    ├── layouts/
    │   ├── app.blade.php
    │   ├── country-modal.blade.php
    │   ├── footer.blade.php
    │   └── header.blade.php
    ├── pages/
    │   ├── about.blade.php
    │   ├── cart.blade.php
    │   ├── checkout.blade.php
    │   ├── contact.blade.php
    │   ├── home.blade.php
    │   ├── login.blade.php
    │   ├── myaccount.blade.php
    │   ├── privacy-policy.blade.php
    │   ├── product-detail.blade.php
    │   ├── products.blade.php
    │   ├── register.blade.php
    │   ├── return-refund-policy.blade.php
    │   ├── shipping-policy.blade.php
    │   ├── terms-conditions.blade.php
    │   ├── thankyou.blade.php
    │   └── wishlist.blade.php
    ├── partials/
    │   └── pagination.blade.php
    ├── country-selection.blade.php
    └── welcome.blade.php
```

Count: 4 (layouts) + 16 (pages) + 1 (partials) + 2 top-level = 23. `VERIFIED_FROM_SOURCE`.

---

## 6. `config/` (10 files)

```
config/
├── app.php
├── auth.php
├── cache.php
├── database.php
├── filesystems.php
├── logging.php
├── mail.php
├── queue.php
├── services.php        # Razorpay + Shiprocket
└── session.php
```

`services.php:38-48` defines `razorpay` and `shiprocket` key names. `queue.php` default/driver per `.env`.
`VERIFIED_FROM_SOURCE`. (Numbered audit sometimes references 13; current set is 10. `CONFLICTING_EVIDENCE` —
this reference reflects actual files.)

---

## 7. `database/` — 3 stock migrations; no domain migrations

```
database/
├── factories/
│   └── UserFactory.php
├── migrations/
│   ├── 0001_01_01_000000_create_users_table.php
│   ├── 0001_01_01_000001_create_cache_table.php
│   └── 0001_01_01_000002_create_jobs_table.php        # 3 total
├── seeders/
│   └── DatabaseSeeder.php
└── database.sqlite
```

**Zero domain migrations** in the storefront. All 93 tables come from the shared DB (owned by
`palVarnanDashboard`) and from the SQL dump `palvarnan_db (2).sql`. This is a **critical** structural fact.
`VERIFIED_FROM_SOURCE` ([`VERIFIED_FACTS.md:27-29`](../audit/VERIFIED_FACTS.md#counts-verified_from_source),
[`../01-project-inventory.md:57`](../01-project-inventory.md#2-directory-map-non-vendor)).

---

## 8. `public/`

```
public/
├── assets/        # css/, images/, js/, sass/, webfonts/
├── .htaccess
├── favicon.ico
├── index.php
└── robots.txt
```

`index.php` is the Laravel front controller. `VERIFIED_FROM_SOURCE`.

---

## 9. Bootstrap (Laravel 12)

[`bootstrap/app.php`](../../bootstrap/app.php):

- Routes: `web: __DIR__.'/../routes/web.php'`, `commands: __DIR__.'/../routes/console.php'`, `health: '/up'`
- Middleware: `withMiddleware` sets `trustProxies(at: '*')` (no global middleware array beyond defaults; no auth
  middleware applied globally)
- Exceptions: default

This confirms **no `app/Http/Middleware` directory** exists and middleware is bootstrapped via `app.php`.
`VERIFIED_FROM_SOURCE`.

---

## 10. Related

- [`DEVELOPER_SETUP.md`](./DEVELOPER_SETUP.md) — install/DB import
- [`ARCHITECTURE.md`](./ARCHITECTURE.md) — request lifecycle
- [`../01-project-inventory.md`](../01-project-inventory.md)
- [`../10-file-structure.md`](../10-file-structure.md) — full file listing