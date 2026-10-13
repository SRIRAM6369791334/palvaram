# 25 — Forms and Validation

---

## 1. Inventory

Verified against `routes/web.php`, `app/Http/Controllers/`, and `resources/views/`.

| Form / action | Route | HTTP | Auth | Server validation | Ownership check |
|---|---|---|---|---|---|
| Country select | `/select-country` | GET + POST | no | **none** | n/a |
| Country store | `/select-country` | POST | no | **none** | n/a |
| Register | `/register-store` | POST | no | **yes** (5 rules) | n/a |
| Login | `/login-store` | POST | no | **yes** (2 rules) | n/a |
| Forgot password | `/forgot-password` | POST | no | **yes** (1 rule) | n/a |
| **Reset password** | `/reset-password-direct` | POST | no | **yes** (2 rules) | **n/a — S1** |
| Profile update | `/profile-update` | PUT | **no** | **yes** | **fatal when anonymous** |
| Logout | `/logout` | POST | no | none | n/a |
| Cart view | `/cart` | GET | no | none | scoped (user or guest cookie) |
| **Cart bulk update** | `/cart-update` | POST | no | **none** | **none — IDOR** |
| **Cart remove** | `/cart-remove/{id}` | **GET** | no | **none** | **none — IDOR + state change on GET** |
| Cart add | `/cart/store` | POST | no | **none** | keyed on user/ip |
| Cart ajax update | `/cart/ajax-update` | POST | no | **none** | **scoped correctly** |
| Cart ajax remove | `/cart/ajax-remove` | POST | no | **none** | **scoped correctly** |
| Cart subtotal | `/cart/subtotal` | GET | no | none | scoped |
| Wishlist toggle | `/wishlist/toggle` | POST | **no** | **none** | `Auth::id()` may be `null` |
| Wishlist view | `/wishlist` | GET | **no** | none | `Auth::id()` may be `null` |
| Wishlist remove | `/wishlist/{id}/remove` | **DELETE** | no | **none** | **scoped correctly** |
| Wishlist check | `/wishlist/check` | GET | yes | none | scoped |
| Wishlist check-product | `/wishlist/check-product` | GET | yes | none | scoped |
| Checkout view | `/checkout` | GET | yes | none | — |
| **Prepare payment** | `/checkout/prepare-payment` | POST | yes | **none** | — |
| Get couriers | `/checkout/get-couriers` | POST | yes | **none** | — |
| Get shipping charge | `/checkout/get-shipping-charge` | POST | yes | **none** | — |
| Get cities | `/checkout/get-cities` | POST | yes | **none** | — |
| Place order | `/checkout/place-order` | POST | yes | **yes** (9 rules) | cart scoped by user |
| Thank-you | `/checkout/thankyou/{order}` | GET | yes | n/a | **scoped correctly** |
| Save address | `/checkout/address` | POST | yes | **yes** (10 rules) | user from `Auth::id()` |
| Update address | `/checkout/address/{address}` | PUT | yes | **yes** (10 rules) | **`abort_unless` — correct** |
| Delete address | `/checkout/address/{address}` | DELETE | yes | n/a | **`abort_unless` — correct** |

**Zero Form Request classes exist.** All validation is inline, and `CartController` and `WishlistController` have **no validation at all**.

### Forms that do not exist

| Referenced | Where | Reality |
|---|---|---|
| Contact form | `header.blade.php:114`, `:150`, `footer.blade.php:53` link to `contact.html` | **No `ContactController`, no route, no view, no file in `public/`.** Every "Contact Us" link is a 404 |
| Forgot-password view | needed by `Password::sendResetLink()` | **Does not exist** |
| Reset-password view | needed to collect a token | **Does not exist** — and no token is used anyway |
| `web.php` at all | `routes/` | **Only `web.php` exists.** No `api.php`, so every endpoint is a session-authenticated web route |

---

## 2. Validation by form

### 2.1 Register — `AuthController::registerStore()`

```php
$request->validate([
    'last_name'    => 'required',
    'name'         => 'required',
    'email'        => 'required|email|unique:users,email',
    'phone_number' => 'required',
    'password'     => 'required|min:6',
]);
```

| Rule present | Issue |
|---|---|
| `password: min:6` | weak; no complexity, no breach check |
| **no `confirmed`** | the form has no confirmation field |
| no `Password::defaults()` | policy not centralised |
| `phone_number: required` only | **no uniqueness rule**, yet `users.phone_number` carries `users_phone_number_unique` — so the failure surfaces as a raw SQL duplicate-key error, not a validation message |
| `name` / `last_name` no `max:` | unbounded (columns are `varchar(255)`, so a long value truncates or errors) |
| `country_id` | taken from `session('country_id')`, **never validated** — guests registering without picking a country store `null` |

### 2.2 Login — `AuthController::login()`

```php
$request->validate([
    'username' => 'required',
    'password' => 'required',
]);

$user = User::where('email', $request->username)
            ->orWhere('phone_number', $request->username)
            ->first();

if (!$user || !Hash::check($request->password, $user->password)) { … 422 … }

Auth::login($user);
```

Missing:

| Gap | Impact |
|---|---|
| **No `throttle`** | unlimited credential stuffing |
| **`$request->session()->regenerate()` absent** | **session fixation** — the pre-login session id survives authentication |
| no captcha | bots unopposed |
| generic 422 message | good — does not disclose whether the account exists |
| `email` lookup is case-sensitive in MySQL's default collation only by luck | `Foo@x.com` vs `foo@x.com` behaviour depends on the column collation |
| no `Auth::attempt()` | hand-rolled; skips Laravel's `attempt()` events and lockout integration |

`ProfileController::logout()` **does** call `invalidate()` and `regenerateToken()` — the asymmetry is the defect: the vulnerable side is login.

### 2.3 Password reset — **S1, account takeover**

```php
public function resetPassword(Request $request)
{
    $request->validate([
        'email'    => 'required|email',
        'password' => 'required|min:6',
    ]);

    $user = User::where('email', $request->email)->first();
    if (!$user) { return 422 'User not found'; }

    $user->password = Hash::make($request->password);
    $user->save();

    Auth::logout();
}
```

**No `token` field is read. No `Password::tokenExists()`. No comparison. No reset view. No reset route that carries a token** — the route is literally named `password.reset.direct`.

| Consequence | Severity |
|---|---|
| Any unauthenticated POST with a known email resets that account's password | **S1 — full account takeover** |
| Response distinguishes known from unknown emails (`'User not found'`) | S2 — enables account enumeration |
| No rate limit | S2 — the reset endpoint is an unthrottled oracle |
| No email to the owner | takeover is silent |
| No `password_confirmation` | typo becomes a new password |
| `Auth::logout()` at the end | logs out whoever is currently authenticated — odd, but not harmful |

`forgotPassword()` calls the real `Password::sendResetLink()` correctly and returns proper status JSON. The link it mails would point at a route that **does not exist**, so the flow dead-ends at step two. The dangerous part is that `resetPassword()` needs no token at all.

### 2.4 Profile update — fatal for anonymous users

Route is `Route::put('/profile-update', …)` with **no `auth` middleware**. The controller starts:

```php
$user = auth()->user();
$request->validate([
    'email' => 'required|email|unique:users,email,' . $user->id,
]);
```

`$user` is `null` when unauthenticated, so `$user->id` throws **before validation runs** — a 500 with a stack trace in `laravel.log`. Not a data-integrity bug, but an unhandled error on a public endpoint.

Password change is **better than documented elsewhere**: it requires `current_password`, `confirmed`, and `min:6`, and verifies with `Hash::check` before writing. The write uses `bcrypt()` rather than `Hash::make()`, which is functionally equivalent here.

### 2.5 Addresses — `CheckoutController::validateAddress()` `:672-686`

```php
return $request->validate([
    'first_name'       => ['required', 'string', 'max:255'],
    'last_name'        => ['required', 'string', 'max:255'],
    'phone_number'     => ['required', 'string', 'max:20'],
    'email'            => ['required', 'email', 'max:255'],
    'address_line_one' => ['required', 'string', 'max:255'],
    'address_line_two' => ['nullable', 'string', 'max:255'],
    'landmark'         => ['nullable', 'string', 'max:255'],
    'city'             => ['required', 'string', 'max:255'],
    'state'            => ['required', 'string', 'max:255'],
    'pincode'          => ['required', 'string', 'max:10'],
]);
```

This is the **best-validated form in the application** — typed rules with length caps throughout.

Remaining gaps:

| Gap | Consequence |
|---|---|
| `pincode: max:10`, no format rule | any string up to 10 chars accepted; `max:10` instead of `size:6` |
| `state` is free text | no `exists:states,name`; a typo silently produces an unshippable order |
| `area_id` not accepted | the `UserAddress::area()` relation and `country()` relation are unreachable from the form |
| `country_id` not accepted | the `UserAddress::country()` broken relation (R-03) can never be populated correctly |
| no default-address flag | `user_addresses.is_default` is never set |
| `state` and `country` written to `product_order_user_addresses` are unchecked strings | — |

**Ownership is handled correctly** on update and delete:

```php
public function updateAddress(Request $request, UserAddress $address)
{
    abort_unless($address->user_id === Auth::id(), 403);
```

`destroyAddress()` is identical. This is the pattern the rest of the app should copy.

### 2.6 Cart — no validation on any write

| Action | Validation | Notes |
|---|---|---|
| `store()` | **none** | `product_id`, `product_varient_id`, `quantity` all raw. `quantity` is only defaulted (`?? 1`); a negative or 1000-unit value is accepted, and `increment('product_quantity', 1)` has no ceiling |
| `update()` | **none** | iterates `$request->quantities` as `cartId => qty`. If `quantities` is absent, a PHP warning; if present, `Cart::where('id', $cartId)->update([...])` with **no owner scope** |
| `remove()` | **none** | `Cart::findOrFail($id)` — **no owner scope** — reached by `GET` |
| `ajaxUpdate()` | **none** | **owner-scoped** (`user_id` or `ip_addresscart` + `whereNull('user_id')`) |
| `ajaxRemove()` | **none** | **owner-scoped** |

Two important corrections to earlier assumptions:

- **`Cart::where('product_varient_id', …)` does not match on `product_id`.** A cart can legitimately hold one row per variant, so this is right.
- **The guest cart key is inconsistent across the controller.** `index()` and `subtotal()` read `request()->cookie('guest_token') ?? request()->ip()`, but `store()`, `ajaxUpdate()`, and `ajaxRemove()` use `$request->ip()` only. The `guest_token` cookie is **never set by any code in this repository**, so the `??` fallback always wins in practice — but the intent is clearly broken, and the moment a cookie *is* set, `store()` will write a row the reader cannot find.

`carts.guest_token` is **not a column**. The intended design (a real guest token) was never implemented; ownership rests on IP address, which is shared by offices, NATs, and mobile carriers.

### 2.7 Wishlist

| Action | Validation | Ownership |
|---|---|---|
| `toggle()` | **none** | `Auth::id()` — **the route has no `auth` middleware**, so an anonymous POST writes `user_id = null`. Later reads filter `user_id = null` and can surface another visitor's rows |
| `index()` | **none** | `Auth::id()` may be `null` |
| `remove()` | **none** | `Wishlist::where('id', $id)->where('user_id', Auth::id())->delete()` — **scoped correctly** |
| `check()` | **none** | scoped; behind `auth` |
| `checkProduct()` | **none** | scoped; behind `auth` |

`remove()` is `Route::delete(...)` — **correct verb and correct scope**. This is the one destructive endpoint that gets both right.

Two source comments contain **mojibake**: `WishlistController.php` contains `// �+? correct column name` and `// �+? correct column`. Harmless, but they indicate the file was edited through an encoding-mismatched tool.

### 2.8 Checkout — `placeOrder()`

```php
$rules = [
    'shipping_name'    => 'required|string',
    'shipping_email'   => 'required|email',
    'shipping_phone'   => 'required',
    'shipping_door_no' => 'required',
    'shipping_street'  => 'required',
    'shipping_city'    => 'required',
    'shipping_state'   => 'required',
    'shipping_pincode' => 'required',
    'payment_method'   => 'required|in:Online Payment,Cash on Delivery',
];
```

The shape is **flat `shipping_*` fields, not a `shipping_address` array**. There is **no `billing_address` field at all** — billing is populated from the shipping fields (`:299-311`).

| Field | Assessment |
|---|---|
| `payment_method` | `in:Online Payment,Cash on Delivery` — the two real values |
| `shipping_pincode: required` | no format rule |
| `shipping_state: required` | no `exists:states,name` |
| `shipping_phone: required` | no length or numeric rule; Shiprocket takes `substr(…, -10)` of the digit-stripped value |
| `shipping_*` no `max:` | unbounded |
| **`shipping_charge`** | **not validated at all** and read straight from the request at `:286` |
| **`coupon_discount`** | **not validated at all** and read straight from the request at `:287` |
| `total` | **not a parameter** — good; the total is computed |
| `products` / `products.*.quantity` | **not parameters** — line items come from the DB cart |

`preparePayment()` has **no `$request->validate()` call whatsoever**, yet reads `shipping_charge` and `coupon_discount` from the same request (`:191-192`). Two endpoints, two formulas, one untrusted input.

### 2.9 Checkout AJAX helpers

| Endpoint | Defect |
|---|---|
| `getShippingCharge()` | `trim($request->state_name)` on a possibly-absent field → PHP warning; no validation; name-based lookup returns 404 on a typo |
| `getCitiesByState()` | same; **always returns 200** with an empty list, so the UI cannot distinguish "no cities" from "bad state" |
| `getShiprocketCouriers()` | no validation on the pincode or weight it forwards to Shiprocket |

---

## 3. Cross-cutting defects

| ID | Defect |
|---|---|
| V-01 | **No Form Request classes** — validation is untestable and unreusable |
| V-02 | **`$e->getMessage()` returned to clients** on order failure (`:429`) and payment init (`:218`) — leaks SQL, column names, and Razorpay errors |
| V-03 | **No rate limiting anywhere** — login, register, contact, reset, and every checkout POST |
| V-04 | No password confirmation on register or reset; present on profile change |
| V-05 | **The entire Contact feature is missing** — four links point at a nonexistent page |
| V-06 | No file uploads anywhere — none of the forms accept files |
| V-07 | **No ownership check on `CartController::update()` or `remove()`** — any visitor edits or deletes any cart row by id |
| V-08 | `quantity` is never validated on any cart path — negative and unbounded values are accepted |
| V-09 | **Cart remove is a `GET` route** that deletes — prefetchable, cacheable, logged, and linkable |
| V-10 | **No `idempotency` protection anywhere** — `placeOrder` has no double-submit guard; a retry creates a second order |
| V-11 | Register and reset leak account existence via `'User not found'` |
| V-12 | **`state` and `pincode` are free text** in checkout and addresses — the two fields courier integration depends on |
| V-13 | `fetch()` calls in Blade must each carry the CSRF header; none of them read it from a shared helper |
| V-14 | **`/profile-update` 500s on anonymous requests** because `auth()->user()` is dereferenced before validation |
| V-15 | **Guest-cart identity is inconsistent** — cookie on read, IP on write |
| V-16 | Wishlist `toggle` and `index` are reachable while logged out and key on `user_id = null` |

---

## 4. Corrected validation

### 4.1 `PlaceOrderRequest`

```php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PlaceOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'shipping_name'    => ['required', 'string', 'max:255'],
            'shipping_email'   => ['required', 'email', 'max:255'],
            'shipping_phone'   => ['required', 'string', 'regex:/^[0-9]{10,15}$/'],
            'shipping_door_no' => ['required', 'string', 'max:255'],
            'shipping_street'  => ['required', 'string', 'max:255'],
            'shipping_area'    => ['nullable', 'string', 'max:255'],
            'shipping_city'    => ['required', 'string', 'max:255'],
            'shipping_state'   => ['required', 'string', Rule::exists('states', 'name')],
            'shipping_pincode' => ['required', 'string', 'regex:/^[0-9]{6}$/'],
            'payment_method'   => ['required', Rule::in(['Online Payment', 'Cash on Delivery'])],

            // Razorpay callback — required only for prepaid.
            'razorpay_order_id'   => ['required_if:payment_method,Online Payment', 'string', 'max:255'],
            'razorpay_payment_id' => ['required_if:payment_method,Online Payment', 'string', 'max:255'],
            'razorpay_signature'  => ['required_if:payment_method,Online Payment', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'shipping_pincode.regex'        => 'Enter a valid 6-digit PIN code.',
            'shipping_phone.regex'          => 'Enter a valid phone number.',
            'shipping_state.exists'         => 'Choose a state we ship to.',
        ];
    }
}
```

`shipping_charge`, `coupon_discount`, and any total are **deliberately absent.** Accepting them would reintroduce the client-controlled pricing defect. They should be computed by a `PricingService` from the cart plus a server-side courier quote and a real `coupons` lookup.

### 4.2 `UpdateCartRequest` with ownership

```php
public function rules(): array
{
    return [
        'quantities'                => ['required', 'array'],
        'quantities.*'              => ['required', 'integer', 'min:0', 'max:20'],
    ];
}

public function withValidator($validator): void
{
    $validator->after(function ($validator) {
        $ids = array_map('intval', array_keys($this->input('quantities', [])));

        $owned = Cart::whereIn('id', $ids)
            ->when(Auth::check(),
                fn ($q) => $q->where('user_id', Auth::id()),
                fn ($q) => $q->where('ip_addresscart', $this->ip())->whereNull('user_id'))
            ->pluck('id');

        if ($owned->count() !== count(array_unique($ids))) {
            $validator->errors()->add('quantities', 'One or more cart lines are not yours.');
        }
    });
}
```

Then one `UPDATE ... WHERE id IN (...)` — the current loop performs **N queries and N statements** for a single form post.

### 4.3 Cart remove

```php
// routes/web.php
Route::delete('/cart/{id}', [CartController::class, 'remove'])->name('cart.remove');
```

```php
public function remove(Request $request, int $id)
{
    $query = Cart::whereKey($id)
        ->when(Auth::check(),
            fn ($q) => $q->where('user_id', Auth::id()),
            fn ($q) => $q->where('ip_addresscart', $request->ip())->whereNull('user_id'));

    abort_if($query->first() === null, 404);

    $query->delete();

    return $request->expectsJson()
        ? response()->json(['success' => true])
        : back()->with('success', 'Product removed from cart');
}
```

### 4.4 Guest cart identity

```php
private function cartIdentity(Request $request): string
{
    if ($request->user()) {
        return 'u:' . $request->user()->getAuthIdentifier();
    }

    if (! $request->hasCookie('guest_token')) {
        $token = Str::random(40);
        $request->cookies->set('guest_token', $token);
        cookie('guest_token', $token, 60 * 24 * 30);
    }

    return 'g:' . $request->cookie('guest_token');
}
```

Store this in the existing `carts.ip_addresscart` column, or add a real `guest_token` column via the guarded migration in [22-database-integrity.md](22-database-integrity.md). Use `cartIdentity()` in **all six** cart methods so reads and writes agree.

### 4.5 Password reset with a token

```php
public function resetPassword(ResetPasswordRequest $request)
{
    $status = Password::reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function ($user, string $password) {
            $user->forceFill([
                'password'       => Hash::make($password),
                'remember_token' => Str::random(60),
            ])->save();
        }
    );

    return $status === Password::PASSWORD_RESET
        ? redirect()->route('login')->with('status', __($status))
        : back()->withInput($request->only('email'))->withErrors(['email' => __($status)]);
}
```

Plus a `GET /reset-password/{token}` view and `password_confirmation` in the form. **Laravel implements all of this**; the application replaced it with 25 lines that skip the token.

### 4.6 Login hardening and rate limiting

```php
Route::post('/login-store', [AuthController::class, 'login'])
    ->middleware('throttle:5,1')
    ->name('login.store');

Route::post('/register-store', …)->middleware('throttle:5,1');
Route::post('/forgot-password', …)->middleware('throttle:3,1');
Route::post('/reset-password-direct', …)->middleware('throttle:3,1');
Route::post('/checkout/place-order', …)->middleware('throttle:10,1');
```

```php
$request->validate([
    'username' => ['required', 'string', 'max:255'],
    'password' => ['required', 'string', 'max:255'],
]);

// …lookup and Hash::check as before…

$request->session()->regenerate();   // ← the missing line
Auth::login($user, remember: (bool) $request->boolean('remember'));
```

### 4.7 Profile update

```php
Route::put('/profile-update', [ProfileController::class, 'update'])
    ->middleware('auth')
    ->name('profile.update');
```

```php
$user = $request->user();   // never null behind 'auth'
```

One middleware line removes the 500 and the null-dereference.

---

## 5. Suggested effort

| Change | Effort |
|---|---|
| Add `PlaceOrderRequest`, `UpdateCartRequest`, `SaveAddressRequest`, `ResetPasswordRequest`, `LoginRequest` | **M** |
| Remove client money fields from the checkout contract | **S** |
| Scope `CartController::update()` and `remove()` to the owner | **S** |
| Convert cart remove from `GET` to `DELETE` and update Blade callers | **S** |
| Implement token-based reset via `Password::reset` + a reset view | **M** |
| Add `auth` middleware to `/profile-update`, `/wishlist/toggle`, `/wishlist` | **S** |
| Unify guest-cart identity across the six cart methods | **M** |
| Add `throttle` middleware to 5 routes | **S** |
| Add `confirmed` + `Password::defaults()` | **S** |
| Replace `$e->getMessage()` with structured errors | **M** |
| Add `exists:` checks for `state`, `size:6` for `pincode`, phone regex | **S** |
| Either build the Contact feature or remove the four links | **S** / **M** |
| Add contact-message storage and an inbox | **L** |
| **Total** | **≈3 d** |

---

## 6. Related documents

- [05-user-flows.md](05-user-flows.md) — form-by-form walkthrough
- [06-api-and-routes.md](06-api-and-routes.md) — the 38 route definitions
- [08-business-logic.md](08-business-logic.md) — where totals are computed
- [13-security-audit.md](13-security-audit.md) — SEC-numbered findings
- [20-known-issues.md](20-known-issues.md) — BUG-numbered defects
- [27-api-reference.md](27-api-reference.md) — request and response shapes