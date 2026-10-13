# 23 — External Services

Four outbound integrations, none of them abstracted, none of them monitored.

---

## 1. Inventory

| Service | Purpose | Auth | Code | Status |
|---|---|---|---|---|
| **Razorpay** | Payment gateway | HMAC signature | `CheckoutController.php` (inline) + `vendor/razorpay/` | **BROKEN in a clean deploy** |
| **Shiprocket** | Fulfilment / shipping | HTTP Basic + bearer token | `app/Services/ShiprocketService.php` | Partially working |
| **`MAIN_URL` image host** | Product images | none | `SearchController.php:29` | Breaks under `config:cache` |
| **Mail** | Password reset | — | Laravel `Password` facade | **Non-functional** (`MAIL_MAILER=log`) |
| **Telescope / Pulse** | Monitoring | — | tables only | **Tables present, packages absent** |

**No inbound webhooks** from either payment or shipping provider.

---

## 2. Razorpay

### 2.1 Configuration

```php
'razorpay' => [
    'key'    => env('RAZORPAY_KEY'),
    'secret' => env('RAZORPAY_SECRET'),
],
```

Neither key is in `.env.example`.

### 2.2 The dependency defect — S1

The SDK is physically present:

```
vendor/razorpay/razorpay/          ← directory exists
```

But it is registered **nowhere**:

| Artifact | `razorpay` matches |
|---|---|
| `composer.json` | 0 |
| `composer.lock` | 0 |
| `vendor/composer/installed.json` | 0 |
| `vendor/composer/autoload_psr4.php` | 0 |
| `vendor/composer/autoload_classmap.php` | 0 |

`CheckoutController.php:26-31` papers over this:

```php
if (!class_exists('\Razorpay\Api\Api')) {
    require_once base_path('vendor/razorpay/razorpay/src/Api/Api.php');
    // …plus every class the gateway touches, recursively
}
```

**Consequences:**

| Action | Result |
|---|---|
| `composer install` on a fresh host | `Class "Razorpay\Api\Api" not found` — exactly what `laravel.log` records |
| `composer update` | `vendor/razorpay` is wiped (not a declared dependency) → hard 500 |
| Deleting `vendor/` | Razorpay disappears permanently |
| Git history | the manual block was committed; nobody noticed the SDK is unmanaged |

**Fix:**

```bash
composer require razorpay/razorpay:^2.9
```

then delete `CheckoutController.php:26-31` and `require` the SDK normally. This is BUG-08.

### 2.3 Payment flow

```
POST /place-order
  1. Validate address, payment_method, products
  2. GET  total from the CLIENT request          ← BUG-05
  3. computeRazorpayChecksum(razorpay_order_id, razorpay_payment_id)
  4. POST api.razorpay.com/v1/orders             (amount, currency, receipt, notes?)
  5. Return {order_id, key, amount, …} to Blade
  6. RazorCheckout.open(options)
  7. Handler → POST /place-order with payment ids
  8. compareChecksum() → signature verify
  9. BEGIN: insert order, items, addresses, transaction, log, status
 10. UPDATE stocks
 11. Delete cart lines
 12. Shiprocket createOrder  (inside the try, failure swallowed)
 13. Shiprocket generateAWB
 14. COMMIT
 15. redirect → /thank-you
```

### 2.4 Defects

| ID | Defect | Detail |
|---|---|---|
| BUG-05 | **Client controls the charged amount** | the total comes from `$request->total`, not the server |
| BUG-48 | Two total formulas disagree | `:193` floors at `1`, `:288` does not |
| BUG-47 | `order_number` uses `uniqid()` | no uniqueness guarantee; `uniqid()` is time-based and guessable |
| BUG-22 | Amount never re-verified against Razorpay | only the signature is checked, so a *valid* signature on a *different* amount is not detected |
| BUG-21 | No idempotency | double-click ⇒ two orders, two payments |
| BUG-66 | `pickup_scheduled` never written | column exists in `$fillable` |
| — | **No webhook** | a payment captured while the app is down has no path to the local DB |
| — | **No refund** | any failure after capture requires manual intervention |
| — | `notes` payload | **not sent** in current source — no PII leak there, but also no reference |

**The amount-verification gap is the sharpest risk.** `compareChecksum` proves the *response* came from Razorpay. It does not prove the amount matches what the shopper agreed to pay. Re-fetching the payment via `Payment::fetch($id)` and asserting `amount === $computedTotal` closes it.

### 2.5 Corrected order

```php
// 1. Compute server-side only — never read $request->total
$totals = app(OrderTotalsCalculator::class)->forCart($userId);

// 2. Create the Razorpay order
$rzp = $gateway->createOrder($totals->grandTotal, $orderNumber);

// 3. On callback: verify the signature
if (! $gateway->verifySignature($orderId, $paymentId, $signature)) abort(400);

// 4. AND verify the amount independently
if ($gateway->fetchPayment($paymentId)->amount !== $totals->grandTotal * 100) {
    // Refund and abort — do not ship
}

// 5. Idempotency: UNIQUE(razorpay_order_id) on product_orders
// 6. Insert the order BEFORE dispatching fulfilment
// 7. Dispatch fulfilment to a queue; record its failure
```

---

## 3. Shiprocket

### 3.1 Token flow — VERIFIED

```
getToken()                                       ShiprocketService.php
  Cache::remember('shiprocket_token', 8h, fn)
    POST /auth
      usernme  : config('services.shiprocket.email')
      password : config('services.shiprocket.password')
    → {token, expires_in}
  401 or curl 28 → cache forget + 1 retry
  else          → cached (8h)
```

`expires_in` from Shiprocket is **ignored**; the 8-hour TTL is hard-coded. If Shiprocket shortens the real lifetime, cached tokens start failing — the 401-retry path covers it, so this degrades to an extra login rather than an outage.

### 3.2 Methods (11) and usage

| Method | Line | Called from | Used |
|---|---|---|---|
| `getToken()` | 15 | internally | yes |
| `getPickupLocations()` | 36 | — | **no** |
| `getShippingServices()` | 45 | `getShiprocketCouriers()` | yes |
| `generateManifest()` | 60 | — | **no** |
| `generateAWB()` | 73 | `CheckoutController` | yes |
| `createOrder()` | 86 | `CheckoutController` | yes |
| `getOrderDetail()` | 110 | — | **no** |
| `cancelOrder()` | 123 | — | **no** |
| `createPickup()` | 133 | **nothing** | **no** |
| `printLabel()` | 146 | — | **no** |
| `printInvoice()` | 159 | — | **no** |
| `trackAWB()` | 172 | — | **no** |

7 of 11 are unused. **`createPickup()` (`generatePickup` in the controller's naming) has no caller** — orders are created and labels generated, but pickup is never scheduled, so nothing gets collected (BUG-18).

### 3.3 Defects

| ID | Defect | Detail |
|---|---|---|
| BUG-41 | `SHIPROCKET_BASE_URL` in `.env` is never read | `config/services.php` has no `base_url` key; the default is always used |
| BUG-39 | **No timeout, no retry** | `cURL error 28` appears in `laravel.log` — the request hangs until PHP's default |
| BUG-20 | Failures swallowed | `CheckoutController.php:525-527` catches and continues; the shopper sees a successful order |
| BUG-18 | Pickup never scheduled | `generatePickup()` uncalled |
| BUG-38 | `shiprocket_shipment_id` receives the **order id**, not an AWB | wrong value stored |
| BUG-40 | No 401 re-auth on any call except `getToken()` | an expired mid-session token fails the whole checkout |
| — | No webhook | `trackAWB()` exists but is never called; status depends on the dashboard's scheduled sync |
| — | Service duplicated verbatim in the dashboard | every fix applied twice |

### 3.4 `placeOrder()` field mapping — reviewed

| Shiprocket field | Source | Correct |
|---|---|---|
| `order_id` | `order_number` (`uniqid()`) | acceptable |
| `order_date` | `date('Y-m-d H:i:s')` | **UTC, not IST** — BUG-61 |
| `pickup_location_name` | `config(...pickup_location)` | yes |
| `pickup_pincode` | `config(...pickup_pincode)` | `641006` — Coimbatore; hard-coded assumption |
| `billing_*` | `user_address` | yes |
| `shipping_*` | `user_address` | yes |
| `line_items[].sku` | `productstocks.sku` | yes |
| `line_items[].name` | `products.product_name` | yes |
| `line_items[].units` | `product_quantity` | yes |
| `line_items[].selling_price` | `offer_price ?? product_price` | **uses the client-influenced path** |
| `line_items[].weight` | `weight` | two different rounding heuristics |
| `line_items[].length/breadth/height` | `0` | **always zero** — courier dimensional weight is wrong |
| `payment_method` | `COD`/`Prepaid` | yes |
| `transaction_fee` | `COD` only | yes |

**Dimensional fields hard-coded to 0** means Shiprocket computes shipping from billable weight only. This under-charges shipping on bulky goods and is invisible until a reconciliation report.

### 3.5 Corrected client

```php
public function __construct(private readonly ?string $baseUrl = null) {}

private function client(): \GuzzleHttp\Client
{
    return new \GuzzleHttp\Client([
        'base_uri' => $this->baseUrl ?? config('services.shiprocket.base_url'),
        'auth'     => [config('services.shiprocket.email'), config('services.shiprocket.password')],
        'timeout'  => (int) config('services.shiprocket.timeout', 10),
        'connect_timeout' => 5,
        'http_errors' => false,
        'verify'   => true,          // never false
        'headers'  => ['Accept' => 'application/json'],
    ]);
}

public function request(string $method, string $path, array $json = []): array
{
    for ($attempt = 1; $attempt <= 3; $attempt++) {
        try {
            $response = $this->client()->request($method, $path, ['json' => $json]);
        } catch (ConnectException) {
            if ($attempt === 3) throw new ShiprocketUnavailable();
            usleep(200_000 * $attempt);
            continue;
        }

        if ($response->getStatusCode() === 401) {
            Cache::forget('shiprocket_token');
            continue;                       // getToken() re-authenticates
        }

        $body = json_decode((string) $response->getBody(), true) ?? [];
        if ($response->getStatusCode() >= 400) {
            Log::warning('shiprocket.error', ['path' => $path, 'status' => $response->getStatusCode(), 'body' => $body]);
            throw new ShiprocketFailed($body['message'] ?? 'Shiprocket request failed');
        }
        return $body;
    }
    throw new ShiprocketUnavailable();
}
```

---

## 4. `MAIN_URL` image host

```php
// SearchController.php:29
$baseImageUrl = rtrim(env('MAIN_URL'), '/') . '/images/';
```

`.env` → `MAIN_URL=http://127.0.0.1:8001/`

Two defects:

1. **`env()` outside `config/`** returns `null` once `config:cache` is set ⇒ `"/images/"` ⇒ every search-result image is broken (BUG-27).
2. **Hard-coded to loopback.** On any host that is not the dev machine, the URL is unreachable.

**Fix:** move to `config('services.main_url')` and store a real public host, or better, serve images from this app and drop `MAIN_URL` entirely.

---

## 5. Mail

```
MAIL_MAILER=log
```

`AuthController::forgotPassword()` calls `Password::sendResetLink()`. The message is written to `storage/logs/laravel.log` and **never sent** (BUG-62).

Compounding this: `AuthController::resetPassword()` accepts a new password **with no token** (BUG-01). So the feature is doubly broken — the link never arrives, and if one did, it would not be required.

**Fix:** `MAIL_MAILER=smtp` with real credentials, plus a genuine token-verifying reset route.

---

## 6. Monitoring — effectively absent

| Signal | Present? |
|---|---|
| Telescope | tables present, **package absent** |
| Pulse | not installed |
| `health` route | none |
| Shiprocket/Razorpay alerting | none |
| Exception alerting | none — `laravel.log` is read by nobody |
| Money-loss detection | none |

**The highest-value monitoring addition is a daily reconciliation job:**

```sql
-- Payments captured upstream with no local order
SELECT t.razorpay_payment_id, t.amount, t.created_at
  FROM product_transactions t
  LEFT JOIN product_orders o ON o.id = t.order_id
 WHERE o.id IS NULL AND t.payment_status = 'Paid';
```

Empty result = healthy. Non-empty = money received, nothing shipped. That query catches BUG-04, BUG-20, BUG-21, and BUG-22 in one sweep.

---

## 7. Integration risk summary

| ID | Risk | Likelihood | Impact |
|---|---|---|---|
| IR-01 | Razorpay SDK wiped by any dependency operation | **certain** | total payment outage |
| IR-02 | Checkout 500s on missing columns | **certain** until migrated | payment captured, order lost |
| IR-03 | Shiprocket timeout hangs the request | high | checkout stall, no timeout |
| IR-04 | Shiprocket failure reported as success | high | orders created that will never ship |
| IR-05 | No pickup ⇒ nothing collected | high | every order stuck |
| IR-06 | Status drift between the apps | high | admin shows the wrong state |
| IR-07 | Amount mismatch not detected | medium | under- or over-charge |
| IR-08 | No webhook ⇒ no recovery after downtime | medium | lost payments |
| IR-09 | Credentials in web-reachable scripts | **certain** | account takeover |
| IR-10 | No reconciliation ⇒ silent money loss | **certain** | undetected indefinitely |

**IR-01, IR-02, and IR-09 must be resolved before any other integration work.** See [29-remediation-plan.md](29-remediation-plan.md).

---

## 8. Related documents

- [08-business-logic.md](08-business-logic.md) — order flow logic
- [09-dependencies.md](09-dependencies.md) — the Razorpay defect in detail
- [12-services-and-integrations.md](12-services-and-integrations.md) — method-level reference
- [18-configuration-and-env.md](18-configuration-and-env.md) — all integration env keys
- [22-database-integrity.md](22-database-integrity.md) — schema prerequisites
- [29-remediation-plan.md](29-remediation-plan.md) — the fix sequence