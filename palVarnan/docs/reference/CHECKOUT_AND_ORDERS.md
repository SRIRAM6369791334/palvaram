# CHECKOUT_AND_ORDERS.md

**Purpose:** The multi-table order write, addresses, and the thank-you page.

---

## 1. Checkout flow

1. `GET /checkout` ([`CheckoutController@index`](../../app/Http/Controllers/CheckoutController.php#L38-L84)) — builds cart/address/state; redirects if empty.
2. `POST /checkout/get-couriers` ([`getShiprocketCouriers`](../../app/Http/Controllers/CheckoutController.php#L89-L167)) — Shiprocket serviceability.
3. `POST /checkout/prepare-payment` ([`preparePayment`](../../app/Http/Controllers/CheckoutController.php#L172-L220)) — creates Razorpay order; uses client-supplied shipping/coupon in totals.
4. `POST /checkout/place-order` ([`placeOrder`](../../app/Http/Controllers/CheckoutController.php#L225-L535)) — validates, verifies Razorpay signature (online), writes multi-table order + stock, clears cart, creates Shiprocket order.
5. `GET /checkout/thankyou/{order}` ([`thankyou`](../../app/Http/Controllers/CheckoutController.php#L540-L548)) — ownership scoped.

`VERIFIED_FROM_SOURCE`.

---

## 2. Multi-table order write (transactional)

Inside `DB::beginTransaction()` ([292-421](../../app/Http/Controllers/CheckoutController.php#L292-L421)), `DB::commit()` at 421.

| Table | Insert (lines) | Notes |
|---|---|---|
| `product_orders` | 295-327 | Master; `order_number` and `order_id` same; `billing_state` set at 306; status `Order Placed`; payment_status Paid/Pending; stores Razorpay/Shiprocket IDs later |
| `product_order_user_addresses` | 330-345 | Order address snapshot |
| `product_transaction_logs` | 348-356 | Transaction log; `amount_credited` set if online |
| `product_tracking` | 359-363 | delivery_status 0 |
| `product_order_items` | 373-385 | Per line; includes `product_image` (line 378) |
| `product_slots` | 388-400 | Operational; per line |
| `productstocks` | 403-408 | Stock deduction: availablestock reduced, salestock increased |
| `product_varients` | 411-416 | Stock sync: product_qty reduced |
| `carts` | 419 | Cleared for user_id |

`VERIFIED_FROM_SOURCE`.

---

## 3. Addresses

- Store: [`storeAddress`](../../app/Http/Controllers/CheckoutController.php#L553-L589)
- Update: [`updateAddress`](../../app/Http/Controllers/CheckoutController.php#L594-L622) — **ownership enforced** (`abort_unless` 596)
- Delete: [`destroyAddress`](../../app/Http/Controllers/CheckoutController.php#L627-L633) — **ownership enforced** (629)
- Thank-you: scoped by `order_number` and `user_id` ([544](../../app/Http/Controllers/CheckoutController.php#L544))

`VERIFIED_FROM_SOURCE`.

---

## 4. Related

- [`BUSINESS_LOGIC.md`](./BUSINESS_LOGIC.md)
- [`PAYMENTS_RAZORPAY.md`](./PAYMENTS_RAZORPAY.md)
- [`SHIPPING_AND_FULFILMENT.md`](./SHIPPING_AND_FULFILMENT.md)