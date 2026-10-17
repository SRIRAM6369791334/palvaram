# Documentation Audit — Progress Tracker (Project A: palVarnanDashboard)

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
| H | Author documentation library (`docs/reference/`) | IN_PROGRESS |
| I | Final verification | NOT_STARTED |

## Canonical reference library (`docs/reference/`)

Created so far: README, PROJECT_OVERVIEW, PROJECT_STRUCTURE, DEVELOPER_SETUP, ARCHITECTURE,
GLOSSARY, FEATURE_CATALOG, BUSINESS_LOGIC, DATABASE_SCHEMA, DATABASE_TABLE_REFERENCE,
DATABASE_RELATIONSHIPS, MIGRATION_AND_SQL_AUDIT, DOCUMENTATION_CHANGELOG.

Background writers are completing: MODEL_REFERENCE, CONTROLLER_REFERENCE,
MIDDLEWARE_AND_AUTHORIZATION, ROUTES_AND_API_REFERENCE, FRONTEND_REFERENCE, INTEGRATION_MAP,
SECURITY_AUDIT, PERFORMANCE_AND_RELIABILITY, TESTING_GUIDE, DEPLOYMENT_AND_CONFIGURATION,
KNOWN_ISSUES_AND_TECHNICAL_DEBT, CHANGE_IMPACT_GUIDE, SOURCE_COVERAGE_AND_VERIFICATION.

## Notes / checkpoints

- Project root: `C:\xampp\htdocs\pauls\palVarnanDashboard`
- Existing docs: 20 numbered docs + README (README references 21-30 which do not exist).
- Framework: Laravel `^8.65`, PHP `^7.3|^8.0`; spatie/permission 6, sanctum 2.11, dompdf, excel,
  telescope, debugbar.
- Counts (source): 76 controllers (incl. 6 Auth scaffold), 63 models, 60 migration files,
  330 web routes, 2 API routes, 93 tables in dump.
- Key findings: missing `SubCategoryController::validateSubCategoryName`; resource `users` missing
  `store()`; spatie roles not route-enforced; password-change routes allow changing others'
  passwords; unauthenticated `GET /api/test` writes and `dd()`s; hardcoded Shiprocket creds at
  ProductController:869/1010, ProductOrdersController:110, ProductSlotController:131/202.
- Scheduler `shiprocket:sync` every 30 min; shared MySQL DB `palvarnan_db` with Project B.
- `AppServiceProvider.php` and `TrustProxies.php` changed ~2026-10-13.

## Verification log

- No source/config/migration/DB changes made.
- Secret-scan of `docs/`: no credential values present (only env var names / file:line references).
