# Deployment and Configuration — `palVarnan`

> Scope: what must be configured, built, and deployed for the `palVarnan` storefront, the
> config-caching hazard, and the shared-database coupling with the dashboard app.
> This is the engineering checklist companion to [`../17-devops-deployment.md`](../17-devops-deployment.md)
> (deployability narrative) and [`../18-configuration-and-env.md`](../18-configuration-and-env.md)
> (full effective-config reference). Those documents remain authoritative for value-level detail;
> this file cites and links rather than repeats.
>
> **Never place credential values in this file.** Environment keys are named, never valued.
>
> Evidence vocabulary: `VERIFIED_FROM_SOURCE`, `VERIFIED_FROM_LIVE_SCHEMA`, `INFERRED`,
> `UNVERIFIED`, `NOT_AVAILABLE`, `CONFLICTING_EVIDENCE`.

---

## 1. Required environment keys (names only)

`.env` currently defines **56** keys; `.env.example` defines **43** (`VERIFIED_FROM_SOURCE`, key-name
extraction of both files). The following are the keys the application actually consumes.

### Framework / runtime

| Key | Consumed by | Note |
|---|---|---|
| `APP_NAME`, `APP_ENV`, `APP_KEY`, `APP_DEBUG`, `APP_URL` | `config/app.php` | `APP_DEBUG=true` in live `.env` — must be `false` off-local |
| `APP_LOCALE`, `APP_FALLBACK_LOCALE`, `APP_FAKER_LOCALE` | `config/app.php` | no `resources/lang/` |
| `APP_MAINTENANCE_DRIVER` | `config/app.php` | stock |
| `BCRYPT_ROUNDS` | `config/hashing.php` | `12` |
| `LOG_CHANNEL`, `LOG_STACK`, `LOG_LEVEL`, `LOG_DEPRECATIONS_CHANNEL` | `config/logging.php` | `debug`/`single` in live `.env` |
| `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` | `config/database.php` | **shared** DB — see §5 |
| `SESSION_DRIVER`, `SESSION_LIFETIME`, `SESSION_ENCRYPT`, `SESSION_PATH`, `SESSION_DOMAIN` | `config/session.php` | `SESSION_SECURE_COOKIE` is read by config but **absent from both files** |
| `CACHE_STORE` | `config/cache.php:18` | `file` in live `.env` |
| `QUEUE_CONNECTION` | `config/queue.php:16` | `sync` in live `.env` |
| `BROADCAST_CONNECTION`, `FILESYSTEM_DISK` | `config/broadcasting.php`, `config/filesystems.php` | `log` / `local` |
| `MAIL_MAILER`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_SCHEME`, `MAIL_FROM_ADDRESS`, `MAIL_FROM_NAME` | `config/mail.php` | `log` in live `.env` |
| `MEMCACHED_HOST`, `REDIS_CLIENT`, `REDIS_HOST`, `REDIS_PASSWORD`, `REDIS_PORT` | `config/cache.php`, `config/database.php` | unused with `file`/`database` stores |
| `AWS_*` | `config/filesystems.php` | unused with `FILESYSTEM_DISK=local` |
| `VITE_APP_NAME` | Vite bootstrap | see §3 |

### Commerce-critical (defined in `.env`, **missing from `.env.example`**)

| Key | Consumed by | Evidence |
|---|---|---|
| `MAIN_URL` | `SearchController.php:23` via `env('MAIN_URL')` | `VERIFIED_FROM_SOURCE` |
| `RAZORPAY_KEY`, `RAZORPAY_SECRET` | `config/services.php:38-41` (read by `CheckoutController.php:196-197,258`) | `VERIFIED_FROM_SOURCE` |
| `SHIPROCKET_EMAIL`, `SHIPROCKET_PASSWORD` | `config/services.php:44-45` | `VERIFIED_FROM_SOURCE` |
| `SHIPROCKET_PICKUP_LOCATION`, `SHIPROCKET_PICKUP_PINCODE` | `config/services.php:46-47` | `VERIFIED_FROM_SOURCE` |
| `SHIPROCKET_BASE_URL` | **nothing** — `config/services.php` has no `base_url` key; `ShiprocketService.php:15` always falls back to the hard-coded default | `VERIFIED_FROM_SOURCE` |

`DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` are present in `.env` but only
current as **commented** placeholders in `.env.example` (`.env.example:24-28`) — so they are
effectively absent from the example file.

---

## 2. `.env.example` coverage gaps — VERIFIED

Comparing key sets (names only):

| Gap | Keys missing from `.env.example` |
|---|---|
| **Commerce — blocking** | `MAIN_URL`, `RAZORPAY_KEY`, `RAZORPAY_SECRET`, `SHIPROCKET_EMAIL`, `SHIPROCKET_PASSWORD`, `SHIPROCKET_BASE_URL`, `SHIPROCKET_PICKUP_LOCATION`, `SHIPROCKET_PICKUP_PINCODE` |
| **Database — blocking for a real setup** | `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` |

That is **13 keys** present in `.env` but absent from `.env.example`
(56 − 43 = 13). `VERIFIED_FROM_SOURCE`.

Additional mismatches between the two files:

| Key | `.env.example` | Live `.env` | Consequence |
|---|---|---|---|
| `DB_CONNECTION` | `sqlite` (`.env.example:23`) | `mysql` | a developer following the example gets the wrong engine |
| `QUEUE_CONNECTION` | `database` (`.env.example:38`) | `sync` | example implies async; runtime is synchronous |
| `CACHE_STORE` | `database` (`.env.example:40`) | `file` | example implies shared cache; runtime is per-node |
| `SESSION_DRIVER` | `database` (`.env.example:30`) | `file` | example implies shared sessions; runtime is per-node |
| `APP_NAME` | `Laravel` (`.env.example:1`) | (project default) | example not branded |
| `APP_TIMEZONE` | absent | absent (`config/app.php:68` = `UTC`) | business operates in IST; see [`../18-configuration-and-env.md`](../18-configuration-and-env.md) §5 |

> A recommended corrected `.env.example` is provided in full in
> [`../18-configuration-and-env.md`](../18-configuration-and-env.md) §11 — this document does not
> duplicate it.

---

## 3. Build and install steps

### Dependencies

```bash
composer install --no-dev --optimize-autoloader      # production
# development:
composer install
```

`razorpay/razorpay` is declared in `composer.json:12` **and** present in `composer.lock`
(`"name": "razorpay/razorpay"`, `composer.lock` ~line 3298). `VERIFIED_FROM_SOURCE`. So a clean
`composer install` resolves the payment SDK — the older "absent from the lock file" warning no
longer applies (see [`./SOURCE_COVERAGE_AND_VERIFICATION.md`](./SOURCE_COVERAGE_AND_VERIFICATION.md)).

> The inline `require_once` guard at `CheckoutController.php:26-31` is now redundant on a correct
> install but is harmless; leave it until the dependency install path has been exercised in CI.

### Asset build

```bash
npm install
npm run build        # runs `vite build` (package.json:6)
```

**Caveat — the Vite pipeline is effectively unused.** `package.json` declares Vite 7 + Tailwind 4 +
`laravel-vite-plugin` (`package.json:9-16`), but a grep for `@vite` across
`resources/views/**/*.blade.php` matches **only** `welcome.blade.php:15`, and that view is
unreachable (no route renders it — [`../19-incomplete-work.md`](../19-incomplete-work.md) FW-11).
Every live view loads vendored theme assets from `public/assets/`. `VERIFIED_FROM_SOURCE`.
Consequence: `npm run build` produces output that **no served page references**; the productive
asset path is the committed `public/assets/` tree. This is why `composer run setup`/`composer run dev`
"pass" or "fail" at the Vite step without changing what users see — see
[`../17-devops-deployment.md`](../17-devops-deployment.md) §3 and
[`./KNOWN_ISSUES_AND_TECHNICAL_DEBT.md`](./KNOWN_ISSUES_AND_TECHNICAL_DEBT.md) (`TD-B-04`).

### `composer.json` scripts and their traps

| Script | Definition | Trap |
|---|---|---|
| `setup` | `composer.json:37-44` — install, copy env, key:generate, `migrate --force`, `npm install`, `npm run build` | `migrate --force` creates **only** the 3 stock tables; the 93 table schema is not created. The npm step builds unused assets |
| `dev` | `composer.json:45-48` — `serve` + `queue:listen` + `pail` + `npm run dev` | `queue:listen` has nothing to consume (`QUEUE_CONNECTION=sync`, no `app/Jobs/`); the Vite dev server serves views that no live page references |
| `test` | `composer.json:49-52` — `config:clear` then `artisan test` | runs 2 placeholder tests — see [`./TESTING_GUIDE.md`](./TESTING_GUIDE.md) |

All `VERIFIED_FROM_SOURCE` (`composer.json`).

### Database bootstrap

There are **3 stock migrations** and **0 domain migrations** (`database/migrations/` =
`0001_01_01_000000_create_users_table.php`, `0001_01_01_000001_create_cache_table.php`,
`0001_01_01_000002_create_jobs_table.php`). `VERIFIED_FROM_SOURCE`. The ~90 domain tables come from
the SQL dump `palvarnan_db (2).sql` (93 `CREATE TABLE` statements; `VERIFIED_FROM_LIVE_SCHEMA`,
dump dated 2026-07-31). Import path and the missing hand-applied columns are covered in
[`../17-devops-deployment.md`](../17-devops-deployment.md) §3 and
[`../22-database-integrity.md`](../22-database-integrity.md).

---

## 4. The config-cache hazard — the single most important deployment trap

`SearchController.php:23` reads an environment variable **outside** `config/`:

```php
$baseImageUrl = rtrim(env('MAIN_URL'), '/') . '/images/';
```

`VERIFIED_FROM_SOURCE`. A grep of `app/` for `env(` returns **this one line and no other**.

`env()` only returns a value while configuration is **not cached**. After a standard production step:

```bash
php artisan config:cache
```

…`env('MAIN_URL')` returns `null`, so every search-result image URL becomes
`/images/<file>` (relative) and **breaks**:

```php
rtrim(null, '/') . '/images/'   // → "/images/"
```

**Therefore:** either fix the code **before** enabling `config:cache`, or accept broken search
images. Do **not** run `config:cache` as-is.

Fix (add to `config/services.php`, then read via `config()`):

```php
'main_url' => env('MAIN_URL'),
```

The same class of hazard applies to any future `env()` call added outside `config/`. Verify before
every cache step:

```bash
grep -rn "env(" app/     # expect 0 matches
```

This is the exact check listed in [`../README.md`](../README.md) §8.

**Related config defects** (documented centrally, cross-referenced here):

- `config/services.php` has **no `base_url` key**, so `SHIPROCKET_BASE_URL` in `.env` is dead —
  `ShiprocketService.php:15` always uses the hard-coded default. Switching to the Shiprocket
  sandbox via `.env` silently has no effect. [`../18-configuration-and-env.md`](../18-configuration-and-env.md) §3.
- `config/services.php` carries `razorpay`/`shiprocket` blocks that are **duplicated** across the
  storefront and dashboard apps, so the two can drift. `CONFLICTING_EVIDENCE` risk —
  [`../18-configuration-and-env.md`](../18-configuration-and-env.md) §3.
- `APP_TIMEZONE` is unset and `config/app.php:68` is `'timezone' => 'UTC'` while the business
  operates in IST; date-bucketed order/report data can be off by up to a day.
  [`../18-configuration-and-env.md`](../18-configuration-and-env.md) §5.

---

## 5. Shared-database deployment coupling

The storefront and the dashboard app read and write **the same MySQL schema**.

| Fact | Evidence |
|---|---|
| `DB_DATABASE` points at the shared schema (live `.env`) | `VERIFIED_FROM_SOURCE` |
| `.env.example` does not name the database (commented placeholder) | `.env.example:26` |
| The domain schema is **not** owned by this app — 0 domain migrations here | `database/migrations/` (3 stock files) |
| The schema is versioned by the sibling `palVarnanDashboard` app, which has its own migrations | [`../17-devops-deployment.md`](../17-devops-deployment.md) §7, [`../19-incomplete-work.md`](../19-incomplete-work.md) IW-01 |

### Consequences for deployment

1. **There is no single artefact that deploys both apps' schema.** Deploying the storefront alone
   cannot create the tables it depends on. Import order matters: dump → hand-applied columns →
   dashboard migrations (or a shared migration home).
2. **A column change is a cross-app change.** Adding/renaming/removing a column in the dashboard
   (or by hand) changes the storefront's behaviour with no code change and no migration in this
   repo. Five columns are already written by the storefront but absent from the dump
   (`billing_state` and `shipping_state` are confirmed absent: `billing_state` → 0 matches in
   `palvarnan_db (2).sql`; `VERIFIED_FROM_LIVE_SCHEMA`). Tracked as `KI-B-01`.
3. **Destructive commands are forbidden.** `php artisan migrate:fresh`, `migrate:refresh`, and any
   schema-dropping script run against the shared database destroy dashboard data. This is the core
   reason [`../24-testing-strategy.md`](../24-testing-strategy.md) §6 mandates a **separate**
   `palvarnan_test` database.
4. **`test_apis.php` / `test_services.php`** in the project root are web-reachable and can mutate
   schema — they must be deleted and their credentials rotated. `CONFLICTING_EVIDENCE` /
   security — see [`../13-security-audit.md`](../13-security-audit.md); **no values reproduced here**.
   The operational blast-radius map lives in [`./CHANGE_IMPACT_GUIDE.md`](./CHANGE_IMPACT_GUIDE.md).

### Web-root requirement

The document root must be `public/`, never the project root. If the project root is served, the
root-level test scripts (and the SQL dump if it sits inside the served tree) become web-reachable.
[`../17-devops-deployment.md`](../17-devops-deployment.md) §5.

---

## 6. Pre-deployment checklist (condensed)

Token-by-token detail is in [`../17-devops-deployment.md`](../17-devops-deployment.md) §5 and
[`../18-configuration-and-env.md`](../18-configuration-and-env.md) §12. The minimum:

- [ ] `APP_ENV=production`, **`APP_DEBUG=false`** (`VERIFIED_FROM_SOURCE`: currently `local`/`true`)
- [ ] Document root → `public/`
- [ ] `composer install --no-dev --optimize-autoloader`
- [ ] Add the 13 missing keys to `.env.example`; set real `RAZORPAY_*` and `SHIPROCKET_*`
- [ ] **Fix `env('MAIN_URL')` in `SearchController.php:23` → `config('services.main_url')` before** `config:cache`
- [ ] Dedicated least-privilege DB user (currently a high-privilege account with an empty password in `.env`; do not reproduce the value — rotate it)
- [ ] Real `MAIL_MAILER` — otherwise password reset is non-functional (`MAIL_MAILER=log` today)
- [ ] Delete `test_apis.php` / `test_services.php`; rotate Razorpay, Shiprocket, and DB credentials
- [ ] `SESSION_SECURE_COOKIE=true`, `SESSION_ENCRYPT=true` behind HTTPS
- [ ] Introduce a queue (`QUEUE_CONNECTION=database|redis` + worker) **before** shipping any queued fulfilment work
- [ ] Import the schema and apply the hand-written columns; verify with the reconciliation query in [`../README.md`](../README.md) §8

---

## 7. Related documents

- [`../17-devops-deployment.md`](../17-devops-deployment.md) — deployability narrative, CI, monitoring
- [`../18-configuration-and-env.md`](../18-configuration-and-env.md) — full effective-config table and corrected `.env.example`
- [`../09-dependencies.md`](../09-dependencies.md) — composer/npm lock state and asset provenance
- [`../22-database-integrity.md`](../22-database-integrity.md) — schema bootstrap and missing migrations
- [`./PERFORMANCE_AND_RELIABILITY.md`](./PERFORMANCE_AND_RELIABILITY.md) — queue/cache runtime impact
- [`./CHANGE_IMPACT_GUIDE.md`](./CHANGE_IMPACT_GUIDE.md) — shared-DB blast radius
- [`./KNOWN_ISSUES_AND_TECHNICAL_DEBT.md`](./KNOWN_ISSUES_AND_TECHNICAL_DEBT.md) — `KI-B-*` / `TD-B-*` register
