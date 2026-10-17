# CONFIGURATION_AND_ENV.md

**Purpose:** Config files and the environment variable names in play.

---

## 1. Config files (10)

`app.php`, `auth.php`, `cache.php`, `database.php`, `filesystems.php`, `logging.php`, `mail.php`, `queue.php`,
`services.php`, `session.php`. `VERIFIED_FROM_SOURCE`.

Services defined: `razorpay` (38-41), `shiprocket` (43-48). `VERIFIED_FROM_SOURCE`.

---

## 2. Effective environment values (current `.env`)

| Var | Observed | Evidence |
|---|---|---|
| `APP_ENV` | `local` | `.env` |
| `APP_DEBUG` | `true` | `.env` |
| `APP_URL` | `http://localhost` | `.env` |
| `DB_CONNECTION` | `mysql` | `.env` |
| `DB_HOST` | `127.0.0.1` | `.env` |
| `DB_DATABASE` | `palvarnan_db` | `.env` |
| `SESSION_DRIVER` | `file` | `.env` |
| `QUEUE_CONNECTION` | `sync` | `.env` |
| `CACHE_STORE` | `file` | `.env` |
| `MAIN_URL` | `http://127.0.0.1:8001/` | `.env` (used by `SearchController.php:23`) |
| `MAIL_MAILER` | `log` | `.env` |

Integration env keys are listed by name only (no values). `VERIFIED_FROM_SOURCE`.

Config defaults: [`config/database.php:19`](../../config/database.php#L19) (`DB_CONNECTION` default sqlite),
[`config/app.php:29`](../../config/app.php#L29) (`APP_ENV` default production), `config/app.php:42` (`APP_DEBUG`).
`VERIFIED_FROM_SOURCE`.

---

## 3. Related

- [`DEVELOPER_SETUP.md`](./DEVELOPER_SETUP.md)
- [`../18-configuration-and-env.md`](../18-configuration-and-env.md)
- [`../09-dependencies.md`](../09-dependencies.md)