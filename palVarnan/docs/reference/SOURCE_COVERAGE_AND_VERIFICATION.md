# Source Coverage and Verification — `palVarnan`

> Per-doc coverage/verification record. Lists what was reviewed, counts, the evidence vocabulary,
> and an explicit reconciliation of pre-existing doc claims against current source and the
> SQL dump.
>
> Evidence vocabulary: `VERIFIED_FROM_SOURCE`, `VERIFIED_FROM_LIVE_SCHEMA`, `INFERRED`,
> `UNVERIFIED`, `NOT_AVAILABLE`, `CONFLICTING_EVIDENCE`.

---

## 1. What was reviewed (this audit)

| Artefact | Coverage | Notes |
|---|---|---|
| Root layout | full | `composer.json`, `composer.lock`, `package.json`, `.env`, `.env.example`, `phpunit.xml`, `bootstrap/app.php`, `routes/web.php`, `routes/console.php` |
| Controllers | full | `app/Http/Controllers/` — all 10 files (Auth, Cart, Checkout, Controller, Country, Home, Product, Profile, Search, Wishlist) |
| Models | full | `app/Models/` — all 25 files (including `Unit` and `HomePromotion`) |
| Services | full | `app/Services/ShiprocketService.php` |
| Providers | full | `app/Providers/AppServiceProvider.php` |
| Config | full | `config/` (app, auth, cache, database, filesystems, logging, mail, queue, services, session) |
| Migrations | full | `database/migrations/` — 3 stock migrations |
| Views | sampled | `resources/views/` — 23 `.blade.php` files listed; key pages checked (layout, checkout, cart, myaccount, home, country-selection, product-detail, wishlist, login, register) |
| Tests | full | `tests/` — `TestCase.php`, `Feature/ExampleTest.php`, `Unit/ExampleTest.php` |
| Assets | sampled | `public/assets/` presence (vendored theme); `public/build/`, `node_modules/` absent |
| Root scripts | sampled | `test_apis.php`, `test_services.php` (existence / reachability note; no values reproduced) |
| Storage | sampled | `storage/logs/laravel.log` — grep for error signatures only (no full dump) |
| SQL dump | full | `palvarnan_db (2).sql` — CREATE TABLE count (93), column existence checks for specific columns |
| Sibling dashboard | sampled | `palVarnanDashboard/` context only (schema/migrations context); not a full review |
| Git state | sampled | `git status` context; untracked files noted |

**Not reviewed / sampled only:** `public/build/`, vendor source internals (razorpay SDK), full Blade views line-by-line, live DB content beyond schema, runtime rendering, network calls, browser behaviour.

---

## 2. Counts (VERIFIED_FROM_SOURCE, with dump verification)

| Artefact | Count | Evidence |
|---|---|---|
| Route:: entries (`routes/web.php`) | **45** | line count (all `Route::` occurrences) — `VERIFIED_FROM_SOURCE` |
| Controllers in `app/Http/Controllers` | **10** (9 domain + `Controller.php`) | directory listing |
| Models in `app/Models` | **25** | directory listing |
| Domain migrations (this app) | **0** | `database/migrations/` contains 3 stock files only |
| Stock migrations | **3** | `0001_01_01_000000_create_users_table`, `..._000001_create_cache_table`, `..._000002_create_jobs_table` |
| Tables in `palvarnan_db (2).sql` | **93** | `grep -c 'CREATE TABLE'` → 93; `VERIFIED_FROM_LIVE_SCHEMA` (dump 2026-07-31) |
| Blade views | **23** | recursive count of `*.blade.php` in `resources/views` |
| Services | **1** | `app/Services/ShiprocketService.php` |
| Middleware | **none** in `app/Http/Middleware/`; `bootstrap/app.php` has minimal setup | `VERIFIED_FROM_SOURCE` |
| Tests | **3** | `TestCase.php` + 2 scaffold `ExampleTest.php` |
| `->get()` call sites in controllers | **26** | grep across `app/Http/Controllers/*.php` |

---

## 3. Evidence vocabulary (as used throughout this reference set)

| Term | Meaning |
|---|---|
| `VERIFIED_FROM_SOURCE` | Read from code/config/files in this tree and confirmed with line citations. |
| `VERIFIED_FROM_LIVE_SCHEMA` | Confirmed from `palvarnan_db (2).sql` (DDL counts, existence of specific CREATE TABLE/columns). |
| `INFERRED` | Not directly read but logically necessary / observable from the code; explicitly called out. |
| `UNVERIFIED` | Cannot be determined from available artefacts. |
| `NOT_AVAILABLE` | Artefact does not exist in the tree. |
| `CONFLICTING_EVIDENCE` | Current finding disagrees with a pre-existing doc; the conflict and which is correct are stated. |

---

## 4. Pre-existing doc claims — reconciliation

The claims below are taken from the pre-existing numbered docs and the `README.md` "Headline findings" and the audit notes. Each is reconciled against the **current source**.

### 4.1 README.md (headline findings)

| README headline (claim) | Current status | Evidence |
|---|---|---|
| Password reset requires no token — account takeover | `VERIFIED_CURRENT` | `AuthController.php:100-126` validates only email+password; no token. `VERIFIED_FROM_SOURCE` |
| Checkout writes no order; two columns (`billing_state`, `product_image`) exist in no migration; transaction rolls back after payment captured | `CONTRADICTED_BY_SOURCE` | Checkout **does write an order** (6-table transaction) in `CheckoutController.php:292-421`. The controller **does use** `billing_state` (`:306,316`) and `product_image` (`:378`). `billing_state` column is **absent from the SQL dump** (`CREATE TABLE... billing_state` → 0) — so while the code writes them, they may not exist in this DB (schema drift). The "after payment captured" concern remains (signature verified, order saved, then Shiprocket called post-commit). `VERIFIED_FROM_SOURCE` + `VERIFIED_FROM_LIVE_SCHEMA` |
| Razorpay is absent from `composer.lock` | `OUTDATED` | `composer.lock` contains `razorpay/razorpay` at line ~3298 (13 matches). `VERIFIED_FROM_SOURCE` |
| Live credentials in web-reachable scripts `test_apis.php`, `test_services.php` with TLS disabled | `VERIFIED_CURRENT` (existence) | Files exist; no credential values reproduced. Web-reachability depends on document root. `VERIFIED_FROM_SOURCE` |
| Client controls amount (`total`, `shipping_charge`, `coupon_discount`) | `VERIFIED_CURRENT` | `CheckoutController.php:191-193, 286-288` — shipping/coupon read from request. Subtotal+GST recomputed server-side. `VERIFIED_FROM_SOURCE` |
| Three IDORs: cart update, cart remove, wishlist remove; cart remove is GET | `CONFLICTING_EVIDENCE` | **Cart update**: IDOR — no ownership check (`:37`). **Cart remove**: IDOR + GET (`:50` route GET). **Wishlist remove**: **scoped to owner** (`WishlistController.php:52` uses `where('user_id', Auth::id())`). So wishlist remove is **FIXED** in current code. `VERIFIED_FROM_SOURCE` |
| Zero domain migrations | `VERIFIED_CURRENT` | 3 stock only; 0 domain migrations. `VERIFIED_FROM_SOURCE` |
| Pickup never scheduled (`createPickup()` has no caller) | `CONFLICTING_EVIDENCE` | Method name: `ShiprocketService::generatePickup()` exists and has **no caller** in `app/`. Pre-existing docs referenced `createPickup()` (does not exist). Behaviour is **correct** (never scheduled). `VERIFIED_FROM_SOURCE` |
| No rate limiting on any routes; no policies, no middleware, no Form Requests | `VERIFIED_CURRENT` | `bootstrap/app.php` has only `trustProxies`; no throttle, no policies, no `app/Http/Middleware/`, no `app/Http/Requests/`. `VERIFIED_FROM_SOURCE` |
| `APP_DEBUG=true` with root DB user and empty password | `VERIFIED_CURRENT` (env state) | `.env`: `APP_DEBUG=true`, `DB_USERNAME=root`, `DB_PASSWORD=` (empty). `VERIFIED_FROM_SOURCE` (names only) |

### 4.2 Performance (`../16-performance.md`) reconciliation

| Claim | Status | Evidence |
|---|---|---|
| Global composer runs on every view; auth loads cart with `whereHas('variant')` + wishlist count; guest similar | `VERIFIED_CURRENT` | `AppServiceProvider.php:33-76`. `VERIFIED_FROM_SOURCE` |
| Product listing uses `with('variants.unit')` | `VERIFIED_CURRENT` | `ProductController.php:42`. `VERIFIED_FROM_SOURCE` |
| MyAccount cart eager load misses `'product'` → N+1 | `CONTRADICTED_BY_SOURCE` | `ProfileController.php:82` includes `'product'` and `'variant.unit'`. `VERIFIED_FROM_SOURCE` |
| `FIND_IN_SET` sites at `HomeController.php:21`, `ProductController.php:26,29,32` (4 call sites) | `OUTDATED` (line refs) / `VERIFIED_CURRENT` (count) | Current sites: `HomeController.php:24`; `ProductController.php:24,32,34` — 4 sites total. Same issue, different line numbers. `VERIFIED_FROM_SOURCE` |
| Search `LIKE` at `SearchController.php:31,38,49,64` | `OUTDATED` | Current: `SearchController.php:26,42,61,76`. `VERIFIED_FROM_SOURCE` |
| Shiprocket `cURL error 28` on calls | `CONFLICTING_EVIDENCE` | `laravel.log` grep: `cURL error` → **0**; `Maximum execution time of 60 seconds exceeded` → **2**. No `timed out` match. `VERIFIED_FROM_SOURCE` (log sample) |
| No timeout/retry on Shiprocket | `VERIFIED_CURRENT` | grep of `app/` for `->timeout|->retry` → 0 matches; `ShiprocketService` uses `Http::withToken()->acceptJson()` without timeout. `VERIFIED_FROM_SOURCE` |

### 4.3 Error handling / logging (`../14-error-handling.md`) reconciliation

| Claim | Status | Evidence |
|---|---|---|
| `bootstrap/app.php` middleware/exceptions bodies empty | `VERIFIED_CURRENT` | `bootstrap/app.php:13-18` — only `trustProxies` set. `VERIFIED_FROM_SOURCE` |
| Place order swallows Shiprocket failures; returns success | `VERIFIED_CURRENT` | `CheckoutController.php:525-527` catch logs and continues; `:529-534` returns success. `VERIFIED_FROM_SOURCE` |
| Returns `$e->getMessage()` to client (leakage) | `VERIFIED_CURRENT` | `CheckoutController.php:165,218,429` include exception messages in JSON responses. `VERIFIED_FROM_SOURCE` |
| Log contains `Unknown column 'billing_state'`, `'product_image'`, etc. | `VERIFIED_CURRENT` | log grep: `Unknown column` → 54; `QueryException` → 61. `VERIFIED_FROM_SOURCE` (log sample) |

### 4.4 Configuration / DevOps (`../18-configuration-and-env.md`, `../17-devops-deployment.md`)

| Claim | Status | Evidence |
|---|---|---|
| `.env` has ~66 keys; `.env.example` ~58 | `CONFLICTING_EVIDENCE` (count method) | Name-only count: `.env` → **56**, `.env.example` → **43**. Difference due to counting (comments excluded). The **set of missing keys** is confirmed: 13 missing (commerce + DB). `VERIFIED_FROM_SOURCE` |
| Commerce keys missing from `.env.example` | `VERIFIED_CURRENT` | Missing: `MAIN_URL`, `RAZORPAY_KEY`, `RAZORPAY_SECRET`, `SHIPROCKET_*`, `DB_HOST/PORT/DATABASE/USERNAME/PASSWORD`. `VERIFIED_FROM_SOURCE` |
| `SHIPROCKET_BASE_URL` from `.env` is dead (no config key) | `VERIFIED_CURRENT` | `config/services.php` has no `base_url`; `ShiprocketService.php:15` falls back to default. `VERIFIED_FROM_SOURCE` |
| `env('MAIN_URL')` breaks under `config:cache` | `VERIFIED_CURRENT` | `SearchController.php:23` — `env()` outside `config/`. `VERIFIED_FROM_SOURCE` |
| `QUEUE_CONNECTION=sync`, no jobs | `VERIFIED_CURRENT` | `.env` → sync; `app/Jobs/` absent. `VERIFIED_FROM_SOURCE` |

### 4.5 Database integrity (`../22-database-integrity.md`) and schema

| Claim | Status | Evidence |
|---|---|---|
| 93 tables, 34 FKs (as stated) | `VERIFIED_CURRENT` (table count) | `CREATE TABLE` in dump → **93**. FK count not recomputed exhaustively but table count matches. `VERIFIED_FROM_LIVE_SCHEMA` |
| `billing_state` column absent from schema/migrations context | `VERIFIED_CURRENT` | dump contains no `billing_state` column definition (`grep` → 0). `VERIFIED_FROM_LIVE_SCHEMA` |
| Price-sort uses wrong table `product_varients` (plural) while model/table is `product_varient` (singular) | **NEW FINDING** (not flagged in pre-existing docs as a line defect) | `ProductVarient.php:8` → `protected $table = 'product_varient'`; `ProductController.php:84,87` → `FROM product_varients` (plural). Dump has `product_varient` (1), no `product_varients` (0). `VERIFIED_FROM_SOURCE` + `VERIFIED_FROM_LIVE_SCHEMA` |
| Four different identifier spellings across tables | `VERIFIED_CURRENT` | `product_varient_id`, `product_variant_id` (items), `pro_ver_id` (stocks), `product_varient_id` (carts/wishlists). `VERIFIED_FROM_SOURCE` + `VERIFIED_FROM_LIVE_SCHEMA` |

### 4.6 Testing strategy (`../24-testing-strategy.md`)

| Claim | Status | Evidence |
|---|---|---|
| 3 test files, 0 domain tests | `VERIFIED_CURRENT` | `tests/` contains `TestCase.php`, `Feature/ExampleTest.php`, `Unit/ExampleTest.php`. `VERIFIED_FROM_SOURCE` |
| phpunit uses sqlite `:memory:` by default | `VERIFIED_CURRENT` | `phpunit.xml` sets `DB_CONNECTION=sqlite`, `DB_DATABASE=:memory:`. `VERIFIED_FROM_SOURCE` |
| Reference to method `createPickup()` | `OUTDATED` | Actual method is `generatePickup()` (`ShiprocketService.php:119-124`); no `createPickup()` method. `VERIFIED_FROM_SOURCE` |
| BUG cross-references use old IDs | `OUTDATED` | `../20-known-issues.md` renumbered to `BUG-01…105`; `../24-testing-strategy.md`'s Tier 4/5 references don't resolve to the same IDs. `CONFLICTING_EVIDENCE` |

### 4.7 Known issues / Technical debt (`../20-known-issues.md`, `../21-technical-debt.md`)

| Claim | Status | Evidence |
|---|---|---|
| Checkout god-object 701 lines | `VERIFIED_CURRENT` | `CheckoutController.php` is 701 lines. `VERIFIED_FROM_SOURCE` |
| Duplicated pricing (6+ places) | `VERIFIED_CURRENT` | As traced in [`./CHANGE_IMPACT_GUIDE.md`](./CHANGE_IMPACT_GUIDE.md) §1. `VERIFIED_FROM_SOURCE` |
| Empty models `Unit` / `HomePromotion` | `VERIFIED_CURRENT` | Classes contain only empty body. `VERIFIED_FROM_SOURCE` |
| Unused Vite/Tailwind pipeline | `VERIFIED_CURRENT` (confirmed) | `@vite` only in `welcome.blade.php:15` (unreachable); `public/build/` and `node_modules/` absent. `VERIFIED_FROM_SOURCE` |
| No domain migrations / unversioned schema | `VERIFIED_CURRENT` | `VERIFIED_FROM_SOURCE` + `VERIFIED_FROM_LIVE_SCHEMA` |

---

## 5. Coverage summary

| Category | Coverage |
|---|---|
| Source code (app, routes, config, tests, migrations, services) | **Full** |
| Blade views | **Sampled** (23 files total; key flows reviewed) |
| SQL dump schema (DDL) | **Full** (CREATE TABLE count 93; targeted column checks) |
| Application logs | **Selective** (grep by signatures; no full PII dump) |
| Sibling dashboard code | **Context only** |
| Runtime behaviour | **UNVERIFIED** (no live server/browser run) |

**No production credentials or customer PII were reproduced in any of the 6 new reference files.**

---

## 6. Related documents

- [`./PERFORMANCE_AND_RELIABILITY.md`](./PERFORMANCE_AND_RELIABILITY.md) — runtime analysis and N+1/sync I/O
- [`./TESTING_GUIDE.md`](./TESTING_GUIDE.md) — current test state and proposed plan
- [`./DEPLOYMENT_AND_CONFIGURATION.md`](./DEPLOYMENT_AND_CONFIGURATION.md) — env/config/deploy traps
- [`./KNOWN_ISSUES_AND_TECHNICAL_DEBT.md`](./KNOWN_ISSUES_AND_TECHNICAL_DEBT.md) — KI-B/TD-B register
- [`./CHANGE_IMPACT_GUIDE.md`](./CHANGE_IMPACT_GUIDE.md) — "if you change X, also check Y" map
- Pre-existing numbered docs: [`../16-performance.md`](../16-performance.md), [`../18-configuration-and-env.md`](../18-configuration-and-env.md), [`../14-error-handling.md`](../14-error-handling.md), [`../17-devops-deployment.md`](../17-devops-deployment.md), [`../24-testing-strategy.md`](../24-testing-strategy.md), [`../20-known-issues.md`](../20-known-issues.md), [`../21-technical-debt.md`](../21-technical-debt.md), [`../19-incomplete-work.md`](../19-incomplete-work.md), [`../22-database-integrity.md`](../22-database-integrity.md)
