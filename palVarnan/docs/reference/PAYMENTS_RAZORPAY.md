# PAYMENTS_RAZORPAY.md

**Purpose:** Razorpay order creation and signature verification.

---

## 1. Configuration

- SDK: `razorpay/razorpay: ^2.9` in [`composer.json:12`](../../composer.json#L12) and present in `composer.lock`. `VERIFIED_FROM_SOURCE`.
- Keys: `RAZORPAY_KEY`, `RAZORPAY_SECRET` in [`config/services.php:38-41`](../../config/services.php#L38-L41).
- Fallback autoload: [`CheckoutController.php:26-31`](../../app/Http/Controllers/CheckoutController.php#L26-L31) conditionally requires Razorpay.php if class missing.

`VERIFIED_FROM_SOURCE`.

---

## 2. Prepare payment

[`CheckoutController::preparePayment`](../../app/Http/Controllers/CheckoutController.php#L172-L220):
- Computes totals using client-supplied `shipping_charge`/`coupon_discount` ([191-193](../../app/Http/Controllers/CheckoutController.php#L191-L193))
- Creates Razorpay order: `new Api(key,secret)`, `order->create(['receipt'=>..., 'amount'=> (int)round(total*100), 'currency'=>'INR'])` ([199-204](../../app/Http/Controllers/CheckoutController.php#L199-L204))
- Returns `razorpay_order_id`, `amount`, user details, `razorpay_key` ([206-216](../../app/Http/Controllers/CheckoutController.php#L206-L216))

`VERIFIED_FROM_SOURCE`.

---

## 3. Signature verification (place order)

When `payment_method === 'Online Payment'` ([`CheckoutController::placeOrder:252-268`](../../app/Http/Controllers/CheckoutController.php#L252-L268)):
- Requires `razorpay_payment_id`, `razorpay_signature`, `razorpay_order_id` ([253-255](../../app/Http/Controllers/CheckoutController.php#L253-L255))
- Calls `$api->utility->verifyPaymentSignature($attributes)` with those three ([259-264](../../app/Http/Controllers/CheckoutController.php#L259-L264))
- On failure returns 400 with error ([265-267](../../app/Http/Controllers/CheckoutController.php#L265-L267))

`VERIFIED_FROM_SOURCE`.

---

## 4. Notes

- Amounts sent to Razorpay are in paise (×100). `VERIFIED_FROM_SOURCE`.
- Payment status stored as `"Paid"` after successful verification ([`324`](../../app/Http/Controllers/CheckoutController.php#L324)).

---

## 5. Related

- [`CHECKOUT_AND_ORDERS.md`](./CHECKOUT_AND_ORDERS.md)
- [`../12-services-and-integrations.md`](../12-services-and-integrations.md)