# 09 — Dependencies

Every declared and installed package, what actually works, and what is missing.

---

## 1. Runtime stack

| Component | Declared version | Installed | Notes |
|---|---|---|---|
| PHP | `^8.2` | **UNVERIFIED** — `php -v` not run in this audit | XAMPP PHP 8.2 assumed |
| Laravel Framework | `^12.0` | **UNVERIFIED** (no `vendor/composer/installed.json` entry readable for version) | Laravel 12 skeleton layout confirmed by `bootstrap/app.php` |
| Razorpay PHP SDK | `^2.9` | **BROKEN — see §3** | |
| MySQL | via `config/database.php` | `palvarnan_db` | |
| Node / npm | not pinned (`.nvmrc` absent) | **`node_modules` MISSING** | |
| Vite | `^7.0.7` | not installed | |
| Tailwind CSS | `^4.0.0` | not installed | **Unused — see §4** |
| Laravel Pint | `^1.24` | installed (dev) | Never run |

`composer.json` `require` block is only 4 entries — `php`, `laravel/framework`, `laravel/tinker`, `razorpay/razorpay`. **No Sanctum, no Spatie permissions, no Scout, no Telescope, no Clockwork, no Ignition.**

---

## 2. Full dependency picture

`vendor/composer/installed.json` resolves **86 packages**. Notable absences:

| Package | Expected? | Consequence |
|---|---|---|
| `laravel/sanctum` | No | No API token auth — consistent with a pure server-rendered app |
| `spatie/laravel-permission` | **No** — yet `roles`, `permissions`, `model_has_roles`, `role_has_permissions`, `permission_has_roles` tables exist | Those tables were created by the **dashboard** app. This app does no role checks at all |
| `laravel/telescope` | **No** — yet `telescope_entries`, `telescope_entries_tags`, `telescope_monitoring` tables exist in the dump | Leftover from an install that was removed |
| `barryvdh/laravel-debugbar` | No | No request inspector in dev |
| `spatie/laravel-medialibrary` | No | Product images are raw `<img src>` paths |
| `barryvdh/laravel-dompdf` | No | **No invoice/receipt PDF** |
| `intervention/image` | No | No image resizing; 50 Unsplash demo images ship at full size |

**Installed dev tooling:** `phpunit/phpunit ^11.5.3`, `mockery`, `fakerphp/faker`, `laravel/pail`, `laravel/pint`, `laravel/sail`, `nunomaduro/collision`.

---

## 3. Razorpay — **BROKEN DEPENDENCY** — VERIFIED

This is the most consequential dependency defect found.

### Evidence

| Check | Result |
|---|---|
| `vendor/razorpay/razorpay/` exists on disk | **Yes** — full `src/` tree, `Api.php` present, `Razorpay.php` present |
| `"name": "razorpay/razorpay"` in `composer.lock` | **0 matches** |
| `razorpay` in `vendor/composer/installed.json` | **0 matches** |
| `razorpay` in `vendor/composer/installed.php` | **0 matches** |
| `Razorpay` in `vendor/composer/autoload_psr4.php` | **0 matches** |
| `Razorpay` in `vendor/composer/autoload_static.php` | **0 matches** |

**The package is physically present but was never registered with Composer's autoloader.** `vendor/razorpay/razorpay/composer.json` declares `psr-4: { "Razorpay\\Api\\": "src/" }`, but that mapping is absent from the generated autoload files because the package is not in the lock/installed manifests.

### Why the code still "works"

`CheckoutController.php:26-31` contains a manual loader:
```php
if (!class_exists('Razorpay\Api\Api')) {
    $razorpayAutoload = base_path('vendor/razorpay/razorpay/Razorpay.php');
    if (file_exists($razorpayAutoload)) {
        require_once $razorpayAutoload;
    }
}
```
This bypasses Composer entirely and hand-rolls class loading. `vendor/razorpay/razorpay/Razorpay.php` presumably `require`s `src/` itself.

### Consequences

1. **Log evidence:** `Class "Razorpay\Api\Api" not found` — **VERIFIED twice** in `storage/logs/laravel.log`. This is exactly the failure the manual loader was added to paper over.
2. **Not reproducible.** `composer install` on a fresh machine will **not** install Razorpay (it is not in the lock file) ⇒ `vendor/razorpay/` absent ⇒ the `file_exists()` guard fails ⇒ the guard silently does nothing ⇒ **every online payment fails with a class-not-found fatal.** The `if (file_exists(...))` means there is no error message about the missing package; it just dies later.
3. `composer.lock` is **stale relative to `composer.json`** — `composer.json` lists `razorpay/razorpay: ^2.9` but the lock's `content-hash` (`c514d8f7…`) predates that edit. Composer will warn "lock file is not up to date with the latest changes in composer.json" on every `composer install`.
4. The `require_once` sits at **file scope**, executed on every request that autoloads `CheckoutController`, including `GET /checkout` — a global side effect in a controller file.

### Fix

```bash
composer require razorpay/razorpay:^2.9 --no-update
composer update razorpay/razorpay
```
Then delete `CheckoutController.php:26-31`. Verify `vendor/composer/installed.json` contains the package and `autoload_psr4.php` maps `Razorpay\Api\` ⇒ `razorpay/razorpay/src/`.

---

## 4. Frontend dependencies — **DECLARED BUT UNUSED**

`package.json` declares 7 dev dependencies. The build config is intact (`vite.config.js` with `laravel-vite-plugin` + `@tailwindcss/vite`, inputs `resources/css/app.css` and `resources/js/app.js`).

**But none of it is used:**

| Check | Result |
|---|---|
| `@vite(...)` directives in any Blade file | **0** |
| `node_modules/` | **MISSING** |
| `public/build/` | **MISSING** |
| `resources/js/app.js` | **22 bytes** — effectively empty |
| `resources/css/app.css` | **390 bytes** — minimal |
| `resources/js/bootstrap.js` | 127 bytes — Laravel's default Axios setup, also unused |

Instead, `layouts/app.blade.php:13-27` loads **hand-copied template assets from `public/assets/` (9.4 MB)**:
```
bootstrap.min.css, select2.min.css, slick.css, jquery-ui.css,
animate.css, aos.css, main.css
+ toastr.css from cdnjs.cloudflare.com
```
and jQuery/Slick/Select2/AOS/toastr/SweetAlert2 from CDN at the bottom of the page.

**Consequences:**
- `composer run dev` (`artisan serve` + `queue:listen` + `pail` + `npm run dev`) **fails immediately** — Vite has nothing to serve.
- `composer run setup` fails at `npm install` → `npm run build` only if the network allows; the output is never referenced by any view.
- The whole asset pipeline is **dead weight**; deleting `package.json`, `vite.config.js`, `resources/css`, `resources/js` would not break a single page.
- 9.4 MB of vendored theme assets are committed to git, including 50 Unsplash demo JPEGs in `home.blade.php`.
- **Third-party assets load from public CDNs** (cdnjs, jQuery, SweetAlert2, Unsplash) — an availability and privacy dependency, and a CSP problem if one is ever added.

---

## 5. `ShiprocketService` — no SDK, hand-rolled HTTP

There is **no Shiprocket SDK dependency.** `app/Services/ShiprocketService.php` (200 lines, 6 KB) uses Laravel's `Http` facade directly:

```php
$this->client() = Http::withToken($this->getToken())->acceptJson();
```

| # | Method | Endpoint | Used by storefront? |
|---|---|---|---|
| 1 | `getToken()` | `POST /auth/login` (cached 8 h) | yes |
| 2 | `checkServiceability()` | `GET /courier/serviceability` | **yes** — `getShiprocketCouriers()` |
| 3 | `createOrder()` | `POST /orders/create/adhoc` | **yes** — `placeOrder()` |
| 4 | `assignAWB()` | `POST /courier/assign/awb` | **yes** |
| 5 | `generatePickup()` | `POST /courier/generate/pickup` | **No caller** |
| 6 | `generateManifest()` | `POST /manifests/generate` | **No caller** |
| 7 | `printManifest()` | `POST /manifests/print` | **No caller** |
| 8 | `generateLabel()` | `POST /courier/generate/label` | **No caller** |
| 9 | `printInvoice()` | `POST /orders/print/invoice` | **No caller** |
| 10 | `trackAWB()` | `GET /courier/track/awb/{awb}` | **No caller** |
| 11 | `getPickupLocations()` | `GET /settings/company/pickup` | **No caller** |

**7 of 11 methods are dead code** — pickup scheduling, manifests, labels and invoices are all unwired. Pickup scheduling is the operationally critical missing piece: orders are created on Shiprocket but **never scheduled for pickup**, so nothing gets collected.

### Service quality issues

| Issue | Line | Detail |
|---|---|---|
| No HTTP timeouts | all | `Http::get()/post()` with no `->timeout()`. The default is 30 s; a hung Shiprocket call blocks a PHP worker for the full duration |
| No retries | all | A single transient 502 fails the whole checkout |
| No `verify: false` bypass | — | Good — Laravel's `Http` verifies TLS by default. **Contrast with `test_apis.php`, which disables it** |
| Error bodies logged verbatim | `:41-44, :69-74, :90-95` | `Log::error('…', ['body' => $response->json()])` can log account metadata |
| Cache key from email | `:28` | `md5($email)` — change the email, orphan the cached token |
| **No 401 re-auth** | `:52-55` | If Shiprocket invalidates the token server-side, the cached 8 h token returns 401 and **every call fails** until the cache expires |
| Duplicated verbatim in the dashboard | — | `palVarnanDashboard/app/Services/ShiprocketService.php` — identical 200 lines. Fixes must be applied twice |

---

## 6. `test_apis.php` and `test_services.php` — dependency-adjacent risk

Two root-level scripts (**untracked in git**, `??` in `git status`) that:

- **Embed live Razorpay and Shiprocket credentials in source** (verified by reading the files; values deliberately not reproduced here).
- **Disable TLS certificate verification** (`CURLOPT_SSL_VERIFYPEER => false`, `CURLOPT_SSL_VERIFYHOST => 0`) — makes MITM interception trivial.
- **`test_apis.php` can create real orders** against the live Razorpay account (order create, payment fetch, refund).
- **`test_services.php` can execute DDL against the live database** (`ALTER TABLE`, `CREATE TABLE`) — it is very likely how `billing_state`, `shipping_state` and `product_image` were added outside version control.

Both sit in the **web root** (`C:\xampp\htdocs\…`), reachable over HTTP under XAMPP's default `DocumentRoot` if the vhost is configured that way.

**Recommendation: delete both, and rotate every credential that appears in them.** Detail in [13-security-audit.md](13-security-audit.md) SEC-01/SEC-02.

---

## 7. Composer scripts

| Script | What it does | Problem |
|---|---|---|
| `setup` | `composer install` → copy `.env` → `key:generate` → **`migrate --force`** → `npm install` → `npm run build` | **`migrate --force` runs 3 default migrations and does NOT create the domain schema.** It gives the false impression of a working install. The frontend steps fail (see §4) |
| `dev` | `serve` + `queue:listen` + `pail` + `npm run dev` via `concurrently` | fails at the Vite step; no queue jobs exist anyway |
| `test` | `config:clear` + `artisan test` | The only "meaningful" command; runs 2 placeholder tests |
| `post-autoload-dump` | `package:discover` | fine |
| `post-update-cmd` | `vendor:publish --tag=laravel-assets --force` | **overwrites `public/` assets on every `composer update`** — combined with 9.4 MB of committed theme assets this is a footgun |

`composer.json` `require` includes `laravel/tinker` in **production** dependencies, which is unnecessary.

---

## 8. Dependency risk summary

| # | Finding | Severity | Fix |
|---|---|---|---|
| DEP-01 | Razorpay absent from `composer.lock` and the autoloader; masked by a manual `require_once` | **CRITICAL** — unreproducible deploys, payments fail on fresh installs | `composer update razorpay/razorpay`, delete the manual loader |
| DEP-02 | `composer.lock` stale vs `composer.json` (`content-hash` mismatch) | **HIGH** | Re-lock |
| DEP-03 | `node_modules/` and `public/build/` missing; no `@vite` in any view | MEDIUM (cosmetic — nothing breaks) | Delete the pipeline, or adopt it properly |
| DEP-04 | 9.4 MB of vendored theme assets + 50 Unsplash images committed | MEDIUM | Prune; move to a CDN or a real product-image table |
| DEP-05 | Third-party JS/CSS from public CDNs | MEDIUM | Self-host, add a CSP |
| DEP-06 | `ShiprocketService` duplicated verbatim across both apps | HIGH | Extract to a shared package, or delete the storefront copy |
| DEP-07 | 7 of 11 Shiprocket methods unused — **no pickup scheduling** | **HIGH** (operational) | Wire `generatePickup()` after order creation |
| DEP-08 | No HTTP timeouts or retries on Shiprocket calls | HIGH | `->timeout(10)->retry(2, 200)` |
| DEP-09 | No 401 re-authentication path | MEDIUM | Detect 401, flush cache, retry once |
| DEP-10 | `setup` script implies a migration that does not exist | HIGH | Add real migrations or document the dump import |
| DEP-11 | `laravel/tinker` in production requires | LOW | Move to `require-dev` |
| DEP-12 | Telescope/RBAC tables exist with no corresponding packages | LOW | Document as dashboard-owned; drop if unused |

---

## 9. Related documents

- [12-configuration.md](18-configuration-and-env.md — `config/services.php`, `.env`
- [13-security-audit.md](13-security-audit.md) — SEC-01/02 credential exposure
- [27-api-reference.md](27-api-reference.md) — Shiprocket endpoint shapes
- [23-external-services.md](23-external-services.md) — Razorpay/Shiprocket configuration detail
- [30-project-status.md](30-project-status.md) — current readiness verdict