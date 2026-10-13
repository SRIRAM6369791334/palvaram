# 19 — Frontend Architecture

## Overall shape

`VERIFIED` — a server-rendered Blade admin panel built on the **Symox** Bootstrap 5 admin template,
compiled with Laravel Mix 6. No SPA, no Vue/React/Livewire component layer, no `@vite`. State lives
in the database; interactivity is jQuery-era plugin scripts plus inline `<script>` blocks.

## View inventory — `VERIFIED`

197 Blade files.

| Location | Files | Purpose |
| --- | --- | --- |
| `resources/views/pages/` | 87 | Application screens (69 use `@extends`) |
| `resources/views/layouts/` | 16 | Shared shell partials |
| `resources/views/pages/ajaxPages/` | 8 | HTML fragments returned by AJAX |
| `resources/views/auth/` | 6 | Login / register / password reset |
| `resources/views/components/` | 1 | Single Blade component |
| `resources/views/invoicePages/` | 1 | Dompdf invoice template |
| `resources/views/exports/` | 2 | Excel/PDF export templates |
| `resources/views/emails/` | 1 | `order_status_updated.blade.php` |
| `resources/views/` (root) | **76** | **Symox template demo pages** |

`BROKEN` — **76 root-level view files are unmodified Symox demo pages** that belong to a design
agency's sales template and have nothing to do with a dairy-products dashboard:

```
ui-alerts  ui-buttons  ui-cards  ui-carousel  ui-colors  ui-dropdowns  ui-general
ui-grid  ui-images  ui-modals  ui-offcanvas  ui-placeholders  ui-progressbars
ui-tabs-accordions  ui-typography  ui-video
tables-advanced  tables-basic
icons-boxicons  icons-dripicons  icons-feather  icons-fontawesome  icons-materialdesign
charts-apex  charts-chartjs
ecommerce-add-product  ecommerce-cart  ecommerce-checkout  ecommerce-customers
ecommerce-orders  ecommerce-product-detail  ecommerce-products  ecommerce-shops
email-inbox  email-read
apps-calendar  apps-chat
contacts-grid  contacts-list  contacts-profile
extended-lightbox  extended-notifications  extended-rangeslider  extended-rating
extended-sweet-alert
form-advanced  form-editors  form-elements  form-mask  form-uploads
form-validation  form-wizard
invoices-detail  invoices-list
maps-google  maps-leaflet  maps-vector
pages-404  pages-500  pages-comingsoon  pages-faqs  pages-maintenance
pages-pricing  pages-starter  pages-timeline  layouts-vertical
```

`VERIFIED` — **none are routed** in `routes/web.php` (322 live definitions, none matching these
patterns). They are unreachable by URL but still ship in the repository, are still discovered by
`@extends` tooling, and account for **~39% of all Blade files**. `pages-404.blade.php`,
`pages-500.blade.php`, and `pages-maintenance.blade.php` are the Laravel-standard error views and
should be kept; the rest should be deleted.

`INCOMPLETE` — 87 files in `pages/` minus 69 `@extends` means ~18 files do not declare a parent.
These are almost certainly the `ajaxPages` fragments and partial includes, but the count was not
individually traced.

## Layout system

`VERIFIED` — 16 partials in `resources/views/layouts/`:

| Partial | Lines | Role |
| --- | --- | --- |
| `master.blade.php` | 35 | **Primary shell.** `<html>`, head includes, `@yield('content')`. |
| `master-without_nav.blade.php` | 18 | Shell without the sidebar — **note the underscore**, while references elsewhere use `master-without-nav`. **Name mismatch.** |
| `master-layouts.blade.php` | 28 | Layout gallery (Symox demo) |
| `topbar.blade.php` | 338 | Header, notifications, user menu |
| `sidebar.blade.php` | 424 | **Largest nav partial.** Nav tree + permission-gated `@if` blocks |
| `right-sidebar.blade.php` | 155 | Secondary panel |
| `horizontal.blade.php` | 612 | **Largest single view file.** Horizontal nav variant |
| `vendor-scripts.blade.php` | 40 | Plugin `<script>` includes |
| `head-css.blade.php` | 28 | Stylesheet includes |
| `app.blade.php` | 82 | Alternate app shell |
| `footer.blade.php` | 17 | — |
| `page-title.blade.php` | 15 | — |
| `title-meta.blade.php` | 7 | — |
| `body.blade.php` | **1** | Effectively empty |
| `main.blade.php` | **2** | Effectively empty |
| `menu.blade.php` | **3** | Effectively empty |

`BROKEN` — three near-empty partials (`body`, `main`, `menu`) have no identified consumer. If any
view `@include`s them it renders an empty container; if none do, they are dead.

`INCOMPLETE` — the only shells used by application views appear to be the 35-line
`master.blade.php` and the 18-line `master-without_nav.blade.php`. `app.blade.php` (82 lines),
`horizontal.blade.php` (612 lines), and `master-layouts.blade.php` (28 lines) appear to be Symox
demo shells. `UNVERIFIED` — which of them application views actually extend.

### CSRF token placement — `BROKEN`

`VERIFIED` — `csrf_field()` / `csrf_token()` appear in only **7** of 87 `pages/` views, while
**76** views contain an inline `<script>` block. Most forms therefore rely on either:

- a token injected by JS at runtime, or
- `@csrf` in a shared partial, or
- **no CSRF token at all**.

The largest risk case is AJAX: a hand-built `$.ajax` POST does not get a token implicitly the way
`@csrf` in a Blade form does.

**Action:** audit every `$.ajax`/`axios` POST in `pages/` for an `X-CSRF-TOKEN` header.

## Authorization in the UI — `BROKEN` (see [11-authorization-rbac.md](11-authorization-rbac.md))

`VERIFIED` — `resources/views/layouts/sidebar.blade.php` (424 lines) gates navigation with
repeated inline checks of the form `Auth::user()->hasRole('Admin')`, and `topbar.blade.php` uses
`Auth::check()` for the profile menu. `BROKEN` — because `spatie/laravel-permission` middleware
aliases are **not registered** in `app/Http/Kernel.php`, no route is actually protected by role.
The sidebar hides links that the server will happily serve. Any authenticated user who types a URL,
or any route outside the `auth` group, bypasses every check.

`BROKEN` — `RoleController::updatePermissions()` accepts a raw `permissions` array from the request
with no validation, so the UI gate is also writable by anyone who can reach it.

## JavaScript approach — `INCOMPLETE`

`VERIFIED` — 76 inline `<script>` blocks across `resources/views/pages/`. Scripts are embedded in
the markup rather than extracted to `resources/js/`, so:

- none are minified, bundled, or content-hashed;
- there is no way to code-split page-specific JS;
- a syntax error in one view's inline block affects only that page, which is at least isolated.

`VERIFIED` — `webpack.mix.js` (264 lines) compiles `resources/js/app.js` to
`public/assets/js/app.js` and combines 29 `resources/js/pages/*.init.js` demo scripts. The
compiled output exists: `public/assets/js/` holds 102 files.

`BROKEN` — `webpack.mix.js:222-251` builds 29 page-init scripts that are **Symox demo
initialisers** (`calendar.init.js`, `gmaps.init.js`, `coming-soon.init.js`,
`ecommerce-shops.init.js`, …). None are application code. The same applies to the 25 third-party
library bundles at lines 20-169.

## Asset pipeline — `VERIFIED`

```js
const folder = { src: "resources/", dist: "public/", dist_assets: "public/assets/" }
```

| Output | Location | Size |
| --- | --- | --- |
| CSS | `public/assets/css/` (13 files) | 2.31 MB |
| **Fonts** | `public/assets/fonts/` (28 files) | **7.24 MB** |
| **Images** | `public/assets/images/` (73 files) | **8.94 MB** |
| JS | `public/assets/js/` (102 files) | 2.80 MB |
| Third-party libs | `public/assets/libs/` (49 files) | 3.66 MB |
| User uploads | `public/images/` (673 files) | — |

`BROKEN` — **`public/assets/fonts/` at 7.24 MB and `public/assets/images/` at 8.94 MB are copied
wholesale** by `mix.copyDirectory()` at lines 204 and 208. Every icon font variant, demo image, and
template asset ships to production because the copy is unconditional.

`BROKEN` — `public/images1/` is a second, orphaned copy of template imagery referenced by no
controller or model (see [15-file-handling.md](15-file-handling.md)).

`VERIFIED` — `mix-manifest.json` (165 lines) is committed, so Blade can use `mix()` for versioning.
`UNVERIFIED` — how many views actually call `mix()` versus hardcoding asset paths; hardcoded paths
defeat cache-busting.

### RTL pipeline — `INCOMPLETE`

`VERIFIED` — `WebpackRTLPlugin` is registered at `webpack.mix.js:214-218` and generates RTL CSS
variants for every stylesheet. `rtlcss`, `gulp-rtlcss`, and `webpack-rtl-plugin` are all declared in
`package.json` (see [18-dependencies.md](18-dependencies.md)). The application targets
`Asia/Kolkata` and English; `UNVERIFIED` — whether any RTL stylesheet is ever served.

## Charts and maps — `INCOMPLETE`

`VERIFIED` — five map/chart libraries are bundled and their demo initialisers compiled:
**ApexCharts**, **Chart.js**, **jsVectorMap** (with `world-merc.js`), **Leaflet**, and **gmaps**
(abandoned). Dashboards here are server-rendered Blade with chart data passed inline, so chart
configuration lives in view files rather than a maintainable config layer.

`VERIFIED` — 13 chart-related `*.init.js` files are compiled from the Symox demo set.

## Rich text editors — `INCOMPLETE`

`VERIFIED` — **CKEditor 5** (`@ckeditor/ckeditor5-build-classic: ^27.0.0`), **TinyMCE 5**
(`^5.6.2`), and **Quill 1.3.7** are all installed, and CKEditor + Quill are compiled into
`public/assets/libs/`. Three editors, no standardisation. `UNVERIFIED` — which one product
description fields use; likely more than one.

## AJAX fragments — `INCOMPLETE`

`VERIFIED` — `resources/views/pages/ajaxPages/` holds 8 files. This is the only partial-rendering
mechanism; there is no component or live-viewing layer.

`BROKEN` — `ajaxPages` uses an **underscore** while sibling directories use hyphens
(`invoicePages` uses camelCase). Three naming conventions coexist across view directories,
guaranteeing occasional path errors.

## Recommendations

1. **Delete the 76 Symox demo views** from `resources/views/`. Keep only `pages-404`,
   `pages-500`, `pages-maintenance`. This is a zero-risk deletion — no route references them.
2. Delete the 29 demo `*.init.js` entries and the `app_pages_assets` loop in `webpack.mix.js`;
   remove unused entries from `third_party_assets`.
3. Replace `mix.copyDirectory()` for fonts and images with an explicit manifest, or purge unused
   icon variants. Recover most of the 7.5 MB static payload.
4. Delete `public/images1/`.
5. Register the Spatie `role` and `permission` middleware aliases and apply `role:Admin` to the
   admin routes so the sidebar reflects real enforcement.
6. Standardise view directory naming and fix the `master-without-nav` hyphen/underscore mismatch.
7. Move inline `<script>` blocks into `resources/js/pages/` so they are bundled and minified.
8. Audit every AJAX POST for a CSRF header.
9. Pick one rich-text editor and one charting library; remove the rest.
10. Confirm all asset references go through `mix()` for cache-busting.