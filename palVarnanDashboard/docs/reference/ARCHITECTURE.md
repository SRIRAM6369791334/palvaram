# ARCHITECTURE

**Stack:** Laravel 8.65 (MVC), PHP ^7.3|^8.0 [VERIFIED_FROM_SOURCE] — [`composer.json:11,16`](../../palVarnanDashboard/composer.json#L11), [`composer.json:16`](../../palVarnanDashboard/composer.json#L16).

## MVC & structure

Standard Laravel MVC: routes → controllers → models → views [INFERRED].  
Controllers: 76 (recursive), Models: 63, Services: 1 [VERIFIED_FROM_SOURCE] — [`VERIFIED_FACTS.md:24-26,31`](../audit/VERIFIED_FACTS.md#L24-L26,L31). See [PROJECT_STRUCTURE.md](./PROJECT_STRUCTURE.md) and [04-directory-structure.md](../04-directory-structure.md).

## Authentication

- Auth scaffold under `app/Http/Controllers/Auth/` (6 files) [VERIFIED_FROM_SOURCE] — [`VERIFIED_FACTS.md:25`](../audit/VERIFIED_FACTS.md#L25)
- `DashboardUser` is authenticatable model wired to web guard [VERIFIED_FROM_SOURCE] — [`01-project-overview.md:56-59`](../01-project-overview.md#L56-L59), [10-authentication.md](../10-authentication.md)
- `User` and `DeliveryPerson` also implement `Authenticatable` [INFERRED]
- `Auth::routes()` registered at `routes/web.php:89`; app routes wrapped in `Route::middleware(['auth'])->group(...)` with root using `auth` middleware (line 92) [VERIFIED_FROM_SOURCE] — [`web.php:89,91-92`](../../palVarnanDashboard/routes/web.php#L89), [`web.php:91-92`](../../palVarnanDashboard/routes/web.php#L91-L92)

## Authorization (RBAC)

- Spatie `laravel-permission` v6 installed [VERIFIED_FROM_SOURCE] — [`composer.json:21`](../../palVarnanDashboard/composer.json#L21)
- Permission tables migration exists [VERIFIED_FROM_SOURCE] — [`01-project-overview.md:48-50`](../01-project-overview.md#L48-L50)
- Roles/permissions UI present (`RoleController`, `DashboardUserController`) [VERIFIED_FROM_SOURCE] — [`01-project-overview.md:48-50`](../01-project-overview.md#L48-L50)
- **Not enforced at route layer:** no `role:`, `permission:`, or `can:` middleware used; only `auth` appears [VERIFIED_FROM_SOURCE] — [`VERIFIED_FACTS.md:38-40`](../audit/VERIFIED_FACTS.md#L38-L40)
- Authorization checks may exist inside controllers in places [INFERRED] — see [11-authorization-rbac.md](../11-authorization-rbac.md), [BUSINESS_LOGIC.md](./BUSINESS_LOGIC.md)

## Routing

- `web.php`: ~635 lines, 330 `Route::` entries [VERIFIED_FROM_SOURCE] — [`VERIFIED_FACTS.md:28,37`](../audit/VERIFIED_FACTS.md#L28,L37)
- `api.php`: 2 routes (`/user` sanctum, `GET /api/test` no middleware) [VERIFIED_FROM_SOURCE] — [`api.php:18,25`](../../palVarnanDashboard/routes/api.php#L18), [`api.php:25`](../../palVarnanDashboard/routes/api.php#L25)
- **Gaps/broken references:** `SubCategoryController::validateSubCategoryName` called but missing; `DashboardUserController` resource references `store()` but missing; `ProductwiseController::index` route commented out in favor of explicit methods [VERIFIED_FROM_SOURCE] — [`VERIFIED_FACTS.md:41-60`](../audit/VERIFIED_FACTS.md#L41-L60), [`web.php:136,336,373-378,390`](../../palVarnanDashboard/routes/web.php#L136), [`web.php:336`](../../palVarnanDashboard/routes/web.php#L336), [`web.php:373-378`](../../palVarnanDashboard/routes/web.php#L373-L378), [`web.php:390`](../../palVarnanDashboard/routes/web.php#L390)

See detailed analysis: [08-routing.md](../08-routing.md).

## Services & integration styles

- **ShiprocketService** (`app/Services/ShiprocketService.php`): uses Laravel HTTP client, caches token via `Cache::remember`, reads from `config('services.shiprocket.*')` [VERIFIED_FROM_SOURCE] — [`ShiprocketService.php:1-50`](../../palVarnanDashboard/app/Services/ShiprocketService.php#L1-L50)
- **Inline cURL (unsafe):** 5 locations in controllers embed hardcoded email/password in JSON POSTs [VERIFIED_FROM_SOURCE] — [`VERIFIED_FACTS.md:71-79`](../audit/VERIFIED_FACTS.md#L71-L79), [README reconciliations](./README.md#reconciliation-headlines-vs-existing-docs)
- **Pattern:** codebase mixes safe service with unsafe inline cURL blocks [INFERRED] — [`VERIFIED_FACTS.md:78-79`](../audit/VERIFIED_FACTS.md#L78-L79)

## Scheduling

- Console command: `SyncShiprocketStatus` with signature `shiprocket:sync` [VERIFIED_FROM_SOURCE] — [`SyncShiprocketStatus.php:19,26`](../../palVarnanDashboard/app/Console/Commands/SyncShiprocketStatus.php#L19), [`SyncShiprocketStatus.php:26`](../../palVarnanDashboard/app/Console/Commands/SyncShiprocketStatus.php#L26)
- Kernel schedules: `$schedule->command('shiprocket:sync')->everyThirtyMinutes();` [VERIFIED_FROM_SOURCE] — [`Kernel.php:18`](../../palVarnanDashboard/app/Console/Kernel.php#L18)
- Sync maps Shiprocket status codes to local statuses (shipped/out_for_delivery/delivered/return) and updates `product_slots.delivery_status`; may email customer if mailable exists [VERIFIED_FROM_SOURCE] — [`SyncShiprocketStatus.php:43-120`](../../palVarnanDashboard/app/Console/Commands/SyncShiprocketStatus.php#L43-L120)

See [20-scheduling-and-jobs.md](../20-scheduling-and-jobs.md).

## Queues

Default queue driver [INFERRED] (Laravel default). No explicit custom queue worker registration shown in Kernel; background jobs not prominent in audit notes [INFERRED]. B (sibling) runs `queue=sync` [VERIFIED_FROM_SOURCE] — [`VERIFIED_FACTS.md:108`](../audit/VERIFIED_FACTS.md#L108).

## Data model & multi-country

Eloquent models in `app/Models/` (63). Multi-country via `country_id` added to core tables (April 2026) and filters by `session('country_id')` in many screens [VERIFIED_FROM_SOURCE] — [`01-project-overview.md:45-47`](../01-project-overview.md#L45-L47), [`StockController.php:33-37`](../../palVarnanDashboard/app/Http/Controllers/StockController.php#L33-L37) [INFERRED].  
Gift separation via `is_gift` boolean flags [VERIFIED_FROM_SOURCE] — [`01-project-overview.md:40-44`](../01-project-overview.md#L40-L44).

See [06-data-model.md](../06-data-model.md), [05-database-schema.md](../05-database-schema.md).

## Security notes (architectural)

- Routes not RBAC-enforced [BROKEN] [VERIFIED_FROM_SOURCE] — [`VERIFIED_FACTS.md:38-40`](../audit/VERIFIED_FACTS.md#L38-L40)
- Hardcoded creds in controllers [BROKEN] [VERIFIED_FROM_SOURCE] — [README reconciliations](./README.md#reconciliation-headlines-vs-existing-docs)
- Unauthenticated write `GET /api/test` [BROKEN] [VERIFIED_FROM_SOURCE] — [`api.php:25`](../../palVarnanDashboard/routes/api.php#L25)
- Password reset endpoints lack ownership/role checks [BROKEN] [VERIFIED_FROM_SOURCE] — [`VERIFIED_FACTS.md:58-60`](../audit/VERIFIED_FACTS.md#L58-L60)
- Route/method references broken (validateSubCategoryName missing, DashboardUserController store missing) [BROKEN] [VERIFIED_FROM_SOURCE] — [`VERIFIED_FACTS.md:41-46`](../audit/VERIFIED_FACTS.md#L41-L46)

See [13-security-findings.md](../13-security-findings.md) for full register.

## Frontend/build

Laravel Mix, symox admin theme [VERIFIED_FROM_SOURCE] — [`VERIFIED_FACTS.md:17`](../audit/VERIFIED_FACTS.md#L17), [19-frontend-architecture.md](../19-frontend-architecture.md).

## Timeline tweak

Only `AppServiceProvider.php` and `TrustProxies.php` changed ~2026-10-13 [VERIFIED_FROM_SOURCE] — [`VERIFIED_FACTS.md:94-97`](../audit/VERIFIED_FACTS.md#L94-L97).