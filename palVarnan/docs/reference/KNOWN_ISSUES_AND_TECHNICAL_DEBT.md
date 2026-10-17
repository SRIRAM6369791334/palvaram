# Known Issues and Technical Debt — `palVarnan`

> Consolidated defect (`KI-B-nn`) and debt (`TD-B-nn`) register for the `palVarnan` storefront.
> This is the **single index** that points at the deeper, already-authored detail in the numbered
> documents; it does not replace them. Each entry carries severity, evidence, impact, and a
> suggested fix.
>
> Severity: **S1 Critical** (money loss / data loss / account compromise) · **S2 High** ·
> **S3 Medium** · **S4 Low**.
>
> Evidence vocabulary: `VERIFIED_FROM_SOURCE`, `VERIFIED_FROM_LIVE_SCHEMA`, `INFERRED`,
> `UNVERIFIED`, `NOT_AVAILABLE`, `CONFLICTING_EVIDENCE`.
>
> **No credential or PII values appear in this file.**

---

## 1. Defect register — `KI-B-nn`

| ID | Defect | Severity | Evidence |
|---|---|---|---|
| KI-B-01 | No domain migrations / schema unversioned; checkout writes columns absent from the dump | **S1** | `VERIFIED_FROM_SOURCE` + `VERIFIED_FROM_LIVE_SCHEMA` |
| KI-B-02 | Password reset accepts a new password with **no token** — account takeover | **S1** | `VERIFIED_FROM_SOURCE` |
| KI-B-03 | Cart update/remove have no ownership check (IDOR); remove is a `GET` | **S2** | `VERIFIED_FROM_SOURCE` |
| KI-B-04 | Money is partly client-trusted (`shipping_charge`, `coupon_discount`) | **S1** | `VERIFIED_FROM_SOURCE` |
| KI-B-05 | Missing middleware / policies; several routes unauthenticated; no rate limiting | **S2** | `VERIFIED_FROM_SOURCE` |
| KI-B-06 | Shiprocket failures are swallowed; no timeout/retry/re-auth | **S2** | `VERIFIED_FROM_SOURCE` |
| KI-B-07 | Pickup is never scheduled (`generatePickup()` has no caller) | **S2** | `VERIFIED_FROM_SOURCE` |
| KI-B-08 | No idempotency on order placement; `uniqid()` order numbers | **S3** | `VERIFIED_FROM_SOURCE` |
| KI-B-09 | Price-sort subquery targets a non-existent table `product_varients` | **S3** | `VERIFIED_FROM_SOURCE` + `VERIFIED_FROM_LIVE_SCHEMA` |
| KI-B-10 | Search result URLs use slug columns that do not exist | **S3** | `VERIFIED_FROM_SOURCE` + `VERIFIED_FROM_LIVE_SCHEMA` |
| KI-B-11 | Stock: no lock, silent flooring at zero, inconsistent weight/GST heuristics | **S3** | `VERIFIED_FROM_SOURCE` |
| KI-B-12 | No cancel / return / refund path; captured-but-orderless payments undetectable | **S2** | `VERIFIED_FROM_SOURCE` |
| KI-B-13 | Duplicate relationship pairs on models | **S4** | `VERIFIED_FROM_SOURCE` |

### KI-B-01 — No domain migrations; schema unversioned — **S1**

- **Evidence.** `database/migrations/` contains only 3 stock Laravel files
  (`0001_01_01_000000_create_users_table.php`, `0001_01_01_000001_create_cache_table.php`,
  `0001_01_01_000002_create_jobs_table.php`). `VERIFIED_FROM_SOURCE`. The SQL dump
  `palvarnan_db (2).sql` (dated 2026-07-31) contains 93 `CREATE TABLE` statements,
  `VERIFIED_FROM_LIVE_SCHEMA`, and **no `billing_state` column** (`billing_state` → 0 matches).
- **Impact.** The 93-table schema cannot be version-controlled, diffed, or rolled back here. A
  fresh deploy cannot build the database from this repo. Checkout writes columns
  (`billing_state`, `shipping_state`, and per `../20-known-issues.md` BUG-24 also `discount`,
  `product_image`, `courier_name`) that no migration creates — historically causing 500s **after**
  payment capture (`Unknown column 'billing_state'`, 54 occurrences in `laravel.log`).
- **Suggested fix.** Own the schema in one place: either move all domain migrations into this app,
  or version `palVarnan.sql` as a required artefact and add the missing columns as real migrations.
  See [`../22-database-integrity.md`](../22-database-integrity.md) and
  [`./CHANGE_IMPACT_GUIDE.md`](./CHANGE_IMPACT_GUIDE.md).

### KI-B-02 — Password reset without a token — **S1**

- **Evidence.** `AuthController::resetPassword()` (`AuthController.php:100-126`) validates only
  `email` + `password` (`:102-105`), finds the user by email (`:107`), and sets
  `Hash::make($request->password)` (`:117`). No `token`, no signed link, no expiry. The route
  `/reset-password-direct` is public (`routes/web.php:38`). `VERIFIED_FROM_SOURCE`.
- **Impact.** Anyone who knows a registered email address can reset that account's password →
  full account takeover. Also leaks existence via `'User not found'` (`:112`).
- **Suggested fix.** Use `Password::reset()` with a broker token and a `GET /reset-password/{token}`
  route; return a generic message regardless of account existence. Cross-reference the
  security register: [`../13-security-audit.md`](../13-security-audit.md).

### KI-B-03 — Cart IDOR (no ownership check) — **S2**

- **Evidence.** `CartController::update()` `:33-44` iterates `$request->quantities` and runs
  `Cart::where('id', $cartId)->update([...])` (`:37`) — no `user_id` scope. `CartController::remove($id)`
  `:46-62` runs `Cart::findOrFail($id)->delete()` (`:48`) — no scope, and the route is
  `Route::get('/cart-remove/{id}', ...)` (`routes/web.php:50`). `VERIFIED_FROM_SOURCE`.
- **Impact.** Any authenticated (or guest) visitor can mutate or delete another user's cart line by
  guessing an integer id; the delete is reachable by `GET` (CSRF/prefetch risks).
- **Suggested fix.** Scope both writes by `user_id`/guest identity; move removal to `DELETE`.
  The wishlist path is already scoped (`WishlistController.php:52`) — mirror that.
  Tracked as BUG-06/BUG-07 in [`../20-known-issues.md`](../20-known-issues.md).

### KI-B-04 — Client-trusted money — **S1**

- **Evidence.** `CheckoutController::preparePayment()` reads
  `floatval($request->shipping_charge ?? 0)` (`:191`) and `floatval($request->coupon_discount ?? 0)`
  (`:192`); `placeOrder()` repeats both (`:286-287`). The Razorpay order is created for
  `(int) round($total * 100)` (`:202`) where `$total` depends on those request values. The
  `coupons` table is never queried although `Coupon` is imported (`CheckoutController.php:6`).
  `VERIFIED_FROM_SOURCE`.
- **Impact.** A caller can reduce `shipping_charge` or inflate `coupon_discount` and lower the
  charged amount. Note: the **subtotal and GST are recomputed server-side** from the cart
  (`:181-189, 276-284`) — only shipping and discount are trusted. `VERIFIED_FROM_SOURCE`.
- **Suggested fix.** Compute shipping server-side (from a configured rate/`State::shipping_charge`)
  and validate any coupon against the `coupons` table inside the transaction. Ignore all
  client-supplied money fields. See [`../08-business-logic.md`](../08-business-logic.md).

### KI-B-05 — Missing middleware / policies / rate limiting — **S2**

- **Evidence.** `bootstrap/app.php` `withMiddleware` contains only
  `$middleware->trustProxies(at: '*')` (`bootstrap/app.php:13-15`) — no route throttles. There are
  no `app/Policies/`, no `app/Http/Requests/`, and no `app/Http/Middleware/`. `VERIFIED_FROM_SOURCE`.
  Several routes are unauthenticated: `/myaccount` (`routes/web.php:68`),
  `PUT /profile-update` (`:40-41`), `/wishlist` and `/wishlist/toggle` (`:60-61`).
- **Impact.** Access control is re-decided ad hoc in each controller; new endpoints default to
  open. `profile.update` dereferences `auth()->user()` before any null check
  (`ProfileController.php:16-21`) → 500 for a guest. No login/reset throttling enables brute force.
- **Suggested fix.** Add `auth` middleware to the unprotected routes, add `throttle` to auth and
  quote endpoints, and introduce policies for `ProductOrder`/`UserAddress`. Cross-ref
  [`../13-security-audit.md`](../13-security-audit.md) and [`../25-forms-and-validation.md`](../25-forms-and-validation.md).

### KI-B-06 — Swallowed integration failures; no timeout/retry/re-auth — **S2**

- **Evidence.** `CheckoutController::placeOrder()` wraps the post-commit Shiprocket call in
  `try { ... } catch (\Exception $e) { Log::error(...) }` (`:525-527`) and still returns
  `status: success` (`:529-534`). `ShiprocketService` uses `Http::withToken(...)` with no
  `->timeout()`/`->retry()` anywhere (grep of `app/` = 0 matches). `VERIFIED_FROM_SOURCE`.
- **Impact.** A Shiprocket outage produces orders that look complete to the customer and to staff,
  are invisible to reconciliation, and can hang the request (`Maximum execution time of 60 seconds
  exceeded`, ×2 in `laravel.log`). No 401 re-auth path.
- **Suggested fix.** Add `->timeout(10)->retry(2, 200)` and a 401 re-auth path; on failure set a
  `shiprocket_status='FAILED'` marker and surface it; move the call to a queued job.
  See [`./PERFORMANCE_AND_RELIABILITY.md`](./PERFORMANCE_AND_RELIABILITY.md) §3.

### KI-B-07 — Pickup never scheduled — **S2**

- **Evidence.** `ShiprocketService::generatePickup()` (`ShiprocketService.php:119-124`) has **no
  caller** anywhere in `app/`. `VERIFIED_FROM_SOURCE`. (The pre-existing docs call it
  `createPickup()`, a method that does not exist — naming is off; the behavioural claim stands.)
- **Impact.** Orders are created and (optionally) AWB-assigned but never collected — fulfilment
  silently stalls.
- **Suggested fix.** Invoke `generatePickup()` after AWB assignment (in the queued fulfilment job)
  and set `product_orders.pickup_scheduled`.

### KI-B-08 — No idempotency; `uniqid()` order numbers — **S3**

- **Evidence.** `placeOrder()` has no idempotency key; `order_number` is
  `'ORD-' . strtoupper(uniqid())` (`:290`), with `order_id` set to the same value (`:298`).
  The `product_orders_order_number_unique` index exists, so a collision fails loudly rather than
  silently. `VERIFIED_FROM_SOURCE` + `VERIFIED_FROM_LIVE_SCHEMA`.
- **Impact.** A double submit can attempt two orders (no `UNIQUE(razorpay_order_id)` guard);
  `uniqid()` is time-based, not a counter.
- **Suggested fix.** Add a `UNIQUE` constraint on `razorpay_order_id` and reject duplicate
  submissions; prefer a ULID/sequence for order numbers.

### KI-B-09 — Price-sort subquery targets a non-existent table — **S3**

- **Evidence.** `ProductController::index()` builds
  `orderByRaw('(SELECT MIN(offer_price) FROM product_varients WHERE ... )')` (`:84`) and the `MAX`
  variant (`:87`). The model declares `protected $table = 'product_varient'`
  (`ProductVarient.php:8`), and the dump has **no** `product_varients` table (`CREATE TABLE
  \`product_varients\`` → 0 matches) — only `product_varient` (1 match).
  `VERIFIED_FROM_SOURCE` + `VERIFIED_FROM_LIVE_SCHEMA`.
- **Impact.** Selecting sort = price low→high or high→low raises a SQL error / 500.
- **Suggested fix.** Correct the table name to `product_varient` (or better, join through the
  Eloquent relation). Add a feature test for each sort option.

### KI-B-10 — Search result URLs use non-existent slug columns — **S3**

- **Evidence.** `SearchController.php` builds brand URLs from `$item->slug` (`:69`) though `brands`
  has `brand_slug`, and category URLs from `$item->slug` (`:84`) though `categories` has no slug
  column; the variant `LIKE` runs against `product_varient.varient` (`:42`), which is an integer
  column. `VERIFIED_FROM_SOURCE` + `VERIFIED_FROM_LIVE_SCHEMA`.
- **Impact.** Brand/category search links resolve to `/brand/`, `/category/`; variant search can
  never match.
- **Suggested fix.** Use the real columns (`brand_slug`), add a slug to `categories`, and remove or
  redesign the variant `LIKE`.

### KI-B-11 — Stock and pricing heuristics — **S3**

- **Evidence.** Stock deduction floors at zero with no backorder flag:
  `max($productStock->availablestock - $item->product_quantity, 0)` (`CheckoutController.php:405`)
  and `max($variant->product_qty - $item->product_quantity, 0)` (`:413`); no `lockForUpdate()`
  (grep of `app/` = 0 matches). Weight is floored at `0.1` on the quote path (`:110`) but `0.5`
  on the create path (`:460`). GST is computed as `gstRate / 100` (`:64-65, 187-188, 282-283`)
  although the column stores category codes per [`../20-known-issues.md`](../20-known-issues.md)
  BUG-76. `VERIFIED_FROM_SOURCE`.
- **Impact.** Concurrent orders can oversell; two weight figures disagree for the same cart;
  GST may be computed on a code value.
- **Suggested fix.** Add `lockForUpdate()` inside the transaction, reject insufficient stock, and
  extract a single pricing/weight calculator (see `TD-B-03`).

### KI-B-12 — No cancel / return / refund; orderless payments undetectable — **S2**

- **Evidence.** No refund code exists anywhere (grep of `app/`); the payment path verifies the
  signature (`CheckoutController.php:264`) but never re-verifies the amount or fetches the payment.
  `product_transaction_logs` records amounts but **no `razorpay_payment_id`**
  (`ProductTransactionLog::create(...)` `:348-356`). `VERIFIED_FROM_SOURCE`.
- **Impact.** If an order fails after capture, there is no automatic refund and no SQL that can
  detect "Paid with no order row".
- **Suggested fix.** Persist `razorpay_payment_id` in the transaction log, add a reconciliation
  query/job, and add a refund path. See [`../23-external-services.md`](../23-external-services.md).

### KI-B-13 — Duplicate relationship pairs — **S4**

- **Evidence.** `Product::variants()` and `Product::productvari()` both return
  `hasMany(ProductVarient::class)` (`Product.php`); `ProductOrder::user()` and
  `ProductOrder::customer()` both `belongsTo(User::class,'user_id')` (`ProductOrder.php`).
  `VERIFIED_FROM_SOURCE`.
- **Impact.** Ambiguity about which relation is canonical; wasted maintenance.
- **Suggested fix.** Deprecate one of each pair and document the canonical relation.

---

## 2. Technical-debt register — `TD-B-nn`

Debt is cost inherited by the next change ([`../21-technical-debt.md`](../21-technical-debt.md) §1).

| ID | Debt | Severity | Evidence |
|---|---|---|---|
| TD-B-01 | `CheckoutController.php` god-object — 701 lines / 28 275 B | **High** | `VERIFIED_FROM_SOURCE` |
| TD-B-02 | Zero domain tests (2 placeholders) | **High** | `VERIFIED_FROM_SOURCE` |
| TD-B-03 | Pricing duplicated in 6+ places, 3 shapes | **High** | `VERIFIED_FROM_SOURCE` |
| TD-B-04 | Vite/Tailwind pipeline configured but unused | **Medium** | `VERIFIED_FROM_SOURCE` |
| TD-B-05 | `env()` called outside `config/` | **Medium** | `VERIFIED_FROM_SOURCE` |
| TD-B-06 | Empty models `Unit` / `HomePromotion` | **Low** | `VERIFIED_FROM_SOURCE` |
| TD-B-07 | GST column semantics ambiguous (code vs percent) | **Medium** | `INFERRED` |
| TD-B-08 | Money stored/summed as PHP floats; no `decimal:2` casts | **Medium** | `VERIFIED_FROM_SOURCE` |
| TD-B-09 | Misspelled identifiers throughout (`varient`, `ip_addresscart`, …) | **Medium** | `VERIFIED_FROM_SOURCE` |
| TD-B-10 | No Form Requests / DTOs; validation inline in controllers | **Medium** | `VERIFIED_FROM_SOURCE` |
| TD-B-11 | No queue, no jobs, no schedule | **Medium** | `VERIFIED_FROM_SOURCE` |
| TD-B-12 | No CI | **Medium** | `VERIFIED_FROM_SOURCE` |
| TD-B-13 | `ShiprocketService` duplicated across the two apps | **Medium** | [`../18-configuration-and-env.md`](../18-configuration-and-env.md) §3 |
| TD-B-14 | `.env.example` missing 13 keys (commerce + DB) | **Medium** | `VERIFIED_FROM_SOURCE` |

### TD-B-01 — `CheckoutController.php` god-object — **High**

- **Evidence.** `app/Http/Controllers/CheckoutController.php` is 701 lines / 28 275 B
  (`VERIFIED_FROM_SOURCE`). `placeOrder()` alone spans `:225-535` and performs validation
  (`:230-249`), signature verification (`:252-268`), total recomputation (`:276-288`), a multi-table
  transaction (`:292-421`), stock mutation (`:403-415`), cart clearing (`:419`), and external
  integration (`:432-527`).
- **Impact.** The money path cannot be unit-tested or reused (e.g. an admin-created order); every
  change risks a critical bug. Method/line breakdown: [`../21-technical-debt.md`](../21-technical-debt.md) §3.
- **Suggested fix.** Extract `OrderTotalsCalculator`, `StockManager`, `RazorpayGateway`, and a
  queued `FulfilmentDispatcher`; split the controller into page/payment/order/address controllers.

### TD-B-02 — Zero domain tests — **High**

- **Evidence.** 3 test files total (`tests/TestCase.php`, `tests/Feature/ExampleTest.php`,
  `tests/Unit/ExampleTest.php`); no factories; `phpunit.xml` stock (SQLite `:memory:`).
  `VERIFIED_FROM_SOURCE`. Full detail: [`./TESTING_GUIDE.md`](./TESTING_GUIDE.md).
- **Impact.** Every fix is an unverified change; the S1 defects went undetected.
- **Suggested fix.** Extract seams, add factories, build a MySQL test DB, and land the Layer A/B/C
  plan in [`./TESTING_GUIDE.md`](./TESTING_GUIDE.md) §4.

### TD-B-03 — Duplicated pricing logic — **High**

- **Evidence.** Unit price is derived in at least six places with three different null-guard
  shapes: `AppServiceProvider.php:48-50`, `CartController.php:27` (**no guard**),
  `CartController.php:141-142`, `CheckoutController.php:60`, `:184`, `:279`, `:367`, and
  `ProfileController.php:96`; plus three total formulas (`CheckoutController.php:193` floors at
  `max(...,1)`, `:288` no floor, `checkout.blade.php:626` floors at `0`). `VERIFIED_FROM_SOURCE`.
- **Impact.** A pricing change must be applied in ~9 places; they have already diverged. The
  no-guard path (`CartController.php:27`) can silently compute a `0` subtotal.
- **Suggested fix.** One `OrderTotalsCalculator` used by every path. Detail:
  [`../21-technical-debt.md`](../21-technical-debt.md) §4.

### TD-B-04 — Vite/Tailwind configured but unused — **Medium** (confirmed)

- **Evidence.** `package.json` declares Vite 7, Tailwind 4, and `laravel-vite-plugin`
  (`package.json:9-16`). A grep for `@vite` across all Blade views matches **only**
  `welcome.blade.php:15`, and `welcome.blade.php` is unreachable (no route renders it).
  `VERIFIED_FROM_SOURCE`. Live views load committed assets from `public/assets/`.
- **Impact.** `npm run build` produces output no served page references; `composer run dev` fails
  or misleads at the Vite step; false confidence that a modern asset pipeline exists.
- **Suggested fix.** Either delete the pipeline (and the `composer.json` npm steps) or adopt it
  fully by moving the live views onto `@vite` — do not leave it half-configured.

### TD-B-05 — `env()` outside `config/` — **Medium**

- **Evidence.** `SearchController.php:23`:
  `$baseImageUrl = rtrim(env('MAIN_URL'), '/') . '/images/';`. A grep of `app/` for `env(`
  returns this single line. `VERIFIED_FROM_SOURCE`.
- **Impact.** Returns `null` once `php artisan config:cache` runs, breaking every search image URL.
- **Suggested fix.** Add `'main_url' => env('MAIN_URL')` to `config/services.php` and read it via
  `config()`. Detail: [`./DEPLOYMENT_AND_CONFIGURATION.md`](./DEPLOYMENT_AND_CONFIGURATION.md) §4.

### TD-B-06 — Empty models `Unit` / `HomePromotion` — **Low**

- **Evidence.** `app/Models/Unit.php` and `app/Models/HomePromotion.php` contain only
  `class X extends Model { // }` — no `$fillable`, no `$table`, no relations. `VERIFIED_FROM_SOURCE`.
  They are still used: `HomePromotion::where('country_id',...)->where('status',1)->orderBy('sort_order')`
  (`HomeController.php:19-22`) and `ProductVarient::unit()` (`ProductVarient.php`) /
  `Product::variants.unit` eager loads.
- **Impact.** `Unit` is used as a relation target without declared columns; `HomePromotion` is
  queried with columns (`country_id`, `status`, `sort_order`) it never declares. Works by Eloquent
  convention, but the schema contract is invisible in code.
- **Suggested fix.** Add `$table` and `$fillable` (and any relations) so the models document the
  columns they rely on.

### TD-B-07 — GST column semantics — **Medium** (`INFERRED`)

- **Evidence.** `product_varient.product_gst` is described in [`../20-known-issues.md`](../20-known-issues.md)
  BUG-76 as storing category codes (`1-as5`, `2-as12`, …) yet it is divided by 100
  (`CheckoutController.php:65,188,283,383,396`). `VERIFIED_FROM_SOURCE` that the division occurs;
  **`INFERRED`** that the underlying values are codes rather than percentages (not sample-read from
  live rows).
- **Impact.** If the values are codes, GST is computed incorrectly on every order.
- **Suggested fix.** Confirm the column's real semantics against data, then either normalise the
  values or fix the formula; add a unit test pinning the intended behaviour.

### TD-B-08 — Money as floats — **Medium**

- **Evidence.** Money columns are integer types and no order/variant model casts to `decimal:2`
  (e.g. `ProductVarient` has no `casts()`; `ProductOrder` has no money casts). `VERIFIED_FROM_SOURCE`.
- **Impact.** Rounding drift and audit exposure across repeated float arithmetic.
- **Suggested fix.** Cast money to `decimal:2` and centralise rounding in the extracted calculator.

### TD-B-09 — Misspelled identifiers — **Medium**

- **Evidence.** `product_varient` (table), `varient`, `product_varient_id` vs `product_variant_id`,
  `pro_ver_id`, `ip_addresscart`, `availablestock`, `salestock`, `productvari()`.
  `VERIFIED_FROM_SOURCE`. Full table: [`../21-technical-debt.md`](../21-technical-debt.md) §7.
- **Impact.** Grep failures, mental overhead; the plural `product_varients` typo already produced a
  real defect (`KI-B-09`).
- **Suggested fix.** **Do not rename now** (blast radius spans two apps and the shared schema).
  Standardise going forward and document in [`../28-glossary-and-terms.md`](../28-glossary-and-terms.md).

### TD-B-10 — No Form Requests / DTOs — **Medium**

- **Evidence.** `app/Http/Requests/` absent; validation is inline (`CheckoutController::validateAddress()`
  `:672-686`; `placeOrder()` rules `:230-240`). `VERIFIED_FROM_SOURCE`.
- **Impact.** Validation is not reusable and cannot be tested in isolation.
- **Suggested fix.** Introduce Form Requests for checkout/address/register/reset.

### TD-B-11 — No queue, no jobs, no schedule — **Medium**

- **Evidence.** `app/Jobs/` and `app/Console/` absent; `QUEUE_CONNECTION=sync`; `routes/console.php`
  stock. `VERIFIED_FROM_SOURCE`. Detail: [`./PERFORMANCE_AND_RELIABILITY.md`](./PERFORMANCE_AND_RELIABILITY.md) §4.
- **Impact.** All external I/O is synchronous; the correct fix for `KI-B-06`/`KI-B-07` is blocked.
- **Suggested fix.** Introduce a database/Redis queue, a fulfilment job, and a scheduler.

### TD-B-12 — No CI — **Medium**

- **Evidence.** No `.github/workflows/`, no other CI config. `VERIFIED_FROM_SOURCE`.
- **Impact.** Nothing is verified on commit; `composer install` correctness, tests, and Pint are
  never enforced.
- **Suggested fix.** Add the minimal pipeline in [`./TESTING_GUIDE.md`](./TESTING_GUIDE.md) §7.

### TD-B-13 — `ShiprocketService` duplicated across apps — **Medium**

- **Evidence.** The storefront and `palVarnanDashboard` each carry `config/services.php` blocks and
  a Shiprocket client; [`../18-configuration-and-env.md`](../18-configuration-and-env.md) §3 flags the
  config duplication. `CONFLICTING_EVIDENCE` on byte-for-byte equality (not diffed here).
- **Impact.** Integration fixes must be applied twice and can drift.
- **Suggested fix.** Extract a shared package or at least keep the two files aligned and reviewed
  together.

### TD-B-14 — `.env.example` incomplete — **Medium**

- **Evidence.** `.env` has 56 keys, `.env.example` has 43; 13 keys absent, including all Razorpay
  and Shiprocket keys and `MAIN_URL`, plus the `DB_*` block (present only as commented placeholders).
  `VERIFIED_FROM_SOURCE`. Detail: [`./DEPLOYMENT_AND_CONFIGURATION.md`](./DEPLOYMENT_AND_CONFIGURATION.md) §2.
- **Impact.** A developer following `.env.example` cannot configure payments, shipping, or the
  database.
- **Suggested fix.** Add the missing keys with empty/placeholder values (see
  [`../18-configuration-and-env.md`](../18-configuration-and-env.md) §11).

---

## 3. Severity roll-up

| Severity | Count | IDs |
|---|---|---|
| S1 | 2 | KI-B-01, KI-B-02 |
| S2 | 5 | KI-B-03, KI-B-04 is S1 — see above; KI-B-05, KI-B-06, KI-B-07, KI-B-12 |
| S3 | 5 | KI-B-08 … KI-B-11 |
| S4 | 1 | KI-B-13 |
| High debt | 3 | TD-B-01, TD-B-02, TD-B-03 |
| Medium debt | 9 | TD-B-04, 05, 07, 08, 09, 10, 11, 12, 13, 14 |
| Low debt | 1 | TD-B-06 |

> The wider registers live in [`../20-known-issues.md`](../20-known-issues.md) (BUG-01…105),
> [`../19-incomplete-work.md`](../19-incomplete-work.md) (BW/FW/IW), and
> [`../21-technical-debt.md`](../21-technical-debt.md) (TD-01…30). This file is the cross-cutting
> index, not a superset.

---

## 4. Related documents

- [`../20-known-issues.md`](../20-known-issues.md) — full BUG register with root causes
- [`../21-technical-debt.md`](../21-technical-debt.md) — TD register ranked by future-change cost
- [`../19-incomplete-work.md`](../19-incomplete-work.md) — missing/half-built work
- [`../13-security-audit.md`](../13-security-audit.md) — SEC-numbered view of `KI-B-02`, `KI-B-03`, `KI-B-04`, `KI-B-05`
- [`./PERFORMANCE_AND_RELIABILITY.md`](./PERFORMANCE_AND_RELIABILITY.md) — `KI-B-06`, `KI-B-07` runtime detail
- [`./TESTING_GUIDE.md`](./TESTING_GUIDE.md) — what would catch these
- [`./CHANGE_IMPACT_GUIDE.md`](./CHANGE_IMPACT_GUIDE.md) — blast radius per change type
- [`./SOURCE_COVERAGE_AND_VERIFICATION.md`](./SOURCE_COVERAGE_AND_VERIFICATION.md) — reconciliation record
