# PROJECT_STRUCTURE

**Root:** `C:\xampp\htdocs\pauls\palVarnanDashboard` [VERIFIED_FROM_SOURCE] — [`VERIFIED_FACTS.md:11`](../audit/VERIFIED_FACTS.md#L11).

## Top-level (selected)

Key directories/files relevant to reference [INFERRED]; see full structure in [04-directory-structure.md](../04-directory-structure.md).

```
palVarnanDashboard/
├── app/                # Core application (Controllers, Models, Services, Providers, Middleware, Console)
├── bootstrap/
├── config/
├── database/           # migrations/, seeders/, factories/
├── docs/               # Existing numbered docs (01-20) + audit/ + reference/ (this dir)
├── public/
├── resources/          # views/, lang/, assets (Laravel Mix)
├── routes/             # web.php, api.php, console.php, channels.php
├── storage/
├── tests/
├── vendor/
├── composer.json
└── package.json (if present) [INFERRED]
```

## app/ directory

Counts [VERIFIED_FROM_SOURCE] — [`VERIFIED_FACTS.md:24-34`](../audit/VERIFIED_FACTS.md#L24-L34):

- `app/Http/Controllers/` — **76** files (recursive), including **Auth/** subfolder with 6 scaffold files (Login/Register/Forgot/Reset/Confirm/Verification)
- `app/Models/` — **63** models
- `app/Services/` — **1** (`ShiprocketService.php`)
- `app/Console/Commands/` — **1** (`SyncShiprocketStatus.php`, signature `shiprocket:sync`)
- `app/Console/Kernel.php` — schedules `shiprocket:sync` every 30 min
- `app/Http/Middleware/` — Authenticate, EncryptCookies, Localization, PreventRequestsDuringMaintenance, RedirectIfAuthenticated, TrimStrings, TrustHosts, TrustProxies, VerifyCsrfToken [VERIFIED_FROM_SOURCE] — [`VERIFIED_FACTS.md:33`](../audit/VERIFIED_FACTS.md#L33)
- `app/Providers/` — AppServiceProvider, AuthServiceProvider, BroadcastServiceProvider, EventServiceProvider, RouteServiceProvider (+ TelescopeServiceProvider if enabled) [INFERRED] — see [17-configuration.md](../17-configuration.md), [Timeline notes](../audit/VERIFIED_FACTS.md#L94-L97)

**Auth subfolder:** `app/Http/Controllers/Auth/` contains Laravel auth scaffold controllers [VERIFIED_FROM_SOURCE] — [`VERIFIED_FACTS.md:25`](../audit/VERIFIED_FACTS.md#L25), [10-authentication.md](../10-authentication.md).

## Routes

- `routes/web.php` — **~635 lines**, **330 `Route::` entries** [VERIFIED_FROM_SOURCE] — [`VERIFIED_FACTS.md:28,37`](../audit/VERIFIED_FACTS.md#L28,L37), [`web.php`](../../palVarnanDashboard/routes/web.php)
- `routes/api.php` — **2 routes**: `auth:sanctum` `/user`, and **`GET /api/test` (no middleware)** calling `TestController::test()` which writes data [VERIFIED_FROM_SOURCE] — [`api.php:18,25`](../../palVarnanDashboard/routes/api.php#L18), [`api.php:25`](../../palVarnanDashboard/routes/api.php#L25), [`VERIFIED_FACTS.md:29,64-67`](../audit/VERIFIED_FACTS.md#L29,L64-L67)
- `routes/console.php`, `routes/channels.php` — standard [INFERRED]

## Resources/views

Admin theme "symox"; views under `resources/views/` including `pages/` (gifts, products, orders, reports, etc.), `layouts/`, `auth/` [VERIFIED_FROM_SOURCE] — [`VERIFIED_FACTS.md:17`](../audit/VERIFIED_FACTS.md#L17), [`19-frontend-architecture.md`](../19-frontend-architecture.md).

## Database

- `database/migrations/` — **60** migration files [VERIFIED_FROM_SOURCE] — [`VERIFIED_FACTS.md:27`](../audit/VERIFIED_FACTS.md#L27)
- `database/seeders/`, `database/factories/` [INFERRED]
- Live schema: **93** tables in `palvarnan_db (2).sql` [VERIFIED_FROM_SOURCE] — [`VERIFIED_FACTS.md:30`](../audit/VERIFIED_FACTS.md#L30). Note: migrations cover fraction of live tables [VERIFIED_FROM_SOURCE] — [01-project-overview.md:71](../01-project-overview.md#L71)

## Docs

Existing: `docs/01-20.md`, `docs/README.md`, `docs/audit/VERIFIED_FACTS.md`. Reference library created here: `docs/reference/*.md` (8 files) — **no existing files modified** [INFERRED]. See [docs/README.md index](../README.md#document-index) (note 21–30 do not exist) [CONFLICTING_EVIDENCE] — [`docs/README.md:61-71`](../README.md#L61-L71).

## Notable files (cited)

- `app/Services/ShiprocketService.php` [VERIFIED_FROM_SOURCE] — [`ShiprocketService.php`](../../palVarnanDashboard/app/Services/ShiprocketService.php)
- `app/Console/Commands/SyncShiprocketStatus.php` [VERIFIED_FROM_SOURCE] — [`SyncShiprocketStatus.php`](../../palVarnanDashboard/app/Console/Commands/SyncShiprocketStatus.php)
- Controllers with hardcoded Shiprocket creds: `ProductController.php:869,1010`, `ProductOrdersController.php:110`, `ProductSlotController.php:131,202` [VERIFIED_FROM_SOURCE] — cited in [README](./README.md#reconciliation-headlines-vs-existing-docs)

See also: [04-directory-structure.md](../04-directory-structure.md) for detailed tree.