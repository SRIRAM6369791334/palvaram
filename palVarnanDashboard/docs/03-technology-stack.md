# 03 — Technology Stack

## Application framework

`VERIFIED` — `composer.json`

| Item | Value |
| --- | --- |
| Framework | `laravel/framework: ^8.65` |
| PHP requirement | `^7.3 \| ^8.0` |
| Auth scaffolding | `laravel/ui: ^3.3` + `laravel/sanctum: ^2.11` |
| Package discovery block | disables `laravel/telescope` auto-discovery (`extra.laravel.dont-discover`) |
| Minimum stability | `dev` with `prefer-stable: true` |
| Composer package name | `laravel/laravel` (never renamed from the skeleton) |

**Supportability note:** `VERIFIED` — Laravel 8 reached end of life on 2022-01-26 and security
patching ended 2023-01-26. This tree is several versions behind every current security release.

## PHP backend libraries

`VERIFIED` — `composer.json` `require`

| Package | Version | Role in this project |
| --- | --- | --- |
| `barryvdh/laravel-dompdf` | `^2.2` | Invoice and report PDF export. |
| `barryvdh/laravel-debugbar` | `*` | Unpinned wildcard; debug toolbar. |
| `fruitcake/laravel-cors` | `^2.0` | Global CORS handling (installed in global middleware). |
| `guzzlehttp/guzzle` | `^7.0.1` | Shiprocket HTTP client. |
| `laravel/tinker` | `^2.5` | REPL. |
| `maatwebsite/excel` | `^3.1` | Excel export/import. |
| `spatie/laravel-permission` | `6` | Roles and permissions. |
| Autoloaded file | — | `app/Helpers/validation_helpers.php` (global functions). |

`VERIFIED` — `require-dev`

| Package | Version |
| --- | --- |
| `facade/ignition` | `^2.5` |
| `fakerphp/faker` | `^1.9.1` |
| `kitloong/laravel-migrations-generator` | `^6.10` |
| `laravel/sail` | `^1.0.1` |
| `laravel/telescope` | `^4.14` |
| `mockery/mockery` | `^1.4.4` |
| `nunomaduro/collision` | `^5.10` |
| `phpunit/phpunit` | `^9.5.10` |

## Frontend stack

`VERIFIED` — `package.json`, `webpack.mix.js`

| Item | Value |
| --- | --- |
| Package name / version | `symox` 1.2.0 |
| Build tool | `laravel-mix: ^6.0.49` (Laravel Mix 6, Webpack 5) |
| CSS | `bootstrap: ^5.2.0`, `sass: ^1.49.9`, `rtlcss: ^3.0.0` |
| Icons | `feather-icons: ^4.28.0` |
| Charts | `apexcharts: ^3.27.1`, `chart.js: ^3.5.1`, `flot-charts: ^0.8.3` |
| Rich text | `@ckeditor/ckeditor5-build-classic: ^27.0.0`, `quill: ^1.3.7`, `tinymce: ^5.6.2` |
| Uploads | `dropzone: ^5.5.1` |
| Maps | `leaflet: ^1.6.0`, `gmaps: ^0.4.25`, `jsvectormap: ^1.3.1` |
| UI plugins | `metismenujs`, `simplebar`, `sweetalert2: ^11.0.18`, `swiper: ^6.5.6`, `choices.js`, `flatpickr`, `gridjs`, `nouislider`, `rater-js`, `wnumb`, `isotope-layout`, `masonry-layout`, `dragula`, `alertifyjs`, `glightbox`, `imask`, `moment` |
| Templating | Blade |
| RTL | `webpack-rtl-plugin: ^2.0.0` |
| Custom template dep | `admin-resources` from `git+https://github.com/MilkVandi/admin-resources#master` — **pinned to a mutable branch** |

`VERIFIED` — three rich-text editors are declared (CKEditor 5, Quill, TinyMCE) for what is one admin
form surface; only some are actually wired. See [18-dependencies.md](18-dependencies.md).

## Data store and services

`VERIFIED` — `.env`

| Setting | Value |
| --- | --- |
| `DB_CONNECTION` | `mysql` |
| `DB_HOST` / `DB_PORT` | `127.0.0.1` / `3306` |
| `DB_DATABASE` | `palvarnan_db` |
| `CACHE_DRIVER` | `file` |
| `SESSION_DRIVER` | `file` |
| `QUEUE_CONNECTION` | `sync` |
| `BROADCAST_DRIVER` | `log` |
| `FILESYSTEM_DRIVER` | `local` |
| `MAIL_MAILER` | `smtp` |

`VERIFIED` — `QUEUE_CONNECTION=sync` means queued jobs execute inline inside the HTTP request.
There is no worker and no queue backlog protection.

## External services

`VERIFIED` — `.env` and `app/Services/ShiprocketService.php`

| Service | Purpose |
| --- | --- |
| Shiprocket API v2 | Shipping labels, manifests, AWB status sync. Base URL from `SHIPROCKET_BASE_URL`. |
| Razorpay | Column reservation on `product_orders` (migration `2026_08_17_000001_add_shiprocket_and_razorpay_columns_to_product_orders_table.php`). No controller code found. `INCOMPLETE` |
| AWS S3 config | Present in `.env` but `FILESYSTEM_DRIVER=local`; no S3 usage found. `INCOMPLETE` |
| Pusher | Configured in `.env`, broadcasting driven to `log`. No push UI code found. `INCOMPLETE` |

`VERIFIED` — `MOBILE_API_URL` and `MILK_PRODUCT` are custom keys with no matching
`config/*.php` entry, so they are read directly from the environment.

## Dev tooling

`VERIFIED`

- Laravel Mix scripts: `dev`, `development`, `watch`, `watch-poll`, `hot`, `prod`, `production`.
- `laravel/telescope` installed but disabled from auto-discovery; `config/telescope.php` present.
- `barryvdh/laravel-debugbar` installed with no version constraint; `DEBUGBAR_ENABLED=false`.
- `kitloong/laravel-migrations-generator` installed as a dev dependency — indicates migrations were
  generated from an existing database rather than written by hand. This is corroborated by the
  schema drift in [05-database-schema.md](05-database-schema.md).

## Deployment target

`UNVERIFIED` — `.htaccess` at both project root and `public/`, plus `public/web.config`, indicate
the app is expected to run on **Apache under XAMPP on Windows**, not in containers. `laravel/sail`
is installed but there is **no `docker-compose.yml`** in the tree. See
[22-deployment.md](22-deployment.md).