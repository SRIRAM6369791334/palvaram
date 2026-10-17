# VERIFIED FACTS — Project A (`palVarnanDashboard`)

> Working notes for the canonical reference library under `docs/reference/`.
> Every fact was checked directly against source on the audit date.
> Evidence vocabulary: `VERIFIED_FROM_SOURCE`, `VERIFIED_FROM_LIVE_SCHEMA`, `INFERRED`,
> `UNVERIFIED`, `NOT_AVAILABLE`, `CONFLICTING_EVIDENCE`.
> **Never copy secret values into documentation.**

## Identity & stack

- Root: `C:\xampp\htdocs\pauls\palVarnanDashboard`
- Framework: **Laravel `^8.65`** — `VERIFIED_FROM_SOURCE` (`composer.json`)
- PHP: **`^7.3|^8.0`** — `VERIFIED_FROM_SOURCE`
- Key packages: `spatie/laravel-permission: 6`, `laravel/sanctum: ^2.11`,
  `barryvdh/laravel-dompdf: ^2.2`, `maatwebsite/excel: ^3.1`,
  `laravel/telescope: ^4.14`, `barryvdh/laravel-debugbar`.
- Frontend build: Laravel Mix / webpack; admin theme "symox"; author string "MilkVandi".
- DB: shared MySQL database **`palvarnan_db`** (same DB as Project B).

## Counts (VERIFIED_FROM_SOURCE)

| Artefact | Count |
|---|---|
| Controllers in `app/Http/Controllers` (recursive) | **76** files |
| — of which `Auth/` scaffold (Login/Register/Forgot/Reset/Confirm/Verification) | 6 |
| Models in `app/Models` | **63** |
| Migration files in `database/migrations` | **60** |
| `Route::` entries in `routes/web.php` | **330** |
| API routes in `routes/api.php` | 2 (sanctum `/user`, `/api/test`) |
| Tables in SQL dump `palvarnan_db (2).sql` | **93** (`VERIFIED_FROM_LIVE_SCHEMA`) |
| Services | 1 — `app/Services/ShiprocketService.php` |
| Console commands | 1 — `SyncShiprocketStatus.php` (`shiprocket:sync`) |
| Middleware | Authenticate, EncryptCookies, Localization, PreventRequestsDuringMaintenance, RedirectIfAuthenticated, TrimStrings, TrustHosts, TrustProxies, VerifyCsrfToken |

## Routing & authorization

- `routes/web.php` is ~635 lines, 330 routes.
- **`middleware('auth')` appears only once** in `routes/web.php` (line 92, home root). No route uses
  `role:`, `permission:`, or `can:` middleware → **Spatie roles/permissions are defined but not
  enforced at the route layer**. `VERIFIED_FROM_SOURCE`.
- **BROKEN route**: `routes/web.php` lines 136 and 390 call
  `SubCategoryController::validateSubCategoryName`, but that method **does not exist** in
  `app/Http/Controllers/SubCategoryController.php`. `VERIFIED_FROM_SOURCE`.
- **BROKEN resource**: `Route::resource('users', DashboardUserController::class)->only(["index","store"])`
  (line 336) but `DashboardUserController` has no `store()` method (only `index`, `userss`, `update`,
  `destroy`, `update1`). `VERIFIED_FROM_SOURCE`.
- Pre-existing doc claim about `ProductwiseController::index`: line 373 resource route is **commented
  out**; the live routes call `productWiseReport`, `filterProductWiseReport`, `exportExcel`,
  `exportPDF` → that claim is **OUTDATED / CONFLICTING_EVIDENCE** (re-check `ProductwiseController`
  for the four methods).
- `routes/web.php` lines 335-340:
  - `users` resource → `DashboardUserController` (index, store)
  - `userss` → `userss`
  - `updateuser/{id}` → `update`
  - `destroyusers/{id}` → `destroy`
  - **`updatepass/{id}` → `update1`** (line 340)
  - `updatePasss/{userId}` → `UserController::updatePass` (line 370)
  Both password-change routes lack ownership/role checks → an authenticated dashboard user can reset
  **another** user's password. `VERIFIED_FROM_SOURCE` (`DashboardUserController::update1` line ~162,
  `Hash::make` at line 171).

## Unauthenticated write endpoint

- `routes/api.php` line 25: `Route::get("test", [TestController::class, "test"]);` — **no middleware**.
  `TestController::test()` writes a `milk_order_user_addresses` row via
  `MilkOrderUserAddress::create(...)` and then `dd($deliveryPerson)`.
  → unauthenticated GET writes data. `VERIFIED_FROM_SOURCE`.

## Hardcoded credentials — CRITICAL (values NOT reproduced)

Hardcoded Shiprocket login payloads (email + password in plaintext JSON inside controllers) at:
- `app/Http/Controllers/ProductController.php` lines **869** and **1010**
- `app/Http/Controllers/ProductOrdersController.php` line **110**
- `app/Http/Controllers/ProductSlotController.php` lines **131** and **202**

`VERIFIED_FROM_SOURCE`. Document the **locations and pattern only**; never the values.
Note: `app/Services/ShiprocketService.php` uses `config('services.shiprocket.*')` (the correct
pattern), so the codebase mixes the safe service with unsafe inline cURL blocks.

## Shiprocket scheduling

- `app/Console/Kernel.php` line 18: `$schedule->command('shiprocket:sync')->everyThirtyMinutes();`
  `VERIFIED_FROM_SOURCE`.
- `SyncShiprocketStatus` (`shiprocket:sync`) syncs tracking for orders with `shiprocket_order_id`.
- Both apps share one Shiprocket account (see cross-project notes).

## Docker / deployment

- Git history includes "docker deplyeement" and "Add Docker and Render deployment setup for palVarnan
  and palVarnanDashboard" → Dockerfiles / render config may exist. Verify presence; document paths.

## Timeline / change surface

- Most of Project A source is unchanged since ~2026-08-17.
- Only `app/Providers/AppServiceProvider.php` and `app/Http/Middleware/TrustProxies.php` changed
  ~2026-10-13 (trusted proxy / provider tweak) — re-read these and document the current values.

## Documentation gaps (existing docs)

- `docs/README.md` indexes **30** documents but docs `21-…` through `30-…` **do not exist** — only
  `01-…` through `20-…`, `README.md` and `audit/` are present. `VERIFIED_FROM_SOURCE` (directory
  listing). The README index is aspirational/incorrect.

## Cross-project integration (CONFIRMED)

- Both apps use the same MySQL schema `palvarnan_db`; B reads/writes `products`, `product_orders`,
  `product_order_items`, `carts`, `wishlists`, `users`, etc., whose schema is owned by A's migration
  set. A has the scheduler; B runs `queue=sync` and has no scheduler.

## Rules for writers

1. Read the actual source before asserting anything. Cite `file:line`.
2. Reuse and **link** the existing numbered docs (`../01-…` through `../20-…`, `../README.md`) rather
   than copying.
3. Never print credential values; refer to file:line and env keys only.
4. Label every non-obvious claim with an evidence tag.
5. Do not modify anything outside `docs/`.
