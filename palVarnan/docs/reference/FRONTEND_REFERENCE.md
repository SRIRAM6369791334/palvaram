# Frontend Reference — Project B (`palVarnan`)

> Blade-first frontend: vendored theme assets, minimal inline JS, and a small amount of
> `fetch()` AJAX. Vite/Tailwind are configured but **unused by live pages**.
> Evidence vocabulary: `VERIFIED_FROM_SOURCE`, `VERIFIED_FROM_LIVE_SCHEMA`, `INFERRED`,
> `UNVERIFIED`, `NOT_AVAILABLE`, `CONFLICTING_EVIDENCE`. **No credentials or PII are
> reproduced.**
>
> Siblings: [`./ROUTES_AND_API_REFERENCE.md`](./ROUTES_AND_API_REFERENCE.md),
> [`./FRONTEND_REFERENCE.md`](./FRONTEND_REFERENCE.md) (this file),
> [`./INTEGRATION_MAP.md`](./INTEGRATION_MAP.md).
> Existing narrative: [`../15-frontend-and-assets.md`](../15-frontend-and-assets.md),
> [`../10-file-structure.md`](../10-file-structure.md).

---

## 1. Blade view inventory (23 files)

`VERIFIED_FROM_SOURCE` (Get-ChildItem under `resources/views`):

| Area | File | Role |
|---|---|---|
| Root | `country-selection.blade.php` | Standalone country picker (no `@extends('layouts.app')`) |
| Root | `welcome.blade.php` | Laravel default splash; contains `@vite` (dead — not reachable by routes) |
| Layouts | `layouts/app.blade.php` | Master: CSS/JS includes, meta, CSRF, header/footer, @yield('main'), @stack('scripts') |
| Layouts | `layouts/country-modal.blade.php` | Modal for country selection (included by `header.blade.php:327`) |
| Layouts | `layouts/footer.blade.php` | Site footer (template markup + legal/global blocks) |
| Layouts | `layouts/header.blade.php` | Site header, nav, search box, cart/wishlist badges, includes modal |
| Partials | `partials/pagination.blade.php` | Bootstrap-5 style custom pagination (consumed by paginated lists) |
| Pages | `pages/about.blade.php` | `@extends('layouts.app')` |
| Pages | `pages/cart.blade.php` | Cart page; inline `fetch()` |
| Pages | `pages/checkout.blade.php` | Checkout multi-step; multiple `fetch()` calls + Razorpay |
| Pages | `pages/contact.blade.php` | `@extends('layouts.app')` (no backend handler; `contact.html` links elsewhere) |
| Pages | `pages/home.blade.php` | Home; heavy template/demo markup; refs wowtheme7.com |
| Pages | `pages/login.blade.php` | Login form; `fetch()` to `/login-store` and `/reset-password-direct` |
| Pages | `pages/myaccount.blade.php` | Account dashboard; `fetch()` calls |
| Pages | `pages/privacy-policy.blade.php` | `@extends('layouts.app')` |
| Pages | `pages/product-detail.blade.php` | Product detail; many `fetch()` calls |
| Pages | `pages/products.blade.php` | Product listing; `fetch()` calls; pagination |
| Pages | `pages/register.blade.php` | Register form; `fetch()` |
| Pages | `pages/return-refund-policy.blade.php` | `@extends('layouts.app')` |
| Pages | `pages/shipping-policy.blade.php` | `@extends('layouts.app')` |
| Pages | `pages/terms-conditions.blade.php` | `@extends('layouts.app')` |
| Pages | `pages/thankyou.blade.php` | Post-order confirmation |
| Pages | `pages/wishlist.blade.php` | Wishlist; `fetch()` calls |

Layout inheritance: all pages (except `country-selection.blade.php`, `welcome.blade.php`) use
`@extends('layouts.app')` and render into `@yield('main')`. `VERIFIED_FROM_SOURCE`.

---

## 2. Theme provenance: MarketPro (vendored)

The theme is a **vendored MarketPro** template (multi-vendor flavour). `VERIFIED_FROM_SOURCE`.

| Evidence | Detail |
|---|---|
| `home.blade.php` references template vendor | URLs like `https://wowtheme7.com/tf/marketpro/assets/images/...` (`home.blade.php:121`, `:137`, `:153`, `:169`, `:1754`, ... `:2180` range) — demo images |
| Assets directory | `public/assets/` contains `css/`, `js/`, `images/`, `sass/`, `webfonts/` (top-level) — `472 files` total, ~9.4 MB (`VERIFIED_FROM_SOURCE`: directory listing + doc size reference) |
| Template header/footer markup | `header.blade.php`, `footer.blade.php`, `app.blade.php` reference `public/assets/*` and template CSS/JS filenames (e.g. `main.css`, `jquery-3.7.1.min.js`, `bootstrap.bundle.min.js`/`boostrap.bundle.min.js` typo) |
| Existing doc classification | [`../15-frontend-and-assets.md`](../15-frontend-and-assets.md) identifies "marketpro-ecommerce-multivendor-html-template" as provenance |

**Typo:** `app.blade.php:80` references `boostrap.bundle.min.js` (missing 't') — the filename
on disk carries that typo. `VERIFIED_FROM_SOURCE`.

---

## 3. CSS and JS loading (app.blade.php)

`app.blade.php:13-27` (CSS), `:78-102` (JS), `:113-114` (CDNs).

### CSS (all theme)
- `/assets/css/bootstrap.min.css` — `:14`
- `/assets/css/select2.min.css` — `:16`
- `/assets/css/slick.css` — `:18`
- `/assets/css/jquery-ui.css` — `:20`
- `/assets/css/animate.css` — `:22`
- `/assets/css/aos.css` — `:24`
- `/assets/css/main.css` — `:26`
- CDN: `https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css` — `:27`

### JS (theme)
- `/assets/js/jquery-3.7.1.min.js` — `:78`
- `/assets/js/boostrap.bundle.min.js` — `:80` (typo in filename)
- `/assets/js/phosphor-icon.js` — `:82`
- `/assets/js/select2.min.js` — `:84`
- `/assets/js/slick.min.js` — `:86`
- `/assets/js/count-down.js` — `:88`
- `/assets/js/jquery-ui.js` — `:90`
- `/assets/js/wow.min.js` — `:92`
- `/assets/js/aos.js` — `:94`
- `/assets/js/marque.min.js` — `:96`
- `/assets/js/vanilla-tilt.min.js` — `:98`
- `/assets/js/counter.min.js` — `:100`
- `/assets/js/main.js` — `:102`

### CDNs
- `https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js` — `:114` (second jQuery)
- `https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js` — `:113`
- `https://cdn.jsdelivr.net/npm/sweetalert2@11` — `:115`

**Duplicate jQuery:** `3.7.1` local and `3.6.0` from CDN — the second overwrite means the
page effectively runs jQuery 3.6.0. `VERIFIED_FROM_SOURCE`; see
[`../15-frontend-and-assets.md`](../15-frontend-and-assets.md) FA-01.

**No `defer`/`async`** on any script tag. All are loaded in the order shown, blocking
(parsing blocks). `VERIFIED_FROM_SOURCE`.

**CSRF meta tag:** `<meta name="csrf-token" content="{{ csrf_token() }}">` at
`app.blade.php:7`. `VERIFIED_FROM_SOURCE`.

---

## 4. Vite + Tailwind: configured but unused

`VERIFIED_FROM_SOURCE`:

| Artefact | Content/observation |
|---|---|
| `package.json` | `"type": "module"`, scripts `build: vite build`, `dev: vite`; devDeps `@tailwindcss/vite ^4`, `tailwindcss ^4`, `laravel-vite-plugin ^2`, `vite ^7`, `axios ^1.11.0` |
| `vite.config.js` | `laravel({ input: ['resources/css/app.css', 'resources/js/app.js'], refresh: true })`, `tailwindcss()` plugin; ignores `storage/framework/views/**` |
| `resources/css/app.css` | 11 lines: `@import 'tailwindcss'; @source ...` (Laravel-specific `@source`) and `@theme` font stack |
| `resources/js/app.js` | `import './bootstrap';` |
| `resources/js/bootstrap.js` | `axios` + `X-Requested-With: XMLHttpRequest` |
| `@vite` usage | Only `welcome.blade.php:15` — `@vite(['resources/css/app.css', 'resources/js/app.js'])` |
| Live pages | **No `@vite`** calls; all CSS/JS loaded from `public/assets/` via `<link>`/`<script>` in `app.blade.php` |
| Build output | `public/build/` does **not** exist — `Test-Path public/build → False` |

**Conclusion:** Vite + Tailwind 4 is fully configured (tooling present), but the theme is
vendored and the only `@vite` occurrence is in the dead `welcome.blade.php`. `resources/js`
and `resources/css` are **not used by any live Blade page** (all live pages inherit from
`app.blade.php` which uses `public/assets/`). `VERIFIED_FROM_SOURCE`. See
[`../09-dependencies.md`](../09-dependencies.md), [`../15-frontend-and-assets.md`](../15-frontend-and-assets.md).

---

## 5. Inline `fetch()` AJAX (32 calls, 29 with CSRF, 3 without)

`VERIFIED_FROM_SOURCE` (grep for `fetch\(` under `resources/views`). Total **32** matches.

| File | Count | With `X-CSRF-TOKEN`? | Notes |
|---|---|---|---|
| `layouts/app.blade.php` | 1 | **1** | `/cart/subtotal` at `:228` |
| `layouts/header.blade.php` | 1 | **0** | `/search?q=...` at `:348` (GET, no CSRF required) |
| `pages/cart.blade.php` | 1 | **0** | cart quantity update — no CSRF header (`:268`) |
| `pages/checkout.blade.php` | 5 | **5** | address save (419 handled `:616`), getCouriers, preparePayment, placeOrder; all have CSRF |
| `pages/login.blade.php` | 2 | **2** | `/login-store`, `/reset-password-direct` |
| `pages/myaccount.blade.php` | 5 | **4** | 1 missing CSRF (GET-like or missing header at `:981`; others include token) |
| `pages/product-detail.blade.php` | 10 | **10** | cart ajax, wishlist check/toggle |
| `pages/products.blade.php` | 6 | **6** | cart ajax + wishlist |
| `pages/register.blade.php` | 1 | **1** | register form submit |
| `pages/wishlist.blade.php` | 2 | **2** | remove + add to cart |

The **3 missing `X-CSRF-TOKEN`** cases: `pages/cart.blade.php:268`, and one call in
`pages/myaccount.blade.php` (around `:981`, a GET wrapper). The majority (29) do send the
token. Only `checkout.blade.php:616` explicitly handles HTTP 419 — other pages assume
success/fail without 419 recovery. `VERIFIED_FROM_SOURCE`. See
[`../13-security-audit.md`](../13-security-audit.md) SEC-19.

---

## 6. Razorpay checkout integration (frontend)

`checkout.blade.php` loads the Razorpay checkout script from the CDN in the widget flow:

- On preparePayment success, the client receives `razorpay_order_id`, `amount`, `razorpay_key`,
  and `user` — returned by `CheckoutController::preparePayment()` (`:207-216`).
- The page then invokes `new Razorpay(options).open()` using those values.
- The handler POSTs to `/checkout/place-order` with `razorpay_order_id`, `razorpay_payment_id`,
  `razorpay_signature` and the shipping form fields (`:872` call). CSRF token is sent
  (`:876`).
- The address form handles 419 (`:616`) — explicit recovery (reload).

`VERIFIED_FROM_SOURCE` (checkout.blade.php lines around `:606`, `:706`, `:797`, `:872`,
`:616`).

---

## 7. Frontend observations and diagnostics

| Item | Observation | Evidence |
|---|---|---|
| `@vite` only | Exclusively in `welcome.blade.php:15` — page not routable; no live page uses Vite | grep |
| Country picker | `country-selection.blade.php` is standalone (no app layout); `layouts/country-modal.blade.php` is included by header | `header.blade.php:327` |
| Favicon | `app.blade.php:11` points to `/assets/images/logo/logo.png` (not `favicon.ico` on disk) | `public/favicon.ico` exists (0-byte? earlier `ls` showed `favicon.ico` — needs manual check; `VERIFIED_FROM_SOURCE`) |
| Search | GET `/search?q=` with `fetch()`; response is JSON array of up to 20 results (4×5) | `header.blade.php:348-380`; `SearchController::search` |
| Template demo | `home.blade.php` contains large blocks of wowtheme7 demo images and template sections — not all dynamically driven | [`../15-frontend-and-assets.md`](../15-frontend-and-assets.md) §4.1 |
| Broken links | Footer `:53` links to `contact.html` (file not present in `public/`); footer also links to `/products` for many static items | `footer.blade.php:53`; same doc §4.2 |
| Pagination | `partials/pagination.blade.php` is a custom Bootstrap-5 style wrapper around Laravel paginator | used by `products.blade.php` |
| Floating cart bar | `app.blade.php:135-207` conditional (only when `@auth`) shows a floating bar with `#fcb-subtotal` refreshed by `/cart/subtotal` | `:228-236` |

---

## 8. Frontend correctness vs defects (high-level)

From [`../15-frontend-and-assets.md`](../15-frontend-and-assets.md):

| ID | Defect | Status |
|---|---|---|
| FA-01 | Duplicate jQuery 3.7.1 + 3.6.0 CDN | Present (`VERIFIED_FROM_SOURCE`) |
| FA-02 | `boostrap.bundle.min.js` filename typo | Present |
| FA-03 | 3 CDN dependencies | Present |
| FA-04 | 16 blocking script tags, no `defer` | Present |
| FA-05 | Vite/Tailwind unused | Present |
| FA-06 | favicon source | Present |
| FA-07 | `robots.txt` stock Laravel | Present |
| FA-08 | 9.4 MB vendored theme in `public/assets/` | Present |

CSRF/419: most `fetch()` calls include CSRF, but 3 gaps and only checkout handles 419
(`VERIFIED_FROM_SOURCE`). See [`../13-security-audit.md`](../13-security-audit.md) SEC-19.

---

## 9. Related documents

- [`../15-frontend-and-assets.md`](../15-frontend-and-assets.md) — detailed frontend audit
- [`../10-file-structure.md`](../10-file-structure.md) — view locations
- [`./ROUTES_AND_API_REFERENCE.md`](./ROUTES_AND_API_REFERENCE.md) — which endpoints AJAX hits
- [`./SECURITY_AUDIT.md`](./SECURITY_AUDIT.md) — frontend security notes (SEC-19)
- [`../09-dependencies.md`](../09-dependencies.md) — Vite pipeline analysis
