# DEVELOPER_SETUP

**Root:** `C:\xampp\htdocs\pauls\palVarnanDashboard` [VERIFIED_FROM_SOURCE] — [`VERIFIED_FACTS.md:11`](../audit/VERIFIED_FACTS.md#L11).  
**Constraints:** Laravel `^8.65`, PHP `^7.3|^8.0` [VERIFIED_FROM_SOURCE] — [`composer.json:11,16`](../../palVarnanDashboard/composer.json#L11), [`composer.json:16`](../../palVarnanDashboard/composer.json#L16).

## Prerequisites

- PHP **7.3+** compatible with **8.0** context (matches `^7.3|^8.0`) [VERIFIED_FROM_SOURCE] — [`composer.json:11`](../../palVarnanDashboard/composer.json#L11)
- Composer 2.x [INFERRED]
- Node.js + npm (for Laravel Mix build) [INFERRED] — see [19-frontend-architecture.md](../19-frontend-architecture.md)
- MySQL (shared DB `palvarnan_db`) [VERIFIED_FROM_SOURCE] — [`VERIFIED_FACTS.md:18`](../audit/VERIFIED_FACTS.md#L18)
- Web server (Apache/XAMPP as in environment) [INFERRED]

## Installation

From project root [INFERRED]:

```bash
composer install
npm install
```

## Build (frontend)

Laravel Mix (webpack). Build assets per environment [INFERRED] — [`19-frontend-architecture.md`](../19-frontend-architecture.md):

```bash
npm run dev
# or
npm run prod
# or watch
npm run watch
```

## Environment (.env)

Copy example and set keys [INFERRED] — [`composer.json:56-61`](../../palVarnanDashboard/composer.json#L56-L61) shows post-install copies `.env.example` to `.env` and generates key.

Generate app key:

```bash
php artisan key:generate
```

Configure at minimum: `APP_NAME`, `APP_ENV`, `APP_KEY`, `APP_DEBUG`, `APP_URL`, database (`DB_*`), mail, cache/session, and **Shiprocket** service keys (used by `ShiprocketService.php`) [INFERRED].  
Note: `ShiprocketService.php` reads `config('services.shiprocket.email')`, `password`, `base_url` [VERIFIED_FROM_SOURCE] — [`ShiprocketService.php:23-30`](../../palVarnanDashboard/app/Services/ShiprocketService.php#L23-L30).

**Never commit secrets.** Refer to env var names only [RULE].

## Database

- Shared schema `palvarnan_db` [VERIFIED_FROM_SOURCE] — [`VERIFIED_FACTS.md:18`](../audit/VERIFIED_FACTS.md#L18)
- **Migrations:** 60 files in `database/migrations/` [VERIFIED_FROM_SOURCE] — [`VERIFIED_FACTS.md:27`](../audit/VERIFIED_FACTS.md#L27)
- **Live tables:** 93 tables in `palvarnan_db (2).sql` [VERIFIED_FROM_SOURCE] — [`VERIFIED_FACTS.md:30`](../audit/VERIFIED_FACTS.md#L30)
- **Coverage note:** migrations cover only a fraction of live tables [VERIFIED_FROM_SOURCE] — [`01-project-overview.md:71`](../01-project-overview.md#L71) (schema drift). A full domain migration coverage is **not present** [INFERRED] — align with [05-database-schema.md](../05-database-schema.md).

Running migrations (standard): `php artisan migrate` — but expect mismatches vs live schema [INFERRED]; consult [05-database-schema.md](../05-database-schema.md), [07-data-integrity.md](../07-data-integrity.md).

## Auth/scaffold

Laravel auth scaffold present (`Auth/` controllers) [VERIFIED_FROM_SOURCE] — [`VERIFIED_FACTS.md:25`](../audit/VERIFIED_FACTS.md#L25), [10-authentication.md](../10-authentication.md). `DashboardUser` is web guard model [VERIFIED_FROM_SOURCE] — [`01-project-overview.md:56-59`](../01-project-overview.md#L56-L59).

## Scheduling/queues

- Scheduler: `shiprocket:sync` every 30 min (defined in Kernel) [VERIFIED_FROM_SOURCE] — [`Kernel.php:18`](../../palVarnanDashboard/app/Console/Kernel.php#L18)
- Queues: default (no queue worker config details obvious in Kernel beyond default) [INFERRED] — see [20-scheduling-and-jobs.md](../20-scheduling-and-jobs.md)
- Run scheduler locally if needed: `php artisan schedule:run` (or configure cron) [INFERRED]

## Docker assets

Git history references “docker deplyeement” / Docker and Render setup for palVarnan and palVarnanDashboard [VERIFIED_FROM_SOURCE] — [`VERIFIED_FACTS.md:89-91`](../audit/VERIFIED_FACTS.md#L89-L91). Dockerfiles/render configs **may exist** in repo; verify presence in root/related paths [UNVERIFIED] — inspect repo root for `Dockerfile*`, `docker-compose*`, `render.yaml`, etc. [INFERRED].

## Important warnings (from audit)

- **Hardcoded credentials** in 5 controller locations (Shiprocket) — never use in prod; prefer `ShiprocketService` + `.env`/config [VERIFIED_FROM_SOURCE] — [README reconciliations](./README.md#reconciliation-headlines-vs-existing-docs)
- **Unauthenticated write endpoint** `GET /api/test` [VERIFIED_FROM_SOURCE] — [`api.php:25`](../../palVarnanDashboard/routes/api.php#L25); restrict/remove in non-dev [INFERRED]
- **RBAC not enforced at routes** [VERIFIED_FROM_SOURCE] — [`VERIFIED_FACTS.md:38-40`](../audit/VERIFIED_FACTS.md#L38-L40)
- **Broken routes/method references** exist [VERIFIED_FROM_SOURCE] — [`VERIFIED_FACTS.md:41-60`](../audit/VERIFIED_FACTS.md#L41-L60)

## References

- [Existing dev setup notes](../22-deployment.md) (deployment/local) [INFERRED]
- [Tech stack](../03-technology-stack.md), [Config](../17-configuration.md), [Auth](../10-authentication.md), [Scheduling](../20-scheduling-and-jobs.md)