# 27 — API Reference

All **38 routes** in `routes/web.php` (75 lines). Every route is in the `web` group, so CSRF applies to `POST`/`PUT`/`DELETE`. **There is no `routes/api.php`** — no versioned API, no token auth, no `/api/` prefix.

---

## 1. Route table

### Not behind `auth` (28)

| # | Method | URI | Name | Target | CSRF |
|---|---|---|---|---|---|
| 1 | `GET` | `/` | — | `HomeController@index` | n/a |
| 2 | `GET` | `/products` | — | `ProductController@index` | n/a |
| 3 | `GET` | `/product/{slug}` | — | `ProductController@show` | n/a |
| 4 | `GET` | `/product` | — | `Route::view('pages.product-detail')` | n/a |
| 5 | `GET` | `/login` | `login` | `Route::view('pages.login')` | n/a |
| 6 | `GET` | `/register` | `register` | `Route::view('pages.register')` | n/a |
| 7 | `POST` | `/register-store` | `register.store` | `AuthController@registerStore` | yes |
| 8 | `POST` | `/login-store` | `login.store` | `AuthController@login` | yes |
| 9 | `POST` | `/forgot-password` | `password.email` | `AuthController@forgotPassword` | yes |
| 10 | `POST` | `/reset-password-direct` | `password.reset.direct` | `AuthController@resetPassword` | yes |
| 11 | `PUT` | `/profile-update` | `profile.update` | `ProfileController@update` | yes |
| 12 | `POST` | `/logout` | `logout` | `ProfileController@logout` | yes |
| 13 | `GET` | `/select-country` | — | `CountryController@index` | n/a |
| 14 | `POST` | `/select-country` | — | `CountryController@store` | yes |
| 15 | `GET` | `/search` | — | `SearchController@search` | n/a |
| 16 | `GET` | `/cart` | — | `CartController@index` | n/a |
| 17 | `GET` | `/cart/subtotal` | — | `CartController@subtotal` | n/a |
| 18 | `POST` | `/cart-update` | `cart.update` | `CartController@update` | yes |
| 19 | `GET` | `/cart-remove/{id}` | `cart.remove` | `CartController@remove` | **no — deletes on `GET`** |
| 20 | `POST` | `/cart/store` | `cart.store` | `CartController@store` | yes |
| 21 | `POST` | `/cart/ajax-update` | `cart.ajaxUpdate` | `CartController@ajaxUpdate` | yes |
| 22 | `POST` | `/cart/ajax-remove` | `cart.ajaxRemove` | `CartController@ajaxRemove` | yes |
| 23 | `POST` | `/wishlist/toggle` | `wishlist.toggle` | `WishlistController@toggle` | yes |
| 24 | `GET` | `/wishlist` | `wishlist.index` | `WishlistController@index` | n/a |
| 25 | `DELETE` | `/wishlist/{id}/remove` | `wishlist.remove` | `WishlistController@remove` | yes |
| 26 | `GET` | `/wishlist/check` | — | `WishlistController@check` | n/a — **has `auth`** |
| 27 | `GET` | `/wishlist/check-product` | — | `WishlistController@checkProduct` | n/a — **has `auth`** |
| 28 | `GET` | `/myaccount` | `account.index` | `ProfileController@index` | n/a |

### Behind `auth` (10)

| # | Method | URI | Name | Target | CSRF |
|---|---|---|---|---|---|
| 29 | `GET` | `/checkout` | `checkout.index` | `CheckoutController@index` | n/a |
| 30 | `POST` | `/checkout/prepare-payment` | `checkout.preparePayment` | `CheckoutController@preparePayment` | yes |
| 31 | `POST` | `/checkout/get-couriers` | `checkout.getCouriers` | `CheckoutController@getShiprocketCouriers` | yes |
| 32 | `POST` | `/checkout/get-shipping-charge` | `checkout.getShippingCharge` | `CheckoutController@getShippingCharge` | yes |
| 33 | `POST` | `/checkout/get-cities` | `checkout.getCities` | `CheckoutController@getCitiesByState` | yes |
| 34 | `POST` | `/checkout/place-order` | `checkout.placeOrder` | `CheckoutController@placeOrder` | yes |
| 35 | `GET` | `/checkout/thankyou/{order}` | `checkout.thankyou` | `CheckoutController@thankyou` | n/a |
| 36 | `POST` | `/checkout/address` | `address.store` | `CheckoutController@storeAddress` | yes |
| 37 | `PUT` | `/checkout/address/{address}` | `address.update` | `CheckoutController@updateAddress` | yes |
| 38 | `DELETE` | `/checkout/address/{address}` | `address.destroy` | `CheckoutController@destroyAddress` | yes |

### Middleware summary

| Middleware | Count |
|---|---|
| `web` (all, incl. CSRF + session + cookies) | 38 |
| `auth` | **10** |
| `throttle` | **0** |
| Custom middleware | **0** — `app/Http/Middleware/` does not exist |

**28 routes are reachable without authentication, 6 of which write data**: `register-store`, `login-store`, `forgot-password`, `reset-password-direct`, `profile-update`, `logout`, `select-country` POST, `cart-update`, `cart-remove`, `cart/store`, `cart/ajax-update`, `cart/ajax-remove`, `wishlist/toggle`, `wishlist/{id}/remove`.

Ownership is enforced by hand in only four of them — `cart/ajax-update`, `cart/ajax-remove`, `wishlist/{id}/remove`, and the checkout address routes. `cart-update` and `cart-remove` are **not** scoped.

---

## 2. Endpoint reference

### `GET /` — Home

Renders `pages.home` — home promotions, featured products, categories, brands. The template also carries unmodified vendor demo content.

---

### `GET /products` — listing

Query parameters: `category`, `subcategory`, `brand`, `page`. **No sort, no price filter, no search.**

---

### `GET /product/{slug}` — detail

Eager-loads variants, units, images. **500s** when a variant's product is soft-deleted — log: `Attempt to read property "product_image" on null` — or when `variant->unit` is null — `Attempt to read property "name" on null`.

---

### `GET /product` — **BROKEN**

```php
Route::view('/product', 'pages.product-detail');
```

The detail template with **no model bound and no data**. Any add-to-cart widget operates on `null`. Reachable, non-functional, and it competes with `/product/{slug}` for the same template.

**Fix:** delete the route.

---

### `GET /login` · `GET /register`

Static views. Forms post to `/login-store` and `/register-store`.

---

### `POST /register-store`

**Request**

| Field | Rules |
|---|---|
| `last_name` | `required` |
| `name` | `required` |
| `email` | `required`, `email`, `unique:users,email` |
| `phone_number` | `required` — **no uniqueness rule** despite `users_phone_number_unique` |
| `password` | `required`, `min:6` |

`country_id` is taken from `session('country_id')` and never validated.

**Responses**

| Code | Condition |
|---|---|
| `302` | success → `/login` |
| `302` | validation failure |
| `500` | duplicate `phone_number` escapes as a SQL error rather than a field error |

**Defects:** no `confirmed`, no `throttle`, no session regeneration, `email` uniqueness leaks account existence.

---

### `POST /login-store`

**Request**

| Field | Rules |
|---|---|
| `username` | `required` |
| `password` | `required` |

The field is **`username`**, not `email`. Lookup is `email = username OR phone_number = username`, then `Hash::check`.

**Responses**

| Code | Condition |
|---|---|
| `200` | `{ "success": true, "message": "Login successful" }` |
| `422` | `{ "errors": { "username": ["Invalid login credentials"] } }` |

**Defects:** no `throttle`; **no `session()->regenerate()`** (session fixation); generic error message is correct.

---

### `POST /forgot-password`

**Request:** `email` (`required`, `email`)

Calls the real `Password::sendResetLink()` and returns proper status JSON — `{"success": true}` or `422` with the framework's message. `MAIL_MAILER=log` means the mail lands in `storage/logs/laravel.log` instead of an inbox.

**No view in `resources/views/` posts to this route.** The link it would send points at a route that does not exist.

---

### `POST /reset-password-direct` — **S1 ACCOUNT TAKEOVER**

**Request**

| Field | Rules |
|---|---|
| `email` | `required`, `email` |
| `password` | `required`, `min:6` |

```php
$user = User::where('email', $request->email)->first();
if (!$user) { return 422 'User not found'; }
$user->password = Hash::make($request->password);
$user->save();
Auth::logout();
```

**No `token` field is read. No `Password::tokenExists()`. No comparison.** Any unauthenticated request with a known email resets that account's password. There is no `password_confirmation` either.

**Fix:** `Password::reset()` plus a `GET /reset-password/{token}` route and form.

---

### `PUT /profile-update` — **500s when anonymous**

No `auth` middleware. The controller does `$user = auth()->user();` then interpolates `$user->id` into the rule string — a null dereference **before validation executes**.

| Field | Rules |
|---|---|
| `name` | `required` |
| `last_name` | `required` |
| `email` | `required`, `email`, `unique:users,email,{id}` — `{id}` is `null` when anonymous |
| `phone_number` | `nullable` |
| `current_password` | `required` **only when `password` is present** |
| `password` | `required`, `confirmed`, `min:6` — **only when present** |

The password path is correctly implemented: it verifies `current_password` with `Hash::check` before writing `bcrypt($request->password)`. Both `profile_image` and `address` are accepted by no rule and read by no code.

---

### `POST /logout`

Preserves `session('country_id')`, calls `Auth::logout()`, `invalidate()`, `regenerateToken()`, then restores the country. **Correctly implemented** — this is why the missing `regenerate()` in `login()` is the anomaly, not the rule.

---

### `GET /select-country` · `POST /select-country`

`POST` looks the country up with `LOWER(name) = ?`, stores `session(['country_id' => …])`, redirects to `/`. On no match it flashes `'Invalid country selected.'` — `country-selection.blade.php` **never renders that flash**, so the visitor sees a bare redirect with no message. The view also does not extend the site layout, so it has no header, nav, or cart badge.

---

### `GET /search`

**Request:** `q`

Returns **JSON**, not HTML. There is no search results page.

```json
[
  { "name": "Chili Powder", "type": "Product",  "image": "https://…/images/product_images/…", "url": "http://…/product/chili-powder-1776686978" },
  { "name": "Mozzarella - 500", "type": "Variant", "image": "…", "url": "…" },
  { "name": "P&G",           "type": "Brand",    "image": "…", "url": "http://…/brand/" },
  { "name": "Masalas",       "type": "Category", "image": "…", "url": "http://…/category/" }
]
```

Four queries, each `LIKE "%q%"`, each limited to 5. No ordering, no relevance, no debounce in the header.

| Defect | Detail |
|---|---|
| Variant search matches `product_varient.varient`, which is **`int(11)`** | `LIKE '%…%'` on an integer column can never match — the entire variant branch is dead code |
| `env('MAIN_URL')` | returns `null` under `config:cache`, so image URLs collapse to `/images/…` |
| Brand URL uses `$item->slug` | `brands` has **`brand_slug`**, not `slug` ⇒ `/brand/` |
| Category URL uses `$item->slug` | `categories` has **no slug column at all** ⇒ `/category/` |
| `url()` on every result | per-row URL generation inside a loop |
| Only product URLs resolve | the other three link types 404 |

---

### `GET /cart`

`Cart::with(['product','variant'])`, scoped to `user_id` when logged in, else to `ip_addresscart` matched against `cookie('guest_token') ?? ip()` with `whereNull('user_id')`. Then `whereHas('variant')`.

`index()` computes `$subtotal` with `$item->variant->offer_price * $item->product_quantity` — **no null coalescing**, unlike `subtotal()`, which uses `?? 0`. If `offer_price` is `NULL` on any variant, `index()` returns PHP `0` and emits a warning while `subtotal()` silently treats it as `0`.

---

### `GET /cart/subtotal`

**Request:** none

```json
{ "subtotal": 2450 }
```

Single key, `sum()` over `offer_price ?? 0 * quantity`. Same guest-identity fallback as `index()`.

---

### `POST /cart/store`

**Request** — **no validation whatsoever**

| Field | Type | Notes |
|---|---|---|
| `product_id` | any | not validated |
| `product_varient_id` | any | not validated |
| `quantity` | any | defaults to `1`; **no `min`, no `max`, no stock check** |
| `pre_order` | any | passed to `create()` but **stripped by `Cart::$fillable`** |
| `pre_note` | any | never read by the controller |

**Responses**

| Code | Body |
|---|---|
| `200` | `{"status":"incremented","message":"Cart updated!"}` when the line exists |
| `200` | `{"status":"added","message":"Product added to cart!","quantity":1}` when it does not |

The existence check matches on `user_id` **or** `ip_addresscart` + `whereNull('user_id')` — correct scoping. But an existing line is always `increment('product_quantity', 1)`, so **a second add of 3 units adds 1**; the response's `quantity` is also hard-coded to `1` even when it incremented. Every current Blade caller sends `1`, so the bug is latent.

---

### `POST /cart-update` — **IDOR, no validation**

**Request:** `quantities` — an **associative array of `cartId => qty`**.

```php
foreach ($request->quantities as $cartId => $qty) {
    Cart::where('id', $cartId)->update(['product_quantity' => $qty]);
}
```

- **No `user_id` or `ip_addresscart` scope** ⇒ any visitor rewrites any cart line by id.
- **No validation** ⇒ negative, zero, and arbitrarily large quantities are accepted.
- **No existence check** ⇒ a non-existent id silently updates 0 rows.
- **N queries for one form post.**
- If `quantities` is absent, `$request->quantities` is `null` and the `foreach` emits a warning.

Earlier documentation described this endpoint as taking `product_varient_id` / `product_quantity`. **That is `ajaxUpdate`'s contract, not this one.**

---

### `GET /cart-remove/{id}` — **IDOR + wrong verb**

```php
$cart = Cart::findOrFail($id);
$cart->delete();
```

- **Deletes on `GET`.** No CSRF protection applies, prefetchers and crawlers can trigger it, and it is linkable.
- **No ownership scope** ⇒ any visitor deletes any cart line.
- Returns JSON when `request()->ajax()`, else `back()`.

**Fix:** `Route::delete('/cart/{id}', …)` with the owner scope applied.

---

### `POST /cart/ajax-update`

**Request:** `product_varient_id`, `quantity` — **no validation**.

Scoped correctly: `user_id` when logged in, else `ip_addresscart` + `whereNull('user_id')`. Matches on **`product_varient_id` only**, which is correct — a cart legitimately holds one row per variant.

```json
{ "success": true }
```
```json
{ "success": false }
```

No subtotal in the response; the Blade view refetches `/cart/subtotal` separately.

---

### `POST /cart/ajax-remove`

**Request:** `product_varient_id` — no validation.

Scoped correctly, same guest fallback. Matches on **`product_varient_id`, not the cart line id** — so removing a variant deletes the row by variant, which can differ from what the user clicked if duplicate rows exist.

```json
{ "success": true }
```

---

### `POST /wishlist/toggle`

**Request** — no validation

| Field | Notes |
|---|---|
| `product_id` | sent as `product_id` |
| `variant_id` | **note the name** — cart endpoints use `product_varient_id` |

Scoped on `Auth::id()`, which is **`null` when logged out** because the route has no `auth` middleware. An anonymous toggle writes `user_id = null, ip_address = <ip>`, and a later `index()` reads `user_id = null` — surfacing rows created by any other anonymous visitor.

```json
{ "success": true, "status": "added" }
```
```json
{ "success": true, "status": "removed" }
```

Duplicates are handled in PHP via `first()` then `delete()`/`create()`; a race can insert two rows because `wishlists` has **no unique constraint** on `(user_id, product_id, product_varient_id)`.

---

### `GET /wishlist`

Eager-loads `product.variants.unit` and `variant.unit`. **No `auth` middleware** ⇒ reads `user_id = null`. 500s on a null `variant` or `unit` relation.

---

### `DELETE /wishlist/{id}/remove` — **correctly scoped**

```php
Wishlist::where('id', $id)->where('user_id', Auth::id())->delete();
```

Right verb, right scope, `firstOrFail`-free but harmless (a `DELETE` affecting 0 rows is indistinguishable from success to the caller). Earlier documentation called this an IDOR — **it is not.** The residual issue is that `Auth::id()` is `null` for anonymous callers, so it deletes `user_id IS NULL` rows.

---

### `GET /wishlist/check` · `GET /wishlist/check-product`

The **only two individually-middleware'd routes outside the checkout group** — both have `auth`. Used by the product-page buttons. Neither validates its input.

`checkProduct()` has mojibake in its source comments (`// �+? correct column name`) — evidence of an encoding-mismatched edit.

---

### `GET /myaccount` — **no `auth` middleware**

`ProfileController@index()` reads `Auth::id()` and, for a guest, returns the login view from inside the controller rather than via middleware. Loads wishlist, cart, orders, addresses, and **all** states (`State::all()` — no country filter). 500s on a null `unit` relation.

---

## 3. Checkout API

All ten behind `auth`.

### `GET /checkout`

| Element | Source |
|---|---|
| line items | cart, `whereHas('variant')` |
| subtotal | `variant->offer_price ?? product->product_price ?? 0` |
| GST | computed per item from `variant->product_gst` |
| shipping charge | from the client request — **unvalidated** |
| discount | from the client request — **unvalidated** |
| total | client-influenced, floored |

**`product_varient` has no `weight`, `length`, `breadth`, or `height` column**, so any weight this view computes comes from hard-coded fallbacks. The order path uses `0.5` kg; this path uses a different constant. Two different numbers for the same parcel.

---

### `POST /checkout/prepare-payment`

**Request**

| Field | Type | Trusted? |
|---|---|---|
| `shipping_charge` | numeric | **client-set, unbounded, unvalidated** |
| `coupon_discount` | numeric | **client-set, unbounded, unvalidated** |

**No `$request->validate()` call exists in this method.** Subtotal and GST *are* recomputed from the cart at `:181-189`; `shipping_charge` and `coupon_discount` are `floatval()`-ed straight from the request at `:191-192`.

```php
$total = max(($subtotal + $gstAmount + $shipping) - $discount, 1);
```

**Response** `200`

```json
{
  "status": "success",
  "razorpay_order_id": "order_XXXXXXXXXXXXXX",
  "amount": 2450,
  "user": { "name": "…", "email": "…", "phone": "…" },
  "razorpay_key": "rzp_live_XXXXXXXX"
}
```

`amount` is in **rupees**, not minor units — the Blade view multiplies before handing it to the Razorpay checkout script. `razorpay_key` is public by design; the secret never leaves the server.

| Issue | Detail |
|---|---|
| Client sets shipping and discount | a large `coupon_discount` reduces the total to the `max(…, 1)` floor |
| `coupons` table never queried | there is no server-side coupon validation to stop it |
| `max(…, 1)` floor | `placeOrder()` has no floor — the two totals can disagree |
| `receipt` is `'rcpt_' . uniqid()` | not the internal `order_number`; nothing to reconcile |
| **no `notes` payload** | only `receipt`, `amount`, `currency` are sent — **no PII reaches Razorpay here** |
| no idempotency key | a retry can create a second Razorpay order |
| error path | `'Payment initiation failed: ' . $e->getMessage()` reaches the browser |
| `Razorpay\Api\Api` is `require_once`'d at `:26-31` | the SDK is not in `composer.lock` |

---

### `POST /checkout/get-couriers`

**Request**

| Field | Type | Validated? |
|---|---|---|
| `weight` | numeric | no |
| `pincode` | string | no |

Calls `ShiprocketService::checkServiceability()` (`GET /courier/serviceability`), which forwards whatever it is given.

**Response** `200`

```json
{
  "status": "success",
  "services": [ { "courier_name": "…", "charge": 145.0, "delivery_days": "3", "id": "…" } ]
}
```

**No timeout and no retries** on the HTTP call. A slow Shiprocket response stalls the page indefinitely.

---

### `POST /checkout/get-shipping-charge`

**Request:** `state_name` — no validation. `trim($request->state_name)` on an absent field emits a warning.

```json
{ "status": "success", "shipping_charge": 85 }
```
```json
{ "status": "error", "message": "State not found." }
```
HTTP `404` on a miss. Lookup is by **name**, so a typo is indistinguishable from a genuinely unsupported state.

---

### `POST /checkout/get-cities`

**Request:** `state_name` — no validation.

```json
{ "status": "success", "cities": ["Coimbatore", "Chennai"] }
```

`District::where('state_name', …)->orderBy('name')->pluck('name')` — a **string join to `states.name`**, with no FK and no index. Always returns `200`, so the UI cannot tell "no cities" from "bad state".

---

### `POST /checkout/place-order` — the critical endpoint

**Request** — 9 validated fields, **flat `shipping_*` strings, not address objects**

| Field | Rules | Trusted? |
|---|---|---|
| `shipping_name` | `required`, `string` | validated |
| `shipping_email` | `required`, `email` | validated |
| `shipping_phone` | `required` | no length or numeric rule |
| `shipping_door_no` | `required` | — |
| `shipping_street` | `required` | — |
| `shipping_city` | `required` | — |
| `shipping_state` | `required` | no `exists:states,name` |
| `shipping_pincode` | `required` | no format rule |
| `payment_method` | `required`, `in:Online Payment,Cash on Delivery` | validated |
| **`shipping_charge`** | **not a validated rule** | **read raw at `:286`** |
| **`coupon_discount`** | **not a validated rule** | **read raw at `:287`** |
| `razorpay_order_id` | not a rule; presence checked manually when `payment_method === 'Online Payment'` | — |
| `razorpay_payment_id` | same | — |
| `razorpay_signature` | same | — |

**There is no `billing_address` parameter.** Billing is populated from the shipping fields at `:299-311`. **There is no `products` parameter and no `total` parameter** — line items come from the DB cart, and the total is computed.

```php
$total = ($subtotal + $gstAmount + $shipping) - $discount;   // :288 — no floor
```

**Responses**

| Code | Condition |
|---|---|
| `200` | `{ "status": "success", "message": "Order placed successfully!", "order_id": "ORD-…", "amount": … }` |
| `400` | empty cart, missing Razorpay details, or failed signature verification |
| `422` | validation failed — `{status, message, errors}` |
| `500` | transaction failure — **historically the normal path** |

**Log-confirmed failures**

```
SQLSTATE[42S22]: Column not found: 1054 Unknown column 'billing_state' in 'field list'
SQLSTATE[42S22]: Column not found: 1054 Unknown column 'product_image' in 'field list'
```

**The money-loss sequence:** Razorpay payment succeeds → `DB::beginTransaction()` → `ProductOrder::create()` fails on the missing column → `DB::rollBack()` → `500` to the browser. **The customer has paid and no order exists.** Log evidence shows later successful entries, which implies unversioned manual `ALTER TABLE` statements in production that no migration in this repository records.

**Correct behaviour in current source**

| Concern | Status |
|---|---|
| Stock decrement | `max(availablestock - qty, 0)` — cannot go negative, but **silently oversells** and sets no backorder flag |
| Row locking | **none** — no `lockForUpdate()`, so two concurrent orders can both pass a stock check |
| Shiprocket | runs **after** `DB::commit()` at `:421`, so a gateway failure cannot roll back the order |
| Shiprocket failures | `catch (\Exception)` at `:525` logs and **swallows**; the customer still sees success |
| `shiprocket_shipment_id` | **assigned from `$shipResponse['shipment_id']`** and passed as `shipment_id` to `assignAWB` — **correct**, contrary to earlier documentation |
| `payment_status` | set to `'Paid'` at `:324` **inside** the transaction, before the inserts that can fail |
| Shiprocket payload logged | `Log::info('Creating Shiprocket order', ['payload' => …])` writes the full customer address to disk |
| Pickup scheduling | `generatePickup()` is **never called** — orders exist but are never collected |
| `payment_method` mapping | `'Online Payment' → 'Prepaid'`, else `'COD'` — correct |
| `billing_country` | hard-coded `'India'` |

---

### `GET /checkout/thankyou/{order}`

```php
$order = ProductOrder::with('items')
    ->where('order_number', $orderNumber)
    ->where('user_id', Auth::id())
    ->firstOrFail();
```

**Ownership is enforced.** `{order}` is a plain string parameter, not route-model binding, and the query is scoped by `user_id` with `firstOrFail()` — another user's order yields `404`, not `403`, and reveals nothing. Earlier documentation claimed this was an IDOR; **it is not.**

---

### `POST /checkout/address` · `PUT /checkout/address/{address}` · `DELETE /checkout/address/{address}`

All three behind `auth`. Validation goes through the shared `validateAddress()` helper — **10 rules with typed array syntax and length caps**, the best-validated form in the application.

**Ownership is enforced on update and delete:**

```php
abort_unless($address->user_id === Auth::id(), 403);
```

Earlier documentation claimed these were IDORs. **They are not.** The `403` also leaks existence — a `404` from a scoped `findOrFail()` would be marginally better.

Remaining gaps: `pincode` is `max:10` with no format rule, `state` is unvalidated free text, and neither `area_id` nor `country_id` is accepted, so `UserAddress::area()` and the broken `country()` relation are unreachable from the form.

`storeAddress()` catches `\Throwable` and returns a generic message — **not** `$e->getMessage()`. It logs the real exception. This is better than earlier documentation claimed.

---

## 4. Response-shape inconsistencies

| Endpoint | Returns |
|---|---|
| `POST /register-store` | `302` redirect with flash |
| `POST /login-store` | `422` `{errors:{…}}` on failure |
| `GET /cart/subtotal` | `{subtotal}` |
| `POST /cart/ajax-update` | `{success: true\|false}` |
| `POST /cart/ajax-remove` | `{success: true}` |
| `POST /cart/store` | `{status, message, quantity?}` |
| `POST /wishlist/toggle` | `{success, status}` |
| `POST /checkout/prepare-payment` | `{status, razorpay_order_id, amount, user, razorpay_key}` |
| `POST /checkout/place-order` | `{status, message, …}` |
| `GET /checkout/get-cities` | `{status, cities}` |

**Two conventions coexist.** `AuthController` uses Laravel's `{errors: {field: [...]}}` shape for 422; `CheckoutController` uses `{status, message}`. Blade code must know which one to expect. Success is sometimes `{success: true}`, sometimes `{status: 'success'}`.

**No global exception handler.** `bootstrap/app.php` has an empty `withExceptions()`. SQL errors, missing-class errors, and Shiprocket exceptions all surface as raw 500 pages or JSON with `$e->getMessage()`.

---

## 5. Route defect summary

| ID | Defect | Route |
|---|---|---|
| **BUG-01** | password reset needs no token — **S1** | `POST /reset-password-direct` |
| **BUG-06** | cart update IDOR + no validation | `POST /cart-update` |
| **BUG-07** | cart remove IDOR **on `GET`** | `GET /cart-remove/{id}` |
| **BUG-09** | account page reachable unauthenticated | `GET /myaccount` |
| **BUG-10** | profile update 500s when anonymous | `PUT /profile-update` |
| **BUG-11** | wishlist toggle/index unauthenticated → `user_id = null` | `POST /wishlist/toggle`, `GET /wishlist` |
| **BUG-13** | no `throttle` anywhere | all 38 |
| **BUG-67** | view with no data | `GET /product` |
| BUG-24 | variant search on an `int(11)` column | `GET /search` |
| BUG-25 | brand URL uses a non-existent `slug` | `GET /search` |
| BUG-26 | category URL uses a non-existent `slug` | `GET /search` |
| BUG-50 | country-select error flash never rendered | `POST /select-country` |
| — | guest-cart identity inconsistent between reads and writes | 6 cart routes |
| — | 2 dead endpoints | `get-shipping-charge`, `get-cities` |

**10 of 38 routes carry a security or correctness defect. 28 of 38 need no authentication.**

Correctly implemented, worth preserving: `DELETE /wishlist/{id}/remove` (scoped), both checkout address write routes (403 on foreign rows), `GET /checkout/thankyou/{order}` (owner-scoped, 404 on miss), `POST /logout` (invalidate + regenerate), `POST /cart/ajax-update` and `POST /cart/ajax-remove` (scoped), `POST /login-store` (generic error).

---

## 6. Proposed corrected surface

```php
Route::middleware('auth')->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');

    Route::post('/checkout/place-order', [CheckoutController::class, 'placeOrder'])
        ->middleware('throttle:5,1')
        ->name('checkout.placeOrder');

    Route::get('/checkout/thankyou/{order}', [CheckoutController::class, 'thankyou'])
        ->middleware('can:view,order')
        ->name('checkout.thankyou');

    Route::resource('checkout.address', AddressController::class)->only(['store', 'update', 'destroy']);

    Route::get('/myaccount', [ProfileController::class, 'index'])->name('account.index');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

Route::put('/profile-update', …)->middleware('auth');            // fixes the 500
Route::post('/wishlist/toggle', …)->middleware('auth');
Route::get('/wishlist', …)->middleware('auth');

Route::delete('/cart/{id}', [CartController::class, 'remove'])->name('cart.remove');
Route::delete('/wishlist/{id}', [WishlistController::class, 'remove']);

Route::post('/login-store', …)->middleware('throttle:5,1');
Route::post('/register-store', …)->middleware('throttle:5,1');
Route::post('/forgot-password', …)->middleware('throttle:3,1');
Route::post('/reset-password', …)->middleware('throttle:3,1');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');

Route::get('/search', [SearchController::class, 'search'])->middleware('throttle:30,1');
```

Net change: `auth` on the four unprotected write paths, `GET`-deletes become `DELETE`, `throttle` on every credential and order endpoint, a real reset-token route, and ownership moved from ad-hoc `where` clauses into policies.

---

## 7. Related documents

- [06-api-and-routes.md](06-api-and-routes.md) — routing architecture
- [05-user-flows.md](05-user-flows.md) — who calls what
- [13-security-audit.md](13-security-audit.md) — SEC-numbered findings
- [25-forms-and-validation.md](25-forms-and-validation.md) — field-by-field validation
- [08-business-logic.md](08-business-logic.md) — controller internals
- [22-database-integrity.md](22-database-integrity.md) — the missing-column migrations