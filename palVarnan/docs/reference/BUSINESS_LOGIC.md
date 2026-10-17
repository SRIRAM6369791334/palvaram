# BUSINESS_LOGIC.md

**Purpose:** Pricing math (subtotal from variant offer_price/ product_price, GST from variant product_gst,
shipping from State.shipping_charge, coupon discount), the client-supplied `shipping_charge`/`coupon_discount`
risk, order lifecycle/statuses (`Order Placed` → Shiprocket), stock deduction in `productstocks` + `product_varients`,
multi-table order write, cart merge behavior (guest by ip_addresscart, user by user_id). Cite `CheckoutController.php` lines.

---

## 1. Pricing math (server-side calculations)

### 1.1 Cart/index pricing
- **Cart index** ([`CartController@index:11-31`](../../app/Http/Controllers/CartController.php#L11-L31)): subtotal =
  `sum($item->variant->offer_price * $item->product_quantity)` (uses variant offer_price; does **not** add GST/shipping).
  `VERIFIED_FROM_SOURCE`.

- **Checkout index** ([`CheckoutController@index:38-84`](../../app/Http/Controllers/CheckoutController.php#L38-L84)): for each cart item
  - `price = $item->variant->offer_price ?? $item->product->product_price ?? 0` ([`60`](../../app/Http/Controllers/CheckoutController.php#L60))
  - `qty = $item->product_quantity`
  - `subtotal += price*qty` ([`62`](../../app/Http/Controllers/CheckoutController.php#L62))
  - `gstRate = $item->variant->product_gst ?? 0`; `totalGst += (price*qty)*(gstRate/100)` ([`64-66`](../../app/Http/Controllers/CheckoutController.php#L64-L66))
  - weight: `($item->variant->weight ?? 0.5)*qty` ([`67-69`](../../app/Http/Controllers/CheckoutController.php#L67-L69))
  Shipping is **not** added in index subtotal (view passes separate `subtotal`, `totalGst`, `totalWeight`). `VERIFIED_FROM_SOURCE`.

### 1.2 Prepare payment
[`CheckoutController::preparePayment:172-220`](../../app/Http/Controllers/CheckoutController.php#L172-L220):
- Compute `subtotal` and `gstAmount` same as above ([`181-189`](../../app/Http/Controllers/CheckoutController.php#L181-L189), uses same pricing).
- `shipping = floatval($request->shipping_charge ?? 0)` ([`191`](../../app/Http/Controllers/CheckoutController.php#L191))
- `discount = floatval($request->coupon_discount ?? 0)` ([`192`](../../app/Http/Controllers/CheckoutController.php#L192))
- `total = max(($subtotal + $gstAmount + $shipping) - $discount, 1)` ([`193`](../../app/Http/Controllers/CheckoutController.php#L193))
- Razorpay order amount is `(int) round($total * 100)` ([`202`](../../app/Http/Controllers/CheckoutController.php#L202))

**Risk:** `shipping_charge` and `coupon_discount` come **from the request** (client-supplied). No server-side
reconciliation against `State.shipping_charge` or any Coupon model lookup. `VERIFIED_FROM_SOURCE`.

### 1.3 Place order (final totals)
[`CheckoutController::placeOrder:275-288`](../../app/Http/Controllers/CheckoutController.php#L275-L288):
- Recalculates `subtotal` and `gstAmount` from cart ([`276-284`](../../app/Http/Controllers/CheckoutController.php#L276-L284)).
- `shipping = floatval($request->shipping_charge ?? 0)` ([`286`](../../app/Http/Controllers/CheckoutController.php#L286))
- `discount = floatval($request->coupon_discount ?? 0)` ([`287`](../../app/Http/Controllers/CheckoutController.php#L287))
- `total = ($subtotal + $gstAmount + $shipping) - $discount` ([`288`](../../app/Http/Controllers/CheckoutController.php#L288))

**CRITICAL:** These are **client-controlled values**. The only server-side guard is that Razorpay signature is
verified when paying online ([`252-268`](../../app/Http/Controllers/CheckoutController.php#L252-L268)) — but the
signature verifies the Razorpay order details (amount/currency/receipt context) created by `preparePayment()`;
if the attacker can influence the final submitted `total` shape or if the frontend submits mismatched values,
the DB will store whatever client sent (and Razorpay flow depends on how amounts are handled). Also for **COD**,
no payment signature verification exists. **This is a price-tampering risk.** `VERIFIED_FROM_SOURCE`,
`INFERRED` (business logic risk).

Shipping source lookup exists separately: [`getShippingCharge()`](../../app/Http/Controllers/CheckoutController.php#L638-L654)
looks up `states.shipping_charge` by state name; that value is returned to frontend but the final stored
`shipping_charge` on order is the **request value**, not the server-fetched value. `VERIFIED_FROM_SOURCE`.

Coupon model: imported ([`CheckoutController.php:6`](../../app/Http/Controllers/CheckoutController.php#L6)) but
**never used** (no `Coupon::` calls). `coupon_discount` is purely client-supplied. `VERIFIED_FROM_SOURCE`.

---

## 2. Order lifecycle & statuses

### 2.1 Order creation
- `orderNumber = 'ORD-' . strtoupper(uniqid())` ([`CheckoutController.php:290`](../../app/Http/Controllers/CheckoutController.php#L290))
- `order_id` set same as `orderNumber` ("dashboard compatibility") ([`CheckoutController.php:298`](../../app/Http/Controllers/ProductOrder.php#L21) comment reflected in code at [`298`](../../app/Http/Controllers/CheckoutController.php#L298))
- `status = 'Order Placed'` ([`CheckoutController.php:322`](../../app/Http/Controllers/CheckoutController.php#L322))
- `payment_method` = `"Online Payment"` or `"Cash on Delivery"` ([`CheckoutController.php:323`](../../app/Http/Controllers/CheckoutController.php#L323))
- `payment_status` = `"Paid"` if online, else `"Pending"` (COD) ([`CheckoutController.php:324`](../../app/Http/Controllers/CheckoutController.php#L324))

### 2.2 Related tables written
Inside transaction ([`CheckoutController.php:292-421`](../../app/Http/Controllers/CheckoutController.php#L292-L421)):
1. `product_orders` — master ([`295-327`](../../app/Http/Controllers/CheckoutController.php#L295-L327))
2. `product_order_user_addresses` — order address snapshot ([`330-345`](../../app/Http/Controllers/CheckoutController.php#L330-L345))
3. `product_transaction_logs` — transaction log ([`348-356`](../../app/Http/Controllers/CheckoutController.php#L348-L356))
4. `product_tracking` — tracking record (delivery_status 0) ([`359-363`](../../app/Http/Controllers/CheckoutController.php#L359-L363))
5. Per line item: `product_order_items` ([`373-385`](../../app/Http/Controllers/CheckoutController.php#L373-L385)) and `product_slots` ([`388-400`](../../app/Http/Controllers/CheckoutController.php#L388-L400))
6. Stock deduction (see §3)
7. Clear carts for user (`Cart::where('user_id',$userId)->delete()`) ([`419`](../../app/Http/Controllers/CheckoutController.php#L419))
- `DB::commit()` at [`421`](../../app/Http/Controllers/CheckoutController.php#L421); on error `DB::rollBack()` ([`424`](../../app/Http/Controllers/CheckoutController.php#L424)).

### 2.3 Shiprocket integration (post-commit)
After commit ([`433-527`](../../app/Http/Controllers/CheckoutController.php#L433-L527)):
- Build `order_items` array for Shiprocket ([`442-458`](../../app/Http/Controllers/CheckoutController.php#L442-L458)) (uses variant data; HSN hardcoded `441122` at [`450`](../../app/Http/Controllers/CheckoutController.php#L450), `INFERRED`/not from DB).
- Compute weight/dimensions ([`443-463`](../../app/Http/Controllers/CheckoutController.php#L443-L463)) with defaults (weight 0.5, dims 10).
- Build billing name split ([`465-467`](../../app/Http/Controllers/CheckoutController.php#L465-L467)).
- Call `ShiprocketService::createOrder()` ([`498`](../../app/Http/Controllers/CheckoutController.php#L498)); payload includes `order_id` = `orderNumber`, payment method mapped to `"Prepaid"` or `"COD"`, totals, dimensions, pickup_location from config ([`470-495`](../../app/Http/Controllers/CheckoutController.php#L470-L495)).
- If response has `order_id` and `shipment_id`, update `product_orders` with `shiprocket_order_id`, `shiprocket_shipment_id`, `shiprocket_status` ([`500-506`](../../app/Http/Controllers/CheckoutController.php#L500-L506)).
- If `selected_courier_id` provided, call `assignAWB()` ([`510-523`](../../app/Http/Controllers/CheckoutController.php#L510-L523)); on success update `awb_code`, `courier_name`, `shiprocket_status='AWB Assigned'`.
- Shiprocket API errors are caught and logged; **do not rollback DB** (already committed). `VERIFIED_FROM_SOURCE`.

**Lifecycle summary:** `Order Placed` (local) → Shiprocket order created (status stored) → optionally AWB assigned.
**Pickup never scheduled:** `ShiprocketService::generatePickup()` exists ([`119-124`](../../app/Services/ShiprocketService.php#L119-L124))
but has **no caller** in `app/`. `VERIFIED_FROM_SOURCE`.

---

## 3. Stock deduction

In `placeOrder()` loop over cart items ([`402-416`](../../app/Http/Controllers/CheckoutController.php#L402-L416)):
1. **`productstocks` table**: find by `pro_ver_id = $item->product_varient_id` ([`403`](../../app/Http/Controllers/CheckoutController.php#L403))
   - `availablestock = max($productStock->availablestock - $qty, 0)` ([`405`](../../app/Http/Controllers/CheckoutController.php#L405))
   - `salestock = ($productStock->salestock ?? 0) + $qty` ([`406`](../../app/Http/Controllers/CheckoutController.php#L406))
   - save ([`407`](../../app/Http/Controllers/CheckoutController.php#L407))
2. **`product_varients` table**: find by id ([`411`](../../app/Http/Controllers/CheckoutController.php#L411))
   - `product_qty = max($variant->product_qty - $qty, 0)` ([`413`](../../app/Http/Controllers/CheckoutController.php#L413))
   - save ([`414`](../../app/Http/Controllers/CheckoutController.php#L414))

This is a **multi-table stock write** (two locations). No row-locking observed (no `lockForUpdate()` etc.);
stock values can go negative in effect only via `max(...,0)`. `VERIFIED_FROM_SOURCE`.

Models: [`ProductStock`](../../app/Models/ProductStock.php), [`ProductVarient`](../../app/Models/ProductVarient.php).

---

## 4. Multi-table order write (detailed)

Tables and key fields written:

| Table | What written | Key linkage | Evidence |
|---|---|---|---|
| `product_orders` | master totals, addresses (billing/shipping), status, payment info, courier, Shiprocket IDs/awb/status | PK/identity; `order_number` and `order_id` = same string | [`295-327`](../../app/Http/Controllers/CheckoutController.php#L295-L327) |
| `product_order_user_addresses` | shipping address snapshot | links by `order_id = $orderNumber` and `user_id` | [`330-345`](../../app/Http/Controllers/CheckoutController.php#L330-L345) |
| `product_transaction_logs` | transaction record: `order_id`, `order_date`, `order_amount`, `amount_credited` (if online else 0), `amount_debited` 0, `user_id`, `slot_id=null` | `order_id = $orderNumber` | [`348-356`](../../app/Http/Controllers/CheckoutController.php#L348-L356) |
| `product_tracking` | `delivery_status = 0` | `order_id = $orderNumber`, `user_id` | [`359-363`](../../app/Http/Controllers/CheckoutController.php#L359-L363) |
| `product_order_items` | per line: product/variant refs, names, `product_image`, `variant_name`, qty, price, gst_rate, gst_amount, total | `order_id = $order->id` (uses model PK) | [`373-385`](../../app/Http/Controllers/CheckoutController.php#L373-L385) |
| `product_slots` | per line (operational): `order_id = $orderNumber`, product/variant, qty, names, rates/totals, `gst_amt`, `gst_per`, `product_value`, `delivery_status=0` | `order_id = $orderNumber` | [`388-400`](../../app/Http/Controllers/CheckoutController.php#L388-L400) |
| `carts` | cleared for `user_id` | delete rows | [`419`](../../app/Http/Controllers/CheckoutController.php#L419) |

All inside a single `DB::beginTransaction()` with `DB::commit()` on success. `VERIFIED_FROM_SOURCE`.

---

## 5. Cart merge behavior (guest vs user)

- **Guest cart identity:** stored with `ip_addresscart` and `user_id = NULL`. Also view composer uses
  `guest_token` cookie or IP ([`AppServiceProvider.php:56,59-72`](../../app/Providers/AppServiceProvider.php#L56-L72)).
- **User cart:** stored with `user_id = Auth::id()`.
- **No merge logic implemented.** `AuthController@login()` ([`46-72`](../../app/Http/Controllers/AuthController.php#L46-L72))
  only calls `Auth::login($user)`; there is no code that moves guest cart rows (where `user_id NULL` and
  matching IP/guest_token) to the authenticated user. Guest cart remains orphaned after login in terms of
  being merged into user cart. `VERIFIED_FROM_SOURCE`, `INFERRED`.

Cart queries:
- Auth: `Cart::where('user_id', Auth::id())` ([`CartController@index:15-17`](../../app/Http/Controllers/CartController.php#L15-L17), etc.)
- Guest: `where('ip_addresscart', ...)->whereNull('user_id')` ([`CartController@index:18-21`](../../app/Http/Controllers/CartController.php#L18-L21))

So guest and user carts are **separate** by design and never merged. `VERIFIED_FROM_SOURCE`.

---

## 6. Order ownership & thank-you

[`CheckoutController::thankyou()`](../../app/Http/Controllers/CheckoutController.php#L540-L548):
- Loads order with items: `ProductOrder::with('items')->where('order_number',$orderNumber)->where('user_id', Auth::id())->firstOrFail()`
- Ownership enforced by `user_id` filter. `VERIFIED_FROM_SOURCE`.

Address ownership enforced in update/destroy ([`596,629`](../../app/Http/Controllers/CheckoutController.php#L596-L629)).

---

## 7. Related

- [`CHECKOUT_AND_ORDERS.md`](./CHECKOUT_AND_ORDERS.md)
- [`PAYMENTS_RAZORPAY.md`](./PAYMENTS_RAZORPAY.md)
- [`SHIPPING_AND_FULFILMENT.md`](./SHIPPING_AND_FULFILMENT.md)
- [`CART_AND_WISHLIST.md`](./CART_AND_WISHLIST.md)
- [`../08-business-logic.md`](../08-business-logic.md)
- [`../11-models-and-relationships.md`](../11-models-and-relationships.md)