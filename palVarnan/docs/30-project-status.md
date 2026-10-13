# 30 — Project Status

---

## 1. Verdict

| Question | Answer |
|---|---|
| Is the application installable? | **No** — `composer install` leaves Razorpay unusable (BUG-08) |
| Does the storefront render? | **Yes**, with ~700 lines of vendor template demo visible on the homepage |
| Can a shopper browse and search? | **Partly** — search returns wrong URLs and can never match a variant (BUG-24…26) |
| Can a shopper add to cart? | **Yes**, for the current UI (every caller sends `1`) |
| **Can a shopper pay and receive an order?** | **No** — checkout fails on two missing columns, after payment capture (BUG-04) |
| Is the schema reproducible? | **No** — 3 Laravel default migrations, 0 domain migrations |
| Is the money path safe? | **No** — the client controls the amount; the amount is never re-verified (BUG-05, BUG-22) |
| Is access control sound? | **No** — tokenless password reset; three IDORs (BUG-01, 06, 07, 12) |
| Is it deployable to production? | **No** — `APP_DEBUG=true`, root DB user, no CI, incomplete `.env.example` |
| Is it tested? | **No** — 2 placeholder files, 0 application tests |

**A shopper can browse, search, and build a cart. A shopper cannot complete a purchase.** Everything upstream of checkout is functional enough to demo; everything downstream is either broken or absent.

---

## 2. Feature status

### Working (VERIFIED)

| Area | Status |
|---|---|
| Homepage rendering | works |
| Product listing with category / subcategory / brand filters | works |
| Product detail | works, except soft-deleted variants (BUG-31) |
| Cart view, add, update, remove | works; IDOR on update/remove |
| Cart subtotal AJAX badge | works |
| Wishlist add / remove / check | works; writes `user_id = null` |
| Register, login, logout | works; logout is correctly implemented |
| Address create | works |
| Checkout page rendering | works behind `auth` |
| Shiprocket courier quote | works, no timeout |
| COD order path | **fails** on missing columns |
| Prepaid order path | **fails** on missing columns after capture |
| Contact form | works, honeypot only |

### Broken

| Area | Defect |
|---|---|
| Checkout / place-order | BUG-04 — payment captured, order not saved |
| Password reset | BUG-01 — no token verification |
| Search results URLs | BUG-25, BUG-26 — `/brand/`, `/category/` |
| Variant search | BUG-24 — `LIKE` on an `int` column |
| Search images under `config:cache` | BUG-27 — `env()` returns `null` |
| Razorpay after any dependency install | BUG-08 — not in `composer.lock` |
| `GET /product` | BUG-67 — renders with no data |
| `myaccount` for guests | BUG-09 — no `auth` |
| `profile.update` for guests | BUG-10 — null dereference |
| Country-selection error message | BUG-50 — flashed, never rendered |

### Absent

Reviews · compare · coupons · blog · multi-vendor · order tracking · refunds · returns · cancellation · webhooks · inventory reservation · queue workers · scheduled tasks · notifications · emails · multi-currency · search results page · 404/500 pages · CI · Docker · deployment scripts · monitoring · backups.

21 schema tables and ~700 lines of template markup correspond to features that do not exist in code. The purchased template was built for a **multi-vendor marketplace**; the application implements a **single-vendor storefront**.

---

## 3. Code metrics

| Metric | Value |
|---|---|
| PHP files in `app/` | 39 |
| Total `app/` size | ≈75 KB |
| Controllers | 10 |
| **Largest controller** | **`CheckoutController.php` — 701 lines, 28 275 B (25% of all app code)** |
| Eloquent models | 24 |
| Routes | 38 |
| Routes behind `auth` | 10 |
| Blade views | ≈60 |
| `fetch()` calls in Blade | 32 |
| Sites lacking the CSRF header | 3 |
| Tests | 2, both placeholders |
| Domain migrations | **0** |
| `composer run` scripts | 8, of which 4 fail |

---

## 4. Dependency health

| Package | Version | Assessment |
|---|---|---|
| `laravel/framework` | `^12.0` | current |
| `php` | `^8.2` | appropriate |
| `razorpay/razorpay` | **absent from the lock file** | **S1** — the SDK exists only in `vendor/` |
| `guzzlehttp/guzzle` | present | pulled transitively, not declared |
| `tailwindcss`, `vite`, `laravel-vite-plugin`, `axios` | declared | **unused** — no `@vite`, no `node_modules`, no `public/build` |
| Theme assets (MarketPro) | vendored | 472 files, 9.4 MB, incl. 50 Unsplash images |

`composer.lock` is stale — its `content-hash` does not match `composer.json`, and `composer run dev` fails at the Vite step. **A fresh deploy cannot process an order.**

---

## 5. Configuration risk

| Setting | Value | Risk |
|---|---|---|
| `APP_DEBUG` | `true` | **Critical** — stack traces against a live database |
| `DB_USERNAME` / `DB_PASSWORD` | `root` / empty | **Critical** |
| `MAIL_MAILER` | `log` | Password reset is non-functional |
| `APP_TIMEZONE` | `UTC` | Order dates off by up to one day (IST) |
| `QUEUE_CONNECTION` | `sync` | No async processing anywhere |
| `SESSION_SECURE_COOKIE` | unset | Cookie not marked secure |
| `SESSION_ENCRYPT` | `false` | Session payload unencrypted |
| `SHIPROCKET_BASE_URL` | set in `.env` | Never read — no config key |
| `.env.example` | 58 of 66 keys | **Payments and shipping cannot be configured** |
| `main_url` | `env()` outside config | Breaks under `config:cache` |

---

## 6. Security posture

**8 critical · 16 high** issues. The most severe:

| ID | Finding |
|---|---|
| SEC-01 | Password reset requires no token — **account takeover** |
| SEC-02 | Live Razorpay and Shiprocket credentials in web-reachable scripts |
| SEC-03 | TLS verification disabled on live payment calls |
| SEC-04 | Client controls the amount charged |
| SEC-05 | Cart update has no ownership check |
| SEC-06 | Cart remove has no ownership check **and is a `GET`** |
| SEC-07 | Razorpay missing from `composer.lock` |
| SEC-08 | Checkout writes nothing — payment captured, order lost |

Plus: no rate limiting on any of 38 routes, no policies, no `FormRequest` classes, no custom middleware, user enumeration on register and reset, error messages returned to clients, and `root` DB access with `APP_DEBUG=true`.

**No security review has been performed by anyone else.** This is an automated source audit.

---

## 7. Operational readiness

| Capability | Status |
|---|---|
| CI | **none** |
| Tests | 2 placeholders |
| Deployment automation | **none** |
| Migrations for the actual schema | **none** |
| Schema ownership | split across two applications |
| Backups | **none documented** |
| Monitoring / alerting | **none** |
| Error tracking | **none** |
| Money reconciliation | **none** |
| Log rotation | **none** — one unbounded `laravel.log` |
| Scheduled tasks | **none** |
| Queue workers | none configured (`sync`) |

**A payment captured but not persisted would not be detected by anything in this system.** That is the single most important operational gap.

---

## 8. Delivery history

| Signal | Observation |
|---|---|
| Git history | one commit |
| Untracked source | the entire checkout feature — payment service, 6 models, confirmation view |
| Duplicate app directory | `palVarnan1/` sits beside the working copy |
| Dashboard repo hygiene | committed `storage/framework/sessions` and compiled views |
| Schema snapshots | three different versions — the SQL dump, `composer.lock`, the dashboard migrations |
| Test scripts | `test_apis.php` / `test_services.php` in the web root with production credentials |

The untracked checkout feature explains much of the quality gap: **the money path was never committed, so it was never reviewed, diffed, or reverted.**

---

## 9. Risk register

| # | Risk | Likelihood | Impact | Mitigation |
|---|---|---|---|---|
| R1 | Customer pays, no order saved | **certain today** | lost revenue, chargeback | Phase 1a |
| R2 | Account takeover via password reset | high | data loss | Phase 2 |
| R3 | Razorpay vanishes on the next deploy | **certain** | total payment outage | Phase 1b |
| R4 | Credentials harvested from the test scripts | **certain if deployed** | full compromise | Phase 1c |
| R5 | One user edits another's cart | high | data integrity | Phase 3b |
| R6 | Under- or over-charge via client totals | high | financial | Phase 4a |
| R7 | Duplicate orders on double-submit | medium | financial | Phase 4c |
| R8 | Oversell | medium | financial | Phase 4d |
| R9 | Orders created that never ship | high | customer trust | Phase 5b |
| R10 | Nothing is ever collected — pickup never scheduled | **certain today** | total operational failure | Phase 5b |
| R11 | Silent money loss goes undetected | **certain** | unbounded | Phase 10 reconciliation |
| R12 | Stack traces expose schema to the public | **certain if deployed as-is** | information disclosure | Phase 1d |

**R1, R3, R4, R10, R11, and R12 are not conditional — they are current behaviour.**

---

## 10. Effort to a production-ready state

| Phase | Scope | Effort |
|---|---|---|
| 0 | Make it reproducible | 0.5 d |
| 1 | Stop the bleeding (checkout works, creds rotated) | 2 d |
| 2 | Token-based password reset | 1 d |
| 3 | Ownership and authorization | 1.5 d |
| 4 | Money path correctness | 2 d |
| 5 | Integration reliability | 2 d |
| 6 | Deployability | 2 d |
| 7 | Checkout decomposition | 4 d |
| 8 | Test suite | 4.5 d |
| 9 | Frontend cleanup | 3 d |
| 10 | Schema and structure | 3 d |
| **Total** | | **≈25.5 d** |

**Minimum to stop losing money: Phases 0-2 — 3.5 days.** That closes all 8 critical issues.

---

## 11. Recommendation

The architecture is sound for its scale — server-rendered Blade, a session cart, and a single-vendor checkout is the right shape for this business. The problems are not architectural; they are **omissions and unmanaged changes**:

- three columns were added to the database by hand and never migrated
- a payment SDK was dropped into `vendor/` instead of being declared
- the whole checkout feature was never committed
- Laravel's password reset was replaced with 25 lines that skip the token
- authorization was never implemented, only assumed

**Ship the 3.5-day P0 phase before anything else.** After that, Phases 3-6 (**8.5 days**) make the money path correct and the system deployable. Phases 7-10 are engineering quality: decomposition, tests, and schema ownership — worth doing, but they are not what is currently costing money.

---

## 12. Documentation set

| # | Document | Covers |
|---|---|---|
| 01 | [project-inventory](01-project-inventory.md) | artifacts, versions, sizes |
| 02 | [architecture](02-architecture.md) | request lifecycle, layering |
| 03 | [module-map](03-module-map.md) | module responsibilities |
| 04 | [feature-inventory](04-feature-inventory.md) | F-numbered features and status |
| 05 | [user-flows](05-user-flows.md) | UF-numbered flows |
| 06 | [api-and-routes](06-api-and-routes.md) | routing architecture |
| 07 | [database-schema](07-database-schema.md) | 93 tables, column-level |
| 08 | [business-logic](08-business-logic.md) | pricing, cart, checkout logic |
| 09 | [dependencies](09-dependencies.md) | Composer, npm, asset provenance |
| 10 | [file-structure](10-file-structure.md) | every file and its purpose |
| 11 | [models-and-relationships](11-models-and-relationships.md) | 24 models |
| 12 | [services-and-integrations](12-services-and-integrations.md) | Razorpay, Shiprocket methods |
| 13 | [security-audit](13-security-audit.md) | SEC-01…25 |
| 14 | [error-handling](14-error-handling.md) | logging, exceptions, error paths |
| 15 | [frontend-and-assets](15-frontend-and-assets.md) | Blade, JS, CSS, images |
| 16 | [performance](16-performance.md) | queries, N+1, payload sizes |
| 17 | [devops-deployment](17-devops-deployment.md) | deployability, CI, backups |
| 18 | [configuration-and-env](18-configuration-and-env.md) | every config key |
| 19 | [incomplete-work](19-incomplete-work.md) | BW / FW / IW numbered gaps |
| 20 | [known-issues](20-known-issues.md) | BUG-01…90 |
| 21 | [technical-debt](21-technical-debt.md) | TD-01…30 |
| 22 | [database-integrity](22-database-integrity.md) | DB-01…22 |
| 23 | [external-services](23-external-services.md) | IR-01…10 |
| 24 | [testing-strategy](24-testing-strategy.md) | 30 tests, 5 tiers |
| 25 | [forms-and-validation](25-forms-and-validation.md) | V-01…14 |
| 26 | [data-model-diagram](26-data-model-diagram.md) | Mermaid ER diagrams |
| 27 | [api-reference](27-api-reference.md) | all 38 routes |
| 28 | [glossary-and-terms](28-glossary-and-terms.md) | vocabulary and ID scheme |
| 29 | [remediation-plan](29-remediation-plan.md) | 11 phases, 25.5 days |
| 30 | [project-status](30-project-status.md) | this document |

---

## 13. Audit scope and limitations

**Verified against:** source code, `routes/web.php`, `composer.json` / `composer.lock`, `vendor/composer/` autoload maps, `.env` and `.env.example`, all `config/` files, all Blade views, `database/migrations/`, `storage/logs/laravel.log`, `palvarnan_db (2).sql`, `palVarnanDashboard` migration and command sources, and git status/history.

**Not verified — no live database, web server, or browser was available:**

| Limitation | Consequence |
|---|---|
| Live schema state | manual `ALTER TABLE` changes could not be confirmed; three columns are inferred from log behaviour |
| Whether config is currently cached | `env()` behaviour under `config:cache` is predicted, not observed |
| Runtime rendering | findings come from source reading and log evidence, not from executing pages |
| Third-party API behaviour | Razorpay and Shiprocket responses were not exercised |
| Payment flow end-to-end | the failure is inferred from `laravel.log` SQL errors, which is strong but not a live observation |
| Git history intent | one commit; authorship and sequencing are unknown |
| `palVarnan1/` | contents not audited |

**Some log entries predate the current code** — notably the `Razorpay\Api\Api` not-found error, which the inline `require_once` now masks, and view-not-found errors that predate the current templates. **Current-path defects were re-confirmed by reading source.**

**No application source, dependency, configuration, or schema was modified during this audit.** All 31 documents are in `docs/`.