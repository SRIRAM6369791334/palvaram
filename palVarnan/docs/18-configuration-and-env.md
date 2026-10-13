# 18 — Configuration and Environment

All 10 `config/` files are Laravel stock **except `config/services.php`**, which carries two added blocks. This document records every effective value, verified by reading both the config files and `.env`.

---

## 1. Effective configuration table

| Setting | Effective value | Source | Assessment |
|---|---|---|---|
| `APP_NAME` | `Laravel` | `.env` | **Default — never set to the project name** |
| `APP_ENV` | `local` | `.env` | Must be `production` on a live host |
| `APP_DEBUG` | **`true`** | `.env` | **Critical — full stack traces to the browser** |
| `APP_URL` | `http://localhost` | `.env` | Wrong on any real host |
| `APP_TIMEZONE` | **`UTC`** | `config/app.php` | Business operates in IST; all timestamps stored in UTC |
| `APP_LOCALE` | `en` | `.env` | No `resources/lang/` — no i18n |
| `MAINTENANCE_DRIVER` | `file` | `.env` | Stock |
| `BCRYPT_ROUNDS` | `12` | `.env` | Correct |
| `LOG_CHANNEL` / `LOG_STACK` | `stack` / `single` | `.env` | One unbounded file, no rotation |
| `LOG_LEVEL` | **`debug`** | `.env` | Logs every query |
| `DB_CONNECTION` | `mysql` | `.env` | |
| `DB_HOST` / `DB_PORT` | `127.0.0.1` / `3306` | `.env` | |
| `DB_DATABASE` | `palvarnan_db` | `.env` | **Shared with the dashboard app** |
| `DB_USERNAME` | **`root`** | `.env` | **Must not be root in production** |
| `DB_PASSWORD` | **empty** | `.env` | **Critical** |
| `SESSION_DRIVER` | `file` | `.env` | File-backed — no shared state across servers |
| `SESSION_LIFETIME` | `120` (minutes) | `.env` | 2 hours |
| `SESSION_ENCRYPT` | **`false`** | `.env` | Session payload readable server-side unencrypted |
| `SESSION_SECURE_COOKIE` | **unset** → `null` | `config/session.php` | **Cookie not marked secure** |
| `SESSION_SAME_SITE` | `lax` | `config/session.php` | Reasonable |
| `QUEUE_CONNECTION` | **`sync`** | `.env` | **Synchronous — any job blocks the request; no workers** |
| `CACHE_STORE` | `file` | `.env` | Shiprocket token cached per node |
| `MAIL_MAILER` | **`log`** | `.env` | **All mail goes to `storage/logs/laravel.log`** |
| `MAIL_FROM_ADDRESS` | `hello@example.com` | `.env` | Placeholder |
| `FILESYSTEM_DISK` | `local` | `.env` | No S3 |
| `BROADCAST_CONNECTION` | `log` | `.env` | Fine |
| `MAIN_URL` | `http://127.0.0.1:8001/` | `.env` | **Not in `.env.example`**; read via `env()` in `SearchController` |
| `RAZORPAY_KEY` / `RAZORPAY_SECRET` | set (values redacted) | `.env` | **Not in `.env.example`** |
| `SHIPROCKET_EMAIL` / `SHIPROCKET_PASSWORD` | set (values redacted) | `.env` | **Not in `.env.example`** |
| `SHIPROCKET_PICKUP_LOCATION` | `Home` | `.env` | |
| `SHIPROCKET_PICKUP_PINCODE` | `641006` | `.env` | Coimbatore, TN |
| `SHIPROCKET_BASE_URL` | `https://apiv2.shiprocket.in/v1/external` | `.env` | **Dead — no config key reads it** |

---

## 2. `.env` vs `.env.example`

`.env` has **66 keys**, `.env.example` has **58**. The 8 omissions are exactly the commerce-critical ones:

```dotenv
MAIN_URL=
RAZORPAY_KEY=
RAZORPAY_SECRET=
SHIPROCKET_EMAIL=
SHIPROCKET_PASSWORD=
SHIPROCKET_PICKUP_LOCATION=
SHIPROCKET_PICKUP_PINCODE=
SHIPROCKET_BASE_URL=
```

**A developer following `.env.example` cannot set up payments or shipping.** `DB_PORT` is also missing (though it has a config default).

**Fix:** add all 9 to `.env.example` with empty values.

---

## 3. `config/services.php` — the only customised config

Full file is 51 lines: 34 of stock boilerplate plus two blocks.

```php
'razorpay' => [
    'key'    => env('RAZORPAY_KEY'),
    'secret' => env('RAZORPAY_SECRET'),
],

'shiprocket' => [
    'email'           => env('SHIPROCKET_EMAIL'),
    'password'        => env('SHIPROCKET_PASSWORD'),
    'pickup_location' => env('SHIPROCKET_PICKUP_LOCATION', 'Home'),
    'pickup_pincode'  => env('SHIPROCKET_PICKUP_PINCODE', '641006'),
],
```

### Defect — missing `base_url`

`ShiprocketService.php:15`:
```php
$this->baseUrl = config('services.shiprocket.base_url', 'https://apiv2.shiprocket.in/v1/external');
```

There is **no `base_url` key**, so the default is always used. `.env`'s `SHIPROCKET_BASE_URL` is read by nothing. The value currently matches the default, so nothing is broken — but **switching to the Shiprocket sandbox via `.env` will silently have no effect.**

**Fix:**
```php
'shiprocket' => [
    'email'           => env('SHIPROCKET_EMAIL'),
    'password'        => env('SHIPROCKET_PASSWORD'),
    'base_url'        => env('SHIPROCKET_BASE_URL', 'https://apiv2.shiprocket.in/v1/external'),
    'pickup_location' => env('SHIPROCKET_PICKUP_LOCATION', 'Home'),
    'pickup_pincode'  => env('SHIPROCKET_PICKUP_PINCODE', '641006'),
    'timeout'         => (int) env('SHIPROCKET_TIMEOUT', 10),
],
```

### Defect — duplicated across both apps

`palVarnanDashboard/config/services.php` carries its own `razorpay` and `shiprocket` blocks. The two apps can drift apart silently.

---

## 4. The `env()` outside `config/` problem

`SearchController.php:29`:
```php
$baseImageUrl = rtrim(env('MAIN_URL'), '/') . '/images/';
```

**`env()` only works when config is not cached.** Once `php artisan config:cache` runs — a standard production step — `env()` returns **`null`**:

```php
$baseImageUrl = rtrim(null, '/') . '/images/';   // → "/images/"
```

Every search-result image URL becomes relative and **broken**.

**Fix** — add to `config/services.php`:
```php
'main_url' => env('MAIN_URL'),
```
then `config('services.main_url')`.

**Verify no others:** grep for `env(` outside `config/` — this is the only one found in `app/`.

---

## 5. Timezone mismatch

| Layer | Value |
|---|---|
| `config/app.php` | `'timezone' => 'UTC'` |
| `.env` | no `APP_TIMEZONE` key at all |
| `ProductTransactionLog.order_date` | `now()->toDateString()` → **UTC date** |
| `ProductSlot.delivery_date` | written by the dashboard |
| Shiprocket `order_date` | built in `placeOrder()` |

For a business operating in IST (IST = UTC+5:30), a `now()` at 02:00 IST records **the previous day**. `order_date` and any date-bucketed dashboard report will be off by up to one day.

**Fix:** `APP_TIMEZONE=Asia/Kolkata` in `.env` and `.env.example`.

---

## 6. Session and cache store analysis

### Session — `file`

| Concern | Impact |
|---|---|
| Shared state | **None.** Two app servers behind a load balancer would see different sessions ⇒ carts and logins break |
| Disk I/O | Session read + write on **every request** |
| Cleanup | `storage/framework/sessions` accumulates; **no prune scheduled** |
| Encryption | Off |
| Secure flag | Not set |

`ProfileController::logout()` correctly calls `invalidate()` + `regenerateToken()`, which does delete the file. But `AuthController::login()` **does not call `regenerate()`** — session fixation remains open.

### Cache — `file`

`ShiprocketService::getToken()` caches the token with `Cache::remember(..., 8h)`. With a `file` store and multiple servers, **each node logs in independently** to Shiprocket.

**Fix:** `CACHE_STORE=redis` for any multi-node deployment.

---

## 7. Queue — `sync`

`QUEUE_CONNECTION=sync` means any dispatched job **runs inline in the request**. Combined with:
- `routes/console.php` being stock Laravel (no schedules)
- `app/Jobs/` not existing
- `app/Console/` not existing

…there is **no asynchronous processing at all**. `composer run dev` invokes `queue:listen --tries=1`, which has nothing to listen for.

**This matters for the fix plan:** moving Shiprocket order creation to a queue (the right remedy for the swallowed-failure bug) requires `QUEUE_CONNECTION=database|redis` **and** a running worker.

---

## 8. Mail — `log`

`MAIL_MAILER=log` writes messages to `storage/logs/laravel.log` instead of sending them.

| Consequence | Detail |
|---|---|
| Password reset | `forgotPassword()` calls `Password::sendResetLink()`. The "email" lands in the log. The user never receives it |
| Email verification | `MustVerifyEmail` is commented out in `User.php:5`, so no verification mail is attempted |
| Order confirmation | No mailable exists in this app |

**For password reset to function at all, `MAIL_MAILER` must be set to a real transport.**

---

## 9. Config caching status

`bootstrap/cache/` exists with a `.gitignore`. Whether `config.php` / `routes.php` / `services.php` are cached **could not be reliably determined** and is marked **UNVERIFIED**.

**If config is already cached**, `env('MAIN_URL')` in `SearchController` is already returning `null` and search images are broken in the running app — a testable prediction.

---

## 10. Recommended `config/services.php`

```php
'razorpay' => [
    'key'    => env('RAZORPAY_KEY'),
    'secret' => env('RAZORPAY_SECRET'),
],

'shiprocket' => [
    'email'           => env('SHIPROCKET_EMAIL'),
    'password'        => env('SHIPROCKET_PASSWORD'),
    'base_url'        => env('SHIPROCKET_BASE_URL', 'https://apiv2.shiprocket.in/v1/external'),
    'pickup_location' => env('SHIPROCKET_PICKUP_LOCATION', 'Home'),
    'pickup_pincode'  => env('SHIPROCKET_PICKUP_PINCODE', '641006'),
    'timeout'         => (int) env('SHIPROCKET_TIMEOUT', 10),
    'token_ttl'       => (int) env('SHIPROCKET_TOKEN_TTL', 28800),
],

'main_url' => env('MAIN_URL'),
```

---

## 11. Complete `.env.example`

```dotenv
APP_NAME=PalVarnan
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost
APP_TIMEZONE=Asia/Kolkata

MAIN_URL=http://127.0.0.1:8001/

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US
APP_MAINTENANCE_DRIVER=file
BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=palvarnan_db
DB_USERNAME=
DB_PASSWORD=

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database
CACHE_STORE=database

MAIL_MAILER=log
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

RAZORPAY_KEY=
RAZORPAY_SECRET=

SHIPROCKET_EMAIL=
SHIPROCKET_PASSWORD=
SHIPROCKET_BASE_URL=https://apiv2.shiprocket.in/v1/external
SHIPROCKET_PICKUP_LOCATION=Home
SHIPROCKET_PICKUP_PINCODE=641006
SHIPROCKET_TIMEOUT=10
SHIPROCKET_TOKEN_TTL=28800
```

Deliberate changes from the current `.env`: `APP_NAME` set, `APP_TIMEZONE` added, `SESSION_DRIVER` and `CACHE_STORE` moved to `database` (Redis in production), `QUEUE_CONNECTION` moved off `sync`, and all commerce keys included.

---

## 12. Recommendations

| Priority | Action |
|---|---|
| **P0** | `APP_DEBUG=false` on any non-local environment |
| **P0** | Dedicated least-privilege DB user; rotate the password |
| **P0** | Delete `test_apis.php` / `test_services.php`; rotate Razorpay + Shiprocket credentials |
| **P1** | Add the 8 missing keys to `.env.example` |
| **P1** | Add `base_url`, `timeout`, `token_ttl` to `services.shiprocket` |
| **P1** | Move `env('MAIN_URL')` into config **before** enabling `config:cache` |
| **P1** | Set `APP_TIMEZONE=Asia/Kolkata` |
| **P1** | Set a real `MAIL_MAILER` or password reset remains non-functional |
| P2 | `SESSION_SECURE_COOKIE=true`, `SESSION_ENCRYPT=true` behind HTTPS |
| P2 | `CACHE_STORE=redis` for multi-node |
| P2 | `QUEUE_CONNECTION=database` before shipping the queued-fulfilment fix |
| P2 | `LOG_CHANNEL=daily`, `LOG_LEVEL=info` |
| P3 | Schedule `session:prune` daily |
| P3 | Add `.env.timezone` / config tests |

---

## 13. Related documents

- [09-dependencies.md](09-dependencies.md) — Razorpay lock defect
- [13-security-audit.md](13-security-audit.md) — SEC-20 root DB user, SEC-23 cookies
- [16-performance.md](16-performance.md) — config-driven overhead
- [17-devops-deployment.md](17-devops-deployment.md) — production checklist
- [12-services-and-integrations.md](12-services-and-integrations.md) — how these values are consumed