# 24 — Testing Strategy

---

## 1. Current state

```
tests/
├── TestCase.php                      stock
├── Feature/ExampleTest.php           asserts GET / === 200
└── Unit/ExampleTest.php              asserts true === true
```

`phpunit.xml` is the Laravel 12 default. **Application test coverage is 0%.**

The `ExampleTest` for `/` passing is not evidence of health: the route's view renders inside a `View::composer('*')` that swallows its own errors, so the page returns 200 while showing broken fragments.

---

## 2. Why there are no tests

| Cause | Evidence |
|---|---|
| The feature was written without a test pass | the checkout service is untracked in git |
| No CI to run them | no `.github/workflows/` |
| No testable seams — all logic is in controllers | `CheckoutController.php` is 701 lines with no service boundary |
| No database factory | `database/factories/` absent |
| Live-integration scripts served as testing | `test_apis.php`, `test_services.php` — hit production APIs and mutate schema |

That last row is the real problem: **`test_apis.php` and `test_services.php` are not tests.** They have no assertions, use hard-coded production credentials, disable TLS verification, and can create live Shiprocket orders and drop tables. They are a liability, not coverage.

---

## 3. Coverage of the critical paths

| Path | Covered | Blocking defect untested |
|---|---|---|
| Register / login | no | tokenless password reset |
| Password reset | no | no token verification |
| Add to cart | no | no stock check, no ownership |
| Cart update / remove | no | IDOR |
| Checkout page | no | UTC date, duplicate pricing |
| Razorpay order creation | no | client-controlled amount |
| Payment callback | no | no amount verification, no idempotency |
| Order persistence | no | missing columns |
| Shiprocket order + AWB | no | swallowed failure, no pickup |
| Order confirmation | no | — |
| Search | no | int column with `LIKE` |

**11 critical paths, 0 tests.**

---

## 4. Recommended suite

Organised by return on effort. Each tier is independently deployable.

### Tier 1 — money and access (first, before any refactor)

| Test | Type | Asserts |
|---|---|---|
| `PlaceOrderTest::test_order_persists_all_rows` | feature | order, items, address, transaction, log, status all inserted; cart emptied |
| `PlaceOrderTest::test_missing_columns_are_migrated` | feature | shipping/billing state and item image insert cleanly — **regression test for BUG-04** |
| `PlaceOrderTest::test_client_total_is_ignored` | feature | posting `total=1` still charges the server-computed amount — **BUG-05** |
| `PlaceOrderTest::test_bad_signature_is_rejected` | feature | no order, no stock change |
| `PlaceOrderTest::test_amount_mismatch_is_refunded` | feature | captured payment + amount ≠ expected ⇒ refund + no order — **BUG-22** |
| `PlaceOrderTest::test_double_submit_creates_one_order` | feature | `UNIQUE(razorpay_order_id)` holds — **BUG-21** |
| `PasswordResetTest::test_reset_requires_valid_token` | feature | reset without a token ⇒ 403 — **BUG-01** |
| `CartAuthorizationTest::test_user_cannot_modify_another_users_cart` | feature | 403 on update and delete — **BUG-06, BUG-07** |
| `WishlistAuthorizationTest` | feature | 403 on toggle and delete — **BUG-12** |
| `GuestCartMergeTest::test_login_merges_guest_cart` | feature | guest lines migrate to the user — **BUG-43** |
| `StockTest::test_oversell_is_rejected` | feature | requesting more than stock ⇒ 422, no negative stock — **BUG-35** |

### Tier 2 — units, pure and fast

| Test | Asserts |
|---|---|
| `OrderTotalsCalculatorTest` | GST, shipping, discount, floor behaviour across 6 data rows |
| `WeightHeuristicTest` | the kg/gram floor inconsistency — **BUG-37** |
| `RazorpayChecksumTest` | signature generation and comparison against known vectors |
| `OrderNumberTest` | uniqueness across 10 000 generations — **BUG-47** |
| `SearchUrlTest` | brand and category results carry valid slugs — **BUG-25, BUG-26** |

### Tier 3 — HTTP surface

| Test | Asserts |
|---|---|
| `RouteAuthTest` | `myaccount`, `profile.update`, wishlist routes all require auth — **BUG-09…12** |
| `RouteMethodTest` | cart removal is not reachable by `GET` — **BUG-07** |
| `RateLimitTest` | login and reset are throttled — **BUG-13** |
| `ContactFormSpamTest` | honeypot and throttling |
| `CountrySelectionTest` | `session('error')` renders — **BUG-50** |

### Tier 4 — integrations behind fakes

| Test | Asserts |
|---|---|
| `ShiprocketServiceTest` | token cached 8h; 401 triggers re-auth; timeout retried; 4xx raises |
| `PlaceOrderShippingFailureTest` | Shiprocket failure ⇒ order still saved, fulfilment flagged failed, not silently "successful" — **BUG-20** |
| `PickupScheduledTest` | `createPickup()` is called and `pickup_scheduled` set — **BUG-18** |
| `SearchImageUrlTest` | image URLs built from config, not `env()` — **BUG-27** |

### Tier 5 — rendering and regressions

| Test | Asserts |
|---|---|
| `CartPageTest` | renders with a soft-deleted variant without a 500 — **BUG-31, BUG-32** |
| `HomePageTest` | no `wowtheme7.com` or `shop.html` links remain — **BUG-51, BUG-52** |
| `NoDuplicateJqueryTest` | exactly one jQuery `<script>` tag — **BUG-54** |
| `CsrfHeaderTest` | every `fetch()` call sends `X-CSRF-TOKEN` — **BUG-55** |
| `ReconciliationTest` | `Paid` transaction with no order ⇒ the detector alerts |

---

## 5. Test doubles required

Nothing exists today. Needed:

```php
// tests/Fakes/FakeRazorpayGateway.php
interface PaymentGateway {
    public function createOrder(int $amountPaise, string $receipt): string;
    public function verifySignature(string $orderId, string $paymentId, string $signature): bool;
    public function fetchPayment(string $paymentId): object;
    public function refund(string $paymentId, int $amountPaise): string;
}

// tests/Fakes/FakeShiprocketGateway.php
interface ShippingGateway {
    public function getShippingServices(int $weightGrams, string $pincode): array;
    public function createOrder(array $payload): string;
    public function generateAwb(array $payload): string;
    public function createPickup(array $payload): string;
    public function trackAwb(string $awb): array;
}
```

Controllers take these via constructor injection or the container, so no test needs a network call.

### Factories needed

`Product`, `ProductVarient`, `Productstocks`, `Categories`, `Brands`, `User`, `UserAddress`, `Cart`, `Wishlist`, `ProductOrder`, `ProductOrderItem` — **11 factories, currently zero.**

### Integration tests stay manual

`test_apis.php` and `test_services.php` may be rewritten as opt-in scripts under `tests/Integration/` gated by `RUN_LIVE_TESTS=1`, with credentials from `.env`, TLS verification **on**, and no schema mutation. Until then, **delete both files and rotate the credentials.**

---

## 6. Commands

```bash
php artisan test                    # whole suite
php artisan test --filter=PlaceOrder
php artisan test:coverage --min=60  # requires Xdebug or PCOV
```

`phpunit.xml` needs three adjustments:

```xml
<php>
    <env name="APP_ENV" value="testing"/>
    <env name="DB_CONNECTION" value="sqlite"/>
    <env name="DB_DATABASE" value=":memory:"/>
    <env name="MAIL_MAILER" value="array"/>
    <env name="QUEUE_CONNECTION" value="sync"/>
    <env name="RAZORPAY_KEY" value="test_key"/>
    <env name="RAZORPAY_SECRET" value="test_secret"/>
    <env name="SHIPROCKET_EMAIL" value="test@example.invalid"/>
    <env name="SHIPROCKET_PASSWORD" value="test"/>
</php>
```

SQLite is a problem: the schema uses MySQL-specific DDL and `FIND_IN_SET`. **Use a dedicated MySQL test database** rather than `:memory:`:

```dotenv
DB_DATABASE=palvarnan_test
```

created once from the same migrations as production. **No `migrate:fresh` against `palvarnan_db`** — that is how the shared-database problem gets worse.

---

## 7. CI

No workflow exists. Minimum:

```yaml
name: CI
on: [push, pull_request]
jobs:
  test:
    runs-on: ubuntu-latest
    services:
      mysql:
        image: mysql:8
        env:
          MYSQL_ROOT_PASSWORD: root
          MYSQL_DATABASE: palvarnan_test
        ports: ['3306:3306']
        options: >-
          --health-cmd="mysqladmin ping" --health-interval=10s
          --health-timeout=5s --health-retries=5
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with:
          php-version: '8.2'
          extensions: pdo_mysql
          coverage: xdebug
      - run: composer install --no-interaction --prefer-dist
      - run: php artisan migrate --force
      - run: php artisan test --coverage --min=40
```

**`composer install` is the real first test.** It fails today because Razorpay is in `vendor/` but not in the lock file (BUG-08) — exactly the failure a CI run would surface.

---

## 8. Effort

| Tier | Tests | Effort |
|---|---|---|
| 1 — money and access | 11 | **2 d** |
| 2 — units | 5 | **0.5 d** |
| 3 — HTTP surface | 5 | **0.5 d** |
| 4 — integrations | 4 | **1 d** |
| 5 — rendering | 5 | **0.5 d** |
| **Total** | **30** | **≈4.5 d** |

Tier 1 alone, at two days, would have caught **8 of the 8 S1 issues** in [20-known-issues.md](20-known-issues.md).

---

## 9. Testability prerequisites

| Change | Why |
|---|---|
| Extract `OrderTotalsCalculator` | pure function, no framework needed — TD-03, TD-01 |
| Extract `StockManager` | wraps the transaction; `lockForUpdate` is testable in a real DB — BUG-36 |
| Inject `PaymentGateway` | remove the static `Api::` calls — required for Tier 1 |
| Inject `ShippingGateway` | remove the static `ShiprocketService` calls |
| Add factories | 11 models have no seed path |
| Add the 3 migrations | a test DB must be buildable — DB-01, DB-02 |

**Tests cannot be written before these exist.** That is why this suite is sequenced after the refactor in [21-technical-debt.md](21-technical-debt.md) step 7, and why Tier 1 pairs with the P0 fixes.

---

## 10. Related documents

- [20-known-issues.md](20-known-issues.md) — what tests would catch
- [21-technical-debt.md](21-technical-debt.md) — TD-05, and the refactor that enables tests
- [13-security-audit.md](13-security-audit.md) — the access-control paths to cover
- [22-database-integrity.md](22-database-integrity.md) — the migrations tests need
- [29-remediation-plan.md](29-remediation-plan.md) — sequencing