# 17 — Configuration

## Environment (`.env`) — `VERIFIED`

Values below are non-secret. All secrets are `[SECRET_REDACTED]`.

| Key | `.env` value | Assessment |
| --- | --- | --- |
| `APP_NAME` | `Laravel` | **Never renamed from the skeleton.** Shows in emails and logs. |
| `APP_ENV` | `local` | Fine for dev; **must be `production` in deployed environments.** |
| `APP_KEY` | `[SECRET_REDACTED]` | Present. |
| `APP_DEBUG` | **`true`** | **Must be `false` outside local.** See SEC-10. |
| `APP_URL` | `http://localhost` | Wrong for any real host. |
| `LOG_LEVEL` | `debug` | Verbose; noisy and grows logs fast. |
| `DEBUGBAR_ENABLED` | `false` | Good. |
| `MILK_PRODUCT` | `21` | Custom. **No matching `config/*.php` entry** — read directly via `env()`. |
| `MOBILE_API_URL` | present | Custom. Same: no config entry. |
| `DB_CONNECTION` | `mysql` | — |
| `DB_HOST` / `DB_PORT` | `127.0.0.1` / `3306` | Localhost-only; correct for XAMPP. |
| `DB_DATABASE` | `palvarnan_db` | — |
| `DB_USERNAME` | `root` | **`root` with no password restriction is a production risk.** See below. |
| `DB_PASSWORD` | `[SECRET_REDACTED]` | — |
| `BROADCAST_DRIVER` | `log` | Not used; see [16-external-integrations.md](16-external-integrations.md). |
| `CACHE_DRIVER` | `file` | Shiprocket token cached here. Fine; a shared Redis would be better. |
| `QUEUE_CONNECTION` | `sync` | **No queue, no worker.** See [20-scheduling-and-jobs.md](20-scheduling-and-jobs.md). |
| `SESSION_DRIVER` | `file` | Session files under `storage/framework/sessions`. |
| `SESSION_LIFETIME` | `120` | 2 hours. Long for an admin panel. |
| `FILESYSTEM_DRIVER` | `local` | S3 config present but inert. |
| `MAIL_MAILER` | `smtp` | — |
| `SHIPROCKET_BASE_URL` | `https://apiv2.shiprocket.in/v1/external` | Present and correct — **but bypassed by inline cURL.** |

Additional keys present in `.env` with **no code reference found**: `MEMCACHED_HOST`,
`REDIS_HOST`, `REDIS_PASSWORD`, `REDIS_PORT`, `AWS_*`, `PUSHER_*`, `MIX_PUSHER_*`.

`VERIFIED` — `.env` **is** listed in `.gitignore`. `UNVERIFIED` — whether it was ever committed;
`git log` shows a single "Initial commit" and `git status` does not list `.env`, so it is currently
untracked-or-ignored, but history was not inspected.

## `.env.example` is stale — `INCOMPLETE`

`VERIFIED` — `.env.example` is the **unmodified Laravel scaffold** and does not match `.env`:

| Key | `.env.example` | `.env` |
| --- | --- | --- |
| `APP_NAME` | `Laravel` | `Laravel` |
| `DB_DATABASE` | `laravel` | `palvarnan_db` |
| `MAIL_HOST` | `mailhog` | real SMTP host |
| `MAIL_PORT` | `1025` | — |
| `DEBUGBAR_ENABLED` | **absent** | `false` |
| `MILK_PRODUCT` | **absent** | `21` |
| `MOBILE_API_URL` | **absent** | present |
| `SHIPROCKET_*` (5 keys) | **absent** | present |
| `PUSHER_APP_CLUSTER` | `mt1` | different |

A new developer copying `.env.example` gets an application with **no Shiprocket configuration at
all** — and would not discover that Shiprocket works because credentials are hardcoded in
controllers. This actively hides SEC-04.

`VERIFIED` — `.env.example` retains `MEMCACHED_HOST`, `REDIS_*`, `PUSHER_*`, and `MIX_PUSHER_*`
scaffold keys that the application does not use, while omitting every key it actually needs.

## Database credentials — `BROKEN`

`VERIFIED` — `DB_USERNAME=root` on `127.0.0.1`. If MySQL is reachable from anywhere other than
localhost, or if the socket/authentication is permissive, a SQL injection or credential compromise
yields full database control. `DB_DATABASE=palvarnan_db` holds every customer record, order,
address, and password hash in the system.

**Recommendation:** create a dedicated MySQL user scoped to `palvarnan_db` only.

## `config/` inventory — `VERIFIED`

19 files:

```
app.php  auth.php  broadcasting.php  cache.php  cors.php  database.php  dompdf.php
excel.php  filesystems.php  hashing.php  logging.php  mail.php  permission.php
queue.php  sanctum.php  services.php  session.php  telescope.php  view.php
```

### Files that are pure scaffold

`VERIFIED` — unmodified from the Laravel 8 defaults: `broadcasting.php` (52 lines),
`cache.php` (92), `hashing.php` (44), `queue.php` (79), `sanctum.php` (55), `view.php` (30),
`mail.php` (100), `database.php` (126), `session.php` (169), `permission.php` (142),
`telescope.php` (152).

`BROKEN` — `config/database.php` is scaffold, so `mysql` connection options (`strict`,
`engine`, `charset`, `collation`, `timezone`, SSL, and the second `mysql` read/write block) are all
Laravel defaults. With the schema drift in [05-database-schema.md](05-database-schema.md) and
hand-written SQL using `DATEDIFF`/`NOW()` (`ProductStock::scopeDeadStock()`), charset and timezone
settings are worth confirming deliberately.

### Files that were modified

| File | Change | Risk |
| --- | --- | --- |
| `app.php` | `timezone => 'Asia/Kolkata'` | Correct for the domain; confirm it matches the database server timezone. |
| `cors.php` | Wildcard origins/methods/headers on `api/*` | See SEC-16. |
| `dompdf.php` | `allowed_protocols` widened to `file://`, `http://`, `https://` with empty rules | See [15-file-handling.md](15-file-handling.md). |
| `filesystems.php` | `public` disk root changed to `public_path('images/')`; URL left as `APP_URL/storage` | Mismatch — see [15-file-handling.md](15-file-handling.md). |
| `services.php` | `shiprocket` block added | Correct pattern, bypassed by controllers. |

### `config/app.php` locale configuration — `BROKEN`

`VERIFIED`:

```php
'timezone' => 'Asia/Kolkata',
'locale'   => 'en',
'fallback_locale' => 'en',
'faker_locale'    => 'en_US',
```

`App\Http\Middleware\Localization` sets the locale from `session('lang')`, and
`HomeController::lang($locale)` stores an arbitrary locale string with **no validation**:

```php
public function lang($locale)
{
    App::setLocale($locale);
    Session::put('lang', $locale);
    Session::save();
    return redirect()->back()->with('locale', $locale);
}
```

`VERIFIED` — `resources/lang/` contains 9 files: `de/translation.php`, `es/translation.php`,
`it/translation.php`, `ru/translation.php`, and the `en/` defaults (`auth`, `pagination`,
`passwords`, `translation`, `validation`). The German, Spanish, Italian, and Russian files are
scaffold stubs. Only the `en/translation.php` body could be verified as application content;
`UNVERIFIED` — whether the non-English files carry real strings, and which of these five locales
the UI actually offers.

`VERIFIED` — the `lang` route is **not registered**; `routes/web.php:635` has it commented out
(`// Route::get('index/{locale}', [HomeController::class, 'lang']);`). The method is dead.

`VERIFIED` — **`env()` has 0 call sites in `app/`.** `MILK_PRODUCT` and `MOBILE_API_URL` are
therefore either unused or resolved through `config/` entries that were not found. Flagged as
`UNVERIFIED` — needs a grep across `resources/views/` and `config/`.

### `config/session.php` — `INCOMPLETE`

`VERIFIED` — scaffold defaults with `SESSION_DRIVER=file`. Notably **not** configured:

- `'secure' => env('SESSION_SECURE_COOKIE')` — left at the framework default; no
  `SESSION_SECURE_COOKIE` in `.env`.
- `'http_only' => true` (scaffold default) — acceptable.
- `'same_site' => null` (scaffold default) — no cross-site protection configured.

Combined with `SESSION_LIFETIME=120`, session cookies are neither pinned to HTTPS nor given an
explicit SameSite policy.

### `config/logging.php` — `INCOMPLETE`

`VERIFIED` — `LOG_CHANNEL` is not set in `.env`, so the framework default applies. The channel
definitions are scaffold (`single`, `daily`, `slack`, `papertrail`, `stderr`, `syslog`,
`errorlog`), all at `'level' => env('LOG_LEVEL', 'debug')`.

`BROKEN` — with `LOG_LEVEL=debug`, every query-level debug entry is written to
`storage/logs/laravel.log`. There is **no log rotation configured** beyond the unused `daily`
channel's 14-day retention, and no central log shipping. `storage/` is 6 MB and populated.

## Framework configuration gaps

`VERIFIED`

| Item | State |
| --- | --- |
| `config:cache` / `route:cache` in deploy | `UNVERIFIED` — no deployment script exists. |
| `APP_DEBUG=false` guard | No `php artisan` wrapper or `.env` template per environment. |
| Trusted proxies | `TrustProxies` active; `$proxies`/`$headers` are scaffold (`*` trust commented out by default in the shipped file — confirm). |
| `TrustHosts` | Middleware exists but is **commented out** of the global stack. |
| Maintenance mode | `PreventRequestsDuringMaintenance` active. |
| `phpunit.xml` | Present, unmodified. No `.env.testing`. |

## Encryption

`VERIFIED` — `config/app.php`: `'cipher' => 'AES-256-CBC'`, `'key' => env('APP_KEY')`. Standard.

`UNVERIFIED` — whether the `enc_password` column on `users` and `dashboard_users` uses the app key.
If it does, it is a reversible copy of every password. See [10-authentication.md](10-authentication.md).

## Documentation gaps

| Missing | Consequence |
| --- | --- |
| No `.env.testing` | No isolated test environment. |
| No per-environment `.env` templates | `APP_ENV`/`APP_DEBUG` mistakes are easy. |
| `README.md` is the Laravel scaffold | No setup instructions at all. |
| No deployment runbook | See [22-deployment.md](22-deployment.md). |
| No documented cron entry | `shiprocket:sync` never runs unless someone set it up. See [20-scheduling-and-jobs.md](20-scheduling-and-jobs.md). |

## Recommendations

1. Set `APP_DEBUG=false`, `APP_ENV=production`, and a real `APP_URL` per environment.
2. **Rewrite `.env.example`** to match `.env` structurally, with every secret blanked. Remove the
   unused scaffold keys.
3. Create a dedicated MySQL user instead of `root`.
4. Register the `role` and `permission` middleware aliases in `app/Http/Kernel.php`.
5. Set `SESSION_SECURE_COOKIE=true`, `SESSION_SAME_SITE=lax`, and reduce `SESSION_LIFETIME`.
6. Set `LOG_LEVEL=info` outside local and switch to the `daily` channel.
7. Delete the dead `Localization` locale-switcher code, or finish it properly.
8. Add a deployment script that runs `config:cache`, `route:cache`, `view:cache`, and
   `storage:link`.