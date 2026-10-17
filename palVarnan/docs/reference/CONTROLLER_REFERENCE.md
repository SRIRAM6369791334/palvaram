# Controller Reference — Project B (`palVarnan`)

> Canonical reference for every controller and public method in `app/Http/Controllers/`.
> Evidence vocabulary: `VERIFIED_FROM_SOURCE`, `VERIFIED_FROM_LIVE_SCHEMA`, `INFERRED`,
> `UNVERIFIED`, `NOT_AVAILABLE`, `CONFLICTING_EVIDENCE`.
> **No credential or PII values are reproduced in this document.**
>
> Sibling references: [`./ROUTES_AND_API_REFERENCE.md`](./ROUTES_AND_API_REFERENCE.md),
> [`./MIDDLEWARE_AND_AUTHORIZATION.md`](./MIDDLEWARE_AND_AUTHORIZATION.md).
> Narrative context: [`../02-architecture.md`](../02-architecture.md),
> [`../08-business-logic.md`](../08-business-logic.md),
> [`../27-api-reference.md`](../27-api-reference.md).

---

## 1. Inventory

`app/Http/Controllers/` contains **10 PHP files**: 9 domain controllers + the abstract
base `Controller`. All are fully qualified as `App\Http\Controllers\*`.
`VERIFIED_FROM_SOURCE` (directory listing; each file checked line-by-line).

| Controller | File | Lines | Public methods | Auth-protected? |
|---|---|---|---|---|
| `Controller` | `app/Http/Controllers/Controller.php` | 8 | 0 (abstract base) | n/a |
| `AuthController` | `app/Http/Controllers/AuthController.php` | 129 | 4 | **No** — all public |
| `CartController` | `app/Http/Controllers/CartController.php` | 147 | 7 | **Mostly no** |
| `CheckoutController` | `app/Http/Controllers/CheckoutController.php` | **701** | 10 public + 2 private | **Yes** (`auth` group) |
| `CountryController` | `app/Http/Controllers/CountryController.php` | 29 | 2 | **No** |
| `HomeController` | `app/Http/Controllers/HomeController.php` | 28 | 1 | **No** |
| `ProductController` | `app/Http/Controllers/ProductController.php` | 180 | 2 | **No** |
| `ProfileController` | `app/Http/Controllers/ProfileController.php` | 103 | 3 | **Partial / inconsistent** |
| `SearchController` | `app/Http/Controllers/SearchController.php` | 92 | 1 | **No** |
| `WishlistController` | `app/Http/Controllers/WishlistController.php` | 94 | 5 | **Partial** |

Line counts are the checked-out file lengths. `VERIFIED_FROM_SOURCE`.

There is **no** `app/Http/Controllers/Api/` sub-namespace, no invokable controller, and no
`__construct` in any controller. `VERIFIED_FROM_SOURCE`.

---

## 2. `Controller` (base)

`app/Http/Controllers/Controller.php:5-8`

```php
abstract class Controller
{
    //
}
```

Empty abstract base — it declares **no** `AuthorizesRequests`, `ValidatesRequests`, or any
trait. Consequence: no controller can call `$this->authorize(...)`; controllers use
`$request->validate(...)` inline instead. `VERIFIED_FROM_SOURCE`.

---

## 3. `AuthController`

`app/Http/Controllers/AuthController.php` (129 lines). All methods are **public routes**
with no `auth` middleware. Imports `User`, `Auth`, `Hash`, `Password`.

| # | Public method | Lines | Route | Middleware | Purpose |
|---|---|---|---|---|---|
| A1 | `registerStore(Request)` | `:13-43` | `POST /register-store` (`register.store`) | web | Validates 5 fields, reads `session('country_id')`, `User::create([...])`, redirects to `/login` |
| A2 | `login(Request)` | `:46-72` | `POST /login-store` (`login.store`) | web | Validates `username`/`password`; looks up `email OR phone_number`; `Hash::check`; `Auth::login`; returns JSON |
| A3 | `forgotPassword(Request)` | `:75-97` | `POST /forgot-password` (`password.email`) | web | `Password::sendResetLink()`; returns JSON status |
| A4 | `resetPassword(Request)` | `:100-126` | `POST /reset-password-direct` (`password.reset.direct`) | web, **public** | Validates `email`+`password`; finds user by email; overwrites password with `Hash::make`; `Auth::logout()` — **no token**. See **SEC-B-01**. |

Notes:

- `registerStore()` validates `last_name`, `name`, `email` (`unique:users,email`), `phone_number`,
  `password` (`min:6`). `country_id` comes from the session and is **not validated** (`:24`,
  `:30`). `VERIFIED_FROM_SOURCE`.
- `login()` does **not** call `Auth::attempt()` and does **not** regenerate the session —
  session-fixation gap, tracked in [`../13-security-audit.md`](../13-security-audit.md) (SEC-16).
  `VERIFIED_FROM_SOURCE`.
- `forgotPassword()` calls the real broker but no view/route consumes the mailed token; the
  mailer is `log`. `VERIFIED_FROM_SOURCE`.
- `resetPassword()` is the critical unauthenticated takeover path. Full finding:
  [`./SECURITY_AUDIT.md`](./SECURITY_AUDIT.md) SEC-B-01.

---

## 4. `CartController`

`app/Http/Controllers/CartController.php` (147 lines). Imports `Cart`, `Request`, `Auth`.

| # | Public method | Lines | Route | `auth`? | Ownership scope | Purpose |
|---|---|---|---|---|---|---|
| C1 | `index()` | `:11-31` | `GET /cart` | No | `user_id` **or** guest `ip_addresscart` | Renders `pages.cart`; computes subtotal from `variant->offer_price` |
| C2 | `update(Request)` | `:33-44` | `POST /cart-update` (`cart.update`) | No | **NONE — IDOR** | Iterates `$request->quantities` (`cartId => qty`), `Cart::where('id',$cartId)->update(...)` |
| C3 | `remove($id)` | `:46-62` | `GET /cart-remove/{id}` (`cart.remove`) | No | **NONE — IDOR** | `Cart::findOrFail($id)->delete()`; JSON if `ajax()`, else `back()` |
| C4 | `store(Request)` | `:64-93` | `POST /cart/store` (`cart.store`) | No | `user_id` / guest `ip` | Deduplicates on product+variant; `increment` or `Cart::create`; returns JSON |
| C5 | `ajaxUpdate(Request)` | `:95-113` | `POST /cart/ajax-update` (`cart.ajaxUpdate`) | No | **Correct** `user_id` / `ip` | Updates quantity by `product_varient_id`; `{success:bool}` |
| C6 | `ajaxRemove(Request)` | `:115-128` | `POST /cart/ajax-remove` (`cart.ajaxRemove`) | No | **Correct** `user_id` / `ip` | Deletes by `product_varient_id`; `{success:true}` |
| C7 | `subtotal()` | `:130-145` | `GET /cart/subtotal` | No | `user_id` / guest `ip` | `{subtotal:number}` |

Key facts:

- **`update()` and `remove()` enforce no ownership.** Tracked as **SEC-B-03** and **SEC-B-04**
  in [`./SECURITY_AUDIT.md`](./SECURITY_AUDIT.md). `VERIFIED_FROM_SOURCE`.
- `update()` writes `$qty` raw — no validation (`:37-40`). `VERIFIED_FROM_SOURCE`.
- Guest identity is read as `cookie('guest_token') ?? request()->ip()` in `index()`/`subtotal()`
  (`:18`, `:137`) but only `$request->ip()` in `store()`/`ajaxUpdate()`/`ajaxRemove()`
  (`:72`, `:102`, `:122`). Since no code sets `guest_token`, the IP branch always wins — the
  two read/write shapes are inconsistent by intent. `VERIFIED_FROM_SOURCE`.
- `remove()` is reachable by **GET** (`routes/web.php:50`) — a state-changing GET with no CSRF
  coverage. `VERIFIED_FROM_SOURCE`.

---

## 5. `CheckoutController` — **701 lines** (the largest unit)

`app/Http/Controllers/CheckoutController.php`. All ten public methods sit **inside** the
`auth` group (`routes/web.php:71-83`). It is the only controller that performs a DB
transaction, payment capture, and outbound fulfilment calls. `VERIFIED_FROM_SOURCE`.

Top of file `:26-31` conditionally `require_once`s the Razorpay SDK from
`vendor/razorpay/razorpay/Razorpay.php` — a legacy workaround (the package is now declared in
`composer.json:12`, so this block is redundant but harmless). `VERIFIED_FROM_SOURCE`.

| # | Public method | Lines | Route | Purpose |
|---|---|---|---|---|
| K1 | `index(Request)` | `:38-84` | `GET /checkout` | Loads addresses, selected address, cart items, subtotal/GST/weight, `State::all()`; redirects to `/cart` if empty |
| K2 | `getShiprocketCouriers(Request)` | `:89-167` | `POST /checkout/get-couriers` | Computes weight; calls `ShiprocketService::checkServiceability()`; maps/sorts couriers; returns JSON |
| K3 | `preparePayment(Request)` | `:172-220` | `POST /checkout/prepare-payment` | Recomputes subtotal/GST server-side; reads `shipping_charge`/`coupon_discount` from the request; creates a Razorpay order via `new Api(...)`; returns order id, amount, `razorpay_key` |
| K4 | `placeOrder(Request)` | `:225-535` | `POST /checkout/place-order` | Validates 9 fields; verifies Razorpay signature (online); writes 6 tables in a `DB::beginTransaction()`; deducts stock; clears cart; creates Shiprocket order + optional AWB **after commit** |
| K5 | `thankyou($orderNumber)` | `:540-548` | `GET /checkout/thankyou/{order}` | `ProductOrder::with('items')->where('order_number',...)->where('user_id',Auth::id())->firstOrFail()`; renders `pages.thankyou` |
| K6 | `storeAddress(Request)` | `:553-589` | `POST /checkout/address` | `validateAddress()`, `UserAddress::create`, JSON for AJAX else redirect |
| K7 | `updateAddress(Request, UserAddress)` | `:594-622` | `PUT /checkout/address/{address}` | `abort_unless($address->user_id === Auth::id(), 403)`; updates via `validateAddress()` |
| K8 | `destroyAddress(UserAddress)` | `:627-633` | `DELETE /checkout/address/{address}` | `abort_unless(...403)`; deletes |
| K9 | `getShippingCharge(Request)` | `:638-654` | `POST /checkout/get-shipping-charge` | `State::where('name',...)->first()`; returns `{shipping_charge}`; **no caller in Blade** (`INFERRED` dead) |
| K10 | `getCitiesByState(Request)` | `:659-670` | `POST /checkout/get-cities` | `District::where('state_name',...)->pluck('name')`; returns `{cities}`; **no caller in Blade** (`INFERRED` dead) |

Private helpers (not routable):

| Helper | Lines | Purpose |
|---|---|---|
| `validateAddress(Request): array` | `:672-686` | 10 typed rules with length caps — the most complete validator in the app |
| `getCartItems()` | `:688-700` | Cart query scoped to `user_id` / guest `ip` with `whereHas('variant')` |

Critical facts about `placeOrder()` (`:225-535`) — `VERIFIED_FROM_SOURCE`:

1. **It DOES write an order.** Inside `DB::beginTransaction()` (`:292`) it creates rows in:
   `product_orders` (`:295`), `product_order_user_addresses` (`:330`),
   `product_transaction_logs` (`:348`), `product_tracking` (`:359`), then per cart line
   `product_order_items` (`:373`) and `product_slots` (`:388`), deducts
   `productstocks`/`product_varients` (`:403-415`), clears `carts` (`:419`), and `DB::commit()`
   (`:421`). The pre-existing claim *"checkout writes no order"* is **OUTDATED** —
   see [`./SECURITY_AUDIT.md`](./SECURITY_AUDIT.md) (resolved item R-2).
2. Razorpay signature verification for `Online Payment` uses
   `$api->utility->verifyPaymentSignature($attributes)` (`:264`) — the correct SDK helper.
3. `shipping_charge` and `coupon_discount` are `floatval($request->...)` (`:286-288`) — the two
   client-controlled money inputs. See **SEC-B-02**.
4. Shiprocket `createOrder()` (`:498`) and optional `assignAWB()` (`:510`) run **after**
   `DB::commit()` (`:421`), wrapped in a `catch (\Exception)` that only logs (`:525-527`) — a
   fulfilment failure is invisible to the customer.
5. `payment_status` is written `'Paid'` at `:324` **inside** the transaction, before the rows
   that can fail.
6. The full Shiprocket payload (customer name/address/phone/email) is logged at `info`
   (`:497`). Never reproduced here. See [`../13-security-audit.md`](../13-security-audit.md) SEC-18.

Address ownership (`:596`, `:629`) and the thank-you scoping (`:544`) are correct — the house
pattern. `VERIFIED_FROM_SOURCE`.

---

## 6. `CountryController`

`app/Http/Controllers/CountryController.php` (29 lines).

| # | Public method | Lines | Route | Purpose |
|---|---|---|---|---|
| N1 | `index()` | `:11-15` | `GET /select-country` | `Country::orderBy('name')->get()`; renders `country-selection` |
| N2 | `store(Request)` | `:17-28` | `POST /select-country` | Case-insensitive lookup `LOWER(name)`; `session(['country_id'=>...])`; redirect `/`; flash error on miss |

No validation on `store()`; the error flash is rendered by `country-selection.blade.php:46-48`
(the pre-existing claim that the flash is never rendered is `CONFLICTING_EVIDENCE` — the
country-selection view **does** render it; the modal in `layouts/country-modal.blade.php` does
not). `VERIFIED_FROM_SOURCE`.

---

## 7. `HomeController`

`app/Http/Controllers/HomeController.php` (28 lines).

| # | Public method | Lines | Route | Purpose |
|---|---|---|---|---|
| H1 | `index()` | `:11-27` | `GET /` | Redirects to `/select-country` if no `country_id`; loads `HomePromotion` (banners) and `Category` via `FIND_IN_SET`; renders `pages.home` |

The `FIND_IN_SET(?, country_id)` pattern (`:24`) relies on session `country_id`, which
`CountryController::store()` resolves from the DB — not directly injectable, but fragile.
See [`../13-security-audit.md`](../13-security-audit.md) SEC-21. `VERIFIED_FROM_SOURCE`.

---

## 8. `ProductController`

`app/Http/Controllers/ProductController.php` (180 lines). Imports `Brand`, `Cart`, `Category`,
`Product`, `SubCategory`, `Wishlist`, `Request`, `Auth`.

| # | Public method | Lines | Route | Purpose |
|---|---|---|---|---|
| P1 | `index(Request)` | `:16-108` | `GET /products` | Country filter + optional `category`/`subcategory`/`brand`; sort switch (`price_low_high`, `price_high_low`, `name_asc`, `name_desc`, default latest); `per_page` whitelist `[6,12,24,48]`; `paginate`; loads cart/wishlist ids |
| P2 | `show($slug)` | `:110-178` | `GET /product/{slug}` | `firstOrFail` by slug; eager-loads variants/unit/category/brand/childImages; builds related + filler products; cart/wishlist ids; renders `pages.product-detail` |

Both redirect to `/select-country` when no `country_id` (`:18`, `:112`). `VERIFIED_FROM_SOURCE`.
The sort `orderByRaw` sub-selects (`:84`, `:87`) target `product_varients.offer_price`. There is
a commented-out block at `:167-173` (dead code). `VERIFIED_FROM_SOURCE`.

---

## 9. `ProfileController`

`app/Http/Controllers/ProfileController.php` (103 lines). Imports `Cart`, `ProductOrder`,
`Wishlist`, `Request`, `Auth`, `Hash`.

| # | Public method | Lines | Route | `auth`? | Purpose |
|---|---|---|---|---|---|
| R1 | `update(Request)` | `:14-49` | `PUT /profile-update` (`profile.update`) | **No** | `auth()->user()` then `$user->id` → null-deref 500 when anonymous. Updates name/email/phone; optional password change with `current_password` + `confirmed` + `Hash::check` |
| R2 | `logout(Request)` | `:51-65` | `POST /logout` (`logout`) | **No** | Preserves `country_id`; `Auth::logout()`, `invalidate()`, `regenerateToken()`, restores country |
| R3 | `index()` | `:67-99` | `GET /myaccount` (`account.index`) | **No** | Redirects to login if guest (in-controller); loads wishlist/cart/orders/addresses/`State::all()`; renders `pages.myaccount` |

Facts:

- **`update()` is not behind `auth`** but dereferences `auth()->user()` on `:16` before
  validation on `:21` — a public 500. See [`./MIDDLEWARE_AND_AUTHORIZATION.md`](./MIDDLEWARE_AND_AUTHORIZATION.md).
  `VERIFIED_FROM_SOURCE`.
- `logout()` requires no `auth` middleware; it is safe to call anonymously (guards internal).
  `VERIFIED_FROM_SOURCE`.
- `index()` performs its own guest check rather than using middleware (`:69-71`); for a guest
  `Auth::id()` is `null`, but the redirect fires first, so the `user_id = null` read path is
  not reached in this method. `VERIFIED_FROM_SOURCE`.
- Password write uses `bcrypt()` (`:44`) while `User` also casts `password => hashed`
  (`User.php:41`); functionally equivalent here. `VERIFIED_FROM_SOURCE`.

---

## 10. `SearchController`

`app/Http/Controllers/SearchController.php` (92 lines).

| # | Public method | Lines | Route | Purpose |
|---|---|---|---|---|
| S1 | `search(Request)` | `:13-91` | `GET /search` | Trims `q`; four `LIKE '%q%'` queries (Product, ProductVarient, Brand, Category), each `limit(5)`; returns a JSON array |

Facts:

- **Line `:23` calls `env('MAIN_URL')` directly** for the image base URL. Under `php artisan
  config:cache`, `env()` returns `null` outside `config/`, collapsing image URLs to `/images/`.
  Tracked as **SEC-B-06**. `VERIFIED_FROM_SOURCE`.
- Variant search matches `product_varient.varient` (an integer column) with `LIKE` —
  `INFERRED` dead branch. `VERIFIED_FROM_SOURCE` (column type per live schema).
- Brand/Category result URLs use `$item->slug`, but those tables expose `brand_slug` / no slug
  — `INFERRED` broken links. `VERIFIED_FROM_SOURCE`.
- No CSRF concern (GET); no throttle. See [`../13-security-audit.md`](../13-security-audit.md).

---

## 11. `WishlistController`

`app/Http/Controllers/WishlistController.php` (94 lines). Imports `Wishlist`, `Product`,
`ProductVarient`, `Request`, `Auth`.

| # | Public method | Lines | Route | `auth`? | Ownership | Purpose |
|---|---|---|---|---|---|---|
| W1 | `toggle(Request)` | `:12-36` | `POST /wishlist/toggle` (`wishlist.toggle`) | **No** | `user_id = Auth::id()` (may be `null`) | Toggles a wishlist row; `{success, status:'added'\|'removed'}` |
| W2 | `index()` | `:40-48` | `GET /wishlist` (`wishlist.index`) | **No** | `user_id` (may be `null`) | Lists wishlist items; renders `pages.wishlist` |
| W3 | `remove($id)` | `:50-54` | `DELETE /wishlist/{id}/remove` (`wishlist.remove`) | **No** | **`where('id',$id)->where('user_id',Auth::id())`** | Deletes; `{success:true}` |
| W4 | `checkProduct(Request)` | `:56-79` | `GET /wishlist/check-product` | **Yes** | `user_id` | Detects another variant already wishlisted |
| W5 | `check(Request)` | `:81-89` | `GET /wishlist/check` | **Yes** | `user_id` | `{in_wishlist:bool}` |

Key reconciliations:

- **`remove()` IS scoped by `user_id`** (`:52`). The previously reported wishlist IDOR is
  **FIXED / OUTDATED** — see [`./SECURITY_AUDIT.md`](./SECURITY_AUDIT.md) resolved item R-1.
  `VERIFIED_FROM_SOURCE`.
- `toggle()`/`index()` have **no `auth` middleware** (`routes/web.php:60-61`), so anonymous
  callers write/read `user_id = null` rows. `VERIFIED_FROM_SOURCE`.
- `toggle()` reads `variant_id` (`:15`), unlike the cart endpoints' `product_varient_id`.
  `VERIFIED_FROM_SOURCE`.
- The source comments at `:60` and `:85` contain mojibake (`// �+? correct column name`) —
  cosmetic, evidence of an encoding-mismatched edit. `VERIFIED_FROM_SOURCE`.

---

## 12. Public-method count and cross-links

- **35 routable public methods** across 9 concrete controllers (`VERIFIED_FROM_SOURCE`),
  plus 2 private helpers in `CheckoutController` and the empty base class. (The 44 concrete
  route definitions exceed 35 because 9 of them are `Route::view(...)` closures with no
  controller method.)
- Route-by-route mapping: [`./ROUTES_AND_API_REFERENCE.md`](./ROUTES_AND_API_REFERENCE.md).
- Middleware/authz posture: [`./MIDDLEWARE_AND_AUTHORIZATION.md`](./MIDDLEWARE_AND_AUTHORIZATION.md).
- Original narrative: [`../02-architecture.md`](../02-architecture.md),
  [`../27-api-reference.md`](../27-api-reference.md).
- Security register: [`./SECURITY_AUDIT.md`](./SECURITY_AUDIT.md).
