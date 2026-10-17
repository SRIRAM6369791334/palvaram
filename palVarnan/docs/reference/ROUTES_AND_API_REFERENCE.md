# Routes and API Reference — Project B (`palVarnan`)

> Complete, source-verified route register for `routes/web.php` plus the framework health
> route. Evidence vocabulary: `VERIFIED_FROM_SOURCE`, `VERIFIED_FROM_LIVE_SCHEMA`,
> `INFERRED`, `UNVERIFIED`, `NOT_AVAILABLE`, `CONFLICTING_EVIDENCE`.
> **No credentials or PII are reproduced.**
>
> Siblings: [`./CONTROLLER_REFERENCE.md`](./CONTROLLER_REFERENCE.md),
> [`./MIDDLEWARE_AND_AUTHORIZATION.md`](./MIDDLEWARE_AND_AUTHORIZATION.md),
> [`./FRONTEND_REFERENCE.md`](./FRONTEND_REFERENCE.md),
> [`./INTEGRATION_MAP.md`](./INTEGRATION_MAP.md),
> [`./SECURITY_AUDIT.md`](./SECURITY_AUDIT.md).
> Existing narrative: [`../06-api-and-routes.md`](../06-api-and-routes.md),
> [`../27-api-reference.md`](../27-api-reference.md).

---

## 1. Counting and the "45 routes" statement

`VERIFIED_FROM_SOURCE` (`routes/web.php`, 83 lines):

| Measure | Value | How it was counted |
|---|---|---|
| `Route::` occurrences in `routes/web.php` | **45** | matches `^\s*Route::` |
| — of which are the `auth` group **declaration** | 1 | `routes/web.php:71` (`Route::middleware('auth')->group(...)`) |
| Concrete route definitions in `routes/web.php` | **44** | 45 − 1 group opener |
| — of which are `Route::view(...)` closures | 9 | `/login`, `/register`, `/about`, `/contact`, `/product`, `/privacy-policy`, `/terms-conditions`, `/return-refund-policy`, `/shipping-policy` |
| — of which map to controller methods | **35** | 44 − 9 |
| Framework health route registered by `bootstrap/app.php` | 1 | `GET /up` (`bootstrap/app.php:11`) |
| **Total registered routes** | **45** | 44 web + `/up` |

> `CONFLICTING_EVIDENCE` (reconciled): `docs/audit/VERIFIED_FACTS.md` says "45 `Route::`
> entries", which includes the group opener. The number of concrete **endpoints** is 44 in
> `web.php`, or **45** once the framework `/up` route is counted. The table below lists all
> 45 registered endpoints and flags the group declaration separately.
>
> Superseded counts: [`../06-api-and-routes.md`](../06-api-and-routes.md) says 44 and
> [`../27-api-reference.md`](../27-api-reference.md) says 38 — both are `OUTDATED` relative
> to the current tree (a `/forgot-password` + `/reset-password-direct` pair and the
> `/products` sort changes post-date them).

**Key security shape:** the four auth **POST** endpoints (`/register-store`,
`/login-store`, `/forgot-password`, `/reset-password-direct`) are **public**. `auth`
middleware protects **12** routes (10 in the `auth` group + 2 inline wishlist-check routes);
the task's "~10" refers to the group alone — see
[`./MIDDLEWARE_AND_AUTHORIZATION.md`](./MIDDLEWARE_AND_AUTHORIZATION.md) §3.3.

---

## 2. Complete route table (all 45 registered endpoints)

`web` = framework `web` middleware group (session, cookies, CSRF for non-GET, route bindings).
`auth` = Laravel's authenticate middleware. "Auth?" is the effective requirement.

| # | Method | URI | Name | Controller@method | Middleware | Auth? | Purpose |
|---|---|---|---|---|---|---|---|
| 1 | GET | `/select-country` | — | `CountryController@index` | web | No | Render country picker (`country-selection`) |
| 2 | POST | `/select-country` | — | `CountryController@store` | web | No | Set `session('country_id')`; redirect `/` |
| 3 | GET | `/login` | `login` | `Route::view` → `pages.login` | web | No | Login page |
| 4 | GET | `/register` | `register` | `Route::view` → `pages.register` | web | No | Registration page |
| 5 | GET | `/about` | `about` | `Route::view` → `pages.about` | web | No | About page |
| 6 | GET | `/contact` | `contact` | `Route::view` → `pages.contact` | web | No | Contact page (no handler; form does not exist) |
| 7 | GET | `/product` | — | `Route::view` → `pages.product-detail` | web | No | **Broken** — detail view with no `$product` |
| 8 | GET | `/privacy-policy` | `privacy.policy` | `Route::view` → `pages.privacy-policy` | web | No | Policy page |
| 9 | GET | `/terms-conditions` | `terms.conditions` | `Route::view` → `pages.terms-conditions` | web | No | Policy page |
| 10 | GET | `/return-refund-policy` | `refund.policy` | `Route::view` → `pages.return-refund-policy` | web | No | Policy page |
| 11 | GET | `/shipping-policy` | `shipping.policy` | `Route::view` → `pages.shipping-policy` | web | No | Policy page |
| 12 | GET | `/` | — | `HomeController@index` | web | No | Home (redirects to `/select-country` if unset) |
| 13 | GET | `/search` | — | `SearchController@search` | web | No | **AJAX** JSON type-ahead (4 `LIKE` queries) |
| 14 | GET | `/products` | — | `ProductController@index` | web | No | Catalogue with filters + sort + pagination |
| 15 | POST | `/register-store` | `register.store` | `AuthController@registerStore` | web | **No (public)** | Create user; redirect `/login` |
| 16 | POST | `/login-store` | `login.store` | `AuthController@login` | web | **No (public)** | Session login; JSON |
| 17 | POST | `/forgot-password` | `password.email` | `AuthController@forgotPassword` | web | **No (public)** | `Password::sendResetLink` (mailer = log) |
| 18 | POST | `/reset-password-direct` | `password.reset.direct` | `AuthController@resetPassword` | web | **No (public)** | **CRITICAL** — token-less password overwrite (SEC-B-01) |
| 19 | PUT | `/profile-update` | `profile.update` | `ProfileController@update` | web | **No** | Profile/password update — 500s when anonymous |
| 20 | POST | `/logout` | `logout` | `ProfileController@logout` | web | No | `Auth::logout` + invalidate + token regen |
| 21 | GET | `/cart` | — | `CartController@index` | web | No | Cart page (user or guest-IP scoped) |
| 22 | POST | `/cart-update` | `cart.update` | `CartController@update` | web | **No** | Bulk quantity update — **IDOR** (SEC-B-03) |
| 23 | GET | `/cart-remove/{id}` | `cart.remove` | `CartController@remove` | web | **No** | Delete cart line — **IDOR + state-changing GET** (SEC-B-04) |
| 24 | POST | `/cart/store` | `cart.store` | `CartController@store` | web | No | **AJAX** add/increment; JSON |
| 25 | POST | `/cart/ajax-update` | `cart.ajaxUpdate` | `CartController@ajaxUpdate` | web | No | **AJAX** update by variant (scoped); JSON |
| 26 | POST | `/cart/ajax-remove` | `cart.ajaxRemove` | `CartController@ajaxRemove` | web | No | **AJAX** remove by variant (scoped); JSON |
| 27 | POST | `/wishlist/toggle` | `wishlist.toggle` | `WishlistController@toggle` | web | **No** | **AJAX** toggle; writes `user_id = null` for guests |
| 28 | GET | `/wishlist` | `wishlist.index` | `WishlistController@index` | web | **No** | Wishlist page; reads `user_id = null` for guests |
| 29 | DELETE | `/wishlist/{id}/remove` | `wishlist.remove` | `WishlistController@remove` | web | No | Remove (scoped by `user_id`; **IDOR fixed**) |
| 30 | GET | `/wishlist/check` | — | `WishlistController@check` | web + **auth** | **Yes** | **AJAX** `{in_wishlist}` |
| 31 | GET | `/wishlist/check-product` | — | `WishlistController@checkProduct` | web + **auth** | **Yes** | **AJAX** other-variant check |
| 32 | GET | `/product/{slug}` | — | `ProductController@show` | web | No | Product detail |
| 33 | GET | `/cart/subtotal` | — | `CartController@subtotal` | web | No | **AJAX** `{subtotal}` |
| 34 | GET | `/myaccount` | `account.index` | `ProfileController@index` | web | **No** | Account dashboard (self-guards in-controller) |
| 35 | GET | `/checkout` | `checkout.index` | `CheckoutController@index` | web + **auth** | **Yes** | Checkout page |
| 36 | POST | `/checkout/prepare-payment` | `checkout.preparePayment` | `CheckoutController@preparePayment` | web + **auth** | **Yes** | **AJAX** create Razorpay order |
| 37 | POST | `/checkout/get-couriers` | `checkout.getCouriers` | `CheckoutController@getShiprocketCouriers` | web + **auth** | **Yes** | **AJAX** Shiprocket serviceability |
| 38 | POST | `/checkout/get-shipping-charge` | `checkout.getShippingCharge` | `CheckoutController@getShippingCharge` | web + **auth** | **Yes** | **AJAX** state shipping charge (no Blade caller — `INFERRED` dead) |
| 39 | POST | `/checkout/get-cities` | `checkout.getCities` | `CheckoutController@getCitiesByState` | web + **auth** | **Yes** | **AJAX** districts by state (no Blade caller — `INFERRED` dead) |
| 40 | POST | `/checkout/place-order` | `checkout.placeOrder` | `CheckoutController@placeOrder` | web + **auth** | **Yes** | **AJAX** create order + payment verify + fulfilment |
| 41 | GET | `/checkout/thankyou/{order}` | `checkout.thankyou` | `CheckoutController@thankyou` | web + **auth** | **Yes** | Order confirmation (owner-scoped) |
| 42 | POST | `/checkout/address` | `address.store` | `CheckoutController@storeAddress` | web + **auth** | **Yes** | Create address |
| 43 | PUT | `/checkout/address/{address}` | `address.update` | `CheckoutController@updateAddress` | web + **auth** | **Yes** | Update address (`abort_unless` 403) |
| 44 | DELETE | `/checkout/address/{address}` | `address.destroy` | `CheckoutController@destroyAddress` | web + **auth** | **Yes** | Delete address (`abort_unless` 403) |
| 45 | GET | `/up` | — | framework health | web | No | Health check (`bootstrap/app.php:11`) |

> **Group declaration (not an endpoint):** `routes/web.php:71`
> `Route::middleware('auth')->group(function () { ... })` — the container for rows 35–44.

All routes carry the framework `web` group; **no route carries `throttle`**. `VERIFIED_FROM_SOURCE`.

---

## 3. AJAX / JSON endpoints

Every JSON endpoint is a **web** route (session cookie + CSRF), not an `api.php` route —
`routes/api.php` does **not** exist. `VERIFIED_FROM_SOURCE`. CSRF applies to the POST ones.

| Endpoint | Method | Auth? | Request body | Success response | Failure response | Called from |
|---|---|---|---|---|---|---|
| `/search` | GET | No | `q` | `[{name,type,image,url}]` | `[]` (200) | `layouts/header.blade.php:348` |
| `/cart/store` | POST | No | `product_id`, `product_varient_id`, `quantity` | `{status:'added'\|'incremented', message, quantity?}` | 500 | `product-detail.blade.php:1295,1372`; `products.blade.php:1516`; `wishlist.blade.php:208`; `myaccount.blade.php:961` |
| `/cart/ajax-update` | POST | No | `product_varient_id`, `quantity` | `{success:true}` | `{success:false}` | `product-detail.blade.php:1275`; `products.blade.php:1497`; `myaccount.blade.php:1013` |
| `/cart/ajax-remove` | POST | No | `product_varient_id` | `{success:true}` | — | `product-detail.blade.php:1333,1402`; `products.blade.php:1475` |
| `/cart/subtotal` | GET | No | — | `{subtotal:number}` | 500 | `layouts/app.blade.php:228` |
| `/cart-remove/{id}` | GET | No | — | `{success,message}` if `ajax()`, else 302 | 404 | (Blade cart view; state-changing GET) |
| `/wishlist/toggle` | POST | No | `product_id`, `variant_id` | `{success,status:'added'\|'removed'}` | 500 | `product-detail.blade.php:1668`; `products.blade.php:1744` |
| `/wishlist/check` | GET | **Yes** | `product_id`, `variant_id` | `{in_wishlist:bool}` | — | `product-detail.blade.php:1505,1556`; `products.blade.php:1630` |
| `/wishlist/check-product` | GET | **Yes** | `product_id`, `exclude_variant_id` | `{has_other_variant, existing_variant, new_variant}` | — | `product-detail.blade.php:1604,1642`; `products.blade.php:1716` |
| `/checkout/prepare-payment` | POST | **Yes** | `shipping_charge`, `coupon_discount` (client-set) | `{status:'success', razorpay_order_id, amount, user:{name,email,phone}, razorpay_key}` | 500 `{status:'error', message}` | `checkout.blade.php:797` |
| `/checkout/get-couriers` | POST | **Yes** | `pincode`, `payment_method` | `{status:'success', couriers:[…]}` | `{status:'error', message}` | `checkout.blade.php:706` |
| `/checkout/get-shipping-charge` | POST | **Yes** | `state_name` | `{status, shipping_charge}` | `{status:'error', message}` 404 | none in Blade (`INFERRED` dead) |
| `/checkout/get-cities` | POST | **Yes** | `state_name` | `{status, cities:[…]}` | always 200 | none in Blade (`INFERRED` dead) |
| `/checkout/place-order` | POST | **Yes** | 9 `shipping_*` + `payment_method` + `razorpay_*` | `{status:'success', order_number, redirect_url}` | 400/422/500 | `checkout.blade.php:872` |
| `/checkout/address` | POST | **Yes** | 10 address fields | `{success:true, message, address}` | `{success:false, message}` 422 | `checkout.blade.php:606` |

`VERIFIED_FROM_SOURCE` (controller methods + Blade `fetch()` call sites; see
[`./FRONTEND_REFERENCE.md`](./FRONTEND_REFERENCE.md) for the full `fetch()` census).

> Note: **29 `fetch()` calls send `X-CSRF-TOKEN`** and **3 do not** — the cart quantity
> update in `cart.blade.php:268` and one `myaccount.blade.php` call. Only
> `checkout.blade.php:616` handles HTTP 419. See
> [`../13-security-audit.md`](../13-security-audit.md) SEC-19 and
> [`../15-frontend-and-assets.md`](../15-frontend-and-assets.md) §3.

---

## 4. Route-name inventory

Names are only assigned to some routes; many URLs must be hard-coded. `VERIFIED_FROM_SOURCE`.

| Named routes | Value |
|---|---|
| `login`, `register`, `about`, `contact` | view routes |
| `privacy.policy`, `terms.conditions`, `refund.policy`, `shipping.policy` | view routes |
| `register.store`, `login.store` | auth POST |
| `password.email`, `password.reset.direct` | password flows |
| `profile.update`, `logout` | profile |
| `cart.update`, `cart.remove`, `cart.store`, `cart.ajaxUpdate`, `cart.ajaxRemove` | cart |
| `wishlist.toggle`, `wishlist.index`, `wishlist.remove` | wishlist |
| `account.index` | `/myaccount` |
| `checkout.index`, `checkout.preparePayment`, `checkout.getCouriers`, `checkout.getShippingCharge`, `checkout.getCities`, `checkout.placeOrder`, `checkout.thankyou` | checkout |
| `address.store`, `address.update`, `address.destroy` | address book |

**Unnamed** (hard-coded URLs): `/`, `/search`, `/products`, `/product/{slug}`, `/product`,
`/cart`, `/cart-update` (note the hyphen, not a `/cart/update` name), `/cart-remove/{id}`,
`/cart/subtotal`, `/wishlist/check`, `/wishlist/check-product`, `/select-country` (both verbs).

---

## 5. Route defects (source-verified, summarised)

Full register with severity/impact/recommendation is in
[`./SECURITY_AUDIT.md`](./SECURITY_AUDIT.md). Route-level mapping:

| Route | Defect | Tag |
|---|---|---|
| `POST /reset-password-direct` | No token/expiry — unauthenticated account takeover | **SEC-B-01** |
| `POST /checkout/place-order` | Client-supplied `shipping_charge` / `coupon_discount` | **SEC-B-02** |
| `POST /cart-update` | IDOR + no quantity validation | **SEC-B-03** |
| `GET /cart-remove/{id}` | IDOR + state-changing GET (no CSRF) | **SEC-B-04** |
| all routes | No `throttle` anywhere | **SEC-B-05** |
| `GET /search` | `env('MAIN_URL')` direct call | **SEC-B-06** |
| `GET /myaccount` | Public route; self-guards in-controller | §5.4 of middleware doc |
| `PUT /profile-update` | No `auth` → 500 when anonymous | §5.4 of middleware doc |
| `GET /product` | View route with no bound model | `../27-api-reference.md` §2 |
| `GET /checkout/get-shipping-charge`, `get-cities` | No Blade caller (`INFERRED` dead) | `../27-api-reference.md` |

---

## 6. Related documents

- [`./CONTROLLER_REFERENCE.md`](./CONTROLLER_REFERENCE.md) — controller@method detail
- [`./MIDDLEWARE_AND_AUTHORIZATION.md`](./MIDDLEWARE_AND_AUTHORIZATION.md) — auth posture
- [`./FRONTEND_REFERENCE.md`](./FRONTEND_REFERENCE.md) — Blade call sites
- [`./INTEGRATION_MAP.md`](./INTEGRATION_MAP.md) — Razorpay/Shiprocket endpoints behind checkout
- [`./SECURITY_AUDIT.md`](./SECURITY_AUDIT.md) — SEC-B register
- [`../06-api-and-routes.md`](../06-api-and-routes.md) — earlier route narrative
- [`../27-api-reference.md`](../27-api-reference.md) — earlier request/response shapes
- [`../25-forms-and-validation.md`](../25-forms-and-validation.md) — validation per field
