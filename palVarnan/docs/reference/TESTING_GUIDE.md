# Testing Guide — `palVarnan`

> Scope: the **current** test surface, how to run it, and a **proposed** domain test plan.
> This file is the operational companion to [`../24-testing-strategy.md`](../24-testing-strategy.md).
> Where §3 of that document already enumerates a tiered suite, this guide keeps the *what to test*
> short and focuses on *how to run*, *what exists today*, and *what must be built first*, with
> source citations. Cross-references, not duplication.
>
> Evidence vocabulary: `VERIFIED_FROM_SOURCE`, `VERIFIED_FROM_LIVE_SCHEMA`, `INFERRED`,
> `UNVERIFIED`, `NOT_AVAILABLE`, `CONFLICTING_EVIDENCE`.

---

## 1. Current state — VERIFIED

The entire test tree is `VERIFIED_FROM_SOURCE` (directory listing + file reads):

```
tests/
├── TestCase.php                     142 B   stock Laravel 12 abstract TestCase
├── Feature/ExampleTest.php          359 B   asserts GET / === 200
└── Unit/ExampleTest.php             243 B   asserts true === true
```

| Fact | Value | Evidence |
|---|---|---|
| Test files | **3** (`TestCase.php` + 2 `ExampleTest.php`) | directory listing |
| Domain tests | **0** | no other files |
| Test suite config | `phpunit.xml` present, Laravel 12 default | `phpunit.xml` |
| DB used by tests | `DB_CONNECTION=sqlite`, `DB_DATABASE=:memory:` | `phpunit.xml` |
| Factories | `database/factories/` absent | directory listing |
| CI | no `.github/workflows/` | [`../17-devops-deployment.md`](../17-devops-deployment.md) §6 |

**Application coverage is effectively 0%.** The `Feature/ExampleTest` passing is not evidence of
health — the homepage renders inside the `View::composer('*')` closure
(`AppServiceProvider.php:33`) and can return 200 while showing broken fragments
(cross-ref [`../24-testing-strategy.md`](../24-testing-strategy.md) §1).

> **Note on the sqlite default.** Laravel 12's stock `phpunit.xml` uses an in-memory SQLite
> database. That default is present here. It is **not sufficient** for this application: the domain
> schema is MySQL/MariaDB and uses SQL that SQLite cannot express
> (`FIND_IN_SET(...)`, `ON UPDATE current_timestamp()`, `int` money columns). See §5.
> `VERIFIED_FROM_SOURCE` + [`../24-testing-strategy.md`](../24-testing-strategy.md) §6.

---

## 2. How to run tests

### The configured runner

`composer.json:49-52` defines the `test` script:

```json
"test": [
    "@php artisan config:clear --ansi",
    "@php artisan test"
]
```

### Commands

```bash
composer test                       # clears config, then runs the suite
# or directly:
php artisan test                    # whole suite
php artisan test --filter=PlaceOrder
php artisan test --coverage --min=40     # requires Xdebug or PCOV
vendor/bin/phpunit                  # bypasses the artisan wrapper
vendor/bin/pint --test              # style check (config present, never run in CI)
```

Today every command above runs **2 placeholder tests** and exits green. That green run says nothing
about the application. `VERIFIED_FROM_SOURCE`.

### The real first test is `composer install`

`composer install` is the only currently meaningful "test": it must be able to resolve every
declared dependency. As of the audit it **does** resolve `razorpay/razorpay` because the package is
now present in `composer.lock` (`"name": "razorpay/razorpay"` — `composer.lock` line ~3298;
`VERIFIED_FROM_SOURCE`). This corrects the older claim that Razorpay is absent from the lock file
(see [`./SOURCE_COVERAGE_AND_VERIFICATION.md`](./SOURCE_COVERAGE_AND_VERIFICATION.md)).

---

## 3. Why there are no domain tests

| Cause | Evidence |
|---|---|
| No testable seams — logic lives inside a 701-line controller | `CheckoutController.php` is 701 lines; `placeOrder()` alone spans `:225-535` |
| No factories, so no seed path for the 25 models | `database/factories/` absent |
| No CI to force a test pass | [`../17-devops-deployment.md`](../17-devops-deployment.md) §6 |
| "Testing" was done by live scripts, not assertions | `test_apis.php` / `test_services.php` — cross-ref [`../24-testing-strategy.md`](../24-testing-strategy.md) §2 |

**Sequencing constraint:** tests cannot meaningfully be written before two prerequisites exist —
(a) a buildable test database, and (b) extracted, injectable services. That ordering is argued in
[`../24-testing-strategy.md`](../24-testing-strategy.md) §9 and
[`../21-technical-debt.md`](../21-technical-debt.md) §10, and is not repeated here.

---

## 4. Proposed test plan

Three layers. Each layer is independently valuable and can land without the others.

### Layer A — Unit tests (pure, fast, no framework, no DB)

Extract the pricing arithmetic into a pure class **first** (currently it exists in six places —
[`../21-technical-debt.md`](../21-technical-debt.md) §4), then test it directly.

| Test | Asserts | Catches |
|---|---|---|
| `OrderTotalsCalculatorTest` | subtotal, GST (`gst/100` — `CheckoutController.php:65,188,283`), shipping, discount, floor behaviour | `TD-B-03`, `KI-B-11` |
| `WeightHeuristicTest` | the `0.1` floor (`CheckoutController.php:110`) vs `0.5` floor (`:460`) inconsistency | `KI-B-11` |
| `OrderNumberTest` | `'ORD-'.strtoupper(uniqid())` (`:290`) uniqueness across many generations | `KI-B-08` |
| `SearchUrlTest` | brand/category result URLs build from a **real** slug column | `KI-B-05` |

Notes gathered from source while scoping these:

- GST is computed as `gstRate / 100` where `gstRate` is `product_varient.product_gst`
  (`CheckoutController.php:64-65, 187-188, 282-283, 382-383`). Whether that column holds a
  percentage or a category code is a **data question** — see [`../22-database-integrity.md`](../22-database-integrity.md)
  and `TD-B-07`. A unit test can pin the current behaviour and make the intended behaviour explicit.
- Shiprocket weight is floored differently on the quote path vs the create path
  (`:110` vs `:460`), so the same cart can be quoted at one weight and shipped at another.
  `VERIFIED_FROM_SOURCE`.

### Layer B — Feature tests (HTTP + DB, the money and access paths)

These need a real MySQL test database (§5) and factories (§6). Highest return on effort.

| Test | Asserts | Catches |
|---|---|---|
| `PlaceOrderTest::test_order_persists_all_rows` | order + items + address + transaction log + tracking + slots all inserted; cart emptied | `KI-B-01` |
| `PlaceOrderTest::test_client_total_is_ignored` | posting attacker-chosen `shipping_charge`/`coupon_discount` does not move the charged amount | `KI-B-04` |
| `PlaceOrderTest::test_bad_signature_is_rejected` | `verifyPaymentSignature` failure ⇒ no order, no stock change | — |
| `PlaceOrderTest::test_double_submit_creates_one_order` | requires the idempotency work first | `KI-B-10` |
| `PasswordResetTest::test_reset_requires_valid_token` | `POST /reset-password-direct` without a token ⇒ rejected | `KI-B-02` |
| `CartAuthorizationTest::test_user_cannot_modify_another_users_cart` | user A cannot `update`/`remove` user B's line ⇒ 403 | `KI-B-03` |
| `WishlistAuthorizationTest` | wishlist toggle/remove are scoped to `Auth::id()` | — (already scoped) |
| `RouteAuthTest` | `myaccount`, `profile-update`, wishlist-check, checkout group all require auth | `KI-B-06` (auth gaps) |
| `RouteMethodTest` | cart removal is **not** reachable by `GET` (`routes/web.php:50`) | `KI-B-03` |
| `OversellTest` | requesting more than stock ⇒ rejected; stock never goes negative | `KI-B-11` |

IDOR tests are explicitly in scope because the current code is **verified** to lack ownership
checks: `CartController::update()` (`CartController.php:33-44`, `Cart::where('id',$cartId)->update(...)`
at `:37`) and `CartController::remove($id)` (`:46-62`, `Cart::findOrFail($id)` at `:48`).
`VERIFIED_FROM_SOURCE`. The wishlist path is already scoped
(`WishlistController.php:52`), so its test is a regression guard, not a bug reproduction.

### Layer C — Integration tests behind fakes

Goal: no test performs a network call. This requires introducing the interfaces described in §6.

| Test | Asserts | Catches |
|---|---|---|
| `ShiprocketServiceTest` | token is cached for the configured TTL (`ShiprocketService.php:30`); a 401 triggers re-auth | `KI-B-07` |
| `PlaceOrderShippingFailureTest` | a Shiprocket failure leaves the order saved **and** marks fulfilment failed — not silently "success" | `KI-B-06` |
| `PickupScheduledTest` | the pickup method is actually invoked (`generatePickup()`, `ShiprocketService.php:119-124`, currently **no caller**) | `KI-B-13` |
| `SearchImageUrlTest` | image base URL comes from config, not `env('MAIN_URL')` (`SearchController.php:23`) | `TD-B-05` |

> **Naming caution:** the pre-existing suite references a method `createPickup()`; the method that
> actually exists is `generatePickup()`. Tests must target the real name.
> `VERIFIED_FROM_SOURCE` (method list of `ShiprocketService`) + `CONFLICTING_EVIDENCE` with
> [`../24-testing-strategy.md`](../24-testing-strategy.md) §4 Tier 4.

---

## 5. Test database configuration

The stock `phpunit.xml` uses SQLite in memory. **Do not use it for domain tests** — the schema is
MySQL/MariaDB and contains MySQL-only expressions. Recommended change:

```xml
<php>
    <env name="APP_ENV" value="testing"/>
    <env name="DB_CONNECTION" value="mysql"/>
    <env name="DB_DATABASE" value="palvarnan_test"/>
    <env name="MAIL_MAILER" value="array"/>
    <env name="QUEUE_CONNECTION" value="sync"/>
</php>
```

`palvarnan_test` must be **created once** from the same source as production. Because this app has
**zero domain migrations** ([`../19-incomplete-work.md`](../19-incomplete-work.md) IW-01), that
source is the SQL dump `palvarnan_db (2).sql` (93 `CREATE TABLE` statements;
`VERIFIED_FROM_LIVE_SCHEMA`, dump dated 2026-07-31) plus the hand-applied columns recorded in
[`../22-database-integrity.md`](../22-database-integrity.md).

> **Never run `migrate:fresh` / destructive commands against `palvarnan_db`.** It is the shared
> production database also used by the dashboard app — see
> [`./DEPLOYMENT_AND_CONFIGURATION.md`](./DEPLOYMENT_AND_CONFIGURATION.md) and
> [`./CHANGE_IMPACT_GUIDE.md`](./CHANGE_IMPACT_GUIDE.md).

---

## 6. Doubles and factories that must be built first

Nothing exists today (`VERIFIED_FROM_SOURCE`).

| Need | Why |
|---|---|
| `PaymentGateway` interface + `FakeRazorpayGateway` | remove the static `new Api(...)` / `$api->utility->verifyPaymentSignature(...)` calls (`CheckoutController.php:199,258,264`) so signature logic is testable |
| `ShippingGateway` interface + `FakeShiprocketGateway` | remove static `new ShiprocketService()` (`:113,434`) so order/quote logic is testable without network |
| Factories for the ~11 order/cart models | `Product`, `ProductVarient`, `ProductStock`, `Cart`, `User`, `UserAddress`, `ProductOrder`, `ProductOrderItem`… (see [`../24-testing-strategy.md`](../24-testing-strategy.md) §5) |
| A buildable test schema | see §5 |

Until the interfaces exist, Layer B/C tests will instantiate real HTTP clients. Do not paper over
that with `Http::fake()` on code that constructs its own client — inject instead.

---

## 7. CI

No workflow exists. A minimal pipeline (build a MySQL test DB, run the suite, run Pint) is sketched
in [`../17-devops-deployment.md`](../17-devops-deployment.md) §6 and
[`../24-testing-strategy.md`](../24-testing-strategy.md) §7. Note the standing caveat: CI needs the
schema, and the schema is not in version control in this app — the sequencing problem is the same
as §3.

---

## 8. Reconciliation with `../24-testing-strategy.md`

| `../24-testing-strategy.md` statement | Status |
|---|---|
| §1 "3 files: TestCase + 2 scaffold ExampleTest" | **`VERIFIED_CURRENT`** |
| §1 "`phpunit.xml` is the Laravel 12 default" | **`VERIFIED_CURRENT`** (uses sqlite `:memory:`) |
| §4 Tier 4 tests `createPickup()` | **`OUTDATED`** — method is `generatePickup()` |
| §4/§5 `BUG-nn` cross-references (e.g. `BUG-47`, `BUG-51`, `BUG-54`) | **`OUTDATED`** — `../20-known-issues.md` was renumbered to `BUG-01…105`; the referenced IDs no longer resolve to the same defects |
| §9 "add the 3 migrations" / "DB-01, DB-02" | **`CONFLICTING_EVIDENCE`** — those migrations still do not exist; the action is aspirational, not done |

Full reconciliation table: [`./SOURCE_COVERAGE_AND_VERIFICATION.md`](./SOURCE_COVERAGE_AND_VERIFICATION.md).

---

## 9. Related documents

- [`../24-testing-strategy.md`](../24-testing-strategy.md) — the 30-test, 5-tier proposal and effort estimate
- [`../21-technical-debt.md`](../21-technical-debt.md) — `TD-05` (no tests) and the refactor that enables tests
- [`../20-known-issues.md`](../20-known-issues.md) — the defects these tests would pin
- [`../13-security-audit.md`](../13-security-audit.md) — the access-control paths to cover
- [`../22-database-integrity.md`](../22-database-integrity.md) — the schema a test DB must reproduce
- [`./KNOWN_ISSUES_AND_TECHNICAL_DEBT.md`](./KNOWN_ISSUES_AND_TECHNICAL_DEBT.md) — `KI-B-*` / `TD-B-*` register
