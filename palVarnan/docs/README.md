# PalVarnan — Project Documentation

A complete, read-only technical audit of the `palVarnan` storefront at `C:\xampp\htdocs\pauls\palVarnan`, plus the sibling admin application at `C:\xampp\htdocs\pauls\palVarnanDashboard`.

**Audit date:** 2026-10-01 (original) · **Reconciliation:** 2026-10-17 · **Documents:** 31 existing + 24+ canonical reference docs · **Method:** static source analysis of code, configuration, schema dump, and application logs.

> **No application source, dependency, configuration, or database was modified.** Every file in this directory is documentation.

## Canonical reference library (new, source-verified)

A reconciled, source-verified canonical library is available under [`docs/reference/`](./reference/).

- Start here: [`docs/reference/README.md`](./reference/README.md)
- Changelog: [`docs/reference/DOCUMENTATION_CHANGELOG.md`](./reference/DOCUMENTATION_CHANGELOG.md)
- Source coverage & verification: [`docs/reference/SOURCE_COVERAGE_AND_VERIFICATION.md`](./reference/SOURCE_COVERAGE_AND_VERIFICATION.md)
- Verified facts used: [`docs/audit/VERIFIED_FACTS.md`](./audit/VERIFIED_FACTS.md)
- Reconciliation highlights (current vs 2026-10-01 doc claims): see [`docs/reference/README.md#reconciliation-highlights`](./reference/README.md#reconciliation-highlights) and [`docs/reference/SOURCE_COVERAGE_AND_VERIFICATION.md#claim-reconciliation`](./reference/SOURCE_COVERAGE_AND_VERIFICATION.md#claim-reconciliation).

Key corrections (VERIFIED_FROM_SOURCE): checkout **does** write orders (not broken as previously claimed); Razorpay **is** present in `composer.lock`; route count is **45** (was 38), models **25** (was 24); `SearchController.php:23` uses `env('MAIN_URL')`; wishlist remove IDOR is **fixed** (scopes by user_id); cart update/remove still lack ownership; password reset remains **critical** (no token); `generatePickup()` has no callers (pickup never scheduled).

## 1. Headline findings (reconciled)

| # | Finding | Severity |
|---|---|---|
| 1 | **Password reset requires no token** — `/reset-password-direct` accepts email+password with no token. Account takeover. [`VERIFIED_FROM_SOURCE`](./reference/SECURITY_AUDIT.md) | **Critical** |
| 2 | **Cart IDORs remain** — `CartController::update()` and `remove($id)` lack ownership checks (GET remove). Wishlist remove is fixed. [`VERIFIED_FROM_SOURCE`](./reference/SECURITY_AUDIT.md) | **Critical** |
| 3 | **Client-trusted amounts** — `shipping_charge` and `coupon_discount` come from the request in `CheckoutController::placeOrder()`. [`VERIFIED_FROM_SOURCE`](./reference/BUSINESS_LOGIC.md) | **Critical** |
| 4 | **Zero domain migrations** — only 3 stock migrations; 93 tables unversioned. Schema owned by dashboard app. [`VERIFIED_FROM_SOURCE`](./reference/MIGRATION_AND_SQL_AUDIT.md) | **High** |
| 5 | **Pickup never scheduled** — `ShiprocketService::generatePickup()` has no callers. [`VERIFIED_FROM_SOURCE`](./reference/INTEGRATION_MAP.md) | **High** |
| 6 | **No rate limiting/policies/form requests** — ad-hoc authz only. [`VERIFIED_FROM_SOURCE`](./reference/MIDDLEWARE_AND_AUTHORIZATION.md) | **High** |

## 2. Project shape

| | |
|---|---|
| **Stack** | Laravel 12, PHP 8.2, Blade, MySQL, jQuery, Vite 7 + Tailwind 4 (configured; theme is vendored) |
| **Integrations** | Razorpay (payments), Shiprocket (fulfilment) |
| **Tables** | 93 (SQL dump 2026-07-31) |
| **Controllers** | 10 (CheckoutController 701 lines) |
| **Models** | 25 |
| **Routes** | 45 (web.php); only ~10 behind `auth` |
| **Domain migrations** | 0 (3 stock only) |
| **Tests** | 3 scaffold files; 0 application tests |

## 3. Index (existing numbered docs)

The existing 31 numbered docs remain authoritative in detail; the new [`docs/reference/`](./reference/) canonical library reconciles them against current source. Use reference/ for a verified, up-to-date view.

[Existing numbered docs index remains as originally structured; see below for reference links.]

## Reference library quick links

- [`reference/README.md`](./reference/README.md)
- [`reference/PROJECT_OVERVIEW.md`](./reference/PROJECT_OVERVIEW.md)
- [`reference/ARCHITECTURE.md`](./reference/ARCHITECTURE.md)
- [`reference/ROUTES_AND_API_REFERENCE.md`](./reference/ROUTES_AND_API_REFERENCE.md)
- [`reference/CONTROLLER_REFERENCE.md`](./reference/CONTROLLER_REFERENCE.md)
- [`reference/MODEL_REFERENCE.md`](./reference/MODEL_REFERENCE.md) (pending completion)
- [`reference/DATABASE_SCHEMA.md`](./reference/DATABASE_SCHEMA.md)
- [`reference/DATABASE_TABLE_REFERENCE.md`](./reference/DATABASE_TABLE_REFERENCE.md)
- [`reference/SECURITY_AUDIT.md`](./reference/SECURITY_AUDIT.md) (pending completion)
- [`reference/SOURCE_COVERAGE_AND_VERIFICATION.md`](./reference/SOURCE_COVERAGE_AND_VERIFICATION.md)

## Evidence vocabulary

| Term | Definition |
|---|---|
| **VERIFIED_FROM_SOURCE** | Read directly from source with file:line citation |
| **VERIFIED_FROM_LIVE_SCHEMA** | Confirmed against SQL dump/schema artifacts |
| **INFERRED** | Reasonable from structure but not directly observable |
| **UNVERIFIED** | Cannot be determined from available artifacts |
| **NOT_AVAILABLE** | Does not exist in codebase |
| **CONFLICTING_EVIDENCE** | Existing doc conflicts with source (corrected) |

## Audit limitations

No live database, web server, or browser was available. Verified against source, `composer.lock`, autoload maps, `.env.example`, `config/`, Blade views, `database/migrations/`, `storage/logs/laravel.log`, SQL dump `palvarnan_db (2).sql`, dashboard sources, git state. Some log entries predate current code. See [`reference/SOURCE_COVERAGE_AND_VERIFICATION.md`](./reference/SOURCE_COVERAGE_AND_VERIFICATION.md).
