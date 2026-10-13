# 17 — DevOps and Deployment

---

## 1. Current state: no deployment infrastructure exists

| Asset | Status |
|---|---|
| `Dockerfile` / `docker-compose.yml` | **Absent** |
| `.github/workflows/` | **Absent** — no CI of any kind |
| `Procfile` | **Absent** |
| `deploy/`, `.deploy/` | **Absent** |
| `nginx.conf` / `apache` vhost | **Absent** |
| `.env.example` | **Present but incomplete** — missing `MAIN_URL`, `RAZORPAY_*`, `SHIPROCKET_*` |
| Web server config | XAMPP defaults only |
| Database provisioning | Manual `mysqldump` import from `palvarnan_db (2).sql` |
| `composer.lock` | **Stale** — Razorpay missing |
| Asset build | **Broken** — `node_modules` and `public/build` absent, no `@vite` in views |
| Process manager | **Absent** |
| Monitoring / alerting | **Absent** |

**There is no repeatable way to deploy this application.** A new environment must be reconstructed by hand from the SQL dump plus knowledge of the unrecorded `ALTER TABLE` statements.

---

## 2. Runtime prerequisites

| Requirement | Version | Source | Notes |
|---|---|---|---|
| PHP | `^8.2` | `composer.json:9` | `php -v` **not verified** in this audit |
| PHP extensions | `ext-json` (Razorpay), `pdo_mysql`, `mbstring`, `openssl`, `curl`, `dom`, `fileinfo`, `tokenizer`, `ctype`, `xml`, `bcmath` *(not used but conventional)* | Razorpay `composer.json` + Laravel 12 | `bcmath` is **not used** — money is handled in floats |
| Composer | 2.x | — | |
| MySQL | **UNVERIFIED** | `config/database.php` default | 93 tables, 34 FKs, `utf8mb4` |
| Node.js | **not pinned** — no `.nvmrc` | `package.json` | Only needed for the unused Vite pipeline |
| Web server | Apache/Nginx + PHP-FPM | XAMPP | Must serve `public/`, never the project root |

**PHP extension check:** `composer.json` declares no `ext-*` requirements beyond what Razorpay needs, so `composer install` will not catch a missing extension. Verify manually with `php -m`.

---

## 3. Local setup (XAMPP, current working method)

### Verified working sequence

```bash
# 1. Web server
#    XAMPP Apache + MySQL running; document root must map to this project's public/

# 2. PHP dependencies
cd C:\xampp\htdocs\pauls\palVarnan
composer install

# 3. Environment
cp .env.example .env        # Windows: copy .env.example .env
php artisan key:generate

# 4. Database  ← THIS DOES NOT CREATE THE SCHEMA
#    The dump must be imported manually:
mysql -u root -p < "C:\xampp\htdocs\pauls\palvarnan_db (2).sql"

# 5. Permissions (Linux/macOS)
chmod -R 775 storage bootstrap/cache
```

### What `php artisan migrate` actually does

```bash
php artisan migrate
```

Runs **3 migrations**:

| Migration | Tables |
|---|---|
| `0001_01_01_000000_create_users_table.php` | `users`, `password_reset_tokens`, `sessions` |
| `0001_01_01_000001_create_cache_table.php` | `cache`, `cache_locks` |
| `0001_01_01_000002_create_jobs_table.php` | `jobs`, `job_batches`, `failed_jobs` |

**Zero domain tables.** `products`, `carts`, `product_orders` and the other ~90 tables come from the SQL dump.

### Traps

| Trap | Consequence |
|---|---|
| `composer run setup` | Runs `migrate --force`, then `npm install && npm run build` — **the npm step fails**, and the migration creates no domain tables. It *looks* like a successful install but yields a non-functional app |
| `composer run dev` | Runs `artisan serve` + `queue:listen` + `pail` + `npm run dev` — **fails at the Vite step**. No queue jobs exist to listen for |
| `composer install` on a fresh clone | Razorpay is **not in the lock file** ⇒ not installed ⇒ every online payment fails |
| Missing `billing_state` / `shipping_state` / `product_image` | Checkout 500s after payment is captured |
| `MAIN_URL` missing from `.env.example` | Search images resolve to `/images/…` |
| Document root at project root | **`test_apis.php` and `test_services.php` become web-reachable** — live credentials exposed |

---

## 4. Required environment variables

`.env` in this project contains **66 keys**; `.env.example` contains only **58**, omitting the eight that actually matter for commerce.

### Missing from `.env.example` — must be added

```dotenv
MAIN_URL=http://127.0.0.1:8001/

RAZORPAY_KEY=
RAZORPAY_SECRET=

SHIPROCKET_EMAIL=
SHIPROCKET_PASSWORD=
SHIPROCKET_PICKUP_LOCATION=Home
SHIPROCKET_PICKUP_PINCODE=641006
SHIPROCKET_BASE_URL=https://apiv2.shiprocket.in/v1/external
```

`DB_PORT` is also absent from `.env.example` (defaults to 3306 via `config/database.php`).

### `SHIPROCKET_BASE_URL` is dead

`config/services.php` has **no `base_url` key**, so `ShiprocketService.php:15`'s
`config('services.shiprocket.base_url', 'https://apiv2.shiprocket.in/v1/external')`
always falls back to the default. **Editing `SHIPROCKET_BASE_URL` in `.env` has no effect.** Add the key to the config to make sandbox switching possible.

### Values that must change before going live

| Key | Current (`.env`) | Required |
|---|---|---|
| `APP_ENV` | `local` | `production` |
| `APP_DEBUG` | **`true`** | **`false`** |
| `APP_URL` | `http://localhost` | real HTTPS URL |
| `DB_USERNAME` | **`root`** | dedicated least-privilege user |
| `DB_PASSWORD` | **empty** | strong secret |
| `MAIL_MAILER` | `log` | `smtp` or SES — **password reset currently only works via the log file** |
| `MAIL_FROM_ADDRESS` | `hello@example.com` | a real sender |
| `SESSION_SECURE_COOKIE` | unset | `true` behind HTTPS |
| `SESSION_ENCRYPT` | `false` | `true` |
| `QUEUE_CONNECTION` | `sync` | `database` or `redis` — required for the Shiprocket job |
| `CACHE_STORE` | `file` | `redis` for multi-node (Shiprocket token) |
| `LOG_LEVEL` | `debug` | `info` |
| `LOG_CHANNEL` | `stack`/`single` | `daily` with retention |
| `BCRYPT_ROUNDS` | `12` | fine |

---

## 5. Production checklist

### Before deploying

- [ ] `APP_DEBUG=false`, `APP_ENV=production`
- [ ] `php artisan config:cache` — **only after fixing `SearchController`'s `env('MAIN_URL')`**
- [ ] `php artisan route:cache`, `php artisan view:cache`
- [ ] Web server document root → `public/` (**not** the project root)
- [ ] `composer install --no-dev --optimize-autoloader`
- [ ] **Razorpay present in `composer.lock`** (currently not)
- [ ] `php artisan storage:link` if any public uploads exist
- [ ] TLS certificate installed; force HTTPS
- [ ] `debug` log level off; `daily` channel with retention
- [ ] Security headers: HSTS, `X-Frame-Options`/`frame-ancestors`, `X-Content-Type-Options`, `Referrer-Policy`, CSP

### Database

- [ ] Import `palvarnan_db (2).sql`
- [ ] **Apply the three missing `ALTER TABLE` statements** — see [22-database-integrity.md](22-database-integrity.md)
- [ ] **Import the dashboard's ~57 migrations or apply their schema**, since this app has no domain migrations
- [ ] Create a least-privilege DB user
- [ ] Set `innodb_flush_log_at_trx_commit=1`, `max_connections` appropriately
- [ ] Enable binlog archiving for point-in-time recovery
- [ ] **Backups scheduled and a restore actually tested**
- [ ] `session:prune` scheduled (no pruning task exists — `routes/console.php` is stock)

### Secrets

- [ ] **`test_apis.php` and `test_services.php` deleted**
- [ ] **Razorpay key and secret rotated**
- [ ] **Shiprocket password rotated**
- [ ] **MySQL password rotated** (`root` with an empty password is currently in `.env`)
- [ ] `.env` outside the web root, mode `600`, never committed
- [ ] `.gitignore` extended with `test_*.php`, `*.sql`

### Third-party

- [ ] Razorpay webhook endpoint (for reliable payment confirmation) — **does not exist**
- [ ] Shiprocket webhook receiver — **does not exist**
- [ ] Live/test mode separation verified — currently driven only by `.env` values

---

## 6. CI/CD — what is missing

No `.github/workflows/`, no `.gitlab-ci.yml`, no Jenkinsfile. **Nothing runs on commit or pull request.**

### Recommended minimum pipeline

```yaml
name: CI
on: [push, pull_request]
jobs:
  test:
    runs-on: ubuntu-latest
    services:
      mysql: { image: mysql:8, env: { MYSQL_ROOT_PASSWORD: root }, ports: ['3306:3306'] }
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with: { php-version: '8.2', coverage: none }
      - run: composer install --no-progress --prefer-dist
      - run: cp .env.example .env
      - run: php artisan key:generate
      - run: php artisan migrate --force
      - run: vendor/bin/phpunit          # currently 2 placeholder tests
      - run: vendor/bin/pint --test       # currently never run
```

Add a deploy stage only after the above is green. Note that CI needs the SQL dump or real migrations to build a working database — see §7.

---

## 7. The deployment blocker

**This application cannot be deployed from version control today.** Verified:

| Blocker | Evidence |
|---|---|
| Domain schema is unversioned here | `database/migrations/` has 3 Laravel defaults; the ~90 domain tables live in `palVarnanDashboard` |
| Checkout migrations exist nowhere | No file creates `billing_state`, `shipping_state`, `product_image` |
| Razorpay is unresolvable | Absent from `composer.lock` and `installed.json` |
| The checkout feature is uncommitted | `app/Services/`, 6 order models, `thankyou.blade.php`, and modified `CheckoutController`/`routes/web.php` are all untracked |
| Asset build is broken | `npm install`/`npm run build` produce output no view references |

**Minimum to make this deployable:**
1. Commit the currently untracked source.
2. Create the three missing migrations.
3. Re-lock Razorpay.
4. Decide the schema's home: either move all domain migrations into this app, or document `palVarnan.sql` as a required artifact and version it.

---

## 8. Monitoring and alerting

**None.** `config/logging.php` is stock — no Sentry, Bugsnag, Slack, or Papertrail channel. `routes/console.php` is stock Laravel, so **no scheduled task exists** (no `session:prune`, no order reconciliation, no Shiprocket status sync in this app).

Minimum viable additions:

| Signal | Why |
|---|---|
| `5xx` rate alert | Checkout failures are currently invisible until a customer complains |
| Shiprocket API failure counter | Failures are swallowed at `:525-527` |
| "Unknown column" log signature | Would have caught SEC-04 immediately |
| Cart-to-order conversion | Detects checkout breakage |
| Razorpay `payment_status=Paid` with no `product_orders` row | **The money-loss signature** from SEC-04 |

---

## 9. Related documents

- [09-dependencies.md](09-dependencies.md) — Razorpay lock defect, unused Vite pipeline
- [10-file-structure.md](10-file-structure.md) — git state, untracked files
- [18-configuration-and-env.md](18-configuration-and-env.md) — full env reference
- [19-incomplete-work.md](19-incomplete-work.md) — missing infrastructure
- [22-database-integrity.md](22-database-integrity.md) — schema bootstrap
- [29-remediation-plan.md](29-remediation-plan.md) — sequenced plan