# 29 — Remediation Plan

Sequenced so that each phase leaves the application working and makes the next phase safer. Effort figures assume one developer familiar with the codebase.

---

## 1. Principles

1. **Protect money first.** Anything touching payment, stock, or ownership comes before anything cosmetic.
2. **Make it reproducible before refactoring it.** An unreproducible working tree cannot be safely refactored.
3. **Add tests before the risky refactor**, not after.
4. **Ship each phase separately.** A single 4-day mega-PR cannot be reviewed, reverted, or debugged.
5. **Do not rename identifiers.** The misspellings are load-bearing across two applications and 57 migrations.

---

## 2. Phase overview

| Phase | Scope | Effort | Gate |
|---|---|---|---|
| **0** | Make it reproducible | **0.5 d** | working tree committed |
| **1** | Stop the bleeding | **2 d** | checkout completes end-to-end |
| **2** | Close the S1s | **1 d** | no unauthenticated account takeover |
| **3** | Ownership and authorization | **1.5 d** | every write is scoped |
| **4** | Money path correctness | **2 d** | totals verified server-side |
| **5** | Integration reliability | **2 d** | no swallowed failures |
| **6** | Deployability | **2 d** | `composer install && migrate` works |
| **7** | Checkout decomposition | **4 d** | class under 200 lines |
| **8** | Test suite | **4.5 d** | 30 tests, CI green |
| **9** | Frontend cleanup | **3 d** | no dead links |
| **10** | Structural | **3 d** | schema owned by one app |
| | **Total** | **≈25.5 d** | |

Phases 0-2 (**3.5 days**) remove every S1 issue. That is the highest-value window in this document.

---

## 3. Phase 0 — Make it reproducible (0.5 d)

Nothing else is safe until the source is committed. The checkout feature — the whole payment path — exists only in the working tree.

```bash
git add -A
git commit -m "Add checkout, Razorpay integration, and order models"
git checkout -b feature/remediation-phase-1
```

| Item | Action |
|---|---|
| `app/Services/` (incl. `ShiprocketService.php`) | commit |
| 6 order/payment models | commit |
| `thankyou.blade.php` | commit |
| `palVarnan1/` duplicate directory | **delete** — or move outside the repo |
| `.env` | confirm it is git-ignored |

**Gate:** a fresh clone contains every file needed to process an order.

---

## 4. Phase 1 — Stop the bleeding (2 d)

The only S1 that blocks revenue.

### 1a. Add the three missing migrations

```php
// database/migrations/2026_09_01_000001_add_shipping_columns_to_product_orders_table.php
Schema::table('product_orders', function (Blueprint $table) {
    $table->string('billing_state')->nullable()->after('billing_pincode');
    $table->string('shipping_state')->nullable()->after('shipping_pincode');
    $table->boolean('pickup_scheduled')->default(false)->after('shiprocket_status');
});

// database/migrations/2026_09_01_000002_add_product_image_to_product_order_items_table.php
Schema::table('product_order_items', function (Blueprint $table) {
    $table->string('product_image')->nullable()->after('varient_name');
});
```

**Verify against the live database first** — these columns may already exist there from a manual `ALTER TABLE` (the log shows successful writes after the failures). Use `information_schema.columns`; if present, add `Schema::hasColumn()` guards so the migration is idempotent.

Fixes **BUG-04**, DB-01, DB-02. This is **0.5 d**.

### 1b. Install Razorpay properly

```bash
composer require razorpay/razorpay:^2.9
```

Delete the manual `require_once` block at `CheckoutController.php:26-31`. Confirm the lock file, `installed.json`, and `autoload_psr4.php` all contain `razorpay`.

Fixes **BUG-08**, IR-01. **0.5 d.**

### 1c. Rotate credentials and delete the test scripts

```bash
git rm --cached test_apis.php test_services.php
rm test_apis.php test_services.php
```

Rotate: Razorpay key/secret, Shiprocket email/password, MySQL root password.

Fixes **BUG-02**, **BUG-03**, SEC-02, SEC-03, IR-09. **0.5 d.**

### 1d. Hardening the environment

| Change | Value |
|---|---|
| `APP_DEBUG` | `false` |
| `APP_URL` | real host |
| `APP_NAME` | `PalVarnan` |
| DB user | new least-privilege account, not `root` |
| `.env` | confirm git-ignored |

Fixes **BUG-16**, **BUG-17**, SEC-20. **0.5 d.**

**Gate:** a test checkout completes and persists an order.

---

## 5. Phase 2 — Close the S1s (1 d)

### Token-based password reset

Laravel already implements this correctly; the application replaced it with 25 bespoke lines.

```php
// routes/web.php
Route::get('/forgot-password', [AuthController::class, 'showForgotForm'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->middleware('throttle:3,1');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->middleware('throttle:3,1');
```

```php
public function resetPassword(Request $request)
{
    $request->validate([
        'token'    => 'required',
        'email'    => 'required|email',
        'password' => 'required|confirmed|min:8|Password::defaults()',
    ]);

    $status = Password::reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function ($user, $password) {
            $user->forceFill([
                'password'       => Hash::make($password),
                'remember_token' => Str::random(60),
            ])->save();
        }
    );

    return $status === Password::RESET_LINK_SENT
        ? back()->with('status', __($status))
        : back()->withInput($request->only('email'))->withErrors(['email' => __($status)]);
}
```

Add `Password::defaults()` in `AppServiceProvider::boot()`, and build the two missing views.

Also set a real `MAIL_MAILER` — with `log` the link never arrives (BUG-62).

Fixes **BUG-01**, BW-02, BW-03, BW-04. **1 d.**

---

## 6. Phase 3 — Ownership and authorization (1.5 d)

### 3a. Close the middleware gaps

```php
Route::middleware('auth')->group(function () {
    Route::get('/myaccount', [ProfileController::class, 'index'])->name('account.index');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/wishlist/toggle', [WishlistController::class, 'toggle']);
    Route::get('/wishlist', [WishlistController::class, 'index']);
    Route::delete('/wishlist/{id}', [WishlistController::class, 'remove']);
});
```

Fixes **BUG-09, BUG-10, BUG-11, BUG-12**. **0.5 d.**

### 3b. Scope cart and wishlist writes

```php
public function update(Request $request, UpdateCartRequest $validated)
{
    $line = Cart::where('product_varient_id', $validated->validated('product_varient_id'))
        ->where(fn ($q) => $q->where('user_id', Auth::id())
                       ->orWhere('ip_addresscart', $request->ip()))
        ->first();

    abort_if($line === null, 403);

    $line->update(['product_quantity' => $validated->validated('product_quantity')]);

    return response()->json(['success' => 'Cart updated successfully']);
}
```

Convert `GET /cart-remove/{id}` → `DELETE /cart/{id}`, and delete by **line id** with an owner scope, not variant id.

Fixes **BUG-06, BUG-07, BUG-46**. **0.5 d.**

### 3c. Scope address and order access

```php
$address = UserAddress::where('user_id', Auth::id())->findOrFail($id);
```

Same for `GET /checkout/thankyou/{order}`.

### 3d. Add policies

`app/Policies/` does not exist. Start with `ProductOrderPolicy` (owns PII — TD-30) and `UserAddressPolicy`.

### 3e. Add rate limiting

```php
Route::post('/login-store', …)->middleware('throttle:5,1');
Route::post('/register-store', …)->middleware('throttle:5,1');
Route::post('/forgot-password', …)->middleware('throttle:3,1');
Route::post('/contact', …)->middleware('throttle:3,1');
```

Fixes **BUG-13**, SEC-13. **0.5 d.**

**Gate:** no unauthenticated or cross-user write path remains.

---

## 7. Phase 4 — Money path correctness (2 d)

### 4a. Never trust client totals

```php
public function rules(): array
{
    return [
        // …addresses, payment_method, products
        // total, shipping_charge and coupon_discount are intentionally NOT accepted
    ];
}
```

```php
$totals = app(OrderTotalsCalculator::class)->forCart($items, $shippingState);
```

Fixes **BUG-05**, BUG-48. **0.5 d.**

### 4b. Verify the Razorpay amount

```php
if (! $gateway->verifySignature($orderId, $paymentId, $signature)) {
    abort(400, 'Invalid payment signature.');
}

$payment = $gateway->fetchPayment($paymentId);

if ($payment->amount !== $totals->grandTotal * 100) {
    $gateway->refund($paymentId, $payment->amount);   // money back, do not ship
    Log::critical('razorpay.amount_mismatch', [
        'expected' => $totals->grandTotal * 100,
        'received' => $payment->amount,
    ]);
    abort(400);
}
```

Fixes **BUG-22**. **0.5 d.**

### 4c. Idempotency

Add the unique index, then catch the duplicate:

```php
Schema::table('product_orders', fn (Blueprint $t) =>
    $t->unique('razorpay_order_id'));
```

```php
try {
    $order = $this->insertOrder(/* … */);
} catch (QueryException $e) {
    if (! str_contains($e->getMessage(), 'razorpay_order_id')) throw $e;
    return redirect()->route('checkout.thankyou', Order::where('razorpay_order_id', $orderId)->value('id'));
}
```

Fixes **BUG-21**, DB-05. **0.5 d.**

### 4d. Stock safety

```php
$variant = ProductVarient::whereKey($variantId)->lockForUpdate()->firstOrFail();

abort_if(
    $variant->stock()->where('availablestock', '>', 0)
        ->whereRaw('availablestock - salestock >= ?', [$qty])->exists() === false,
    422,
    'Not enough stock.'
);
```

Fixes **BUG-35, BUG-36**, DB-17. **0.5 d.**

**Gate:** the amount charged is computed only on the server, verified against Razorpay, and applied once.

---

## 8. Phase 5 — Integration reliability (2 d)

### 5a. Shiprocket client hardening

Add a `base_url` config key, a 10-second timeout, three retries with backoff, 401 re-auth, and structured logging. Never `verify => false`.

Fixes **BUG-39, BUG-40, BUG-41**. **0.5 d.**

### 5b. Fail loudly

```php
try {
    $shiprocketId = $shipping->createOrder($payload);
    $awb          = $shipping->generateAwb($shiprocketId);
    $shipping->createPickup(['order_id' => $shiprocketId]);   // ← never called today

    $order->update([
        'shiprocket_order_id'   => $shiprocketId,
        'shiprocket_awb'        => $awb['awb_number'] ?? null,
        'shiprocket_shipment_id'=> $awb['shipment_id'] ?? null,   // not the order id
        'shiprocket_status'     => 'PICKUP SCHEDULED',
        'pickup_scheduled'      => true,
    ]);
} catch (ShippingException $e) {
    $order->update(['shiprocket_status' => 'SHIPPING_FAILED']);
    Log::critical('order.shipping_failed', ['order_id' => $order->id, 'error' => $e->getMessage()]);
    // order stays saved; ops is alerted
}
```

Fixes **BUG-17, BUG-18, BUG-20, BUG-38, BUG-66**. **1 d.**

### 5c. Move fulfilment to a queue

```dotenv
QUEUE_CONNECTION=database
```

```php
ShipOrder::dispatch($order->id)->afterCommit();
```

Fixes BUG-20 latency, removes the synchronous HTTP call from the checkout path. **0.5 d.**

**Gate:** no integration failure is silently reported as success.

---

## 9. Phase 6 — Deployability (2 d)

| Task | Fixes |
|---|---|
| Complete `.env.example` with the 8 missing keys | BUG-86, IW-08 |
| Add `shiprocket.base_url`, `timeout`, `token_ttl` to `config/services.php` | BUG-41 |
| Move `env('MAIN_URL')` into config | BUG-27 |
| `APP_TIMEZONE=Asia/Kolkata` | BUG-61, BUG-37 |
| `SESSION_SECURE_COOKIE=true`, `SESSION_ENCRYPT=true` | BUG-63 |
| `LOG_CHANNEL=daily`, `LOG_LEVEL=info` | — |
| Schedule `session:prune` daily | BUG-84 |
| Add CI workflow | BUG-85, TD-22 |
| Document the deploy procedure | IW-07, IW-09 |
| `.gitignore` the committed dashboard `storage/` files | BUG-88 |

**2 d.**

---

## 10. Phase 7 — Checkout decomposition (4 d)

Only after Phase 8's Tier-1 tests exist, or write them alongside.

| Step | Extract | Fixes |
|---|---|---|
| 1 | `OrderTotalsCalculator` | TD-03, TD-11 — the 6 price sites collapse to 1 |
| 2 | `StockManager` | BUG-35, BUG-36 |
| 3 | `RazorpayGateway` (interface) | testability, BUG-22 |
| 4 | `ShiprocketGateway` (interface) | testability |
| 5 | `FulfilmentDispatcher` (queued job) | BUG-18, BUG-20 |
| 6 | `AddressController` | 701-line class |

Target: `CheckoutController` under 200 lines; `placeOrder()` under 40. See [21-technical-debt.md](21-technical-debt.md) §3 and [23-external-services.md](23-external-services.md) §3.5.

**4 d.**

---

## 11. Phase 8 — Test suite (4.5 d)

30 tests across 5 tiers, as specified in [24-testing-strategy.md](24-testing-strategy.md). CI green on every push.

**4.5 d.**

---

## 12. Phase 9 — Frontend cleanup (3 d)

| Task | Fixes |
|---|---|
| Remove one jQuery copy | BUG-54 |
| Rename `boostrap.bundle.min.js` | BUG-74 |
| Add the 3 missing CSRF headers | BUG-55 |
| Handle HTTP 419 globally | BUG-56 |
| Remove 101 dead links | BUG-51 |
| Replace ~700 lines of demo on the homepage | BUG-52 |
| Correct the footer contacts | BUG-53 |
| Delete `welcome.blade.php` and the Vite pipeline | BUG-68, BUG-69, BUG-70 |
| Add `loading="lazy"` and `alt` text | FW-20, FW-21 |
| Fix the `favicon.ico` and the logo path | BUG-72, BUG-73 |
| Add `debounce` to search | BUG-60 |

**3 d.**

---

## 13. Phase 10 — Structural (3 d)

| Task | Fixes |
|---|---|
| Decide schema ownership across the two apps | DB-03, DB-22, IW-01, IW-13 |
| Add FKs on `carts`/`wishlists`/`product_orders.user_id` | DB-04 |
| Add unique indexes | DB-05, DB-06, DB-07 |
| Fix the 3 broken relationships | DB-08, DB-09, DB-10 |
| Fix `roles.user_id` inversion and the wrong item FK | DB-11, DB-12 |
| Add the missing indexes | DB-14 |
| Normalise `country_id` | DB-15 |
| Add a `varient` text column or full-text index | DB-16 |
| Align `$fillable` with the schema | DB-18 |
| Align the `'Order Placed'` status vocabulary with the dashboard | BUG-19 |
| Add a money-loss reconciliation job | IR-10 |
| Drop or document the 21 unused tables | DB-21 |

**3 d.**

---

## 14. Coverage check

| Issue | Phase |
|---|---|
| BUG-01 (tokenless reset) | **2** |
| BUG-02 (live credentials) | **1c** |
| BUG-03 (TLS disabled) | **1c** |
| BUG-04 (checkout fails) | **1a** |
| BUG-05 (client amount) | **4a** |
| BUG-06 (cart IDOR) | **3b** |
| BUG-07 (cart remove) | **3b** |
| BUG-08 (Razorpay lock) | **1b** |
| All S2 (BUG-09…24) | **3, 4, 5** |
| DB-01, DB-02 | **1a** |
| DB-03 | **10** |
| TD-01 (701-line class) | **7** |
| TD-05 (no tests) | **8** |

**All 8 S1 issues are resolved by the end of Phase 2 — 3.5 days.**

---

## 15. What is deliberately deferred

| Item | Why |
|---|---|
| Renaming `varient` → `variant` | Touches the schema, 24 models, two apps, 57 migrations. High risk, zero runtime benefit |
| Mails, notifications, PDFs | Feature work, not defect repair |
| Reviews, compare, coupons, multi-vendor | Present in the schema, absent from the product |
| Rewriting Blade with components | Large refactor; do it after the checkout path is stable |
| `palVarnanDashboard` cleanup | Separate application, separate owner |
| Containerisation | Useful, not urgent on XAMPP-style hosting |

---

## 16. Verification commands

```bash
composer install --no-interaction     # must succeed — verifies Razorpay
php artisan migrate --force           # must apply the 3 new migrations
php artisan route:list --except-vendor | wc -l   # 38 → expect fewer
php artisan test                      # 0 → 30 tests
grep -rn "env(" app/                  # expect 0 matches
grep -rn "wowtheme7\|shop.html" resources/views/   # expect 0 matches
grep -c "jquery" resources/views/layouts/app.blade.php   # expect 1 CDN-free block
```

Plus a manual end-to-end checkout for COD **and** Prepaid, and a SQL reconciliation query returning zero rows:

```sql
SELECT t.razorpay_payment_id FROM product_transactions t
  LEFT JOIN product_orders o ON o.id = t.order_id
 WHERE o.id IS NULL AND t.payment_status = 'Paid';
```

---

## 17. Related documents

- [20-known-issues.md](20-known-issues.md) — the BUG register this plan resolves
- [21-technical-debt.md](21-technical-debt.md) — the TD items and refactor order
- [13-security-audit.md](13-security-audit.md) — SEC-numbered security findings
- [22-database-integrity.md](22-database-integrity.md) — the DB items
- [23-external-services.md](23-external-services.md) — the IR items
- [24-testing-strategy.md](24-testing-strategy.md) — the 30 tests
- [29-remediation-plan.md](29-remediation-plan.md) — this document