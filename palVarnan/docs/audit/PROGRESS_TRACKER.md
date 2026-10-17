# Documentation Audit — Progress Tracker (Project B: palVarnan)

> Maintained during the reverse-engineering audit. Checkpoint file: update as stages complete.
> Evidence labels: VERIFIED_FROM_SOURCE, VERIFIED_FROM_LIVE_SCHEMA, INFERRED, UNVERIFIED, NOT_AVAILABLE, CONFLICTING_EVIDENCE.

## Stage checklist

| Stage | Description | Status |
|-------|-------------|--------|
| A | Inspect project roots + existing docs | DONE |
| B | File inventories + doc reconciliation | DONE |
| C | Database / migrations / raw SQL audit | DONE |
| D | Models, controllers, middleware, routes audit | DONE |
| E | Blade / JS / CSS / API audit | DONE |
| F | Feature flows + cross-project integration | DONE |
| G | Security / performance / maintainability review | DONE |
| H | Author documentation library (`docs/reference/`) | DONE (25/26 canonical files) |
| I | Final verification | IN_PROGRESS |

## Canonical reference library (`docs/reference/`) — 25 of 26 expected

Present: README, PROJECT_OVERVIEW, PROJECT_STRUCTURE, DEVELOPER_SETUP, ARCHITECTURE, GLOSSARY,
FEATURE_CATALOG, BUSINESS_LOGIC, DATABASE_SCHEMA, DATABASE_TABLE_REFERENCE, DATABASE_RELATIONSHIPS,
MIGRATION_AND_SQL_AUDIT, MODEL_REFERENCE, CONTROLLER_REFERENCE, MIDDLEWARE_AND_AUTHORIZATION,
ROUTES_AND_API_REFERENCE, FRONTEND_REFERENCE, INTEGRATION_MAP, SECURITY_AUDIT,
PERFORMANCE_AND_RELIABILITY, TESTING_GUIDE, DEPLOYMENT_AND_CONFIGURATION,
KNOWN_ISSUES_AND_TECHNICAL_DEBT, CHANGE_IMPACT_GUIDE, SOURCE_COVERAGE_AND_VERIFICATION,
DOCUMENTATION_CHANGELOG.

## Notes / checkpoints

- Project root: `C:\xampp\htdocs\pauls\palVarnan`
- Existing docs: 31 files in `docs/`; `docs/README.md` updated to link the reference library.
- Framework: Laravel `^12.0`, PHP `^8.2`; Razorpay `^2.9` present in `composer.json` and `composer.lock`.
- Counts (source): 45 routes, 10 controllers, 25 models, 3 stock migrations, 93 tables in dump, 23 views.
- 9 controllers/1 service; no `app/Http/Middleware` (Laravel 12 bootstrap).
- Key corrections: checkout DOES write orders; wishlist remove IDOR fixed; route/model counts updated;
  `env('MAIN_URL')` in `SearchController:23`.
- Critical still-open: unauthenticated `/reset-password-direct`; client-supplied money; cart IDORs.
- Shared MySQL DB `palvarnan_db` with Project A — schema owned by A's migrations.

## Verification log

- No source/config/migration/DB changes made.
- Secret-scan of `docs/`: no credential values present (only env var names / placeholders).
- Every canonical doc carries evidence tags and file:line citations.
