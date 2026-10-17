# DEVELOPER_SETUP.md

**Purpose:** Prerequisites (PHP 8.2, Composer, Node, MySQL), install steps (`composer install`, `npm install`,
`npm run build`, `.env`, `php artisan key:generate`), how routes/views map, and the fact there are NO domain
migrations so the DB must be imported from the SQL dump. Note the shared database caveat with the dashboard app.

---

## 1. Prerequisites

| Tool | Version/Requirement | Notes |
|---|---|---|
| PHP | **8.2** | Matches `composer.json:9` (`^8.2`). `VERIFIED_FROM_SOURCE` |
| Composer | 2.x recommended | For dependencies (`razorpay/razorpay`, Laravel 12). `INFERRED` |
| Node.js | LTS (modern) | For Vite/Tailwind build (`vite 7`, `@tailwindcss/vite 4`). `INFERRED` (from `package.json`) |
| MySQL | Compatible with utf8mb4 | Default connection `mysql`. `VERIFIED_FROM_SOURCE` ([`config/database.php:46`](../../config/database.php#L46)) |
| Git | Any recent | Repository state: 4 commits, latest `a864d15`. `VERIFIED_FROM_SOURCE` ([`../audit/VERIFIED_FACTS.md:153-156`](../audit/VERIFIED_FACTS.md#git--repository-state)) |

Platform: `win32` (audit environment). `VERIFIED_FROM_SOURCE` ([`../audit/VERIFIED_FACTS.md` env section context]).

---

## 2. Clone/repo state

- Repo root in audit: `C:\xampp\htdocs\pauls` (contains `palVarnan/`, `palVarnanDashboard/`, `palvarnan_db (2).sql`).
- This app: `C:\xampp\htdocs\pauls\palVarnan`.
- **Shared DB:** both apps point to `palvarnan_db` ([`../01-project-inventory.md:36`](../01-project-inventory.md#1-what-kind-of-project-is-this)).
  `VERIFIED_FROM_SOURCE`.

---

## 3. Installation (standard Laravel 12 flow)

1. **Install PHP dependencies**
   ```bash
   composer install
   ```
   Verifies `razorpay/razorpay: ^2.9` is present (`composer.json:12`). `VERIFIED_FROM_SOURCE`.

2. **Install Node dependencies**
   ```bash
   npm install
   ```

3. **Build assets (Vite)**
   ```bash
   npm run build
   ```
   Uses `vite.config.js` (Laravel + Tailwind plugins). `VERIFIED_FROM_SOURCE`.

4. **Environment file**
   ```bash
   cp .env.example .env  # or copy manually
   ```
   Review `.env` values. **Do not commit `.env`**. See [`CONFIGURATION_AND_ENV.md`](./CONFIGURATION_AND_ENV.md)
   for variable names (no secrets printed here). `VERIFIED_FROM_SOURCE`.

5. **Generate app key**
   ```bash
   php artisan key:generate
   ```

6. **Database import (REQUIRED — no domain migrations)**

   This app has **zero domain migrations** (only 3 stock Laravel migrations):
   - [`0001_01_01_000000_create_users_table.php`](../../database/migrations/0001_01_01_000000_create_users_table.php)
   - [`0001_01_01_000001_create_cache_table.php`](../../database/migrations/0001_01_01_000001_create_cache_table.php)
   - [`0001_01_01_000002_create_jobs_table.php`](../../database/migrations/0001_01_01_000002_create_jobs_table.php)

   Therefore **you cannot `php artisan migrate` to create the storefront schema**. You **must import** the
   provided SQL dump `palvarnan_db (2).sql` (from repo root `C:\xampp\htdocs\pauls\palvarnan_db (2).sql`) into
   MySQL and ensure `.env` points to that database:
   
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=palvarnan_db
   DB_USERNAME=root
   DB_PASSWORD=
   ```
   
   `VERIFIED_FROM_SOURCE` — [`VERIFIED_FACTS.md:27-29`](../audit/VERIFIED_FACTS.md#counts-verified_from_source),
   [`../01-project-inventory.md:36-38`](../01-project-inventory.md#1-what-kind-of-project-is-this).

7. **Serve**
   ```bash
   php artisan serve
   ```
   (or use your web server pointing to `public/`).

---

## 4. Shared database caveat (critical)

- **Both `palVarnan` and `palVarnanDashboard` connect to the same database `palvarnan_db`.**
- The **dashboard app owns all schema migrations** and is the canonical source of domain schema for both.
- Any schema change must be coordinated; manual `ALTER TABLE` drift is possible. `VERIFIED_FROM_SOURCE`
  ([`../01-project-inventory.md:36-38`](../01-project-inventory.md#1-what-kind-of-project-is-this)),
  [`../22-database-integrity.md`](../22-database-integrity.md).

---

## 5. How routes and views map (quick orientation)

- Routes defined in [`routes/web.php`](../../routes/web.php): **45** entries. Auth-protected group
  ([`routes/web.php:71-83`](../../routes/web.php#L71-L83)) covers checkout, address CRUD, thank-you.
  Other routes (cart/wishlist toggle/index, product/show, search) are mostly public. `VERIFIED_FROM_SOURCE`.
- Views: [`resources/views`](../../resources/views) — 23 Blade files (`pages/16`, `layouts/4`, `partials/1`, +2).
  Layout: [`layouts/app.blade.php`](../../resources/views/layouts/app.blade.php) includes `header/footer/country-modal`.
- Controllers: 10 in [`app/Http/Controllers/`](../../app/Http/Controllers). `VERIFIED_FROM_SOURCE`.
- Country gate: if `session('country_id')` unset, home redirects to `/select-country`
  ([`HomeController.php:13-15`](../../app/Http/Controllers/HomeController.php#L13-L15)).

See [`ROUTES_AND_ENDPOINTS.md`](./ROUTES_AND_ENDPOINTS.md), [`VIEWS_AND_TEMPLATES.md`](./VIEWS_AND_TEMPLATES.md),
[`CONTROLLERS.md`](./CONTROLLERS.md) for full mapping.

---

## 6. Environment & third-party keys (names only)

Referenced in config:
- Razorpay: `RAZORPAY_KEY`, `RAZORPAY_SECRET` ([`config/services.php:38-41`](../../config/services.php#L38-L41))
- Shiprocket: `SHIPROCKET_EMAIL`, `SHIPROCKET_PASSWORD`, `SHIPROCKET_PICKUP_LOCATION`, `SHIPROCKET_PICKUP_PINCODE`,
  `SHIPROCKET_BASE_URL` (used in service default; env not explicitly named in `.env` but service defaults;
  also present in env scan) ([`config/services.php:43-48`](../../config/services.php#L43-L48),
  [`app/Services/ShiprocketService.php:15`](../../app/Services/ShiprocketService.php#L15))
- Mail: `MAIL_MAILER`, `MAIL_HOST`, etc. (`.env` shows `MAIL_MAILER=log`)
- App: `APP_ENV`, `APP_DEBUG`, `APP_KEY`, `APP_URL`, `MAIN_URL` ([`.env`](../../.env))

**Never** put real secret values in docs. `VERIFIED_FROM_SOURCE`.

---

## 7. Known setup notes / caveats

- **No domain migrations:** import SQL dump. `MUST` — not optional.
- **Queue:** `QUEUE_CONNECTION=sync` in current `.env` → jobs run synchronously. `VERIFIED_FROM_SOURCE`.
- **Cache/session:** `file` driver by default in current `.env`. `VERIFIED_FROM_SOURCE`.
- **Trust proxies:** set to `*` in `bootstrap/app.php:14`. `VERIFIED_FROM_SOURCE`.
- **Root test scripts exist:** `test_apis.php`, `test_services.php` in repo root — contain sensitive-looking
  integration scaffolding in audit; **do not reproduce**. Refer to [`../13-security-audit.md`](../13-security-audit.md).
  `VERIFIED_FROM_SOURCE`.

---

## 8. Related

- [`../01-project-inventory.md`](../01-project-inventory.md)
- [`../17-devops-deployment.md`](../17-devops-deployment.md)
- [`../18-configuration-and-env.md`](../18-configuration-and-env.md)
- [`CONFIGURATION_AND_ENV.md`](./CONFIGURATION_AND_ENV.md)