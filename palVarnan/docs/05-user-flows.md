# 05 — User Flows

Twelve user journeys, reverse engineered from the Blade templates and the controllers they call. Each flow lists every step, every failure point, and the evidence.

---

## Flow index

| # | Flow | Actors | End-to-end status |
|---|---|---|---|
| UF-1 | First visit → country selection | Guest | Works |
| UF-2 | Register | Guest | Works |
| UF-3 | Login | Guest → User | Works (no lockout) |
| UF-4 | "Forgot password" | User | **BROKEN — no usable path; plus an open takeover endpoint** |
| UF-5 | Browse catalogue → product detail | Guest / User | Works |
| UF-6 | Search | Guest / User | **Partially broken** |
| UF-7 | Add to cart (guest) | Guest | Works (no merge later) |
| UF-8 | Add to cart (user) | User | Works |
| UF-9 | Wishlist | Guest / User | Partially broken |
| UF-10 | Cart → update → remove | User | **BROKEN — IDOR** |
| UF-11 | Checkout → pay → order | User | **BROKEN — payment captured, order not saved** |
| UF-12 | My Account | User | Partially broken |

---

## UF-1 — First visit → country selection

```
1. GET /                              routes/web.php:16
2. HomeController::index()            HomeController.php:11
3.   if (!session('country_id'))  ──►  true
4.   return redirect('/select-country')
5. GET /select-country                CountryController::index()
6.   Country::orderBy('name')->get()
7.   render country-selection.blade.php  (standalone page, NOT the app layout)
8. User picks "India", form POSTs country=India
9. POST /select-country               CountryController::store()
10.  Country::whereRaw('LOWER(name) = ?', [strtolower('India')])->first()
11.  session(['country_id' => 243])
12.  redirect('/')
13. GET /  → session now set → renders pages/home
```

| Step | Failure point | What the user sees | Evidence |
|---|---|---|---|
| 6 | `countries` table empty | Empty dropdown, no explanation | No guard in `index()` |
| 10 | User types a name that differs from `countries.name` | `redirect('/select-country')->with('error','Invalid country selected.')` — **but the flash key is `error`, and the standalone view never renders `session('error')`** | `CountryController.php:21`; grep: no `session('error')` in `country-selection.blade.php` |
| 13 | `pages/home` throws | Laravel error page | Log contains `View [home` and `Undefined variable $banners` errors from earlier iterations |

**Note:** `country-selection.blade.php` does **not** extend `layouts.app`, so it has no header, no cart badge and no CSS from the app shell. It is a bare page.

---

## UF-2 — Register

```
1. GET /register                       routes/web.php:12 → Route::view('pages.register')
2. User fills form
3. register.blade.php:162  fetch(form.action, {method:'POST', body: FormData})
4. POST /register-store                routes/web.php:22 (name: register.store)
5. AuthController::registerStore()     AuthController.php:14
6.   $request->validate([
       last_name => required, name => required,
       email => required|email|unique:users,email,
       phone_number => required, password => required|min:6 ])
7.   $countryId = session('country_id')
8.   User::create([country_id, name, email, phone_number, last_name, password=>Hash::make()])
9.   return redirect('/login')->with('success','Registered Successfully')
10. GET /login → app.blade.php:117 renders SweetAlert2 success toast
```

**User is NOT logged in after registering.** They must log in separately.

| Failure point | Behaviour | Evidence |
|---|---|---|
| Validation fails | HTTP 422 JSON; `register.blade.php` shows field errors | — |
| Duplicate email | `unique:users,email` → 422. **`users_email_unique` index exists** so this is index-backed | SQL dump |
| No `country_id` in session | `country_id` saved as `NULL`; `users.country_id` is nullable so it succeeds silently | `AuthController.php:23, 28` |
| `MAIL_MAILER=log` | No verification email is attempted at all (`MustVerifyEmail` commented out) | `User.php:5` |
| **No rate limiting** | Registration can be hammered to enumerate emails via the `unique` rule's 422 response | No throttle middleware |

---

## UF-3 — Login

```
1. GET /login                          routes/web.php:11 → Route::view('pages.login')
2. User types email OR phone into a field labelled/named "username"
3. login.blade.php:224  fetch(route('login.store'), {method:'POST'})
4. POST /login-store                   routes/web.php:24 (name: login.store)
5. AuthController::login()             AuthController.php:44
6.   validate: username required, password required
7.   User::where('email', $username)->orWhere('phone_number', $username)->first()
8.   if (!$user || !Hash::check($password, $user->password))
9.       return 422 {errors:{username:['Invalid login credentials']}}
10.  Auth::login($user)                 → session row written to `sessions`
11.  return 200 {success:true, message:'Login successful'}
12. login.blade.php reloads → AppServiceProvider composer now uses the user_id branch
```

| Failure point | Behaviour | Evidence |
|---|---|---|
| Wrong credentials | 422 with generic "Invalid login credentials" — **no user enumeration** | `:56-62` |
| **Brute force** | **No rate limit, no lockout, no delay.** Unlimited attempts. | No throttle middleware; `bootstrap/app.php:14-16` empty |
| Ambiguous username | `email = 'x' OR phone_number = 'x'` can match two different users; `->first()` picks arbitrarily | `:57-59` |
| No `country_id` | Login succeeds; the AppServiceProvider works but `/`, `/products`, `/product/*` still redirect to `/select-country` | — |
| Guest cart | **Not merged.** Guest cart rows stay `user_id = NULL` and become unreachable | No merge code anywhere |

---

## UF-4 — "Forgot password" — **BROKEN**

```
1. User clicks "Forgot password?" on login.blade.php
2. login.blade.php:153  fetch(route('password.reset.direct'), ...)
   ── NOTE: the form posts DIRECTLY to the reset endpoint, not to /forgot-password
3. POST /reset-password-direct        AuthController::resetPassword()  :95
4.   validate: email required|email, password required|min:6
5.   User::where('email', $request->email)->first()
6.   if (!$user) → 422 {errors:{email:['User not found']}}   ← USER ENUMERATION
7.   $user->password = Hash::make($request->password); $user->save();
8.   Auth::logout();
9.   return 200 {success:true, message:'Password updated successfully'}
```

### Two distinct defects

**BUG (A) — Open account takeover. CRITICAL.**
Step 3-9 requires only an email address and a new password. **There is no reset token, no `password_reset_tokens` lookup, no expiry, no confirmation of the current password, and no notification to the account owner.** Verified by reading `AuthController.php:95-120`: the method body contains no reference to `token` or `Password::reset()`.

The route name itself — `password.reset.direct` — records that the developer knew this was a shortcut.

**BUG (B) — The legitimate flow is orphaned.**
`AuthController::forgotPassword()` (`:72-93`) does call the real, token-based `Password::sendResetLink()`, and `routes/web.php:23` exposes it as `password.email`. But:
- **No view contains a form that posts to `password.email`.** (grep confirms only `route('password.reset.direct')` is used.)
- **No route/view accepts a reset token.** There is no `GET /reset-password/{token}`, and `resetPassword()` ignores tokens entirely.
- `MAIL_MAILER=log` (`config/mail.php` default + `.env`), so even the emailed link would only appear in `storage/logs/laravel.log`.

Net effect: the correct flow is 100% dead code, and the insecure shortcut is the only live path.

---

## UF-5 — Browse catalogue → product detail

```
GET /products
  ProductController::index()                        :17
    guard: session('country_id')                    :18-20
    Category::whereRaw("FIND_IN_SET(?,country_id)")  :26   ← full scan, no index
    SubCategory::whereRaw(...)                       :29   ← full scan
    Brand::whereRaw(...)                             :32   ← full scan
    Product::with('variants.unit')                  :34   ← eager load (good)
      where(country_id = X or country_id IS NULL)   :37-40
      [optional] where category_id / subcategory_id / brand_id from query string
    Cart lookup → pluck all product_varient_id      :44-64
    Wishlist lookup → pluck all product_id          :59-64 (only if Auth::check)
    ->latest()->paginate(12)                        :65
  render pages/products

GET /product/{slug}
  ProductController::show($slug)                    :71
    guard: session('country_id')
    Product::with('variants.unit','category','brand')->where('slug',$slug)->firstOrFail()
    related: same category_id, != id, country-visible, latest()->take(8)
  render pages/product-detail
```

| Failure point | Behaviour | Evidence |
|---|---|---|
| No country in session | Redirect `/select-country` | `:18-20, :73-75` |
| Unknown slug | **404** via `firstOrFail()` | `:82` |
| `FIND_IN_SET` on a `varchar` country_id | **Unindexable full table scan** for categories, subcategories and brands on every listing request | `HomeController.php:21`, `ProductController.php:26,29,32` |
| `GET /product` (no slug) | Route exists at `routes/web.php:13` → renders `pages.product-detail` with **no controller data**. The view dereferences `$product` ⇒ **fatal error**. | `routes/web.php:13`; log has `View [pages.product-details` errors |
| `SubCategory` relationship | `Unknown column 'sub_categories.category_id'` — **verified in log** | Log; `SubCategory.php:9-12`; `Category.php:21-24` |
| `?category=` unvalidated | MySQL coerces the value; no 422, silent empty result | `:42-44` |
| Product deleted | Soft-deleted rows excluded; but cart/wishlist rows referencing them survive ⇒ **null relation** | Log: `Attempt to read property "product_image" on null` |

---

## UF-6 — Search — **PARTIALLY BROKEN**

```
1. User types in the header search box
2. header.blade.php:835  fetch(`/search?q=${encodeURIComponent(query)}`)
   ── no debounce, no minimum length, fired on every keystroke
3. GET /search                          SearchController::search()  :15
4.   $q = trim($request->q);  if (!$q) return []
5.   baseImageUrl = rtrim(env('MAIN_URL'),'/') . '/images/'          :29
6.   Product::where('product_name','LIKE',"%$q%")->limit(5)->get()     :31-33   ✅ WORKS
7.   ProductVarient::with('product')->where('varient','LIKE',"%$q%")  :37-39   ❌ IMPOSSIBLE
8.   Brand::where('brand_name','LIKE',"%$q%")->limit(5)              :48-50   ⚠ URL BROKEN
9.   Category::where('category_name','LIKE',"%$q%")->limit(5)        :63-65   ⚠ URL BROKEN
10.  return JSON array of {name, type, image, url}
```

| Step | Defect | Detail |
|---|---|---|
| 7 | **`product_varient.varient` is `int(11)`** in the schema. MySQL coerces `'%abc%'` to `0`; `int_col LIKE 'pattern'` can never be true. **Variant search returns nothing, always.** | SQL dump `CREATE TABLE product_varient`; `SearchController.php:41` |
| 8 | `$item->slug` on a `Brand` — the column is **`brand_slug`**, and `Brand` declares no `slug`. Result URL: **`/brand/`**. No route matches → the user clicks and gets a 404. | SQL dump; `SearchController.php:83` |
| 9 | `$item->slug` on a `Category` — **the `categories` table has no `slug` column at all**. Result URL: **`/category/`** → 404. | SQL dump; `SearchController.php:103` |
| 5 | `env('MAIN_URL')` returns **`null`** if `php artisan config:cache` has been run → image URLs become `"/images/foo.jpg"` (broken images) | See [12-configuration.md](18-configuration-and-env.md |
| 2 | No debounce, no min-length, no rate limit | — |
| 6-9 | `LIKE '%…%'` cannot use an index ⇒ 4 full table scans per keystroke | — |

---

## UF-7 — Add to cart (guest)

```
1. products/product-detail blade: fetch(route('cart.store'), {method:'POST', body:{product_id, product_varient_id, quantity}})
2. POST /cart/store                     CartController::store()  :62
3.   lookup existing: Cart::where('ip_addresscart', $request->ip())
                          ->whereNull('user_id')
                          ->where('product_id', …)->where('product_varient_id', …)->first()
4.   if found  → $existing->increment('product_quantity', 1)  → {status:'incremented'}
5.   else      → Cart::create([user_id=>null, ip_addresscart=>$request->ip(),
                               product_id, product_varient_id, product_quantity, pre_order=>0])
                              → {status:'added', quantity:1}
6. Blade calls updateCartCount(delta) / refreshCartSubtotal()
7. refreshCartSubtotal() → GET /cart/subtotal
8.   CartController::subtotal()  :98   ← reads cookie('guest_token') ?? ip()
9. Header + floating cart bar update
```

| Failure point | Behaviour | Evidence |
|---|---|---|
| 5 | `Cart::$fillable` has **no `pre_order`** ⇒ the value is silently discarded. The `carts.pre_order` column therefore always stays `NULL` | `Cart.php:10-16`, `CartController.php:86` |
| **Identity split** | `store()` writes `request()->ip()`. `index()`, `subtotal()` and `AppServiceProvider` read `cookie('guest_token') ?? ip()`. **Nothing ever sets a `guest_token` cookie** (verified by grep across all views), so both fall back to `ip()`. Behind a reverse proxy every guest shares one cart. | `CartController.php:16, 82, 101`; `AppServiceProvider.php:43` |
| Variant no longer exists | `whereHas('variant')` filters it out of the cart page ⇒ **the line silently vanishes** from the customer's cart with no notice | `:29, :107` |
| Out of stock | **No stock check.** You can add 999 units of a sold-out product. | `store()` has no stock query |
| Guest → login | **Cart is not merged.** Guest rows keep `user_id = NULL`. The user sees an empty cart after logging in. | No merge code exists |

---

## UF-8 — Add to cart (logged-in user)

Identical to UF-7 except the lookup is scoped by `user_id = Auth::id()` and `ip_addresscart` still stores the IP (harmless).

`AuthController::login()` performs **no** migration of `user_id IS NULL` rows to the new user id.

---

## UF-9 — Wishlist

### Authenticated user (correct path)

```
1. Blade asks: GET /wishlist/check?product_id=&variant_id=     (middleware: auth) ✅
2. Blade asks: GET /wishlist/check-product?product_id=&exclude_variant_id=  ✅
   → warns "you already have variant X of this product in your wishlist"
3. User clicks heart → POST /wishlist/toggle
4. WishlistController::toggle()  :12
5.   existing = Wishlist::where(user_id, product_id, product_varient_id)->first()
6.   if existing → delete → {status:'removed'}
7.   else → Wishlist::create([...]) → {status:'added'}
```

### Guest — **BROKEN**

```
1. POST /wishlist/toggle     ← routes/web.php:56, NO middleware('auth')
2. Auth::id() === null
3. existing = Wishlist::where('user_id', null)→ 'user_id IS NULL' → likely null
4. Wishlist::create([user_id => NULL, ip_address => ip, …])   ← orphan row written
5. Guest GET /wishlist  →  Wishlist::where('user_id', null)  →  shows EVERY guest's orphan rows
6. Guest DELETE /wishlist/{id}/remove → where('id',$id)->where('user_id', null)->delete()
                                          → deletes ANY row by ID  ❌ IDOR
```

| Failure point | Behaviour | Evidence |
|---|---|---|
| `toggle` / `index` / `remove` have no `auth` middleware | `Auth::id()` is `null`; operations hit `user_id IS NULL` | `routes/web.php:56-58` |
| `remove` | **Any wishlist row can be deleted by ID.** This is a genuine IDOR. | `WishlistController.php:45-49` |
| `index()` orphan rendering | `wishlist.blade.php:61` dereferences `$item->product->product_image`. `with('product.variants.unit')` does **not** filter out rows whose product was soft-deleted ⇒ **`Attempt to read property "product_image" on null` — VERIFIED in log (3 occurrences)** | Log; `wishlist.blade.php:61` |
| `checkProduct()` label | `$v->unit->short_name` — `unit` may be null; guarded by `?? ''` so it is safe | `:57-59` |

---

## UF-10 — Cart → update → remove — **BROKEN (IDOR)**

### Update (non-AJAX form)

```
1. cart.blade.php:268  fetch(url, {method:'POST', body: FormData with quantities[id]=qty})
2. POST /cart-update                CartController::update()  :35
3.   foreach ($request->quantities as $cartId => $qty)
4.       Cart::where('id', $cartId)->update(['product_quantity' => $qty])
5.   return back()->with('success', …)
```

**Two defects:**
- **(A) No ownership scoping.** `Cart::where('id', $cartId)` — no `user_id`, no `ip_addresscart`. **Any authenticated user (or anonymous visitor, since the route is not in the `auth` group) who enumerates cart IDs can rewrite any cart's quantities.** `carts.id` is a sequential `AUTO_INCREMENT`, so IDs are trivially guessable.
- **(B) No input validation.** `$qty` is written straight from the request. A client can send `quantities[1]=99999999` or `quantities[1]=-50`. `carts.product_quantity` is `int(11)` so MySQL coerces/clamps, but no business limit (stock, max-per-order) is applied.

Evidence: `CartController.php:35-44`.

### Remove

```
1. GET /cart-remove/{id}             ← destructive side effect on GET
2. CartController::remove($id)       :46
3.   $cart = Cart::findOrFail($id);
4.   $cart->delete();
5.   return JSON if request()->ajax() else back()
```

**Three defects:**
- **(A) No ownership check.** Any visitor can delete any cart line by ID.
- **(B) It is a `GET`.** Laravel's `ValidateCsrfToken` middleware only checks state-changing verbs, so this endpoint has **no CSRF protection**. A single `<img src="/cart-remove/5">` on any page deletes someone's cart line.
- **(C) Browser-prefetch risk.** Browsers prefetching links on hover can destroy carts.

Evidence: `routes/web.php:41-42`, `CartController.php:46-60`.

---

## UF-11 — Checkout → pay → order — **BROKEN (CRITICAL)**

```
┌─ CHECKOUT PAGE ─────────────────────────────────────────────────────┐
│ 1. GET /checkout                       CheckoutController::index()  :38
│ 2.   addresses = UserAddress::where('user_id', $user->id)->latest()
│ 3.   cartItems = getCartItems()                                 :688
│ 4.   if empty → redirect('/cart')
│ 5.   subtotal, totalGst, totalWeight computed server-side       :55-69
│ 6.   states = State::all()
│ 7.   render pages/checkout
├─ COURIER QUOTE ────────────────────────────────────────────────────┤
│ 8. changePaymentMethod() → fetchCouriers(pincode, method)  blade:652
│ 9. POST /checkout/get-couriers             getShiprocketCouriers()  :89
│10.   totalWeight = Σ (variant.weight ?? 0.5) * qty            :102-105
│11.   if totalWeight > 10 → /= 1000                          :107-109
│12.   totalWeight = max(totalWeight, 0.1)                     :110
│13.   ShiprocketService->checkServiceability()               ← external HTTP
│14.   rate = COD ? freight_charge + cod_charges : freight_charge  :136
│15.   usort by rate ascending                               :141-143
│16.   cheapest auto-selected in the UI
├─ ONLINE PAYMENT ───────────────────────────────────────────────────┤
│17. click "Place Order" → handlePlaceOrderClick()  blade:733
│18. POST /checkout/prepare-payment              preparePayment()  :172
│19.   recompute subtotal + gst in PHP                       :181-189
│20.   $shipping = floatval($request->shipping_charge)  ← FROM BROWSER
│21.   $discount = floatval($request->coupon_discount)  ← FROM BROWSER
│22.   $total    = max(subtotal+gst+shipping-discount, 1)
│23.   Razorpay Api->order->create(amount = round($total*100))
│24.   returns { razorpay_order_id, razorpay_key, user{name,email,phone} }
│25. Razorpay modal opens → CUSTOMER PAYS  ← MONEY LEAVES THE ACCOUNT
├─ ORDER PLACEMENT ──────────────────────────────────────────────────┤
│26. POST /checkout/place-order                   placeOrder()  :225
│27.   Validator: 9 shipping fields + payment_method in [Online Payment, COD]
│28.   IF Online Payment → verifyPaymentSignature()          :252-268
│29.   getCartItems(); recompute subtotal + gst in PHP
│30.   $total = (subtotal+gst+shipping) - discount      ← no floor this time
│31.   DB::beginTransaction()                          :292
│32.     ① ProductOrder::create()      → product_orders
│33.     ② ProductOrderUserAddress::create() → product_order_user_addresses
│34.     ③ ProductTransactionLog::create()   → product_transaction_logs
│35.     ④ ProductTracking::create()         → product_tracking
│36.     ⑤ foreach cart item:
│37.          ProductOrderItem::create()      → product_order_items
│38.          ProductSlot::create()           → product_slots
│39.          ProductStock: availablestock-=q, salestock+=q
│40.          ProductVarient: product_qty -=q
│41.     ⑥ Cart::where('user_id')->delete()
│42.     DB::commit()                                          :421
│43.   catch(Throwable) → rollBack + Log::error + HTTP 500   :423-430
├─ FULFILMENT (outside the transaction) ─────────────────────────────┤
│44.   ShiprocketService::createOrder()                  ← external HTTP
│45.     on success: order->update(shiprocket_order_id, shipment_id, status)
│46.   if selected_courier_id → assignAWB()  → order->update(awb_code, courier_name)
│47.   catch(Exception) → Log::error ONLY  ← swallowed
├─ RESPONSE ─────────────────────────────────────────────────────────┤
│48. 200 { status:'success', order_number, redirect_url: thankyou }
```

### Where this flow actually stops — **VERIFIED**

| Failure | Evidence |
|---|---|
| **Step 32 FAILS.** `Unknown column 'billing_state' in 'field list'` | `storage/logs/laravel.log` 2026-08-17 05:12:55 |
| **Step 37 FAILS** (after `billing_state` was manually patched). `Unknown column 'product_image' in 'field list'` | `storage/logs/laravel.log` 2026-08-17 |
| Steps 33-41 never run. The transaction rolls back. Cart is not cleared. **No order exists.** | `:423-430` |
| HTTP 500 returned to the browser: `{"status":"error","message":"Order failed to save: SQLSTATE…"}` | `:429` — leaks the raw SQL error to the customer |
| **Step 25 already happened.** The Razorpay payment was captured and `payment_status = 'Paid'` was recorded in the attempted INSERT. **No refund logic exists anywhere in the codebase.** | Log shows `payment_status 'Paid'`, `razorpay_payment_id 'pay_TQiWawBEhaCveJ'` inside the failed statement |
| A later run on the same day succeeded through to step 44 (`Creating Shiprocket order {"order_id":"ORD-6A82999B299AB"}`) ⇒ the columns were **manually added to that one database** outside version control. | Log 2026-08-17 05:18 |

### Secondary failure points

| Step | Defect | Evidence |
|---|---|---|
| 10-12 | Weight heuristic `>10 → /1000` mixes kg and grams arbitrarily; floor is `0.1` here but `0.5` at step 44 ⇒ **quoted rate ≠ actual charge** | `:107-110` vs `:460` |
| 20-21 | Shipping and discount are **client-supplied**. Nothing recomputes them from `states.shipping_charge` or a coupon table. A crafted POST can set `coupon_discount` to a negative number, which **increases** `total_amount`. | `:286-288` |
| 22 vs 30 | `max($total, 1)` in `preparePayment` but no floor in `placeOrder` ⇒ the recorded order total can differ from the amount actually charged | `:193` vs `:288` |
| 45 | `$request->selected_courier_name` is written to `courier_name` **before** the AWB call, then **overwritten** by the AWB response. If the AWB call fails, `courier_name` keeps the raw client string. | `:321` vs `:517-521` |
| 47 | Shiprocket failure is swallowed, yet step 48 still returns `"Order placed successfully!"`. The order row has `shiprocket_status = NULL`, no AWB, and **nothing retries**. | `:525-527` |
| 37-40 | Oversell is silently permitted: `max(stock - qty, 0)` records 0 rather than failing. Two customers can both buy the last unit. | `:403-415` |
| 48-UF-12 | The thank-you page then 404s/blank because no order row exists. | `:540-548` |

---

## UF-12 — My Account — **PARTIAL**

```
1. GET /myaccount                      routes/web.php:57 → ProfileController::index()  :69
   ── NO middleware('auth')
2.   wishlistItems = Wishlist::where('user_id', Auth::id())->with('product.variants','variant.unit')->get()
3.   cartItems     = Cart::where('user_id', Auth::id())->with('product','variant.unit')->get()
4.   orders        = ProductOrder::where('user_id', Auth::id())->with('items.product','items.variant')->latest()->get()
5.   addresses     = UserAddress::where('user_id', Auth::id())->latest()->get()
6.   states        = State::all()
7.   cartSubtotal  = Σ (variant.offer_price ?? product.product_price ?? 0) * qty
8.   render pages.myaccount  (820 lines, 5 tabs)
```

| Failure point | Behaviour | Evidence |
|---|---|---|
| **Guest visits** | `Auth::id()` is `null` ⇒ queries return `user_id IS NULL` rows ⇒ the page renders showing **other users' orphan data** instead of redirecting to login | `routes/web.php:57` has no `auth` |
| Orphan cart line | `$item->variant->unit->short_name` where `unit` is null ⇒ **VERIFIED** `Attempt to read property "name" on null (View: …\pages\myaccount.blade.php)` | Log, 2 occurrences 2026-08-17 |
| Orphan wishlist line | `$item->product` null ⇒ same class of error | `myaccount.blade.php:502, 586` |
| `product_price` | Column does not exist ⇒ silent null ⇒ `?? 0` | SQL dump |
| `State::all()` on every visit | Full states table, uncached | `:82` |
| `PUT /profile-update` as guest | `auth()->user()` is null, then `$user->id` ⇒ fatal | `routes/web.php:30-31`, `ProfileController.php:14-20` |
| Order list | Correctly scoped to the user. But orders will be empty because F-36 never persists them. | `:76-79` |

---

## Flow-level cross-cutting problems

| # | Problem | Affects |
|---|---|---|
| 1 | **`country_id` in session gates `/`, `/products`, `/product/*` but not `/myaccount`, `/cart`, `/wishlist`, `/search`.** A user can deep-link to the cart with no country selected and get a partially-branded page. | UF-1, UF-5, UF-12 |
| 2 | **Guest → user cart/wishlist merge is missing**, so the natural "browse, add, then register" journey loses the basket. | UF-7, UF-8, UF-9 |
| 3 | **No exception handler.** `withExceptions()` is empty, so any uncaught error becomes a full Laravel debug page (or a bare 500 in production). | All |
| 4 | **Every failure after payment capture is a hard 500**, not a recoverable state. There is no "payment received, order pending" record, no retry, no reconciliation job. | UF-11 |
| 5 | **No CSRF token refresh handling except in one place.** `checkout.blade.php:572-575` handles HTTP 419 for address save only; the other 20+ `fetch()` calls do not. | UF-9, UF-10, UF-11 |

---

## Related documents

- [06-api-and-routes.md](06-api-and-routes.md) — the endpoints these flows call
- [08-business-logic.md](08-business-logic.md) — BL-* rules referenced above
- [14-error-handling.md](14-error-handling.md) — where each failure surfaces
- [20-known-issues.md](20-known-issues.md) — bug register with evidence
