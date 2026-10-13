# PalVarnan — Project Documentation

A complete, read-only technical audit of the `palVarnan` storefront at `C:\xampp\htdocs\pauls\palVarnan`, plus the sibling admin application at `C:\xampp\htdocs\pauls\palVarnanDashboard`.

**Audit date:** 2026-10-01 · **Documents:** 31 · **Method:** static source analysis of code, configuration, schema dump, and application logs.

> **No application source, dependency, configuration, or database was modified.** Every file in this directory is documentation.

---

## 1. Headline findings

| # | Finding | Severity |
|---|---|---|
| 1 | **Password reset requires no token** — any request with a known email resets that account's password. Account takeover. | **Critical** |
| 2 | **Checkout writes no order.** Two columns (`billing_state`, `product_image`) exist in no migration. The transaction rolls back **after** payment is captured. | **Critical** |
| 3 | **Razorpay is absent from `composer.lock`.** The SDK sits in `vendor/` and is loaded by a manual `require_once`. Any `composer update` wipes it. | **Critical** |
| 4 | **Live credentials in web-reachable scripts** — `test_apis.php`, `test_services.php`, with TLS verification disabled. | **Critical** |
| 5 | **The client controls the amount charged** — `total`, `shipping_charge`, and `coupon_discount` come from the request. | **Critical** |
| 6 | **Three IDORs** — cart update, cart remove, wishlist remove have no ownership check. Cart remove is a `GET`. | **Critical** |
| 7 | **Zero domain migrations.** 3 Laravel defaults only; 93 tables are unversioned. Schema is owned by the dashboard app. | **High** |
| 8 | **Pickup is never scheduled** — `createPickup()` has no caller. Orders are created and labelled but never collected. | **High** |
| 9 | **No rate limiting** on any of 38 routes; no policies, no middleware, no Form Requests. | **High** |
| 10 | **`APP_DEBUG=true` with a `root` DB user and an empty password.** | **High** |

**A shopper can browse, search, and build a cart. A shopper cannot complete a purchase.**

---

## 2. Project shape

| | |
|---|---|
| **Stack** | Laravel 12, PHP 8.2, server-rendered Blade, MySQL, jQuery |
| **Integrations** | Razorpay (payments), Shiprocket (fulfilment) |
| **Frontend** | Vendored MarketPro theme; 32 inline `fetch()` calls; Vite/Tailwind configured but unused |
| **Tables** | 93, 34 foreign keys |
| **Controllers** | 10 — `CheckoutController.php` is 701 lines (25% of all app code) |
| **Models** | 24 |
| **Routes** | 38; only 10 behind `auth` |
| **Tests** | 2 placeholder files; 0 application tests |
| **Domain migrations** | 0 |
| **Git** | one commit; the entire checkout feature is untracked |

---

## 3. Index

### Foundations

| # | Document | Contents |
|---|---|---|
| 01 | [Project Inventory](01-project-inventory.md) | Framework version, file counts, sizes, git state |
| 02 | [Architecture](02-architecture.md) | Request lifecycle, layering, runtime topology |
| 03 | [Module Map](03-module-map.md) | Module responsibilities and dependencies |
| 04 | [Feature Inventory](04-feature-inventory.md) | F-numbered features with VERIFIED / INCOMPLETE / BROKEN status |
| 05 | [User Flows](05-user-flows.md) | UF-numbered end-to-end flows |

### The application

| # | Document | Contents |
|---|---|---|
| 06 | [API and Routes](06-api-and-routes.md) | Routing architecture, middleware |
| 07 | [Database Schema](07-database-schema.md) | 93 tables, column-level reference |
| 08 | [Business Logic](08-business-logic.md) | Pricing, cart, checkout, payment, fulfilment |
| 09 | [Dependencies](09-dependencies.md) | Composer, npm, asset provenance, lock-file defects |
| 10 | [File Structure](10-file-structure.md) | Every file and its purpose |
| 11 | [Models and Relationships](11-models-and-relationships.md) | 24 models, relations, `$fillable` defects |
| 12 | [Services and Integrations](12-services-and-integrations.md) | Razorpay and Shiprocket method-by-method |
| 13 | [Security Audit](13-security-audit.md) | SEC-01…25, severity-ranked |
| 14 | [Error Handling](14-error-handling.md) | Logging, exceptions, error paths, log evidence |
| 15 | [Frontend and Assets](15-frontend-and-assets.md) | Blade, JS, CSS, images, dead links |

### Quality

| # | Document | Contents |
|---|---|---|
| 16 | [Performance](16-performance.md) | Queries, N+1, payload sizes, index costs |
| 17 | [DevOps and Deployment](17-devops-deployment.md) | Deployability, CI, backups, monitoring |
| 18 | [Configuration and Environment](18-configuration-and-env.md) | Every effective config value; `.env` vs `.env.example` |
| 19 | [Incomplete Work](19-incomplete-work.md) | BW / FW / IW numbered gaps with effort |
| 20 | [Known Issues](20-known-issues.md) | BUG-01…90, severity-ranked, with root causes |
| 21 | [Technical Debt](21-technical-debt.md) | TD-01…30, ranked by future-change cost |
| 22 | [Database Integrity](22-database-integrity.md) | DB-01…22 — missing migrations, FKs, indexes |
| 23 | [External Services](23-external-services.md) | IR-01…10 — integration risk and correct clients |
| 24 | [Testing Strategy](24-testing-strategy.md) | 30 tests in 5 tiers, CI workflow |
| 25 | [Forms and Validation](25-forms-and-validation.md) | V-01…14 — every form, rule-by-rule |
| 26 | [Data Model Diagram](26-data-model-diagram.md) | Mermaid ER diagrams with defects marked inline |
| 27 | [API Reference](27-api-reference.md) | All 38 routes with request and response shapes |
| 28 | [Glossary and Terms](28-glossary-and-terms.md) | Vocabulary, acronyms, identifier scheme |
| 29 | [Remediation Plan](29-remediation-plan.md) | 11 phases, 25.5 days, sequenced |
| 30 | [Project Status](30-project-status.md) | Verdict, metrics, risk register, limitations |

---

## 4. Issue identifier scheme

Every finding carries a stable prefix so a defect can be cited unambiguously.

| Prefix | Meaning | Document | Count |
|---|---|---|---|
| `F-nn` | Features, with status | 04 | 40+ |
| `UF-nn` | User flows | 05 | 16 |
| `SEC-nn` | Security findings | 13 | 25 |
| `BW-nn` | Backend work not finished | 19 | 50 |
| `FW-nn` | Frontend work not finished | 19 | 28 |
| `IW-nn` | Infrastructure gaps | 19 | 18 |
| `BUG-nn` | Known issues, severity S1–S4 | 20 | 90 |
| `TD-nn` | Technical debt items | 21 | 30 |
| `DB-nn` | Database integrity defects | 22 | 22 |
| `IR-nn` | Integration risks | 23 | 10 |
| `V-nn` | Validation defects | 25 | 14 |

### Evidence vocabulary

| Term | Definition |
|---|---|
| **VERIFIED** | Read from source, or confirmed in `storage/logs/laravel.log`, with file and line reference |
| **INCOMPLETE** | Implemented but non-functional, or working only in part of the flow |
| **BROKEN** | Confirmed non-functional, or would fail on a clean deploy |
| **UNVERIFIED** | Not determinable from available artefacts — stated rather than guessed |

---

## 5. Severity scale

| Level | Meaning |
|---|---|
| **S1 Critical** | Money loss, data loss, or account compromise |
| **S2 High** | Exploitable, or blocks revenue |
| **S3 Medium** | Functional defect, or notable maintainability cost |
| **S4 Low** | Hygiene, dead code, cosmetic |

**Priority:** P0 blocks revenue or leaks money · P1 correctness, security, deployability · P2 performance, maintainability · P3 cosmetic.

---

## 6. Issue distribution

### By severity (90 BUG items)

| Severity | Count |
|---|---|
| S1 Critical | 8 |
| S2 High | 16 |
| S3 Medium | 59 |
| S4 Low | 7 |

### By area

| Area | Count |
|---|---|
| Checkout / orders | 15 |
| Frontend / template | 14 |
| Auth / session | 11 |
| Cart | 9 |
| Integrations | 9 |
| DevOps | 9 |
| Data model | 8 |
| Secrets / config | 7 |
| Search | 5 |
| Performance | 3 |

### Root causes

90 issues reduce to 9 root causes. **Fixing the first three resolves roughly half the register.**

| # | Root cause |
|---|---|
| RC-1 | Schema changes applied manually, never migrated |
| RC-2 | No middleware layer — authorization decided ad hoc in controllers |
| RC-3 | Business logic duplicated instead of centralised |
| RC-4 | Trusting the client for anything |
| RC-5 | The template was never pruned |
| RC-6 | No tests, so regressions were invisible |
| RC-7 | Dependencies unmanaged |
| RC-8 | Integration errors swallowed or leaked |
| RC-9 | Secrets handled carelessly |

---

## 7. Recommended sequence

Full detail in [29-remediation-plan.md](29-remediation-plan.md).

| Phase | Scope | Effort |
|---|---|---|
| **0** | Make it reproducible — commit the untracked checkout feature | 0.5 d |
| **1** | Stop the bleeding — 3 migrations, `composer require razorpay`, rotate credentials, `APP_DEBUG=false` | 2 d |
| **2** | Token-based password reset via `Password::reset` | 1 d |
| **3** | Ownership and authorization — `auth` middleware, scoped writes, policies, throttling | 1.5 d |
| **4** | Money path — server-side totals, amount verification, idempotency, stock locks | 2 d |
| **5** | Integration reliability — timeouts, retries, fail loudly, queue fulfilment, schedule pickup | 2 d |
| **6** | Deployability — `.env.example`, config cache safety, CI, deploy docs | 2 d |
| **7** | Checkout decomposition — extract services | 4 d |
| **8** | Test suite — 30 tests | 4.5 d |
| **9** | Frontend cleanup — dead links, demo content, duplicate jQuery, CSRF gaps | 3 d |
| **10** | Schema and structure — shared migration ownership, FKs, indexes | 3 d |
| | **Total** | **≈25.5 d** |

**Phases 0-2 — 3.5 days — close all 8 critical issues.**

---

## 8. Quick verification

After applying fixes, these should all pass:

```bash
composer install --no-interaction       # must succeed — proves Razorpay is declared
php artisan migrate --force             # must apply the 3 new migrations
php artisan test                        # 0 → 30 tests
grep -rn "env(" app/                   # expect 0 matches
grep -rn "wowtheme7\|shop.html" resources/views/   # expect 0 matches
```

And this reconciliation query must return **zero rows** — any row means money was captured with no order:

```sql
SELECT t.razorpay_payment_id FROM product_transactions t
  LEFT JOIN product_orders o ON o.id = t.order_id
 WHERE o.id IS NULL AND t.payment_status = 'Paid';
```

---

## 9. Audit limitations

No live database, web server, or browser was available. Verified against source, `composer.lock`, autoload maps, `.env`, `config/`, Blade views, `database/migrations/`, `storage/logs/laravel.log`, the SQL dump `palvarnan_db (2).sql`, dashboard migration and command sources, and git state.

**Not verified:** live schema state (manual `ALTER TABLE` changes could not be confirmed); whether config is currently cached; runtime rendering; third-party API behaviour; a live end-to-end payment; git history intent; the contents of `palVarnan1/`.

Some log entries predate the current code — notably `Razorpay\Api\Api` not-found, which the inline `require_once` now masks. **Current-path defects were re-confirmed by reading source.**

Full scope in [30-project-status.md](30-project-status.md) §13.

---

## 10. Security handling

`test_apis.php` and `test_services.php` contain live Razorpay and Shiprocket credentials, disable TLS verification, can create live API orders, and can drop tables.

**No secret values are reproduced anywhere in this documentation.** Rotate all credentials and delete both scripts as step 1c of the remediation plan.