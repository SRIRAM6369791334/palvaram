# CONTROLLERS.md

**Purpose:** All 10 controllers and their methods.

---

## 1. Controllers inventory

Files in [`app/Http/Controllers/`](../../app/Http/Controllers): 10 total (9 concrete + base).

| Controller | Lines (approx) | Purpose | Evidence |
|---|---|---|---|
| [`AuthController.php`](../../app/Http/Controllers/AuthController.php) | 129 | Registration, login (email/phone), forgot password, **reset password (direct, no token)** | `VERIFIED_FROM_SOURCE` |
| [`CartController.php`](../../app/Http/Controllers/CartController.php) | 147 | Cart index/add/update/remove/AJAX/subtotal; has IDOR on update/remove | `VERIFIED_FROM_SOURCE` |
| [`CheckoutController.php`](../../app/Http/Controllers/CheckoutController.php) | 701 | Index, couriers, prepare payment (Razorpay), place order (multi-table + Shiprocket), thank-you, address CRUD (ownership enforced) | `VERIFIED_FROM_SOURCE` |
| [`Controller.php`](../../app/Http/Controllers/Controller.php) | base | Base abstract controller | `VERIFIED_FROM_SOURCE` |
| [`CountryController.php`](../../app/Http/Controllers/CountryController.php) | 29 | Country selection gate | `VERIFIED_FROM_SOURCE` |
| [`HomeController.php`](../../app/Http/Controllers/HomeController.php) | 28 | Home banners/categories | `VERIFIED_FROM_SOURCE` |
| [`ProductController.php`](../../app/Http/Controllers/ProductController.php) | 180 | Catalog listing + product detail + related | `VERIFIED_FROM_SOURCE` |
| [`ProfileController.php`](../../app/Http/Controllers/ProfileController.php) | 103 | Profile update, logout, myaccount | `VERIFIED_FROM_SOURCE` |
| [`SearchController.php`](../../app/Http/Controllers/SearchController.php) | 92 | Type-ahead search (uses `env('MAIN_URL')`) | `VERIFIED_FROM_SOURCE` |
| [`WishlistController.php`](../../app/Http/Controllers/WishlistController.php) | 94 | Toggle/index/remove/check/check-product; remove scoped | `VERIFIED_FROM_SOURCE` |

Total 9 domain + Controller base = 10. `VERIFIED_FROM_SOURCE`.

---

## 2. Method-level notes

### AuthController (129 lines)
- `registerStore(Request $request)` — validates, creates user with `country_id` from session ([13-43](../../app/Http/Controllers/AuthController.php#L13-L43))
- `login(Request $request)` — email or phone lookup; `Hash::check`; `Auth::login` ([46-72](../../app/Http/Controllers/AuthController.php#L46-L72))
- `forgotPassword(Request $request)` — `Password::sendResetLink` ([75-97](../../app/Http/Controllers/AuthController.php#L75-L97))
- `resetPassword(Request $request)` — **CRITICAL:** no token; validates email+password, sets new password ([100-126](../../app/Http/Controllers/AuthController.php#L100-L126))

### CartController (147)
- `index()` — scoped by user/ip; filters whereHas('variant') ([11-31](../../app/Http/Controllers/CartController.php#L11-L31))
- `update(Request $request)` — **IDOR:** loops cartIds, updates without ownership ([33-44](../../app/Http/Controllers/CartController.php#L33-L44))
- `remove($id)` — **IDOR + GET:** `findOrFail($id)->delete()` ([46-62](../../app/Http/Controllers/CartController.php#L46-L62))
- `store(Request $request)` — create/increment; guest by ip ([64-93](../../app/Http/Controllers/CartController.php#L64-L93))
- `ajaxUpdate(Request $request)` — scoped by product_varient_id + identity ([95-113](../../app/Http/Controllers/CartController.php#L95-L113))
- `ajaxRemove(Request $request)` — scoped ([115-128](../../app/Http/Controllers/CartController.php#L115-L128))
- `subtotal()` — JSON subtotal ([130-145](../../app/Http/Controllers/CartController.php#L130-L145))

### CheckoutController (701) — key methods
- `index(Request $request)` — cart/address/state ([38-84](../../app/Http/Controllers/CheckoutController.php#L38-L84))
- `getShiprocketCouriers(Request $request)` — serviceability ([89-167](../../app/Http/Controllers/CheckoutController.php#L89-L167))
- `preparePayment(Request $request)` — Razorpay order ([172-220](../../app/Http/Controllers/CheckoutController.php#L172-L220))
- `placeOrder(Request $request)` — multi-table write + stock + Shiprocket ([225-535](../../app/Http/Controllers/CheckoutController.php#L225-L535))
- `thankyou($orderNumber)` — ownership scoped ([540-548](../../app/Http/Controllers/CheckoutController.php#L540-L548))
- `storeAddress/updateAddress/destroyAddress` — ownership enforced on update/destroy ([553-633](../../app/Http/Controllers/CheckoutController.php#L553-L633))
- `getShippingCharge/getCitiesByState` — AJAX lookups ([638-670](../../app/Http/Controllers/CheckoutController.php#L638-L670))
- `validateAddress/getCartItems` — helpers ([672-700](../../app/Http/Controllers/CheckoutController.php#L672-L700))

### WishlistController (94)
- `toggle` ([12-36](../../app/Http/Controllers/WishlistController.php#L12-L36)), `index` ([40-48](../../app/Http/Controllers/WishlistController.php#L40-L48)), `remove` ([50-54](../../app/Http/Controllers/WishlistController.php#L50-L54)) — **scoped by user_id** (FIXED), `checkProduct` ([56-79](../../app/Http/Controllers/WishlistController.php#L56-L79)), `check` ([81-89](../../app/Http/Controllers/WishlistController.php#L81-L89))

### Others
- `HomeController@index` ([11-27](../../app/Http/Controllers/HomeController.php#L11-L27))
- `ProductController@index` ([16-108](../../app/Http/Controllers/ProductController.php#L16-L108)), `show` ([110-178](../../app/Http/Controllers/ProductController.php#L110-L178))
- `ProfileController@update` ([14-49](../../app/Http/Controllers/ProfileController.php#L14-L49)), `logout` ([51-65](../../app/Http/Controllers/ProfileController.php#L51-L65)), `index` ([67-99](../../app/Http/Controllers/ProfileController.php#L67-L99))
- `SearchController@search` ([13-91](../../app/Http/Controllers/SearchController.php#L13-L91)); uses `env('MAIN_URL')` at [23](../../app/Http/Controllers/SearchController.php#L23)
- `CountryController@index` ([11-15](../../app/Http/Controllers/CountryController.php#L11-L15)), `store` ([17-28](../../app/Http/Controllers/CountryController.php#L17-L28))

---

## 3. Related

- [`../11-models-and-relationships.md`](../11-models-and-relationships.md)
- [`ROUTES_AND_ENDPOINTS.md`](./ROUTES_AND_ENDPOINTS.md)
- [`FEATURE_CATALOG.md`](./FEATURE_CATALOG.md)