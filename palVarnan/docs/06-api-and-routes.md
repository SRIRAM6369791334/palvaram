# 06 — API and Routes

All 44 routes in `routes/web.php`, then the implied APIs that do not exist.

---

## 1. Route census — VERIFIED

`routes/web.php` is **75 lines** and defines **44 routes**. There are no route groups beyond one `auth` group, no `throttle`, no `verified`, no signed URLs.

| # | Method | URI | Name | Controller@method | Middleware | Status |
|---|---|---|---|---|---|---|
| 1 | GET | `/select-country` | — | `CountryController@index` | web | OK |
| 2 | POST | `/select-country` | — | `CountryController@store` | web | Partial |
| 3 | GET | `/login` | `login` | `Route::view('pages.login')` | web | OK |
| 4 | GET | `/register` | `register` | `Route::view('pages.register')` | web | OK |
| 5 | GET | `/product` | — | `Route::view('pages.product-detail')` | web | **BROKEN** — no `$product` |
| 6 | GET | `/` | — | `HomeController@index` | web | OK |
| 7 | GET | `/search` | — | `SearchController@search` | web | Partial |
| 8 | GET | `/products` | — | `ProductController@index` | web | OK |
| 9 | POST | `/register-store` | `register.store` | `AuthController@registerStore` | web | OK |
| 10 | POST | `/login-store` | `login.store` | `AuthController@login` | web | No throttle |
| 11 | POST | `/forgot-password` | `password.email` | `AuthController@forgotPassword` | web | **Orphaned** — no caller |
| 12 | POST | `/reset-password-direct` | `password.reset.direct` | `AuthController@resetPassword` | web | **CRITICAL — no token** |
| 13 | PUT | `/profile-update` | `profile.update` | `ProfileController@update` | web | **Missing `auth`** |
| 14 | POST | `/logout` | `logout` | `ProfileController@logout` | web | OK |
| 15 | GET | `/cart` | — | `CartController@index` | web | OK |
| 16 | POST | `/cart-update` | `cart.update` | `CartController@update` | web | **IDOR + no validation** |
| 17 | GET | `/cart-remove/{id}` | `cart.remove` | `CartController@remove` | web | **IDOR + CSRF-unsafe GET** |
| 18 | POST | `/cart/store` | `cart.store` | `CartController@store` | web | OK |
| 19 | POST | `/cart/ajax-update` | `cart.ajaxUpdate` | `CartController@ajaxUpdate` | web | OK |
| 20 | POST | `/cart/ajax-remove` | `cart.ajaxRemove` | `CartController@ajaxRemove` | web | OK |
| 21 | POST | `/wishlist/toggle` | `wishlist.toggle` | `WishlistController@toggle` | web | **Missing `auth`** |
| 22 | GET | `/wishlist` | `wishlist.index` | `WishlistController@index` | web | **Missing `auth`** |
| 23 | DELETE | `/wishlist/{id}/remove` | `wishlist.remove` | `WishlistController@remove` | web | **IDOR** |
| 24 | GET | `/wishlist/check` | — | `WishlistController@check` | **auth** | OK |
| 25 | GET | `/wishlist/check-product` | — | `WishlistController@checkProduct` | **auth** | OK |
| 26 | GET | `/product/{slug}` | — | `ProductController@show` | web | OK |
| 27 | GET | `/cart/subtotal` | — | `CartController@subtotal` | web | OK |
| 28 | GET | `/myaccount` | `account.index` | `ProfileController@index` | web | **Missing `auth`** |
| 29 | GET | `/checkout` | `checkout.index` | `CheckoutController@index` | **auth** | OK |
| 30 | POST | `/checkout/prepare-payment` | `checkout.preparePayment` | `CheckoutController@preparePayment` | **auth** | Partial |
| 31 | POST | `/checkout/get-couriers` | `checkout.getCouriers` | `CheckoutController@getShiprocketCouriers` | **auth** | OK |
| 32 | POST | `/checkout/get-shipping-charge` | `checkout.getShippingCharge` | `CheckoutController@getShippingCharge` | **auth** | **Dead code** |
| 33 | POST | `/checkout/get-cities` | `checkout.getCities` | `CheckoutController@getCitiesByState` | **auth** | **Dead code** |
| 34 | POST | `/checkout/place-order` | `checkout.placeOrder` | `CheckoutController@placeOrder` | **auth** | **BROKEN** |
| 35 | GET | `/checkout/thankyou/{order}` | `checkout.thankyou` | `CheckoutController@thankyou` | **auth** | Unreachable |
| 36 | POST | `/checkout/address` | `address.store` | `CheckoutController@storeAddress` | **auth** | OK |
| 37 | PUT | `/checkout/address/{address}` | `address.update` | `CheckoutController@updateAddress` | **auth** | OK |
| 38 | DELETE | `/checkout/address/{address}` | `address.destroy` | `CheckoutController@destroyAddress` | **auth** | OK |
| 39 | GET | `/up` | — | framework health | web | OK |
| — | — | `/` console route | — | `routes/console.php` | — | OK |

**39 named in `web.php` + `up` + console = 40 registered paths.** (Note: `storage/logs` and `bootstrap/cache` may register additional routes under some configs.)

### Middleware exposure summary

| Exposure | Routes |
|---|---|
| **No `auth` despite needing it** | `profile.update`, `wishlist.toggle`, `wishlist.index`, `wishlist.remove`, `account.index`, `cart.update`, `cart.remove`, `cart.store`, `cart.index`, `cart.subtotal` |
| **Has `auth`** | Routes 24, 25, 29-38 |
| **No throttle anywhere** | Login, register, reset-password, all cart/wishlist writes, search |

---

## 2. JSON endpoints

| Endpoint | Method | Request | Success response | Failure response | Notes |
|---|---|---|---|---|---|
| `/search` | GET | `q` | `[{name,type,image,url}]` or `[]` | none — always 200 | 2 of 4 result types broken. No pagination, no limit param |
| `/cart/subtotal` | GET | — | `{subtotal: number}` | 500 | **Returns `subtotal` only — no `count`.** Reads `guest_token` cookie |
| `/cart/store` | POST | `product_id`, `product_varient_id`, `quantity` | `{status:'added'\|'incremented', message}` (+`quantity:1` on add) | 500 | On an **existing** line the increment is hard-coded to 1 and `quantity` is ignored |
| `/cart/ajax-update` | POST | `product_varient_id`, `quantity` | `{success:true}` | `{success:false}` | First matching line only; no quantity validation |
| `/cart/ajax-remove` | POST | `product_varient_id` | `{success:true}` | — | First matching line only |
| `/cart-remove/{id}` | GET | — | `{success:true, message}` if `ajax()`, else 302 back | 404 if id absent | **State-changing GET** |
| `/wishlist/toggle` | POST | `product_id`, **`variant_id`** | `{success:true, status:'added'\|'removed'}` | 500 | Field is `variant_id`, not `product_varient_id` |
| `/wishlist/check` | GET | `product_id`, `variant_id` | `{in_wishlist:bool}` | — | `auth` |
| `/wishlist/check-product` | GET | `product_id`, `exclude_variant_id` | `{has_other_variant:bool, existing_variant, new_variant}` | — | `auth` |
| `/checkout/get-couriers` | POST | `pincode`, `payment_method` | `{status:'success', couriers:[{name, rate, ...}]}` | `{status:'error', message}` | **Leaks Shiprocket's raw error** |
| `/checkout/prepare-payment` | POST | `subtotal`, `shipping_charge`, `coupon_discount` | `{razorpay_order_id, razorpay_key, user:{name,email,phone}, amount}` | 500 with `$e->getMessage()` | **Accepts client totals** |
| `/checkout/place-order` | POST | 9 shipping fields, `payment_method`, `razorpay_*` | `{status:'success', order_number, redirect_url}` | `{status:'error', message:'Order failed to save: '.$e->getMessage()}` | **Leaks SQL errors** |
| `/checkout/address` | POST | 11 address fields | `{status:'success', message, address}` | `{status:'error', message}` | Best-secured handler |
| `/checkout/get-shipping-charge` | POST | `state_id` | `{status, shipping_charge}` | — | **No caller** |
| `/checkout/get-cities` | POST | `state_id` | `{status, cities:[…]}` | — | **No caller** |

---

## 3. Route-name inconsistencies (VERIFIED)

Three separate naming conventions coexist:

| Convention | Examples | Source |
|---|---|---|
| **Route names** | `checkout.index`, `cart.store`, `wishlist.toggle` | `routes/web.php` |
| **Path segments** | `/cart/store`, `/wishlist/toggle`, `/checkout/address` | `routes/web.php` |
| **Bare paths, no name** | `/cart`, `/cart-update`, `/cart-remove/{id}`, `/search`, `/products`, `/product/{slug}`, `/cart/subtotal`, `/select-country` | `routes/web.php` |

Consequence: `route('cart.index')` **does not exist** even though `/cart` is a real page. Any Blade file needing the cart URL hard-codes `/cart`. Verified: `cart.blade.php` and `header.blade.php` both use literal strings.

Also: `Route::view('/login')` is named but `/register` view route has the same shape — consistent there, but `/product` (line 19) is an **unnamed view route that shadows the real `/product/{slug}` route's purpose** while passing no data.

---

## 4. APIs that do not exist

| Needed by | Expected endpoint | Present? |
|---|---|---|
| Home page wishlist hearts | `GET /api/wishlist/ids` or composer-shared list | **No** — `AppServiceProvider` shares only cart count |
| Cart page badge consistency | shared subtotal via `View::share` | **No** — recomputed per controller |
| Coupon apply/validate | `POST /checkout/apply-coupon` | **No** — button has no handler |
| Coupon list for cart | `GET /coupons` | **No** |
| Order tracking by customer | `GET /track/{awb}` | **No** — `ShiprocketService::trackAWB()` exists but no route calls it |
| Order cancel request | `POST /orders/{id}/cancel` | **No** |
| Return / refund request | `POST /orders/{id}/return` | **No** |
| Guest cart merge on login | `POST /cart/merge` | **No** |
| Stock availability check | `GET /products/{id}/stock` | **No** |
| Reviews | `GET/POST /products/{id}/reviews` | **No** |
| Address list | `GET /checkout/addresses` | **No** (POST-only create; list is inlined in the page) |
| Server-side variant JSON | `GET /products/{id}/variants` | **No** |

---

## 5. HTTP-method violations

| Route | Method | Why it is wrong |
|---|---|---|
| `/cart-remove/{id}` | **GET** | Deletes a record. Laravel's CSRF middleware ignores GET. Crawlers and link prefetchers can trigger it. Should be `DELETE /cart/{id}` |
| `/cart/subtotal` | GET | Correctly idempotent — listed as a positive |
| `/search` | GET | Correct — but unthrottled and unindexed |

---

## 6. Rate-limiting status

**No rate limiting exists anywhere in the project.**

| Endpoint | Risk from unlimited calls |
|---|---|
| `/login-store` | Credential stuffing; no lockout; no CAPTCHA |
| `/forgot-password` + `/reset-password-direct` | Mass account takeover (F-06) |
| `/register-store` | Automated account creation; email enumeration via `unique` 422 |
| `/search` | 4 unindexed `LIKE '%…%'` scans per keystroke ⇒ cheap DoS |
| `/checkout/get-couriers` | **Triggers an authenticated outbound Shiprocket API call.** Unlimited calls ⇒ burns the Shiprocket quota and can get the account rate-limited |
| `/cart/*` writes | Cart pollution / DB write flood |

Recommended baseline (see [13-security-audit.md](13-security-audit.md)): `throttle:6,1` on login, `throttle:3,1` on register/reset, `throttle:30,1` on search, `throttle:15,1` on get-couriers.

---

## 7. Related documents

- [05-user-flows.md](05-user-flows.md) — what calls these endpoints
- [12-configuration.md](18-configuration-and-env.md — `services.*` config used by the payment routes
- [13-security-audit.md](13-security-audit.md) — authz and CSRF findings
- [27-api-reference.md](27-api-reference.md) — copy-paste request examples