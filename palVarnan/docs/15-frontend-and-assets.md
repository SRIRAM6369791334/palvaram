# 15 — Frontend and Assets

---

## 1. Stack

| Layer | Technology | Source |
|---|---|---|
| Templating | Blade, server-rendered | `resources/views` |
| HTTP calls | `fetch()` — **native**, no library | 32 call sites |
| JS libraries | jQuery **3.7.1 + 3.6.0**, Bootstrap, Slick, Select2, jQuery UI, AOS, WOW.js, Marquee, Vanilla Tilt, counter.js, Toastr, SweetAlert2 | `public/assets/js` + CDNs |
| CSS | 7 vendored stylesheets | `public/assets/css` |
| Icons | Phosphor Icons | `phosphor-icon.js` |
| Build | Vite + Tailwind 4 configured, **entirely unused** | `vite.config.js` |
| Component system | **None** — no Blade components | `resources/views/components/` absent |

---

## 2. Asset loading

### `app.blade.php:13-27` (CSS)

```html
<link rel="stylesheet" href="/assets/css/bootstrap.min.css">
<link rel="stylesheet" href="/assets/css/select2.min.css">
<link rel="stylesheet" href="/assets/css/slick.css">
<link rel="stylesheet" href="/assets/css/jquery-ui.css">
<link rel="stylesheet" href="/assets/css/animate.css">
<link rel="stylesheet" href="/assets/css/aos.css">
<link rel="stylesheet" href="/assets/css/main.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
```

### `app.blade.php` (JS)

```html
<script src="/assets/js/jquery-3.7.1.min.js"></script>
<script src="/assets/js/boostrap.bundle.min.js"></script>
<script src="/assets/js/phosphor-icon.js"></script>
<script src="/assets/js/select2.min.js"></script>
<script src="/assets/js/slick.min.js"></script>
<script src="/assets/js/count-down.js"></script>
<script src="/assets/js/jquery-ui.js"></script>
<script src="/assets/js/wow.min.js"></script>
<script src="/assets/js/aos.js"></script>
<script src="/assets/js/marque.min.js"></script>
<script src="/assets/js/vanilla-tilt.min.js"></script>
<script src="/assets/js/counter.min.js"></script>
<script src="/assets/js/main.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
```

### Defects

| # | Defect | Impact |
|---|---|---|
| FA-01 | **jQuery is loaded twice — 3.7.1 locally and 3.6.0 from CDN** | The second load **overwrites `window.jQuery`**, so the whole page runs on 3.6.0 while `public/assets/js/jquery-ui.js`, `slick.min.js`, `select2.min.js` were written for 3.7.1. Plugin version skew. Also a **render-blocking external request** on every page |
| FA-02 | `boostrap.bundle.min.js` — **misspelled filename** | The file on disk is genuinely named `boostrap.bundle.min.js`, so it loads, but every Bootstrap reference in the project carries the typo. A `composer update` (`vendor:publish --tag=laravel-assets --force`) will not fix it; a search-and-replace will |
| FA-03 | 3 CDN dependencies (cdnjs ×2, jsdelivr) | Availability, privacy (visitor IPs leak), and no CSP |
| FA-04 | **16 blocking script tags**, all in `<head>` or before `</body>` with no `defer` | Serialised parsing; `main.js` and `jquery-ui.js` (large) block first paint |
| FA-05 | **No `@vite` anywhere** — the entire Vite/Tailwind pipeline is dead | `composer run dev` and `npm run build` produce nothing used |
| FA-06 | `public/favicon.ico` is **0 bytes**; `app.blade.php:11` points at `/assets/images/logo/logo.png` instead | Broken tab icon |
| FA-07 | `public/robots.txt` is 24 bytes of stock Laravel | No sitemap reference, no disallow rules |
| FA-08 | 9.4 MB of committed theme assets (472 files) in `public/assets/` | Repo bloat; `sass/` sources are committed alongside the compiled `css/` |

---

## 3. AJAX inventory — 32 `fetch()` calls

CSRF header usage, verified per file:

| File | `fetch()` | `X-CSRF-TOKEN` | Verdict |
|---|---|---|---|
| `app.blade.php` | 1 | 1 | OK |
| `header.blade.php` | 1 | **0** | Search is GET, so acceptable |
| `cart.blade.php` | 1 | **0** | **POST without CSRF ⇒ 419** |
| `checkout.blade.php` | 4 | 4 | OK |
| `login.blade.php` | 2 | 2 | OK |
| `myaccount.blade.php` | 4 | **3** | One gap |
| `product-detail.blade.php` | 10 | 10 | OK |
| `products.blade.php` | 6 | 6 | OK |
| `register.blade.php` | 1 | 1 | OK |
| `wishlist.blade.php` | 2 | 2 | OK |
| **Total** | **32** | **29** | **3 gaps** |

**SEC-19 refined:** 29 of 32 calls are correct. The real gaps are `cart.blade.php`'s quantity-update call and one `myaccount.blade.php` call. Only `checkout.blade.php:572-575` handles an HTTP 419 response.

**No global wrapper.** The CSRF token is injected ad hoc per call site, so every new endpoint must remember to add it. A `window.api()` helper would make this structural.

---

## 4. Template debt — MarketPro leftovers

The purchased template is *"marketpro-ecommerce-multivendor-html-template"* (confirmed by the untracked folder `../marketpro-ecommerce-multivendor-html-template-2026-01-10-16-20-47-utc/`).

### `pages/home.blade.php` — 1 991 lines, 162 868 bytes

| Metric | Count |
|---|---|
| Lines | **1 991** |
| Unsplash demo images | **50** |
| `wowtheme7.com` links (template vendor) | **29** |
| Hard-coded demo prices (`14.99` / `28.99` / `60.99`) | **91** |
| Inline `<script>` tags | **0** |

Only the hero slider (`:32-70`) and the category grid (`:97`) are data-driven. The remaining ~700 lines are template demo content showing products the store does not sell, at prices the store does not charge.

### `layouts/header.blade.php` — 889 lines, 41 981 bytes

| Target | Occurrences | Exists? |
|---|---|---|
| `shop.html` | **85** | **No** |
| `contact.html` | 3 | No |
| `blog.html` / `blog-details.html` | 1 each | No |
| `vendor.html` / `vendor-details.html` / `vendor-two.html` / `vendor-two-details.html` | 1 each | No |
| `product-details.html` / `product-details-two.html` | 1 each | No |
| `index-two.html` / `index-three.html` | 1 each | No |
| `checkout.html` / `become-seller.html` / `account.html` | 1 each | No |
| **Total dead links** | **101** | |

The template is named **"multivendor"** and ships vendor pages, "become a seller", and a two-vendor layout — but the application has **no multi-vendor concept at all**. No `vendors` table, no vendor product relationship, no vendor model.

### `footer.blade.php` — US demo contact data

```html
mailto:apnstores369@gmail.com
mailto:apaulskitchen@gmail.com
tel:+(406)555-0120          ← Montana, USA area code
```
An India-only store shipping a US demo phone number and two unrelated Gmail addresses.

### `welcome.blade.php` — 82 568 bytes

Laravel's default marketing page. **No route renders it.** Dead weight of 83 KB in the repository.

---

## 5. Structural problems

| # | Problem | Detail |
|---|---|---|
| FA-09 | **No Blade components** — `resources/views/components/` does not exist | Header, footer, product card, price block, and the entire coupon UI are copy-pasted |
| FA-10 | **No partials directory** — no `@include` beyond `layouts.app` | Every page is one 900–2 000-line file |
| FA-11 | All JS inline in Blade | No `resources/js` modules, no TypeScript, no bundling, no minification, no linting |
| FA-12 | Hard-coded currency `₹` in ~40 places | No `Intl.NumberFormat`, no currency helper |
| FA-13 | Hard-coded country/address assumptions (state, pincode, landmark) | Conflicts with the country selector — see [08-business-logic.md](08-business-logic.md) BR-M1-04 |
| FA-14 | `<title> Pal Varnan</title>` on every page | No per-page title, no meta description, no OpenGraph, no canonical URL (F-57) |
| FA-15 | No `loading="lazy"` on any `<img>` | 50 Unsplash images plus product images all load eagerly |
| FA-16 | No `alt` audit performed | Image accessibility is **UNVERIFIED** |
| FA-17 | No responsive-image handling — no `srcset`, no WebP | Every image ships at its original size |

---

## 6. Working correctly

| Feature | Notes |
|---|---|
| Slick slider | Init'd in `app.blade.php`, hero banners are dynamic |
| Select2 | Used on checkout state and courier dropdowns |
| Toastr | Cart/wishlist AJAX feedback |
| SweetAlert2 | Login/register success toasts (`app.blade.php:117`) |
| AOS / WOW / counter.js | Template scroll animations |
| Type-ahead search | `header.blade.php:835` — product branch works (2 of 4 result types broken) |
| Razorpay checkout | Loaded from `https://checkout.razorpay.com/v1/checkout.js`, modal opens, handler POSTs to `placeOrder` |

---

## 7. Performance impact

| Issue | Cost |
|---|---|
| Two jQuery copies | ~290 KB extra on **every** page, plus a blocking cross-origin request |
| 16 serialised script tags | Each adds a round-trip before parsing continues |
| 50 Unsplash images on the homepage | Several hundred KB of demo imagery that does not belong to the store |
| No image optimisation | No WebP, no lazy loading, no responsive variants |
| No critical-CSS inlining, no HTTP/2 push, no preload hints | — |
| Inline JS in every page | Uncacheable, unminifiable, duplicated across pages |

**Fixing the duplicate jQuery alone (FA-01) is the single highest-value frontend change** — it removes a render-blocking third-party request from every page load.

---

## 8. Recommendations

| Priority | Action |
|---|---|
| **P0** | Remove the CDN jQuery 3.6.0 tag; keep local 3.7.1 (FA-01) |
| **P0** | Delete `test_apis.php` / `test_services.php` and rotate credentials (they live in the web root) |
| P1 | Add a `window.api()` helper that attaches `X-CSRF-TOKEN` and normalises error handling (fixes the 3 CSRF gaps and the missing 419 handling) |
| P1 | Rename `boostrap.bundle.min.js` → `bootstrap.bundle.min.js` everywhere (FA-02) |
| P1 | Remove the ~700 lines of template demo from `home.blade.php` and the 101 dead links from `header.blade.php` |
| P1 | Self-host Toastr/SweetAlert2; then a CSP becomes possible (FA-03) |
| P2 | Add `defer` to non-critical scripts (FA-04) |
| P2 | Delete `welcome.blade.php`, the unused Vite/Tailwind pipeline, and `public/assets/sass/` |
| P2 | Create Blade components: `<x-product-card>`, `<x-price>`, `<x-header>`, `<x-footer>` |
| P2 | Extract inline JS into `resources/js` modules |
| P3 | Add per-page `<title>`, meta description, OpenGraph, canonical |
| P3 | Add `loading="lazy"`, WebP, `srcset` |
| P3 | Remove the US demo contacts from `footer.blade.php` |

---

## 9. Related documents

- [04-feature-inventory.md](04-feature-inventory.md) — F-14 home placeholders, F-49…53 dead pages, F-57 SEO, F-60 favicon
- [05-user-flows.md](05-user-flows.md) — the search and cart AJAX flows
- [09-dependencies.md](09-dependencies.md) — the unused Vite pipeline
- [16-performance.md](16-performance.md) — backend performance
- [21-technical-debt.md](21-technical-debt.md) — the Blade-component refactor plan