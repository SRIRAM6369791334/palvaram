# GLOSSARY

Domain terms, status codes/states, RBAC, scheduler, and evidence tags. Link to existing glossaries where useful: [28-glossary.md](../28-glossary.md) [INFERRED].

## Evidence tags

| Tag | Meaning |
|---|---|
| `VERIFIED_FROM_SOURCE` | Directly observed in source at cited `file:line`. |
| `VERIFIED_FROM_LIVE_SCHEMA` | From SQL/schema evidence (e.g. `palvarnan_db (2).sql`). |
| `INFERRED` | Reasonable conclusion; not explicit. |
| `UNVERIFIED` | Needs runtime/db state. |
| `NOT_AVAILABLE` | Not present in inspected sources. |
| `CONFLICTING_EVIDENCE` | Contradicts existing doc(s). |

(Defined in [VERIFIED_FACTS.md:5-7](../audit/VERIFIED_FACTS.md#L5-L7))

## Domain terms

- **DashboardUser** — Authenticatable model for web guard (dashboard users) [VERIFIED_FROM_SOURCE] — [`01-project-overview.md:56-59`](../01-project-overview.md#L56-L59)
- **ProductOrder / ProductSlot** — Retail order lifecycle tables; statuses hardcoded as strings in queries [VERIFIED_FROM_SOURCE] — [`01-project-overview.md:32-35`](../01-project-overview.md#L32-L35)
- **MilkOrder / MilkSlot** — Dairy subscription orders/slots [VERIFIED_FROM_SOURCE] — [`01-project-overview.md:18-20`](../01-project-overview.md#L18-L20)
- **Gift business** — Separate flow with `is_gift` flags on products/categories/supplier tables [VERIFIED_FROM_SOURCE] — [`01-project-overview.md:36-44`](../01-project-overview.md#L36-L44)
- **Country scoping** — `country_id` filters via `session('country_id')` [VERIFIED_FROM_SOURCE] — [`01-project-overview.md:45-47`](../01-project-overview.md#L45-L47)
- **Dashboard auth guard** — Web guard uses `DashboardUser` [VERIFIED_FROM_SOURCE] — [`01-project-overview.md:56-59`](../01-project-overview.md#L56-L59)
- **Shared DB** — `palvarnan_db` shared with Project B [VERIFIED_FROM_SOURCE] — [`VERIFIED_FACTS.md:18,106-109`](../audit/VERIFIED_FACTS.md#L18,L106-L109)

## Status codes / states

### Product order statuses (local)

Common local statuses appear as string literals (e.g. in HomeController counts/filters): `pending`, `processing`, `packed`, `dispatched`, `shipped`, `out_for_delivery`, `delivered`, `cancelled`, `return` etc [VERIFIED_FROM_SOURCE] — [`HomeController.php:78-88`](../../palVarnanDashboard/app/Http/Controllers/HomeController.php#L78-L88), [`01-project-overview.md:32-35`](../01-project-overview.md#L32-L35). (See [12-business-logic.md](../12-business-logic.md) for exact workflow.)

### Shiprocket states

`SyncShiprocketStatus` maps Shiprocket `current_status_id` to local statuses [VERIFIED_FROM_SOURCE] — [`SyncShiprocketStatus.php:66-89`](../../palVarnanDashboard/app/Console/Commands/SyncShiprocketStatus.php#L66-L89):

| SR status_id | Meaning (SR) | Local status |
|--------------|--------------|--------------|
| 17,18,19     | Picked Up / Shipped / In Transit | `shipped` |
| 20           | Out for Delivery | `out_for_delivery` |
| 21           | Delivered | `delivered` |
| 22,24,25     | Return Initiated / RTO Initiated / RTO Delivered | `return` |

Also updates `product_slots.delivery_status` via `getStatusNum()` [VERIFIED_FROM_SOURCE] — [`SyncShiprocketStatus.php:99-102`](../../palVarnanDashboard/app/Console/Commands/SyncShiprocketStatus.php#L99-L102).

## RBAC

- **Spatie Permission v6**: roles/permissions stored in `roles`, `permissions`, `model_has_roles`, `model_has_permissions`, `role_has_permissions` (standard) [INFERRED] — migration `2026_04_06_124404_create_permission_tables.php` [VERIFIED_FROM_SOURCE] — [`01-project-overview.md:48-50`](../01-project-overview.md#L48-L50)
- **Enforcement:** **not at route layer** (no `role:`, `permission:`, `can:` middleware) [VERIFIED_FROM_SOURCE] — [`VERIFIED_FACTS.md:38-40`](../audit/VERIFIED_FACTS.md#L38-L40)
- **UI:** `RoleController`, `DashboardUserController` for management [VERIFIED_FROM_SOURCE] — [`01-project-overview.md:48-50`](../01-project-overview.md#L48-L50)
- **Privilege gap:** password reset routes allow resetting another user’s password (lack ownership/role checks) [VERIFIED_FROM_SOURCE] — [`VERIFIED_FACTS.md:58-60`](../audit/VERIFIED_FACTS.md#L58-L60)

## Scheduler

- **Command:** `shiprocket:sync` (`App\Console\Commands\SyncShiprocketStatus`) [VERIFIED_FROM_SOURCE] — [`SyncShiprocketStatus.php:19`](../../palVarnanDashboard/app/Console/Commands/SyncShiprocketStatus.php#L19)
- **Schedule:** every 30 minutes in `Kernel::schedule()` [VERIFIED_FROM_SOURCE] — [`Kernel.php:18`](../../palVarnanDashboard/app/Console/Kernel.php#L18)
- **Purpose:** sync tracking for orders with `shiprocket_order_id` and non-terminal statuses (`processing`, `ready_for_pickup`, `shipped`, `out_for_delivery`) [VERIFIED_FROM_SOURCE] — [`SyncShiprocketStatus.php:48-51`](../../palVarnanDashboard/app/Console/Commands/SyncShiprocketStatus.php#L48-L51)

## Shiprocket integration styles

- **Service (safe):** `ShiprocketService.php` uses `config('services.shiprocket.*')` + HTTP client + token caching [VERIFIED_FROM_SOURCE] — [`ShiprocketService.php:9-50`](../../palVarnanDashboard/app/Services/ShiprocketService.php#L9-L50)
- **Inline cURL (unsafe):** hardcoded credentials in controllers [VERIFIED_FROM_SOURCE] — [README reconciliations](./README.md#reconciliation-headlines-vs-existing-docs)

## Misc counts

- Routes web: 330; api: 2 [VERIFIED_FROM_SOURCE] — [`VERIFIED_FACTS.md:28-29`](../audit/VERIFIED_FACTS.md#L28-L29)
- Controllers: 76 (6 Auth); Models: 63; Migrations: 60; Tables (live): 93 [VERIFIED_FROM_SOURCE] — [`VERIFIED_FACTS.md:24-30`](../audit/VERIFIED_FACTS.md#L24-L30)
- Services: 1; Console commands: 1; Middleware: 9 [VERIFIED_FROM_SOURCE] — [`VERIFIED_FACTS.md:31-33`](../audit/VERIFIED_FACTS.md#L31-L33)