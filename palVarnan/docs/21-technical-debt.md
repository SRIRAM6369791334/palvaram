# 21 — Technical Debt

---

## 1. What "debt" means here

Debt is cost **inherited by the next change** — not a bug, not a missing feature. A bug gets fixed and closes. Debt keeps billing until it is paid down, and it makes every subsequent fix more expensive than it should be.

This document ranks debt by the multiplier it places on future work.

---

## 2. Debt register

| ID | Debt | Cost it imposes | Interest |
|---|---|---|---|
| TD-01 | **`CheckoutController.php` is 701 lines / 28 275 B** | Every checkout change touches one giant class | **Very high** |
| TD-02 | **No domain migrations in this app** | Schema cannot be version-controlled, diffed, or rolled back | **Very high** |
| TD-03 | **Pricing computed in 6 places, 3 shapes** | A price change must be applied 6 times correctly | **Very high** |
| TD-04 | **No authorization layer** — no policies, no middleware | Every new endpoint re-litigates access control | **Very high** |
| TD-05 | **Zero automated tests** (2 placeholder files) | Every fix risks regression; no safety net | **Very high** |
| TD-06 | **All JS inline in Blade** | No caching, minification, linting, or unit testing of UI logic | High |
| TD-07 | **No Blade components** | Header, footer, product card copy-pasted | High |
| TD-08 | **Frontend built for "multivendor"; backend is single-vendor** | Dead links everywhere; intent permanently unclear | High |
| TD-09 | **`country_id` is `int` on `products`, CSV on `categories`/`brands`** | Every country query needs bespoke logic; unindexable | High |
| TD-10 | **Misspelled identifiers throughout** (`varient`, `product_varient`, `ip_addresscart`) | Muscle memory, grep failures, permanent mental overhead | Medium |
| TD-11 | **No DTOs/interfaces; arrays + `floatval()` + `??`** | No type safety; runtime surprises | Medium |
| TD-12 | **Money as PHP floats** | Rounding drift; audit exposure | Medium |
| TD-13 | **No Form Requests** | Validation inline in controllers; not reusable | Medium |
| TD-14 | **Errors return `$e->getMessage()` to clients** | Leaks internals; encourages copy-paste | Medium |
| TD-15 | **`ShiprocketService` duplicated verbatim across 2 apps** | Every integration fix applied twice | Medium |
| TD-16 | **Guest identity resolved 6 different ways** | Cart bugs are intermittent and hard to reproduce | Medium |
| TD-17 | **No queue, no jobs, no schedule** | Everything is synchronous and manual | Medium |
| TD-18 | **9.4 MB of vendored theme assets in git** | Slow clones, merge conflicts on binary files | Medium |
| TD-19 | **Git history is one commit; the checkout feature is uncommitted** | No bisect, no blame, no rollback | Medium |
| TD-20 | **`palVarnan1/` duplicate app directory** | Edits may land in the wrong copy | Medium |
| TD-21 | **`env()` called outside `config/`** | Breaks under `config:cache` | Medium |
| TD-22 | **No CI** | Nothing is verified on commit | Medium |
| TD-23 | **`.env.example` omits all commerce keys** | New setups cannot work | Medium |
| TD-24 | **Telescope/RBAC tables with no corresponding packages** | Schema implies features that do not exist | Low |
| TD-25 | **3 duplicate relationships** | Confusion about which is canonical | Low |
| TD-26 | **`created_at`/`updated_at` in `$fillable`** | Timestamp forging | Low |
| TD-27 | **`welcome.blade.php` (83 KB) unreachable** | Repo noise | Low |
| TD-28 | **Dead Vite/Tailwind pipeline** | False confidence; broken `composer run dev` | Low |
| TD-29 | **No `README`** — still the Laravel default | No onboarding documentation | Low |
| TD-30 | **No policies on `ProductOrder`** — PII is serialisable | Privacy exposure by default | Medium |

---

## 3. TD-01 — `CheckoutController.php`

```
app/Http/Controllers/CheckoutController.php   701 lines, 28 275 bytes
```

**25% of all application code** (`app/` totals ~75 KB across 39 files).

| Method | Lines | Responsibility |
|---|---|---|
| `index()` | 38-84 | Render checkout, compute subtotal/GST/weight |
| `getShiprocketCouriers()` | 89-167 | External HTTP quote, rate formatting, sorting |
| `preparePayment()` | 172-220 | Razorpay order creation |
| `placeOrder()` | 225-530 | **Validate, verify payment, 6-table transaction, stock, cart clear, Shiprocket create, AWB** |
| `thankyou()` | ~533-548 | Confirmation page |
| `storeAddress()` | 553-… | Address create |
| `updateAddress()` | … | Address update |
| `destroyAddress()` | … | Address delete |
| `getShippingCharge()` | 638-654 | State charge (dead) |
| `getCitiesByState()` | 659-670 | District cascade (dead) |
| `getCartItems()` | ~688 | Private cart loader |

### Why this is the top debt item

`placeOrder()` alone is **305 lines and 5 responsibilities**:
1. Request validation
2. Payment signature verification
3. Total recomputation
4. A 6-table transaction
5. Stock mutation
6. Cart clearing
7. External API integration

It cannot be unit-tested meaningfully, cannot be reused by an admin-created order, and every change risks the money path. **A bug in this class is BUG-01…05 territory.**

### Target decomposition

```php
app/Services/Payment/RazorpayGateway.php       // createOrder, verifySignature, refund
app/Services/Order/OrderPlacementService.php   // validation + transaction
app/Services/Order/OrderTotalsCalculator.php   // subtotal, GST, shipping, total
app/Services/Inventory/StockManager.php        // deduct, restore, reserve
app/Services/Shipping/ShiprocketGateway.php    // wrapper around ShiprocketService
app/Services/Fulfilment/FulfilmentDispatcher.php // createOrder + AWB + pickup (queued)
app/Http/Controllers/Checkout/CheckoutPageController.php
app/Http/Controllers/Checkout/PaymentController.php
app/Http/Controllers/Checkout/OrderController.php
app/Http/Controllers/Checkout/AddressController.php
app/Http/Requests/PlaceOrderRequest.php
```

Order of work: extract `OrderTotalsCalculator` first (fixes TD-03 as a side effect), then `StockManager`, then the payment gateway, then dispatch fulfilment to a queue.

---

## 4. TD-03 — Pricing duplication

Six implementations, three different shapes — verified against source:

| Site | Expression | `?? 0` guard |
|---|---|---|
| `AppServiceProvider.php:41-43` | `($c->variant->offer_price ?? 0) * $c->product_quantity` | yes |
| `CartController.php:26-28` | `$item->variant->offer_price * $item->product_quantity` | **no** |
| `CartController.php:107-110` | `($item->variant->offer_price ?? 0) * …` | yes |
| `CheckoutController.php:60` | `$item->variant->offer_price ?? $item->product->product_price ?? 0` | yes |
| `CheckoutController.php:182` | same | yes |
| `ProfileController.php:184` (via `ProfileController.php:88`) | same | yes |

Plus two total formulas:

| Site | Formula | Floor |
|---|---|---|
| `CheckoutController.php:193` | `max((subtotal+gst+shipping)-discount, 1)` | `1` |
| `CheckoutController.php:288` | `(subtotal+gst+shipping)-discount` | **none** |
| `checkout.blade.php:626` | `max((base+gst+shipping)-discount, 0)` | `0` |

**Cost of the debt:** a pricing change must be applied in 9 places. They have already diverged. `product_price` is referenced at 4 sites and **does not exist as a column** — the chain always falls through to `offer_price ?? 0`, and site 2 has no guard at all.

**Fix:** one `OrderTotalsCalculator` with a single `unitPrice(Variant, Product)` and `totals(Collection $cartItems, …)`. Both the controller and Blade call it. Also cast money columns to `decimal:2` (TD-12).

---

## 5. TD-05 — No tests

```
tests/TestCase.php
tests/Feature/ExampleTest.php     ← asserts GET / returns 200 — the Laravel default
tests/Unit/ExampleTest.php        ← asserts true === true
```

**Zero application tests.** `phpunit.xml` is stock.

### The tests that should exist first

| Test | Catches |
|---|---|
| `PlaceOrderTest` — full order persists, stock decrements, cart clears | BUG-04, BUG-35 |
| `PasswordResetTest` — a token is required | BUG-01 |
| `CartAuthorizationTest` — user A cannot modify user B's cart | BUG-06, BUG-07, BUG-12 |
| `OrderTotalTest` — server ignores client-supplied totals | BUG-05, BUG-48 |
| `GuestCartMergeTest` | BUG-43 |
| `SearchTest` — brand and category results produce valid URLs | BUG-25, BUG-26 |
| `IdempotencyTest` — a double submit creates one order | BUG-21 |

Target: 15-20 tests covering the money path and the authz paths. That is roughly **2 days** and would have caught the S1s.

---

## 6. TD-09 — `country_id` type conflict

| Table | Type | Semantics |
|---|---|---|
| `products` | `int` | one country, or `NULL` for global |
| `categories`, `brands`, `sub_categories` | `varchar` CSV | many countries |

Hence `FIND_IN_SET(?, country_id)` at 4 sites — **unindexable by construction**.

**Fix:** normalise to a pivot table (`countryables`), or split into `primary_country_id` (indexed int) + `other_country_ids`. Either eliminates the `FIND_IN_SET` scans.

---

## 7. TD-10 — Misspelled identifiers

| Actual | Intended | Where |
|---|---|---|
| `product_varient` | `product_variant` | table name — 24 model files |
| `varient` | `variant` | `product_varient.varient` |
| `product_varient_id` | `product_variant_id` | `carts`, `wishlists`, `product_slots` |
| `product_variant_id` | — | `product_order_items` (**inconsistent with the others**) |
| `pro_ver_id` | `product_varient_id` | `productstocks` |
| `ip_addresscart` | `ip_address_cart` | `carts` |
| `availablestock`, `salestock` | `available_stock`, `sale_stock` | `productstocks` |
| `boostrap.bundle.min.js` | `bootstrap…` | filename |
| `productvari()` | `variants()` | `Product.php` |

**This is why `SearchController` searches an integer column** — nobody noticed `varient` was a typo when writing the `LIKE`.

**Recommendation:** **do not rename now.** The blast radius spans the schema, 24 models, two apps, and the dashboard's 57 migrations. Instead: standardise on the existing spellings going forward, document them in a glossary (see [28-glossary.md](28-glossary.md)), and add a commented alias where a clean name is needed for new code.

---

## 8. TD-05 / TD-22 — Process debt

| Gap | Consequence |
|---|---|
| No CI | Nothing verifies a change |
| One commit | No `git bisect`, no `git blame`, no rollback |
| The checkout feature is uncommitted | A fresh clone **cannot process an order** |
| `palVarnan1/` duplicate directory | Edits may land in the wrong copy |
| Dashboard commits `storage/framework/sessions` and `views` | Merge noise, cache churn |
| No `README` | Nobody can onboard |

**First action:** commit the untracked source. Everything else in this document is harder to reason about while the working tree is unreproducible.

---

## 9. Debt by cost

| Cost | Items | Recommendation |
|---|---|---|
| **Very high** | TD-01, 02, 03, 04, 05 | Fix before any feature work |
| **High** | TD-06, 07, 08, 09 | Fix during the checkout refactor |
| **Medium** | TD-10…23, 30 | Fix opportunistically |
| **Low** | TD-24…29 | Batch into one cleanup pass |

---

## 10. Suggested refactor sequence

Each step is independently valuable and leaves the app working.

1. **Commit the working tree.** Removes TD-19, TD-20 partly.
2. **Add the three missing migrations.** Removes BUG-04 — the only S1 that blocks revenue.
3. **Delete `test_apis.php` / `test_services.php`; rotate credentials.** Removes BUG-02, BUG-03.
4. **Add `auth` middleware to the 6 unprotected routes.** Removes BUG-09…12.
5. **Scope cart/wishlist writes to the owner; move cart-remove to `DELETE`.** Removes BUG-06, BUG-07, BUG-12.
6. **Recompute totals server-side; ignore client money fields.** Removes BUG-05, BUG-48.
7. **Write the 7 tests in §5.** Now refactoring is safe.
8. **Extract `OrderTotalsCalculator`.** Removes TD-03, TD-11.
9. **Extract `StockManager` with `lockForUpdate()`.** Removes BUG-35, BUG-36.
10. **Extract `RazorpayGateway`; verify amount + receipt.** Removes BUG-22.
11. **Dispatch fulfilment to a queue; wire `generatePickup()`.** Removes BUG-18, BUG-20.
12. **Align `'Order Placed'` with the dashboard's status vocabulary.** Removes BUG-19.
13. **Add CI.** Prevents recurrence of TD-05.
14. **Then** the frontend pass: components, JS extraction, template pruning.

---

## 11. Related documents

- [08-business-logic.md](08-business-logic.md) — the duplicated rules themselves
- [11-models-and-relationships.md](11-models-and-relationships.md) — model-level debt
- [20-known-issues.md](20-known-issues.md) — the bug register
- [19-incomplete-work.md](19-incomplete-work.md) — missing work
- [29-remediation-plan.md](29-remediation-plan.md) — the full sequenced plan