# DOCUMENTATION_CHANGELOG — Project B (palVarnan)

> Canonical reference library created as part of the reverse-engineering audit.
> This file is authoritative for what was created/updated in the `docs/` tree during this audit.

## Audit metadata

- Audit date: 2026-10-17
- Scope: `C:\xampp\htdocs\pauls\palVarnan` (Laravel 12 storefront)
- Working directory: `C:\xampp\htdocs\pauls`
- Git branch: (current as recorded)
- Evidence labels used: VERIFIED_FROM_SOURCE, VERIFIED_FROM_LIVE_SCHEMA, INFERRED, UNVERIFIED, NOT_AVAILABLE, CONFLICTING_EVIDENCE

## What changed (documentation only)

### Created in `docs/reference/` (canonical library)

The following canonical docs were created (files present at time of writing this changelog):

- `docs/reference/README.md` — canonical index and reconciliation headlines
- `docs/reference/ARCHITECTURE.md` — request lifecycle, layering, services, constraints
- `docs/reference/BUSINESS_LOGIC.md` — pricing, checkout flow, stock, order lifecycle
- `docs/reference/CHANGE_IMPACT_GUIDE.md` — change-surface map
- `docs/reference/CONTROLLER_REFERENCE.md` — all 10 controllers + methods
- `docs/reference/DATABASE_RELATIONSHIPS.md` — ER + model relations + defects
- `docs/reference/DATABASE_SCHEMA.md` — shared schema (93 tables), domain migrations (0)
- `docs/reference/DATABASE_TABLE_REFERENCE.md` — column-level reference for touched tables
- `docs/reference/DEPLOYMENT_AND_CONFIGURATION.md` — env keys (names only), build, config-cache notes
- `docs/reference/DEVELOPER_SETUP.md` — install/run, DB import from SQL dump
- `docs/reference/FEATURE_CATALOG.md` — feature matrix with status
- `docs/reference/FRONTEND_REFERENCE.md` — Blade views (23), theme, AJAX, Vite/Tailwind
- `docs/reference/GLOSSARY.md` — domain + evidence vocabulary
- `docs/reference/INTEGRATION_MAP.md` — Razorpay, Shiprocket, shared DB coupling
- `docs/reference/KNOWN_ISSUES_AND_TECHNICAL_DEBT.md` — consolidated register
- `docs/reference/MIDDLEWARE_AND_AUTHORIZATION.md` — auth usage, ad-hoc ownership, no policies
- `docs/reference/MIGRATION_AND_SQL_AUDIT.md` — 3 stock migrations vs 93 tables, schema drift
- `docs/reference/PERFORMANCE_AND_RELIABILITY.md` — N+1, sync HTTP, queue=sync
- `docs/reference/PROJECT_OVERVIEW.md` — purpose, stack, integrations, flows
- `docs/reference/PROJECT_STRUCTURE.md` — directory map (no `app/Http/Middleware`)
- `docs/reference/ROUTES_AND_API_REFERENCE.md` — all 45 routes (auth boundary ~10)
- `docs/reference/TESTING_GUIDE.md` — current scaffold (0 domain tests) + plan
- `docs/reference/SOURCE_COVERAGE_AND_VERIFICATION.md` — reviewed scope + claim reconciliation

### Pending in `docs/reference/` (background writers)

The following canonical files are expected to be completed by background subagents (still in progress):

- `docs/reference/MODEL_REFERENCE.md` — 25 models (fillable/guarded/casts/relations; empty models Unit/HomePromotion)
- `docs/reference/SECURITY_AUDIT.md` — source-verified security register (SEC-B-01..SEC-B-nn; notes wishlist IDOR fixed; checkout-does-write corrected)

### Created in `docs/audit/`

- `docs/audit/PROGRESS_TRACKER.md` — stage tracking
- `docs/audit/VERIFIED_FACTS.md` — verified ground truth used by writers

### Updated in `docs/`

- `docs/README.md` — added canonical reference library section linking to `reference/README.md` and reconciliation notes

## Reconciliation notes (highlights)

Key claim reconciliations (source vs pre-existing docs):

- **Checkout writes orders**: `CheckoutController::placeOrder()` writes across product_orders, order_user_addresses, transaction_logs, tracking, order_items, product_slots, reduces stock, clears cart; then calls Shiprocket. Previously claimed "checkout writes no order" → **CONTRADICTED_BY_SOURCE / CORRECTED**.
- **Razorpay in composer.lock**: present (`composer.lock` ~3298). Previously claimed absent → **CORRECTED**.
- **Route/model counts**: routes 45 (was 38); models 25 (was 24) → **CORRECTED**.
- **env() usage**: `SearchController.php:23` uses `env('MAIN_URL')` → **CORRECTED**.
- **Password reset no token**: `/reset-password-direct` requires only email+password → **CRITICAL, still true**.
- **Client-controlled amounts**: `shipping_charge`/`coupon_discount` from request → **STILL PRESENT**.
- **Cart IDOR**: `CartController::update()` and `remove($id)` lack ownership checks → **STILL PRESENT**.
- **Wishlist IDOR**: `WishlistController::remove()` now scopes by `user_id` → **FIXED**.
- **Pickup never scheduled**: `generatePickup()` has no callers (method name differs from doc text) → **STILL TRUE** (behavioral claim holds).
- **No domain migrations**: 3 stock only; 93 tables unversioned → **STILL TRUE**.

## Verification

- All new docs are documentation-only (no source/config/migrations/DB changes).
- Secrets never reproduced; only env var names cited.
- Evidence tags applied throughout.
- Links to existing numbered docs and sibling reference docs.

## Status

- Canonical reference library largely complete (23 of 26 expected files present). 
- Background writers are completing `MODEL_REFERENCE.md` and `SECURITY_AUDIT.md`.
- Cross-project integration confirmed (shared `palvarnan_db`).
- No application code was modified.
