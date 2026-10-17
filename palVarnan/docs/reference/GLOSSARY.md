# GLOSSARY.md

**Purpose:** Domain terms (variant, slot, AWB, courier serviceability, COD, prepaid, guest cart token, order_number vs order_id), identifier schemes, and the evidence vocabulary.

---

## 1. Domain terms

| Term | Definition (source-derived) | Evidence |
|---|---|---|
| **Variant** | A product variant represented by `ProductVarient` (table `product_varient`). Has pricing (`offer_price`, `mrp_price`), quantity (`product_qty`), GST (`product_gst`), weight/dimensions, image, unit/value. | `VERIFIED_FROM_SOURCE` — [`app/Models/ProductVarient.php`](../../app/Models/ProductVarient.php); [`CheckoutController.php`](../../app/Http/Controllers/CheckoutController.php) uses `variant->offer_price`, `variant->product_gst`, `variant->weight`, etc. |
| **Slot** | A packing/operational line recorded in `product_slots` (dashboard operational table). Created during order placement per line item ([`CheckoutController.php:388-400`](../../app/Http/Controllers/CheckoutController.php#L388-L400)). | `VERIFIED_FROM_SOURCE` — [`app/Models/ProductSlot.php`](../../app/Models/ProductSlot.php); [`CheckoutController.php:388-400`](../../app/Http/Controllers/CheckoutController.php#L388-L400) |
| **AWB** | Air Way Bill code assigned by courier via Shiprocket. Set on `product_orders.awb_code` when `assignAWB()` succeeds ([`CheckoutController.php:518-523`](../../app/Http/Controllers/CheckoutController.php#L518-L523)). | `VERIFIED_FROM_SOURCE` — [`ShiprocketService.php:106-111`](../../app/Services/ShiprocketService.php#L106-L111); [`CheckoutController.php:509-523`](../../app/Http/Controllers/CheckoutController.php#L509-L523) |
| **Courier serviceability** | Availability check via Shiprocket to determine which couriers service a delivery pincode with given weight/COD flag. Method [`ShiprocketService::checkServiceability()`](../../app/Services/ShiprocketService.php#L63-77) called by [`CheckoutController::getShiprocketCouriers()`](../../app/Http/Controllers/CheckoutController.php#L89-167). | `VERIFIED_FROM_SOURCE` |
| **COD** | Cash on Delivery. Payment method value `"Cash on Delivery"`. Shiprocket serviceability passes `cod=1` ([`CheckoutController.php:93,120`](../../app/Http/Controllers/CheckoutController.php#L93-L120)). On Shiprocket order creation, payment method becomes `"COD"` ([`CheckoutController.php:485`](../../app/Http/Controllers/CheckoutController.php#L485)). Order `payment_status` becomes `"Pending"` ([`CheckoutController.php:324`](../../app/Http/Controllers/CheckoutController.php#L324)). | `VERIFIED_FROM_SOURCE` |
| **Prepaid / Online Payment** | Online payment via Razorpay. Method `"Online Payment"`. Shiprocket receives `"Prepaid"` ([`CheckoutController.php:485`](../../app/Http/Controllers/CheckoutController.php#L485)). `payment_status` set to `"Paid"` if verified ([`CheckoutController.php:324`](../../app/Http/Controllers/CheckoutController.php#L324)). Razorpay signature verified ([`CheckoutController.php:252-268`](../../app/Http/Controllers/CheckoutController.php#L252-L268)). | `VERIFIED_FROM_SOURCE` |
| **Guest cart token** | Guest identity for cart. Used as `guest_token` cookie fallback (`request()->cookie('guest_token') ?? request()->ip()`) in [`AppServiceProvider.php:56`](../../app/Providers/AppServiceProvider.php#L56) and also guest cart is stored by `ip_addresscart` (and `user_id` null). The term "guest cart token" appears conceptually in the codebase's behavior; the actual cookie key is `guest_token`. | `INFERRED` from [`AppServiceProvider.php:54-72`](../../app/Providers/AppServiceProvider.php#L54-L72) and [`CartController.php`](../../app/Http/Controllers/CartController.php) |
| **Cart identity** | Authenticated: `user_id`. Guest: `ip_addresscart` (and `user_id = NULL`); global view composer may use `guest_token` cookie or IP. No cross-login merge implemented. | `VERIFIED_FROM_SOURCE` — [`CartController.php:11-31,64-93`](../../app/Http/Controllers/CartController.php); [`AppServiceProvider.php`](../../app/Providers/AppServiceProvider.php) |

---

## 2. Identifier schemes (`order_number` vs `order_id`)

| Field | Where used | Purpose/Notes | Evidence |
|---|---|---|
| `order_number` | `product_orders.order_number` | Primary customer-facing order identifier generated as `ORD- + uniqid()` ([`CheckoutController.php:290`](../../app/Http/Controllers/CheckoutController.php#L290)). Also used for Shiprocket `order_id` payload ([`CheckoutController.php:470`](../../app/Http/Controllers/CheckoutController.php#L470)) and for thank-you route lookup ([`CheckoutController.php:544`](../../app/Http/Controllers/CheckoutController.php#L544)). | `VERIFIED_FROM_SOURCE` — [`CheckoutController.php:290,298,470,544`](../../app/Http/Controllers/CheckoutController.php) |
| `order_id` | `product_orders.order_id` | Set to the same value as `order_number` in `placeOrder()` ([`CheckoutController.php:298`](../../app/Http/Controllers/CheckoutController.php#L298)) — **"For dashboard compatibility"** (comment in model). Many related tables use `order_id` as the linking key (e.g. `product_order_user_addresses.order_id`, `product_slots.order_id`, `product_tracking.order_id`, `product_transaction_logs.order_id`). | `VERIFIED_FROM_SOURCE` — [`ProductOrder.php:21`](../../app/Models/ProductOrder.php#L21), [`CheckoutController.php:298`](../../app/Http/Controllers/CheckoutController.php#L298); model relations use `order_id` joins ([`ProductOrder.php:85-98`](../../app/Models/ProductOrder.php#L85-L98)) |

So: `order_number` and `order_id` are **identical** at creation time (the same string). `order_id` is the foreign-key convention used across order-related tables; `order_number` is the visible identifier. `VERIFIED_FROM_SOURCE`.

---

## 3. Status values

| Status area | Values seen | Evidence |
|---|---|---|
| Order status | `status` set to `"Order Placed"` on creation ([`CheckoutController.php:322`](../../app/Http/Controllers/CheckoutController.php#L322)) | `VERIFIED_FROM_SOURCE` |
| Payment status | `"Paid"` (online, after verification) or `"Pending"` (COD) ([`CheckoutController.php:324`](../../app/Http/Controllers/CheckoutController.php#L324)) | `VERIFIED_FROM_SOURCE` |
| Shiprocket status | Stored on `product_orders.shiprocket_status`; may become `"AWB Assigned"` after successful assign ([`CheckoutController.php:520`](../../app/Http/Controllers/CheckoutController.php#L520)); initial value from Shiprocket response stored when present ([`CheckoutController.php:506`](../../app/Http/Controllers/CheckoutController.php#L506)) | `VERIFIED_FROM_SOURCE` |
| Delivery/tracking | `product_tracking.delivery_status` set to `0` initially ([`CheckoutController.php:363`](../../app/Http/Controllers/CheckoutController.php#L363)); `product_slots.delivery_status` set to `0` ([`CheckoutController.php:399`](../../app/Http/Controllers/CheckoutController.php#L399)) | `VERIFIED_FROM_SOURCE` |

---

## 4. Identifier schemes for cart/wishlist/variants

| Entity | Keys | Notes |
|---|---|---|
| Cart line | `id` (primary) is used in `CartController::update()` where loop iterates `$request->quantities` as `$cartId => $qty` and does `Cart::where('id',$cartId)->update(...)` with **no ownership check** ([`CartController.php:33-44`](../../app/Http/Controllers/CartController.php#L33-L44)) — **IDOR**. | `VERIFIED_FROM_SOURCE` |
| Cart identity keys | Auth: `user_id`. Guest: `ip_addresscart` (+ `user_id NULL`). AJAX update/remove scope by `product_varient_id` plus identity ([`CartController.php:95-128`](../../app/Http/Controllers/CartController.php#L95-L128)). | `VERIFIED_FROM_SOURCE` |
| Wishlist | Primary `id`; remove scopes by `id` and `user_id` ([`WishlistController.php:50-54`](../../app/Http/Controllers/WishlistController.php#L50-L54)) — **scoped** (fixed). Toggle/check use `user_id`, `product_id`, `product_varient_id`. | `VERIFIED_FROM_SOURCE` |
| Variant | `ProductVarient` table uses `product_varient` name; foreign keys include `product_varient_id` in cart/wishlist/order items ([`Cart.php:14`](../../app/Models/Cart.php#L14), [`ProductOrderItem.php:17`](../../app/Models/ProductOrderItem.php#L17)). | `VERIFIED_FROM_SOURCE` |

---

## 5. Pricing/GST fields

| Field | Entity | Meaning | Evidence |
|---|---|---|---|
| `offer_price` | `product_varients` / used via variant | Selling price used in calculations (preferred over product price). | `VERIFIED_FROM_SOURCE` — [`CheckoutController.php:60,184,279,367,447`](../../app/Http/Controllers/CheckoutController.php) |
| `product_price` | `products` (fallback) | Product-level price used as fallback when variant price absent. | `VERIFIED_FROM_SOURCE` — [`CheckoutController.php:60`](../../app/Http/Controllers/CheckoutController.php#L60) |
| `product_gst` | `product_varients` | GST rate (percentage) used per line. | `VERIFIED_FROM_SOURCE` — [`CheckoutController.php:64-65,187-188,282-283,382-384,397,449`](../../app/Http/Controllers/CheckoutController.php) |
| `shipping_charge` | `states.shipping_charge` (DB) and request/client | Server may look up by state name ([`CheckoutController.php:638-654`](../../app/Http/Controllers/CheckoutController.php#L638-L654)), but `placeOrder()` also reads `shipping_charge` from request ([`CheckoutController.php:286`](../../app/Http/Controllers/CheckoutController.php#L286)) and stores it — **client-supplied money value**. | `VERIFIED_FROM_SOURCE` |
| `coupon_discount` | Request/client | Read from request ([`CheckoutController.php:287`](../../app/Http/Controllers/CheckoutController.php#L287)), stored on order totals calculation — **client-supplied**. Coupon model exists but never used ([`CheckoutController.php:6`](../../app/Http/Controllers/CheckoutController.php#L6), no `Coupon::` calls). | `VERIFIED_FROM_SOURCE`, `INFERRED` |

---

## 6. Evidence vocabulary (reference form)

| Tag | Definition |
|---|---|
| `VERIFIED_FROM_SOURCE` | Read from PHP/Blade/config/JSON with `file:line`. |
| `VERIFIED_FROM_LIVE_SCHEMA` | Confirmed against `palvarnan_db (2).sql`. |
| `INFERRED` | Conclusion from verified facts. |
| `UNVERIFIED` | Open / not determinable from artefacts. |
| `NOT_AVAILABLE` | Live artefacts unavailable. |
| `CONFLICTING_EVIDENCE` | Disagreement between docs/source; source truth stated. |

---

## 7. Related

- [`../28-glossary-and-terms.md`](../28-glossary-and-terms.md) — longer audit glossary
- [`../08-business-logic.md`](../08-business-logic.md)
- [`../12-services-and-integrations.md`](../12-services-and-integrations.md)