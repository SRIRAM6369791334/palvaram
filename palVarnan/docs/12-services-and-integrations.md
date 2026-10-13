# 12 — Services and Integrations

One service class, two external APIs, and no abstraction layer.

---

## 1. Service inventory

```
app/Services/
└── ShiprocketService.php   200 lines
```

**That is the entire service layer.** No `PaymentService`, `OrderService`, `PricingService`, `InventoryService`, or `ShippingService`. All of that logic lives inline in `CheckoutController.php` (701 lines).

| Service expected | Present? | Logic lives in |
|---|---|---|
| Payment (Razorpay) | **No** | `CheckoutController::preparePayment()` `:172-220` |
| Order placement | **No** | `CheckoutController::placeOrder()` `:225-533` |
| Inventory / stock | **No** | `CheckoutController::placeOrder()` `:402-415` |
| Address book | **No** | `CheckoutController` `:553-633` |
| Pricing / cart totals | **No** | Recomputed twice, slightly differently — see §4.2 |
| Courier quoting | Partially | Wrapped by `ShiprocketService::checkServiceability()` |
| Notification / email | **No** | — |
| Dashboard admin | **No** | Sibling app |
| Webhook receiver | **No** | Status arrives only via the dashboard's scheduler |

---

## 2. `ShiprocketService` — full reference

### 2.1 Authentication

```php
public function getToken()                                          // :25
{
    $email = config('services.shiprocket.email');
    $cacheKey = 'shiprocket_token_' . md5($email ?? 'default');

    return Cache::remember($cacheKey, 8 * 60 * 60, function () {
        $response = Http::post("{$this->baseUrl}/auth/login", [
            'email'    => config('services.shiprocket.email'),
            'password' => config('services.shiprocket.password'),
        ]);
        if ($response->successful()) {
            Log::info('Shiprocket Login Successful');
            return $response->json('token');
        }
        Log::error('Shiprocket Login Failed', [...]);
        throw new \Exception('Shiprocket Authentication Failed: ' . $response->body());
    });
}
```

| Aspect | Value | Assessment |
|---|---|---|
| Protocol | `POST /auth/login` | Correct (Shiprocket v1 external API) |
| Credentials source | `config/services.php` | Correct — no secrets in source |
| Token cache | `Cache::remember`, 8 hours | Reasonable; Shiprocket tokens last 10 days |
| Cache driver | `.env` → `CACHE_STORE=file` | **Multi-server hazard** — §5 |
| Cache key | `md5($email)` | Poor; a fixed key or token hash is cleaner |
| 401 handling | **None** | Defect — §3 |
| TLS | Laravel `Http` default | Correct; verification on |
| Timeout | not set (30 s default) | Defect |
| Retries | none | Defect |

**Log evidence:** `Shiprocket Login Successful` present, alongside 8 successful serviceability quotes. `:74` also logs `Shiprocket Serviceability API Error` for failed lookups.

### 2.2 HTTP client

```php
protected function client() { return Http::withToken($this->getToken())->acceptJson(); }   // :52-55
```

**Synchronous, blocking, no retries, no timeouts.** Every method returns `$response->json()` — the raw decoded body, **with no success/failure discrimination**. Callers must inspect the body themselves:

```php
// CheckoutController.php:500
if (isset($shipResponse['order_id']) && isset($shipResponse['shipment_id'])) { … }
```

This call site **does** guard correctly. `assignAWB()` is also guarded at `:515` (`status == 200`). The service itself still hands raw bodies to any future caller with no safety net.

### 2.3 Method table

| # | Method | Line | HTTP | Endpoint | Called from | Status |
|---|---|---|---|---|---|---|
| 1 | `getToken()` | 25 | POST | `/auth/login` | internal | **Working** |
| 2 | `checkServiceability()` | 63 | GET | `/courier/serviceability` | `getShiprocketCouriers()` | **Working** |
| 3 | `createOrder()` | 85 | POST | `/orders/create/adhoc` | `placeOrder()` `:498` | **Working** |
| 4 | `assignAWB()` | 106 | POST | `/courier/assign/awb` | `placeOrder()` `:510` | Working |
| 5 | `generatePickup()` | 119 | POST | `/courier/generate/pickup` | — | **DEAD** |
| 6 | `generateManifest()` | 132 | POST | `/manifests/generate` | — | **DEAD** |
| 7 | `printManifest()` | 145 | POST | `/manifests/print` | — | **DEAD** |
| 8 | `generateLabel()` | 158 | POST | `/courier/generate/label` | — | **DEAD** |
| 9 | `printInvoice()` | 171 | POST | `/orders/print/invoice` | — | **DEAD** |
| 10 | `trackAWB()` | 184 | GET | `/courier/track/awb/{code}` | — | **DEAD** |
| 11 | `getPickupLocations()` | 194 | GET | `/settings/company/pickup` | — | **DEAD** |

**7 of 11 methods are unused.** The operational consequence is serious: **`generatePickup()` is never called, so orders exist on Shiprocket but are never scheduled for collection** (SR-01).

### 2.4 Error handling

Three of eleven methods log on failure — `getToken` `:41-44`, `checkServiceability` `:69-74`, `createOrder` `:90-95`. **Eight do not**: `assignAWB`, `generatePickup`, `generateManifest`, `printManifest`, `generateLabel`, `printInvoice`, `trackAWB`, `getPickupLocations` all return the error body silently.

Log entries written:

- `Shiprocket Login Successful` (info)
- `Shiprocket Login Failed` (error) — includes the response body
- `Shiprocket Serviceability API Error` (error) — includes params (pincode, weight)
- `Shiprocket API Error Response` (error) — includes the full body
- `Creating Shiprocket order` (info, `:497`) — **logs the entire payload**, including customer name, address, pincode, phone and email

That last one is the largest log exposure: every order writes a full customer address record to disk in plaintext.

---

## 3. Fulfilment workflow — what actually happens

```
CheckoutController::placeOrder()  :432-527   (after DB::commit at :421)
│
├─ Build payload:
│    order_id:        'ORD-…'  (the human-readable order_number)
│    order_date, pickup_location: config(…pickup_location, 'Home')
│    billing_customer_name / billing_last_name  ← split from shipping_name on the last space
│    billing_address:   shipping_door_no . ' ' . shipping_street
│    billing_address_2: shipping_area ?? '-'
│    billing_city / billing_state / billing_pincode  ← all from shipping_* request fields
│    billing_country:   'India'   (hard-coded)
│    billing_email / billing_phone  ← from shipping_email / shipping_phone
│    shipping_is_billing: true
│    order_items[]:  name, sku (variant->sku ?? product->slug ?? 'SKU001'),
│                    units, selling_price, discount: 0, tax, hsn: 441122
│    payment_method:  'Online Payment' → 'Prepaid', else 'COD'
│    shipping_charges, total_discount, sub_total
│    length / breadth / height / weight   ← derived below
│
├─ $shipResponse = createOrder($payload)
├─ if (order_id AND shipment_id present):
│      order->update(shiprocket_order_id, shiprocket_shipment_id, shiprocket_status)
│
└─ if ($request->selected_courier_id):
       $assignResponse = assignAWB(['shipment_id' => $shipmentId, 'courier_id' => …])
       if (status == 200): order->update(awb_code, courier_name, shiprocket_status = 'AWB Assigned')
```

**Good news, verified:** the shipment id is taken from `$shipResponse['shipment_id']` and passed as `shipment_id` to `assignAWB` (`:501`, `:511`). The order-id/shipment-id conflation is **not** present in this code. The payment-method mapping at `:485` is also correct.

### Defects in this flow

| # | Defect | Impact |
|---|---|---|
| SR-01 | **`generatePickup()` never called** | **Orders sit uncollected.** Needs a manual dashboard action per order |
| SR-02 | **No `manifest`, `label` or `invoice` call** | Nothing is ever picked, labelled or printed through the app |
| SR-03 | **`selected_courier_id` is unvalidated** and may be absent | Many orders ship with no AWB at all |
| SR-04 | **Shipping failures are swallowed** — `catch (\Exception)` at `:525` only logs | The customer sees "Order placed successfully!" while the shipment silently does not exist |
| SR-05 | **Weight and dimensions are fictional** — see below | Shiprocket prices on data that does not exist |
| SR-06 | **Whole payload logged at info level** `:497` | Customer address, pincode, phone and email written to disk on every order |
| SR-07 | `shipping_is_billing: true` — the storefront has one address form | Shipping to a different address is impossible |
| SR-08 | **`billing_state` is read from `$request->shipping_state`** but `product_orders.billing_state` is written separately — see §3.1 | — |
| SR-09 | **No webhook receiver** | Status arrives only via the dashboard's scheduler, which **excludes `'Order Placed'`** |
| SR-10 | `transaction_charges: 0` | COD handling fees are never declared to Shiprocket |

### 3.1 Weight and dimensions — verified defect

`placeOrder()` `:453-463` reads:

```php
$itemWeight   = (float) ($item->variant->weight   ?? 0.5);
$maxLength    = max($maxLength,    (float) ($item->variant->length   ?? 10));
$maxBreadth   = max($maxBreadth,   (float) ($item->variant->breadth  ?? 10));
$maxHeight    = max($maxHeight,    (float) ($item->variant->height   ?? 10));
```

The `??` fallbacks make this look handled, but **`product_varient` has no `weight`, `length`, `breadth`, or `height` columns** — verified against the DDL. The model has no such attributes either. So every access is `null`, and every shipment is declared as:

| Field | Value sent, every time |
|---|---|
| `weight` | `0.5` × quantity |
| `length` | `10` |
| `breadth` | `10` |
| `height` | `10` |

Weight floors at `0.5` kg, so a single watch and a single television both ship as a 10×10×10 cm, 0.5 kg parcel. **Courier charges are wrong in both directions**, and volumetric-weight rules may not be satisfied. This is a real business defect, not a cosmetic one.

---

## 4. Razorpay integration

### 4.1 Loading — BROKEN

```php
// CheckoutController.php:26-31 — file scope, runs on every request
if (!class_exists('Razorpay\Api\Api')) {
    $razorpayAutoload = base_path('vendor/razorpay/razorpay/Razorpay.php');
    if (file_exists($razorpayAutoload)) {
        require_once $razorpayAutoload;
    }
}
```

`razorpay/razorpay` is **not in `composer.lock`**, not in `vendor/composer/installed.json`, and not in `autoload_psr4.php`. The package directory exists on disk but is unmanaged by Composer. See [09-dependencies.md](09-dependencies.md) §3.

Log evidence: `Class "Razorpay\Api\Api" not found` — **2 occurrences**. Because of the `file_exists` guard, a fresh install fails *silently* here and dies later at `new Api(...)`.

### 4.2 Order creation — two price formulas

`preparePayment()` `:172-220`:

```php
$cartItems = $this->getCartItems();
if ($cartItems->isEmpty()) { return error 400; }

foreach ($cartItems as $item) {
    $price      = $item->variant->offer_price ?? $item->product->product_price ?? 0;
    $quantity   = $item->product_quantity;
    $subtotal  += $price * $quantity;
    $gstRate    = $item->variant->product_gst ?? 0;
    $gstAmount += ($price * $quantity) * ($gstRate / 100);
}

$shipping = floatval($request->shipping_charge ?? 0);
$discount = floatval($request->coupon_discount ?? 0);
$total    = max(($subtotal + $gstAmount + $shipping) - $discount, 1);

$razorpayOrder = $api->order->create([
    'receipt' => 'rcpt_' . uniqid(),
    'amount'  => (int) round($total * 100),
    'currency' => 'INR',
]);
```

**The subtotal and GST are recomputed server-side from the cart — the client does not control them.** That part is correct. The exposure is narrower and more specific than "the client decides the price":

| Aspect | Value | Assessment |
|---|---|---|
| Subtotal + GST | Recomputed from `getCartItems()` | **Correct** |
| `shipping_charge` | `floatval($request->shipping_charge)` | **Client-controlled, unbounded, not validated** |
| `coupon_discount` | `floatval($request->coupon_discount)` | **Client-controlled, unbounded, no coupon lookup** |
| Floor | `max(…, 1)` | **A negative discount reduces the charge; a large discount makes it ₹1** |
| `coupons` table | **Never queried** | The model exists; no code path reads it. So `coupon_discount` is not a discount — it is a client-supplied number |
| `notes` payload | **Absent** | Current source sends only `receipt`, `amount`, `currency`. **No PII reaches Razorpay from this call** |
| Amount | `(int) round($total * 100)` | Correct conversion to minor units |
| Currency | hard-coded `'INR'` | Inconsistent with the country selector — §6 |
| Receipt | `'rcpt_' . uniqid()` | **Not** the internal `order_number` ⇒ nothing to reconcile against |
| Idempotency | **none** | No `Idempotency-Key` ⇒ double charge on retry |
| Error handling | `'Payment initiation failed: ' . $e->getMessage()` | Leaks gateway internals to the browser |

**Net:** a client can POST `shipping_charge = 0` and `coupon_discount = 999999` and receive a Razorpay order for **₹1**. There is no server-side coupon validation to stop it, because the coupon table is never read.

`placeOrder()` computes the same expression **without** the `max(…, 1)` floor and uses the same client-supplied `shipping_charge` / `coupon_discount` (`:268-274`). So the amount Razorpay was asked to collect and the amount persisted locally are produced by two different formulas from the same untrusted input — they can disagree, and the stored order can hold a negative or zero total.

### 4.3 Signature verification — CORRECT

```php
$api->utility->verifyPaymentSignature([
    'razorpay_order_id'   => $razorpayOrderId,
    'razorpay_payment_id' => $razorpayPaymentId,
    'razorpay_signature'  => $razorpaySignature,
]);
```

The right API with the right parameters. Remaining gaps:

| Defect | Impact |
|---|---|
| Verified `razorpay_order_id` is **not compared** to a stored value | Signature validity is proven, but not that *this payment* belongs to *this order* |
| **Amount not re-verified** against the Razorpay order | Paying ₹1 for a large order verifies fine — and §4.2 makes ₹1 trivially reachable |
| No check that the Razorpay order status is `paid` | — |
| `razorpay_payment_id` has **no unique index** and no local uniqueness check | Replay within Razorpay's window is not blocked |

**Net:** signature checking prevents forgery but does not bind the payment to the order or the amount.

### 4.4 Client-side

`checkout.blade.php` loads `https://checkout.razorpay.com/v1/checkout.js` and calls `new Razorpay(options).open()` with `key`, `order_id`, `amount`, and a `handler` that POSTs to `/checkout/place-order`. `razorpay_key` is returned in the JSON response (`:215`) — that is correct, the key is public by design. The **secret** is never exposed.

---

## 5. Configuration and multi-server hazards

```php
// config/services.php
'shiprocket' => [
    'email'           => env('SHIPROCKET_EMAIL'),
    'password'        => env('SHIPROCKET_PASSWORD'),
    'pickup_location' => env('SHIPROCKET_PICKUP_LOCATION', 'Home'),
    'pickup_pincode'  => env('SHIPROCKET_PICKUP_PINCODE', '641006'),
],
```

**`base_url` is missing from this config.** `ShiprocketService.php:15` reads:

```php
config('services.shiprocket.base_url', 'https://apiv2.shiprocket.in/v1/external')
```

The hard-coded default is always used. `.env` defines `SHIPROCKET_BASE_URL` — **read by nothing.** Changing it has no effect, so a sandbox switch would silently keep hitting production.

`pickup_pincode` is defined but **never read by any code path** — verified.

### Cache-store hazard

`.env` → `CACHE_STORE=file`. The Shiprocket token is cached on local disk.

| Deployment shape | Consequence |
|---|---|
| Single server | Works |
| **Multiple servers / load balancer** | Each node logs in independently — N logins, rapid token rotation risk |
| `CACHE_STORE=database` | Token in the `cache` table, plaintext, 8 h |

**Recommend:** a Redis store shared by all nodes.

---

## 6. Country / currency integration gap

| Component | Reads country? |
|---|---|
| `CountryController` | Yes — sets `session('country_id')` |
| Product / category queries | Yes — `FIND_IN_SET` on the CSV `country_id` |
| Razorpay order | **No** — hard-coded `'INR'` |
| Currency display | **No** — `₹` hard-coded in ~40 Blade locations |
| Shiprocket payload | **No** — `billing_country: "India"` hard-coded at `:480` |
| Shipping | **No** — Indian courier set only (`pickup_location` default `Home`, pincode `641006`) |
| Address form | **No** — India-shaped fields (state, pincode, landmark) |

**Selecting any country other than India produces a storefront that quotes rupees, books Indian couriers, collects an India-shaped address, and charges INR.** `countries.currency` and `countries.iso_code` are never consulted.

---

## 7. Recommendations, in priority order

| Priority | Action | Fix |
|---|---|---|
| **P0** | Re-lock Razorpay | `composer require razorpay/razorpay:^2.9 --no-update && composer update razorpay/razorpay`; delete `CheckoutController.php:26-31` |
| **P0** | Bound and validate `shipping_charge` and `coupon_discount` | Recompute both server-side against a real courier quote and a real coupon lookup; never read them from the request |
| **P0** | Add the `coupons` lookup | Until then `coupon_discount` is an unchecked client number |
| **P0** | Make both total formulas identical | Extract one `PricingService::quote($cart)`; reuse it in `preparePayment` and `placeOrder` |
| **P0** | Guard `product_orders.billing_state`, `shipping_state`, `courier_name`, `discount` | See [22-database-integrity.md](22-database-integrity.md) |
| **P1** | Add `weight` / `length` / `breadth` / `height` to `product_varient` | Until then every parcel is 10×10×10 cm at 0.5 kg (§3.1) |
| **P1** | Verify amount and order linkage after payment | Fetch the Razorpay order; assert `amount === round($total*100)` and that `receipt` carries the internal order number |
| **P1** | Call `generatePickup()` after `createOrder()` | Without it nothing is collected |
| **P1** | Surface Shiprocket failure to the customer or the admin | The `catch` at `:525` swallows it behind a success message |
| **P1** | Add timeouts and retries | `->timeout(10)->retry(2, 200)` on every call |
| **P1** | Add 401 re-authentication | On 401, `Cache::forget($key)` and retry once |
| **P1** | Extract `PaymentService` and `OrderService` | Move 560 lines out of the controller |
| **P2** | Stop logging the full Shiprocket payload at info | Redact name, address, pincode, phone, email (SR-06) |
| **P2** | Log Shiprocket error bodies at `warning`, redacted | Currently raw bodies at `error` |
| **P2** | Add `base_url` to `config/services.php` | Honour `SHIPROCKET_BASE_URL` |
| **P2** | Use a shared cache store for the token | Redis |
| **P2** | Validate `selected_courier_id` against the earlier quote | Prevents arbitrary or stale courier ids |
| **P2** | Wire or delete the 7 dead methods | `generatePickup`, manifests, labels, invoices, tracking |
| **P2** | Implement a Shiprocket webhook receiver | Removes reliance on the dashboard's scheduler |
| **P3** | Add a distinct billing-address form | `shipping_is_billing: true` is hard-coded |
| **P3** | Deduplicate `ShiprocketService` across apps | Extract a shared package |

---

## 8. Related documents

- [09-dependencies.md](09-dependencies.md) — Razorpay lock-file defect
- [11-models-and-relationships.md](11-models-and-relationships.md) — missing `$fillable` targets
- [13-security-audit.md](13-security-audit.md) — client-controlled pricing, log exposure
- [20-known-issues.md](20-known-issues.md) — BUG register
- [22-database-integrity.md](22-database-integrity.md) — missing-column migrations
- [23-external-services.md](23-external-services.md) — Razorpay and Shiprocket API reference
- [27-api-reference.md](27-api-reference.md) — request and response shapes