# PROJECT_OVERVIEW

**Root:** `C:\xampp\htdocs\pauls\palVarnanDashboard` [VERIFIED_FROM_SOURCE] — [`VERIFIED_FACTS.md:11`](../audit/VERIFIED_FACTS.md#L11).  
**Framework:** Laravel **`^8.65`** [VERIFIED_FROM_SOURCE] — [`composer.json:16`](../../palVarnanDashboard/composer.json#L16).  
**PHP constraint:** **`^7.3|^8.0`** [VERIFIED_FROM_SOURCE] — [`composer.json:11`](../../palVarnanDashboard/composer.json#L11), [`VERIFIED_FACTS.md:13`](../audit/VERIFIED_FACTS.md#L13).  
**DB:** Shared MySQL schema **`palvarnan_db`** (same as Project B) [VERIFIED_FROM_SOURCE] — [`VERIFIED_FACTS.md:18,106-109`](../audit/VERIFIED_FACTS.md#L18,L106-L109).

## What this application does

Multi-country dairy and retail administration dashboard (back-office). Not a public storefront [VERIFIED_FROM_SOURCE] — [`01-project-overview.md:5-12`](../01-project-overview.md#L5-L12).

Two primary product families + gift variant:

- **Dairy/subscriptions:** Milk orders/slots, delivery persons, areas/assignments [VERIFIED_FROM_SOURCE] — [`01-project-overview.md:18-23`](../01-project-overview.md#L18-L23), [12-business-logic.md](../12-business-logic.md), [09-controllers.md](../09-controllers.md).
- **Retail/packaged goods:** Catalog (products/variants/thumbnails), brands/categories/subcategories, stock/distribution/transfers, purchase orders, suppliers, order lifecycle [VERIFIED_FROM_SOURCE] — [`01-project-overview.md:24-35`](../01-project-overview.md#L24-L35).
- **Gift business:** Near-duplicate flow with own controllers/views; separated by `is_gift` flags on products/categories/supplier tables [VERIFIED_FROM_SOURCE] — [`01-project-overview.md:36-44`](../01-project-overview.md#L36-L44).

**Cross-cutting:** multi-country (`country_id` filters via `session('country_id')` in many screens), Spatie roles/permissions UI, Shiprocket integration, exports (Excel/PDF), reports [VERIFIED_FROM_SOURCE] — [`01-project-overview.md:45-55`](../01-project-overview.md#L45-L55), [05-database-schema.md](../05-database-schema.md), [16-external-integrations.md](../16-external-integrations.md).

## Packages (Composer)

From [`composer.json:10-22`](../../palVarnanDashboard/composer.json#L10-L22) [VERIFIED_FROM_SOURCE]:

- `laravel/framework: ^8.65`
- `spatie/laravel-permission: 6` ([RBAC docs](../11-authorization-rbac.md))
- `laravel/sanctum: ^2.11`
- `barryvdh/laravel-dompdf: ^2.2`
- `maatwebsite/excel: ^3.1`
- `laravel/telescope: ^4.14` (dev; `dont-discover` in extra [VERIFIED_FROM_SOURCE] — [`composer.json:65-68`](../../palVarnanDashboard/composer.json#L65-L68))
- `barryvdh/laravel-debugbar: *` (dev/runtime present)
- `laravel/ui: ^3.3`, `fruitcake/laravel-cors: ^2.0`, `guzzlehttp/guzzle: ^7.0.1`, `laravel/tinker: ^2.5`
- Dev: `facade/ignition`, `fakerphp/faker`, `kitloong/laravel-migrations-generator`, `laravel/sail`, `mockery/mockery`, `nunomaduro/collision`, `phpunit/phpunit`

## Frontend/build

Laravel Mix / webpack; admin theme "symox"; author "MilkVandi" [VERIFIED_FROM_SOURCE] — [`VERIFIED_FACTS.md:17`](../audit/VERIFIED_FACTS.md#L17), [`19-frontend-architecture.md`](../19-frontend-architecture.md).

## Auth model & users

`DashboardUser` is the authenticatable model wired for web guard [VERIFIED_FROM_SOURCE] — [`01-project-overview.md:56-59`](../01-project-overview.md#L56-L59), [`config/auth.php`](../../palVarnanDashboard/config/auth.php) [INFERRED]. `User` and `DeliveryPerson` also implement `Authenticatable` [INFERRED].

## Roles/permissions (defined, not enforced)

- Spatie migration present: `2026_04_06_124404_create_permission_tables.php` [VERIFIED_FROM_SOURCE] — [`01-project-overview.md:48-50`](../01-project-overview.md#L48-L50), [`05-database-schema.md`](../05-database-schema.md), [`11-authorization-rbac.md`](../11-authorization-rbac.md).
- UI exists for roles/users management (`RoleController`, `DashboardUserController`) [VERIFIED_FROM_SOURCE] — [`01-project-overview.md:48-50`](../01-project-overview.md#L48-L50).
- **Critical:** no `role:`, `permission:`, or `can:` middleware in routes; only `middleware('auth')` at home root (line 92) [VERIFIED_FROM_SOURCE] — [`VERIFIED_FACTS.md:38-40`](../audit/VERIFIED_FACTS.md#L38-L40), [`routes/web.php:91-92`](../../palVarnanDashboard/routes/web.php#L91-L92).
- See [11-authorization-rbac.md](../11-authorization-rbac.md) for enforcement gap and [BUSINESS_LOGIC.md](./BUSINESS_LOGIC.md) for checks present in controllers.

## Integrations

- **Shiprocket**: `app/Services/ShiprocketService.php` uses `config('services.shiprocket.*')` (safe pattern). However, controllers also contain inline cURL login calls with hardcoded credentials in 5 locations [VERIFIED_FROM_SOURCE] — [`16-external-integrations.md`](../16-external-integrations.md), [`VERIFIED_FACTS.md:71-79`](../audit/VERIFIED_FACTS.md#L71-L79), [`ShiprocketService.php:23-30`](../../palVarnanDashboard/app/Services/ShiprocketService.php#L23-L30).
- **Exports**: DomPDF, Excel/Maatwebsite [VERIFIED_FROM_SOURCE] — [`composer.json:13,20`](../../palVarnanDashboard/composer.json#L13), [`composer.json:20`](../../palVarnanDashboard/composer.json#L20), [`15-file-handling.md`](../15-file-handling.md).
- **Monitoring/debug (dev):** Telescope, Debugbar [VERIFIED_FROM_SOURCE] — [`composer.json:12,18,28`](../../palVarnanDashboard/composer.json#L12), [`composer.json:18,28`](../../palVarnanDashboard/composer.json#L18,L28).

## Shared DB & cross-project

Both apps share `palvarnan_db`; A owns migration set. Scheduler runs in A; B uses `queue=sync` with no scheduler [VERIFIED_FROM_SOURCE] — [`VERIFIED_FACTS.md:106-109`](../audit/VERIFIED_FACTS.md#L106-L109).

## Scheduling

`shiprocket:sync` runs every 30 minutes via `App\Console\Kernel::schedule()` [VERIFIED_FROM_SOURCE] — [`Kernel.php:18`](../../palVarnanDashboard/app/Console/Kernel.php#L18), [`20-scheduling-and-jobs.md`](../20-scheduling-and-jobs.md).

## Health notes (from existing docs)

Summary in [01-project-overview.md:64-77](../01-project-overview.md#L64-L77): routing integrity BROKEN, authorization BROKEN, credential hygiene BROKEN, schema migrations incomplete vs live tables, etc. [VERIFIED_FROM_SOURCE].

See also: [13-security-findings.md](../13-security-findings.md), [08-routing.md](../08-routing.md), [26-refactoring-roadmap.md](../26-refactoring-roadmap.md).