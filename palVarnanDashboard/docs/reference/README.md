# Reference Library (Canonical)

**Project A root:** `C:\xampp\htdocs\pauls\palVarnanDashboard` (`Laravel 8.65`, `PHP ^7.3|^8.0`) [VERIFIED_FROM_SOURCE] — [`composer.json:11-22`](../../palVarnanDashboard/composer.json#L11-L22), [`docs/audit/VERIFIED_FACTS.md:11-18`](../audit/VERIFIED_FACTS.md#L11-L18)

**Output location:** `docs/reference/` (this directory). All files are new and created without modifying existing docs [INFERRED].

**Links to existing docs:** [Existing docs README](../README.md) (`../README.md`), [Verified facts audit](../audit/VERIFIED_FACTS.md) (`../audit/VERIFIED_FACTS.md`). Existing numbered docs 01–20 are referenced throughout; see [01–20 index](../README.md#document-index) [VERIFIED_FROM_SOURCE] — [`docs/README.md:32-72`](../README.md#L32-L72).

## Evidence vocabulary

Every non-obvious claim uses one of these tags (see [VERIFIED_FACTS §6](../audit/VERIFIED_FACTS.md#L5-L7), [GLOSSARY](./GLOSSARY.md)):

| Tag | Meaning |
|---|---|
| `VERIFIED_FROM_SOURCE` | Directly observed in source file at cited `file:line`. |
| `VERIFIED_FROM_LIVE_SCHEMA` | Confirmed from SQL/schema evidence (e.g. `palvarnan_db (2).sql`) at cited location. |
| `INFERRED` | Reasonable conclusion from source; not explicitly stated. |
| `UNVERIFIED` | Cannot be confirmed without runtime/database state. |
| `NOT_AVAILABLE` | Information not present in inspected sources. |
| `CONFLICTING_EVIDENCE` | Contradicts existing doc(s); difference documented. |

## Reconciliation headlines (vs existing docs)

- **Docs index mismatch [CONFLICTING_EVIDENCE]**: [`docs/README.md`](../README.md) indexes documents **21–30** (lines ~61–71) but those files **do not exist** under [`docs/`](../) (only `01–20`, `README.md`, `audit/` exist) [VERIFIED_FROM_SOURCE] — [`docs/README.md:61-71`](../README.md#L61-L71), [`docs/` listing](../) (see [audit notes](../audit/VERIFIED_FACTS.md#L100-L103)).
- **Route counts [VERIFIED_FROM_SOURCE]**: `routes/web.php` is **~635 lines** with **330 `Route::` entries**; `routes/api.php` has **2** routes (`/user`, `/api/test`) [VERIFIED_FROM_SOURCE] — [`routes/web.php`](../../palVarnanDashboard/routes/web.php), [`routes/api.php`](../../palVarnanDashboard/routes/api.php), [`VERIFIED_FACTS.md:28-29,37`](../audit/VERIFIED_FACTS.md#L28-L29,L37).
- **Codebase size [VERIFIED_FROM_SOURCE]**: **76** controllers (recursive) including **6** in `Auth/`; **63** models; **60** migrations; **93** tables in `palvarnan_db (2).sql` [VERIFIED_FROM_SOURCE] — [`VERIFIED_FACTS.md:24-34,30`](../audit/VERIFIED_FACTS.md#L24-L34,L30).
- **Hardcoded credentials [VERIFIED_FROM_SOURCE]**: Shiprocket login payloads embedded in plaintext cURL in **5 controller locations**: `ProductController.php:869,1010`; `ProductOrdersController.php:110`; `ProductSlotController.php:131,202` [VERIFIED_FROM_SOURCE] — [`ProductController.php:869,1010`](../../palVarnanDashboard/app/Http/Controllers/ProductController.php#L869), [`ProductController.php:1010`](../../palVarnanDashboard/app/Http/Controllers/ProductController.php#L1010), [`ProductOrdersController.php:110`](../../palVarnanDashboard/app/Http/Controllers/ProductOrdersController.php#L110), [`ProductSlotController.php:131`](../../palVarnanDashboard/app/Http/Controllers/ProductSlotController.php#L131), [`ProductSlotController.php:202`](../../palVarnanDashboard/app/Http/Controllers/ProductSlotController.php#L202). (`ShiprocketService.php` uses `config('services.shiprocket.*')` instead.) [INFERRED].
- **Unauthenticated write endpoint [VERIFIED_FROM_SOURCE]**: `routes/api.php:25` defines `GET /api/test` with **no middleware**, and `TestController::test()` writes a `milk_order_user_addresses` row [VERIFIED_FROM_SOURCE] — [`api.php:25`](../../palVarnanDashboard/routes/api.php#L25), [`VERIFIED_FACTS.md:64-67`](../audit/VERIFIED_FACTS.md#L64-L67).
- **Route/method gaps [VERIFIED_FROM_SOURCE]**: `SubCategoryController::validateSubCategoryName` called at `routes/web.php:136,390` but **does not exist**; `DashboardUserController` `store()` referenced by resource `only(["index","store"])` (line 336) but controller has **no `store()`** method (has `index`, `userss`, `update`, `destroy`, `update1`) [VERIFIED_FROM_SOURCE] — [`web.php:136,336,390`](../../palVarnanDashboard/routes/web.php#L136), [`web.php:336`](../../palVarnanDashboard/routes/web.php#L336), [`web.php:390`](../../palVarnanDashboard/routes/web.php#L390), [`VERIFIED_FACTS.md:41-46,52-60`](../audit/VERIFIED_FACTS.md#L41-L46,L52-L60).
- **Productwise route claim outdated [CONFLICTING_EVIDENCE]**: existing docs claim `ProductwiseController::index` usage; live routes comment out resource (line 373) and instead define `productWiseReport`, `filterProductWiseReport`, `exportExcel`, `exportPDF` (lines 375–378) [VERIFIED_FROM_SOURCE] — [`web.php:373-378`](../../palVarnanDashboard/routes/web.php#L373-L378), [`VERIFIED_FACTS.md:47-50`](../audit/VERIFIED_FACTS.md#L47-L50).
- **Spatie not enforced at routes [VERIFIED_FROM_SOURCE]**: `middleware('auth')` appears only once in `routes/web.php` (line 92); no `role:`, `permission:`, or `can:` middleware used [VERIFIED_FROM_SOURCE] — [`web.php:91-92`](../../palVarnanDashboard/routes/web.php#L91-L92), [`VERIFIED_FACTS.md:38-40`](../audit/VERIFIED_FACTS.md#L38-L40).
- **Password reset privilege issue [VERIFIED_FROM_SOURCE]**: `updatepass/{id}` routes to `DashboardUserController::update1` (line 340) and `UserController::updatePass` (line 370); lack ownership/role checks (any authenticated dashboard user can reset another user’s password) [VERIFIED_FROM_SOURCE] — [`web.php:340,370`](../../palVarnanDashboard/routes/web.php#L340), [`VERIFIED_FACTS.md:58-60`](../audit/VERIFIED_FACTS.md#L58-L60).
- **Shared DB [VERIFIED_FROM_SOURCE]**: Both apps share MySQL schema `palvarnan_db` (same DB as Project B); A owns migrations set [VERIFIED_FROM_SOURCE] — [`VERIFIED_FACTS.md:18,106-109`](../audit/VERIFIED_FACTS.md#L18,L106-L109).
- **Scheduling [VERIFIED_FROM_SOURCE]**: `app/Console/Kernel.php:18` schedules `shiprocket:sync` every 30 minutes via `SyncShiprocketStatus` command [VERIFIED_FROM_SOURCE] — [`Kernel.php:18`](../../palVarnanDashboard/app/Console/Kernel.php#L18), [`SyncShiprocketStatus.php:19`](../../palVarnanDashboard/app/Console/Commands/SyncShiprocketStatus.php#L19).
- **Shiprocket integration styles [INFERRED]**: Safe service (`ShiprocketService.php` using `config('services.shiprocket.*')`) coexists with inline cURL blocks containing hardcoded credentials in controllers [VERIFIED_FROM_SOURCE] — [`ShiprocketService.php:23-30`](../../palVarnanDashboard/app/Services/ShiprocketService.php#L23-L30), controller locations above.

## Reference files (siblings)

1. [README.md](./README.md) — this index (you are here)
2. [PROJECT_OVERVIEW.md](./PROJECT_OVERVIEW.md) — stack, packages, integrations, roles/permissions
3. [PROJECT_STRUCTURE.md](./PROJECT_STRUCTURE.md) — directory map and counts
4. [DEVELOPER_SETUP.md](./DEVELOPER_SETUP.md) — prerequisites, install/build, env, DB notes
5. [ARCHITECTURE.md](./ARCHITECTURE.md) — MVC, auth, RBAC, scheduling, queues, Shiprocket styles
6. [GLOSSARY.md](./GLOSSARY.md) — domain terms, states, RBAC, scheduler, evidence tags
7. [FEATURE_CATALOG.md](./FEATURE_CATALOG.md) — major features and modules
8. [BUSINESS_LOGIC.md](./BUSINESS_LOGIC.md) — workflows, stock, slots/packing, Shiprocket flows, country scoping

**Notes on reuse:** Link to existing numbered docs ([01-project-overview.md](../01-project-overview.md), [08-routing.md](../08-routing.md), [11-authorization-rbac.md](../11-authorization-rbac.md), [12-business-logic.md](../12-business-logic.md), [13-security-findings.md](../13-security-findings.md), [16-external-integrations.md](../16-external-integrations.md), [20-scheduling-and-jobs.md](../20-scheduling-and-jobs.md), etc.) rather than duplicating content [INFERRED].