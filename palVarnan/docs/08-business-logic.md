# 08 — Business Logic

Every business rule that exists in the storefront, with its exact implementation and the places where it is duplicated, missing, or contradicted.

Rule ID prefixes: `BL-` rules · `BR-` requirements · `MDL-` model-level behaviours.

---

## 1. Pricing

### BL-P01 — Price resolution
```
price(variant) = variant.offer_price  ??  product.product_price  ??  0
```
Implemented in **6 places, in 3 different shapes** — verified against source:

| # | Location | Expression | Null-guarded? |
|---|---|---|---|
| 1 | `AppServiceProvider.php:41-43` | `($c->variant->offer_price ?? 0) * $c->product_quantity` | yes |
| 2 | `CartController.php:26-28` | `$item->variant->offer_price * $item->product_quantity` | **NO** |
| 3 | `CartController.php:107-110` | `($item->variant->offer_price ?? 0) * $item->product_quantity` | yes |
| 4 | `CheckoutController.php:60` | `$item->variant->offer_price ?? $item->product->product_price ?? 0` | yes |
| 5 | `CheckoutController.php:182` | same as 4 | yes |
| 6 | `ProfileController.php:88` | same as 4 | yes |

**Defect A:** `products.product_price` does not exist (see [07-database-schema.md](07-database-schema.md) D-S2), so sites 4–6 always resolve to `offer_price ?? 0`. The `?? 0` branch is reachable whenever `offer_price` is `NULL` ⇒ **free products silently priced at ₹0.**

**Defect B:** site 2 (`CartController.php:26-28`) has **no `??` guard at all**. `null * 5` evaluates to `0` in PHP without a warning, so a null-priced line silently shows as ₹0 on the cart page while other pages use the same rule.

**Never consulted:** `product_varient.price`, `.sale_price`, `.mrp_price`, `.product_mrp_price`, `.product_regular_price` — five price columns exist and are unused.

**Never consulted:** `product_varient.price`, `.sale_price`, `.mrp` — three price columns exist and are unused.

### BL-P02 — GST
```
totalGst = Σ (price × qty) × (variant.product_gst / 100)
```
| Location | Line |
|---|---|
| `CheckoutController.php:64-65` | Checkout page |
| `CheckoutController.php:186-187` | `preparePayment` |
| `CheckoutController.php:196` | Razorpay amount base |
| `checkout.blade.php:614-624` | Client-side recompute (display only) |

**Defects:**
- `product_gst` defaults to `0` when null ⇒ tax silently vanishes.
- GST is **added on top of** `offer_price`, so `offer_price` is treated as a pre-tax price. Whether that matches intent is **UNVERIFIED** — no documentation exists.
- No CGST/SGST split, no HSN/SAC code, no tax-inclusive display.

### BL-P03 — Order total
```
total = (subtotal + totalGst + shipping) − discount
```
Implemented **2 times, differently**:

| Location | Formula | Floor |
|---|---|---|
| `CheckoutController.php:288` | `(subtotal + gst + shipping) - discount` | **none** |
| `checkout.blade.php:626` | `max((baseSubtotalWithGst + shipping) - discount, 0)` | `0` |
| `CheckoutController.php:193` | `max(subtotal+gst+shipping-discount, 1)` (Razorpay) | `1` |

**Consequence:** a crafted POST with `coupon_discount = -500` yields a **negative** `total_amount` at `:288`, and the amount Razorpay actually charged (computed at `:193`) differs from the amount recorded on the order. There is no reconciliation.

### BL-P04 — Discount / coupons
**No implementation.** See F-42 in [04-feature-inventory.md](04-feature-inventory.md).
- `Coupon` is imported at `CheckoutController.php:6` and never used.
- `hidden_coupon_discount` is hard-coded `0` in `checkout.blade.php`.
- The `coupons`, `coupon__product`, `coupon__product_varient` tables are populated-or-empty and unread.
- The "Apply Coupon" button (`cart.blade.php:153-154`, `checkout.blade.php:94`) has no handler.

### BL-P05 — Rounding
No rounding anywhere except Razorpay's `round($total * 100)` at `:193`. Paise-level drift is possible between the Razorpay amount and the stored `total_amount`.

---

## 2. Cart

### BL-C01 — Add item
`CartController::store()` `:62-90`

Verified source, `CartController.php:62-90`:

| Input | Handling |
|---|---|
| Authenticated | Lookup `user_id` + product + variant ⇒ `increment('product_quantity', 1)` |
| Guest | Lookup `ip_addresscart` + product + variant + `whereNull('user_id')` ⇒ `increment(…, 1)` |
| Quantity on **existing** line | **Ignored** — the increment is hard-coded to `1` |
| Quantity on **new** line | `$request->quantity ?? 1` is honoured (`:86`), but **every Blade call site sends `quantity: 1`** (verified in `product-detail.blade.php:351, 421`, `wishlist.blade.php:159`, `myaccount.blade.php:790`) ⇒ effectively always 1 |
| Stock | **Not checked.** Unlimited add |
| Price | Not stored — recomputed on every read, so a price change silently alters the basket |
| `pre_order` | Passed as `0` but dropped by `Cart::$fillable` ⇒ always `NULL` |
| Response | `{status:'incremented', message}` or `{status:'added', message, quantity: 1}` — note `quantity` is hard-coded `1` even when the request sent more |

### BL-C02 — Guest identity — **3 different resolvers, verified**

| Site | Expression | Line |
|---|---|---|
| `CartController::store()` | `request()->ip()` | `:74` |
| `CartController::index()` | `request()->cookie('guest_token') ?? request()->ip()` | `:16` |
| `CartController::subtotal()` | `request()->cookie('guest_token') ?? request()->ip()` | `:101` |
| `AppServiceProvider` | `request()->cookie('guest_token') ?? request()->ip()` | `:49` |
| `ProductController::index()` | `request()->ip()` | `:47` |
| `ProductController::show()` | `request()->ip()` | `:96` |

**No code anywhere sets a `guest_token` cookie** (verified across all Blade files), so every site collapses to `request()->ip()`.

Consequences:
- Behind a reverse proxy or load balancer, **`request()->ip()` returns the proxy's address** ⇒ every visitor shares one cart.
- Because `store()` and `index()`/`subtotal()`/`AppServiceProvider` use different expressions, a mismatch is possible the moment a cookie *is* introduced.

### BL-C03 — Guest → user merge
**Does not exist.** `AuthController::login()` performs no cart migration. A guest who adds 5 items, registers, and logs in sees an empty cart.

### BL-C04 — Update quantity — **BROKEN**
`CartController::update()` `:35-44`
```php
foreach ($request->quantities as $cartId => $qty) {
    Cart::where('id', $cartId)->update(['product_quantity' => $qty]);
}
```
No ownership scope. No validation. No stock check.

### BL-C05 — Remove item — **BROKEN**
`CartController::remove($id)` `:46-60` ⇒ `Cart::findOrFail($id)->delete()`. No ownership scope, exposed on `GET`.

### BL-C06 — AJAX update/remove — correctly scoped, partial responses
`ajaxUpdate` / `ajaxRemove` (`:92-113`) correctly scope by `user_id` or `ip_addresscart`. Verified source:

| Method | Query key | Response |
|---|---|---|
| `ajaxUpdate` | `where('product_varient_id', $request->product_varient_id)` → `->first()` | `{success: true}` or `{success: false}` |
| `ajaxRemove` | same key | `{success: true}` |

Two gaps:
- **No validation** of `quantity` in `ajaxUpdate`.
- **Keyed on `product_varient_id`, not the cart-line id** ⇒ if the same variant occupies two cart lines, only the first is updated/deleted.
- Neither returns a recalculated subtotal — the caller must issue a second request to `/cart/subtotal`.

### BL-C07 — Cart subtotal badge — **verified source**
`AppServiceProvider.php:26-69`, registered via `View::composer('*')`, so it runs **on every rendered view**.

```php
$cartItems = Auth::check()
    ? Cart::with('variant')->where('user_id', Auth::id())->whereHas('variant')->get()
    : Cart::with('variant')->where('ip_addresscart', $guestToken)->whereNull('user_id')
          ->whereHas('variant')->get();

$cartCount = $cartItems->unique(fn($item) => $item->product_id . '-' . $item->product_varient_id)->count();
$cartSubtotal = $cartItems->sum(fn($item) => ($item->variant->offer_price ?? 0) * $item->product_quantity);
```

| Issue | Detail |
|---|---|
| Runs on every view | Adds a query to every request, including static-ish pages |
| Guest branch uses `request()->ip()` at `:49` | No `guest_token` cookie is ever set, so this is always the IP — see BL-C02 |
| `carts` has no index on `user_id` or `ip_addresscart` | Full table scan per request |
| `$wishlistCount` is only set in the authenticated branch | Guest branch leaves it at `0` — a **guest wishlist badge is always 0**, which is correct behaviour since guests cannot wishlist |
| No `SoftDeletes` awareness on `product` | Orphan lines are excluded via `whereHas('variant')`, but `product` itself may be soft-deleted and still counted |

### BL-C08 — Empty-cart guard
Only in `CheckoutController::index()` `:51-53`. `CartController::index()` renders an empty page instead.

---

## 3. Checkout & orders

### BL-CHK01 — Weight
```
totalWeight = Σ (variant.weight ?? 0.5) × qty
if totalWeight > 10 → totalWeight /= 1000
totalWeight = max(totalWeight, floor)
```
Two different floors: `0.1` at `:110` (quote), `0.5` at `:460` (order). **The quoted courier rate and the actual Shiprocket order use different weights ⇒ the customer can be charged differently from the quote.**

The `> 10 → /1000` heuristic is also unsound: 25 kg becomes 0.025 kg.

### BL-CHK02 — Shipping charge
Live Shiprocket rates only:
```
rate = COD ? (courier.freight_charge + courier.cod_charges) : courier.freight_charge
```
`:130-140`, then `usort` ascending and the cheapest is auto-selected.

`states.shipping_charge` is read **only** by the dead endpoint `getShippingCharge()` `:638-654`.

### BL-CHK03 — Payment
| Method | Behaviour |
|---|---|
| COD | No Razorpay call. Order placed with `payment_method = 'Cash on Delivery'`. **No COD fee reconciliation, no partial-refund path** |
| Online | `preparePayment()` creates a Razorpay order ⇒ modal ⇒ `placeOrder()` verifies the signature |

Signature verification (`:252-268`) is **correct**: it reads `razorpay_order_id`, `razorpay_payment_id`, `razorpay_signature` and calls
```php
$api->utility->verifyPaymentSignature([
    'razorpay_order_id'   => $razorpayOrderId,
    'razorpay_payment_id' => $razorpayPaymentId,
    'razorpay_signature'  => $razorpaySignature,
]);
```

### BL-CHK04 — Order number
`ORD-` + `strtoupper(Str::random(10))`. **Not guaranteed unique**; no DB constraint, no retry on collision.

### BL-CHK05 — Transaction boundary
```php
DB::beginTransaction();            // :292
  ... 5 table groups ...
DB::commit();                      // :421
// Shiprocket createOrder()         // :432  ← OUTSIDE
// assignAWB()                      // :508  ← OUTSIDE
```
The external call deliberately sits outside the transaction (correct — you don't want a slow HTTP call holding a DB lock), but the **failure path swallows the error** (`:525-527`), so the system reports success with no Shiprocket order.

### BL-CHK06 — Stock mutation
`:402-415`
```php
$stock->availablestock = max($stock->availablestock - $qty, 0);
$stock->salestock      = $stock->salestock + $qty;
$variant->product_qty  = max($variant->product_qty - $qty, 0);
```
Two tables, three fields. `max(..., 0)` **masks oversell** — the order succeeds and stock reads 0. No row-level `lockForUpdate()` ⇒ two concurrent checkouts can both claim the last unit.

### BL-C7 / BL-CHK07 — Stock is never restored
No code path increments `availablestock` or decrements `salestock` on cancellation, return, or refund — because none of those features exist (F-46).

### BL-CHK08 — Idempotency
**None.** `placeOrder()` has no idempotency key. A double-submitted form, a retry after a timeout, or a user pressing "Place Order" twice **creates two orders and charges twice.** Verified: no unique constraint on `razorpay_order_id` in `product_orders` (the column comes from a post-dump migration with no uniqueness).

---

## 4. Accounts

### MDL-A01 — Registration
`AuthController.php:14-40`
| Field | Rule |
|---|---|
| `last_name` | required |
| `name` | required |
| `email` | required, email, unique |
| `phone_number` | required |
| `password` | required, **min:6** (no complexity, no confirmation field) |
| `country_id` | from session; `NULL` if no session |

`MustVerifyEmail` is commented out in `User.php:5`. **`User` has no `country_id` in `$fillable` check** — it is set explicitly.

### MDL-A02 — Password hashing inconsistency
| Path | Hashing |
|---|---|
| Register (`AuthController.php:36`) | `Hash::make()` |
| Login (`AuthController.php:55`) | `Hash::check()` |
| Profile update (`ProfileController.php:29-48`) | `bcrypt()` |
| `User.php` cast | `'password' => 'hashed'` |

**`bcrypt()` does not honour the `hashed` cast**, so a profile-changed password bypasses the model's hashing contract. It happens to work only because `bcrypt()` and `Hash::make()` use the same algorithm and default rounds. Any future change to `HASH_DRIVER` breaks login for those users silently.

### MDL-A03 — Login identity
`email = ? OR phone_number = ?` ⇒ a value present in both columns on **different users** matches ambiguously; `->first()` picks one arbitrarily. No index consideration: neither `email` nor `phone_number` is confirmed unique (only `email` has a unique index).

### MDL-A04 — Password reset — **BROKEN, CRITICAL**
`AuthController::resetPassword()` `:95-120` performs:
```php
$user = User::where('email', $request->email)->first();
if (!$user) return 422 ['User not found'];
$user->password = Hash::make($request->password);
$user->save();
```
**No token. No expiry. No confirmation. No notification.** Full account takeover by email address alone. The legitimate `Password::sendResetLink()` path at `:72-93` has **no caller and no token-accepting route.**

### MDL-A05 — Address book — **best-secured code in the project**
```php
abort_unless($address->user_id === Auth::id(), 403);
```
present in all three of `storeAddress` / `updateAddress` / `destroyAddress` (`:553-633`). This is the pattern every other controller should copy.

Fields are free-text `state` / `pincode` strings, not FKs to `states` / `pincodes` ⇒ typos and silent failed deliveries.

### MDL-A06 — Session regeneration
**Not performed on login or logout.** `AuthController::login()` calls `Auth::login($user)` with no `$request->session()->regenerate()`, and `ProfileController::logout()` calls `Auth::logout()` with no `invalidate()`. **Session-fixation risk.**

---

## 5. Country gating

### BR-M1-01 — `country_id` in session is the tenant discriminator
`HomeController.php:12-15`, `ProductController.php:18-20, 73-75`
```php
if (!session('country_id')) return redirect('/select-country');
```
Applied to `/`, `/products`, `/product/{slug}` only.

### BR-M1-02 — Product visibility
```php
where(fn($q) => $q->where('country_id', $countryId)->orWhereNull('country_id'))
```
`COALESCE`-style global + country-specific products. Applied in `ProductController::index():37-40`, `show():78`, `related():80-88`, and `HomeController`.

### BR-M1-03 — Category / brand / subcategory visibility
```php
Category::whereRaw('FIND_IN_SET(?, country_id)", [$countryId])->get()
```
`country_id` is `varchar` ⇒ `FIND_IN_SET` is **unindexable**. Full scan on every listing page.

### BR-M1-04 — Currency
`checkout.blade.php:494` hard-codes `₹` and `currency = 'INR'`. `countries` has `currency`-like columns and `iso_code`, and `CountryController::index()` returns all countries — but selecting "United Kingdom" still shows rupees and Indian addresses.

**Defects:**
- `iso_code` never stored or used.
- `countries.ship_charge` never read.
- No per-country address form, payment method, or tax rules.
- Selecting any non-India country produces a **mis-labelled, mis-priced storefront.**

---

## 6. Fulfilment

### BL-FUL01 — Shiprocket authentication
`ShiprocketService::getToken()` `:25-47` logs in with `email` + `password` from `config/services.php` and caches the token:
```php
return Cache::remember('shiprocket_token_' . md5($email), 8 * 60 * 60, fn() => /* login */);
```
**Verified correct** — token is cached for 8 hours (Shiprocket's tokens last 10 days, so the 8h window is conservative and safe).

**Defects:**
- The cache key is derived from the **email address**, not the token. Changing the email invalidates the cache; two Shiprocket accounts sharing an email would collide.
- **Cache driver is `database` by default** (`config/cache.php`, store `sessions`+`cache` tables present in the dump) ⇒ the token sits in `cache` in cleartext for 8 hours, and it is **not** invalidated on Shiprocket-side token expiry. A 401 from Shiprocket is **never retried** — `client()` does not check the response code and re-authenticate.
- Failure path logs the **full Shiprocket response body** (`Log::error('Shiprocket Login Failed', ['body' => …])`, `:41-44`), which can include account details.

### BL-FUL02 — Order creation payload
`CheckoutController.php:432-507` builds the Shiprocket order from the local cart. **`sku` is passed as the variant id** and `quantity`, `weight`, `dimensions`, `price`, `shipping_method` etc. are assembled client-shape-wise; there is **no mapping table** between `product_varient` fields and Shiprocket's `customs`/`units` requirements.

### BL-FUL03 — AWB assignment
`:508-523` — only runs `if ($request->selected_courier_id)`. Writes `awb_code`, `courier_name`, `status = 'Order Placed'`.

### BL-FUL04 — Status vocabulary mismatch — **VERIFIED CROSS-APP BUG**

| Side | Values used |
|---|---|
| Storefront writes | `status = 'Order Placed'` (`CheckoutController.php:522`) |
| `palVarnanDashboard/…/SyncShiprocketStatus.php` reads | `processing`, `ready_for_pickup`, `shipped`, `out_for_delivery` |

`Order Placed` is **not in the dashboard's filter**, so **no storefront order is ever synced back from Shiprocket.** The `product_tracking` rows written at `:359-363` stay empty forever, and customers can never see tracking.

### BL-FUL05 — Swallowed failures
```php
} catch (Exception $e) {
    Log::error('Shiprocket createOrder failed: ' . $e->getMessage());
}
```
`:525-527` — no `shiprocket_status = 'FAILED'` marker, no retry, no user-visible warning. The order row simply has `shiprocket_order_id = NULL` and nothing ever revisits it.

### BL-FUL06 — No webhook endpoint
Shiprocket push/pull webhooks are not implemented in either app. Status can only change via the dashboard's manual scheduler.

---

## 7. Search

### BR-SEARCH-01 — Four sources, one endpoint
`SearchController::search()` `:15-140` queries `products`, `product_varient`, `brands`, `categories`.

### BR-SEARCH-02 — `MAIN_URL` for images
```php
$baseImageUrl = rtrim(env('MAIN_URL'), '/') . '/images/';
```
`:29`. **`env()` returns `null` once `config:cache` is run** ⇒ base URL becomes `"/images/"` ⇒ broken images in search results.

### BR-SEARCH-03 — Result URLs are broken
- Products ⇒ `/product/{slug}` ✅ (`products.slug` exists)
- Variants ⇒ `product/{$item->product->slug}` ❌ **unreachable — integer `LIKE`**
- Brands ⇒ `/brand/` ❌ no route, no `slug` column
- Categories ⇒ `/category/` ❌ no route, no `slug` column

### BR-SEARCH-04 — No results page
Header form has `action="#"`; results are only a dropdown. **Pressing Enter does nothing.** No pagination, no result count, no highlighting, no sort.

---

## 8. Rules that are *absent*

| Expected rule | Status |
|---|---|
| Minimum order value / free-shipping threshold | **Absent** |
| Coupon validity window / usage cap / per-product applicability | **Absent** |
| Tax-inclusive display, HSN codes, tax breakup | **Absent** |
| Address validation against `pincodes` | **Absent** |
| COD availability by pincode | **Absent** (COD offered for every pincode that Shiprocket returns, even where the courier rejects COD) |
| Return window / eligibility | **Absent** |
| Stock reservation at add-to-cart | **Absent** |
| Idempotency on order placement | **Absent** |
| Order-number uniqueness guarantee | **Absent** |
| Email notification on order | **Absent** (`MAIL_MAILER=log`) |

---

## 9. Related documents

- [04-feature-inventory.md](04-feature-inventory.md) — feature-to-rule mapping
- [05-user-flows.md](05-user-flows.md) — rule execution order per journey
- [07-database-schema.md](07-database-schema.md) — where rules should have been constraints
- [15-broken-features.md](15-broken-features.md) — remediation for each rule gap
- [20-known-issues.md](20-known-issues.md) — bug register