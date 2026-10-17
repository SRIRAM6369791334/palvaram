# Reference Library — Project B (`palVarnan`)

Canonical, source-verified reference documentation for the `palVarnan` Laravel 12 web storefront.

> This folder is a **read-only documentation set**. No application source, config, migration, test, or
> database was modified to produce it. Every non-obvious statement is tagged with one evidence label.
> Secret values are never reproduced; environment variables are referred to by **name only**.

- Parent audit index: [`../README.md`](../README.md)
- Verified ground truth used by this library: [`../audit/VERIFIED_FACTS.md`](../audit/VERIFIED_FACTS.md)
- Audit progress: [`../audit/PROGRESS_TRACKER.md`](../audit/PROGRESS_TRACKER.md)

---

## 1. How to read this library

This library is **derived from the numbered audit** in `docs/` (see [§4](#4-relationship-to-the-numbered-audit-docs)),
then re-verified line-by-line against current source. Where the two disagree, the reference docs state
the conflict explicitly with a `CONFLICTING_EVIDENCE` tag and give the source truth.

### Evidence vocabulary (used throughout this folder)

| Tag | Meaning |
|---|---|
| `VERIFIED_FROM_SOURCE` | Read directly out of a PHP / Blade / config / JSON file, with a `file:line` citation. |
| `VERIFIED_FROM_LIVE_SCHEMA` | Confirmed against the SQL dump `palvarnan_db (2).sql` (the only schema-of-record; the app has no domain migrations). |
| `INFERRED` | A conclusion drawn from verified facts, not directly asserted by a single line of code. |
| `UNVERIFIED` | Stated because a question is open; not determinable from the artefacts available. |
| `NOT_AVAILABLE` | The artefact (e.g. a live DB, a browser) was not available during authoring. |
| `CONFLICTING_EVIDENCE` | Existing docs, or two source locations, disagree. The reference states the source truth. |

The numbered audit in `../` uses a **different, older vocabulary** (`VERIFIED` / `INCOMPLETE` / `BROKEN` /
`UNVERIFIED`, per `../README.md` §4). This reference library deliberately adopts the finer-grained audit
vocabulary above. `VERIFIED_FROM_SOURCE` here corresponds to the audit's `VERIFIED`.

---

## 2. Reconciliation headline (stale claims now corrected)

The pre-existing `../README.md` carries three headline claims that are **contradicted by current source**.
This library records the source truth:

| # | Stale claim in `../README.md` | Source truth | Evidence |
|---|---|---|---|
| 1 | "**Checkout writes no order** … the transaction rolls back **after** payment." (`../README.md:16`) | `CheckoutController::placeOrder()` **does** create an order and writes 6+ tables inside `DB::beginTransaction()` (with `DB::commit()` at `CheckoutController.php:421`), then creates a Shiprocket order. | `VERIFIED_FROM_SOURCE` — `CheckoutController.php:292-421` |
| 2 | "**Razorpay is absent from `composer.lock`**" (`../README.md:17`) | `razorpay/razorpay: ^2.9` is declared in **`composer.json:12`** and present in **`composer.lock`**. | `VERIFIED_FROM_SOURCE` — `composer.json`, `composer.lock` |
| 3 | "**38 routes**" / "**24 models**" (`../README.md:23,39`) | **45** `Route::` entries in `routes/web.php`; **25** models in `app/Models/`. | `VERIFIED_FROM_SOURCE` — `routes/web.php`, `app/Models/` |

Two more reconciliations carried through this library:

- The previously reported **wishlist-remove IDOR is FIXED** — `WishlistController::remove()` now scopes by
  `user_id` (`WishlistController.php:50-54`). The **cart update/remove IDORs remain** (`CartController.php:33-62`).
  `CONFLICTING_EVIDENCE` vs `../README.md:20` and `../13-security-audit.md`.
- The service method previously named `createPickup()` does not exist; the real name is
  `generatePickup()` and it still has **no caller** (`ShiprocketService.php:119`). The behavioural claim
  ("pickup is never scheduled") holds. `VERIFIED_FROM_SOURCE`.

> The `billing_state` / `product_image` columns cited as "in no migration" are indeed absent from the
> **app's** migrations — because the app has **zero domain migrations** (only 3 stock Laravel ones). The
> columns *are* written by the controller and declared `$fillable`. `VERIFIED_FROM_SOURCE`.

---

## 3. Index — the reference library (25 docs + parent index)

### Core orientation

| Doc | Purpose |
|---|---|
| [`README.md`](./README.md) | This index, the evidence vocabulary, and the reconciliation headline. |
| [`PROJECT_OVERVIEW.md`](./PROJECT_OVERVIEW.md) | What the storefront is, its stack, integrations, roles, and highest-level flow. |
| [`PROJECT_STRUCTURE.md`](./PROJECT_STRUCTURE.md) | Directory map of `app/`, `routes/`, `resources/views`, `config/`, `database/`, `public/`. |
| [`DEVELOPER_SETUP.md`](./DEVELOPER_SETUP.md) | Prerequisites, install steps, route/view mapping, and the SQL-dump import requirement. |
| [`ARCHITECTURE.md`](./ARCHITECTURE.md) | Request lifecycle, MVC layering, the lone service, and the absent layers. |
| [`GLOSSARY.md`](./GLOSSARY.md) | Domain terms, identifier schemes, and the evidence vocabulary. |
| [`FEATURE_CATALOG.md`](./FEATURE_CATALOG.md) | Feature-by-feature status table with controller/method + route backing. |
| [`BUSINESS_LOGIC.md`](./BUSINESS_LOGIC.md) | Pricing math, order lifecycle, stock deduction, multi-table writes, cart identity. |

### Application internals

| Doc | Purpose |
|---|---|
| [`ROUTES_AND_ENDPOINTS.md`](./ROUTES_AND_ENDPOINTS.md) | The 45 routes, middleware coverage, and request/response shapes. |
| [`CONTROLLERS.md`](./CONTROLLERS.md) | All 10 controllers and their methods. |
| [`MODELS_AND_RELATIONSHIPS.md`](./MODELS_AND_RELATIONSHIPS.md) | The 25 Eloquent models, fillables, and relations. |
| [`DATABASE_SCHEMA.md`](./DATABASE_SCHEMA.md) | Tables, keys, and the shared-database ownership story. |
| [`VIEWS_AND_TEMPLATES.md`](./VIEWS_AND_TEMPLATES.md) | The 23 Blade views and their layout composition. |
| [`FRONTEND_AND_ASSETS.md`](./FRONTEND_AND_ASSETS.md) | `public/assets`, Vite/Tailwind, and the vendored MarketPro theme. |
| [`AUTHENTICATION_AND_ACCESS.md`](./AUTHENTICATION_AND_ACCESS.md) | Session auth, register/login/logout, password reset defects, authorization gaps. |
| [`CART_AND_WISHLIST.md`](./CART_AND_WISHLIST.md) | Guest vs. user cart identity, AJAX endpoints, wishlist behavior. |
| [`CHECKOUT_AND_ORDERS.md`](./CHECKOUT_AND_ORDERS.md) | The multi-table order write, addresses, and the thank-you page. |
| [`PAYMENTS_RAZORPAY.md`](./PAYMENTS_RAZORPAY.md) | Razorpay order creation and signature verification. |
| [`SHIPPING_AND_FULFILMENT.md`](./SHIPPING_AND_FULFILMENT.md) | Shiprocket serviceability, order creation, AWB assignment, and un-scheduled pickup. |
| [`CONFIGURATION_AND_ENV.md`](./CONFIGURATION_AND_ENV.md) | Config files and the environment variable names in play. |
| [`SECURITY_POSTURE.md`](./SECURITY_POSTURE.md) | Ranked security findings with source citations. |
| [`ERROR_HANDLING_AND_LOGGING.md`](./ERROR_HANDLING_AND_LOGGING.md) | Exception paths, logging channels, and swallowed integration errors. |
| [`PERFORMANCE.md`](./PERFORMANCE.md) | Query patterns, view composer cost, and N+1 exposure. |
| [`TESTING.md`](./TESTING.md) | The 3 test files and the absence of domain tests. |
| [`DEPLOYMENT_AND_OPERATIONS.md`](./DEPLOYMENT_AND_OPERATIONS.md) | Docker/Render artefacts, queue and scheduler reality, runbook gaps. |

### Parent index

| Doc | Purpose |
|---|---|
| [`../README.md`](../README.md) | The original numbered-audit index and headline findings. |

---

## 4. Relationship to the numbered audit docs

The numbered docs remain the long-form audit. This library **links** to them rather than duplicating:

- Inventory / shape: [`../01-project-inventory.md`](../01-project-inventory.md),
  [`../02-architecture.md`](../02-architecture.md), [`../03-module-map.md`](../03-module-map.md)
- Features / flows: [`../04-feature-inventory.md`](../04-feature-inventory.md),
  [`../05-user-flows.md`](../05-user-flows.md)
- Routes / schema / models: [`../06-api-and-routes.md`](../06-api-and-routes.md),
  [`../07-database-schema.md`](../07-database-schema.md),
  [`../11-models-and-relationships.md`](../11-models-and-relationships.md)
- Logic / integrations: [`../08-business-logic.md`](../08-business-logic.md),
  [`../12-services-and-integrations.md`](../12-services-and-integrations.md),
  [`../23-external-services.md`](../23-external-services.md)
- Quality / risk: [`../13-security-audit.md`](../13-security-audit.md),
  [`../20-known-issues.md`](../20-known-issues.md),
  [`../21-technical-debt.md`](../21-technical-debt.md),
  [`../22-database-integrity.md`](../22-database-integrity.md),
  [`../25-forms-and-validation.md`](../25-forms-and-validation.md)
- Status / plan: [`../29-remediation-plan.md`](../29-remediation-plan.md),
  [`../30-project-status.md`](../30-project-status.md)

---

## 5. Scope limitations

- No live web server, browser, or database was available during authoring; schema claims rest on the dump
  `palvarnan_db (2).sql`. `NOT_AVAILABLE`
- Runtime rendering and third-party API behavior were not exercised. `UNVERIFIED`
- The storefront shares its MySQL database with `palVarnanDashboard`; that app owns the schema.
  `VERIFIED_FROM_SOURCE` (`../01-project-inventory.md`, both `.env` files set `DB_DATABASE=palvarnan_db`)
