# 14 — Error Handling and Logging

---

## 1. Global configuration

### `bootstrap/app.php`

```php
->withMiddleware(function (Middleware $middleware): void {
    //
})
->withExceptions(function (Exceptions $exceptions): void {
    //
})
```
`:13-17`. **Both bodies are empty comments.**

| Missing | Consequence |
|---|---|
| No exception `renderable` / `reportable` | Every exception follows Laravel's default handling |
| No `dontReport` list | Everything is logged, including vendor noise |
| No `respond` for JSON | AJAX clients get HTML error pages |
| No 419/CSRF handler | CSRF failures show a raw "TokenMismatch" page |
| No 404 custom view | Laravel's default error page |
| No `preventLazyLoading` | N+1 queries are silent |

### Logging configuration

`.env` → `LOG_CHANNEL=stack`, `LOG_STACK=single`, `LOG_LEVEL=debug`, `LOG_DEPRECATIONS_CHANNEL=null`.

| Setting | Value | Assessment |
|---|---|---|
| Channel | `single` | One unbounded file, `storage/logs/laravel.log` |
| Level | `debug` | **Everything is written**, including full SQL and stack traces |
| Rotation | none | File grows indefinitely |
| Sentry / Bugsnag / Slack | absent | **No alerting** — nothing pages anyone when checkout breaks |
| `LOG_DEPRECATIONS_CHANNEL=null` | | Deprecations silently dropped, so PHP 8.2+ migration issues go unnoticed |

---

## 2. Log contents — VERIFIED

`storage/logs/laravel.log` contains (verified by reading):

| Category | Examples |
|---|---|
| **SQL failures** | `Unknown column 'billing_state' in 'field list'`, `Unknown column 'product_image' in 'field list'`, `Unknown column 'sub_categories.category_id' in 'where clause'` |
| **Full failed INSERT statements** | Bound values visible ⇒ **customer emails, phone numbers, addresses, `razorpay_payment_id` in plaintext** |
| **View errors** | `View [home] not found`, `View [pages\product-details] not found`, `Undefined variable $banners` |
| **Null-property errors** | `Attempt to read property "product_image" on null` (×3), `Attempt to read property "name" on null` (×2) |
| **Class errors** | `Class "Razorpay\Api\Api" not found` (×2) |
| **Integration activity** | `Shiprocket Login Successful`, `Shiprocket Serviceability API Error`, `Creating Shiprocket order {"order_id":"ORD-6A82999B299AB"}` |
| **Timeouts** | `cURL error 28: Operation timed out` on Shiprocket calls |

### PII exposure

The failed INSERT logs are the concern. A `QueryException` message includes the full SQL with bound parameters, which for `product_orders` means:

```
'billing_email' => <customer email>
'billing_phone' => <customer phone>
'billing_door_no', 'billing_street', 'billing_area', 'billing_city'
'razorpay_payment_id' => pay_TQiWawBEhaCveJ
```

**This log file is a customer-PII database.** It is gitignored (`*.log`), which helps, but:
- No retention policy
- No access control beyond filesystem permissions
- Not shipped anywhere, so nobody is alerted when it grows or when new failure classes appear

---

## 3. Error handling by controller

### Pattern used throughout

```php
try {
    ...
    return response()->json(['status' => 'success', ...]);
} catch (Exception $e) {
    Log::error('... ' . $e->getMessage());
    return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
}
```

### Assessment per site

| Location | Logs | Leaks to client | Verdict |
|---|---|---|---|
| `CheckoutController.php:423-430` (order persistence) | `Log::error('Error saving order: '.$e->getMessage())` | **Yes — SQL + bound PII** | **Critical** |
| `CheckoutController.php:~210-216` (Razorpay) | `Log::error('Payment init failed…')` | **Yes — `$e->getMessage()`** | **Critical** |
| `CheckoutController.php:112-127` (courier quote) | `Log::error('Error fetching couriers…')` | Yes | High |
| `CheckoutController.php:520-527` (Shiprocket create) | `Log::error('Shiprocket createOrder failed…')` | **No — swallowed** | **High (silent failure)** |
| `CheckoutController.php:553+` (address CRUD) | none needed | generic messages | **Good** |
| `CartController` | none | 500s bubble up | Adequate |
| `WishlistController` | none | 500s bubble up | Adequate |
| `SearchController` | none | JSON always 200 | Adequate |
| `AuthController` | none | generic messages | Adequate (except SEC-13) |

### The silent-failure problem

`placeOrder()` reports success regardless:

```php
try {
    $response = $shiprocket->createOrder($orderData);
    $orderId  = $response['order_id'] ?? null;
    if ($orderId) { ...update... }
} catch (Exception $e) {
    Log::error('Shiprocket createOrder failed: ' . $e->getMessage());
    // no shiprocket_status marker, no user-visible warning
}
return response()->json(['status' => 'success', 'order_number' => ..., ...]);
```

**A Shiprocket outage produces orders that look complete to the customer and to staff, and are invisible to the database.** No `shiprocket_status = 'FAILED'`, no retry, no alert. The dashboard's `shipments` view will simply show nothing for them.

---

## 4. HTTP status codes

| Case | Code | Assessment |
|---|---|---|
| Validation failure (`$request->validate`) | 422 | Correct |
| Auth failure | 422 with `errors` | Should be 401/422 with a redirect for HTML |
| Not found (`findOrFail`) | 404 | Correct |
| `abort_unless(..., 403)` on addresses | 403 | Correct |
| Order save failure | **500** with SQL text | **Wrong** — should be 409/422 with a generic message |
| Razorpay failure | **500** with `$e->getMessage()` | **Wrong** |
| CSRF token mismatch | **419** | Correct code, but no friendly handling in JS |
| Successful AJAX | 200 with `{status:'success'}` | Consistent |

**No endpoint returns 201, 204, or 401.** The whole API speaks `200` + `{status}` or `422` + `{errors}`.

---

## 5. Blade-level error handling

### SweetAlert2 / Toastr

| File | Mechanism |
|---|---|
| `app.blade.php:117` | SweetAlert2 success toast from `session('success')` |
| `layouts/app.blade.php` | Toastr for cart/wishlist AJAX feedback |
| `checkout.blade.php:572-575` | **The only place HTTP 419 is handled** |
| `products.blade.php`, `home.blade.php`, `header.blade.php` | Generic AJAX error alerts |

### Missing
- No global `fetch` wrapper that attaches `X-CSRF-TOKEN` (SEC-19)
- No network-error handling — a dropped connection shows a spinner forever
- No retry logic
- No disabled-button state while an action is in flight ⇒ **double-submit is possible on "Place Order"**, and with no idempotency key that means a double charge

### Unhandled flash key

`CountryController::store()` `:21` does:
```php
return redirect('/select-country')->with('error', 'Invalid country selected.');
```
`country-selection.blade.php` **never renders `session('error')`** (verified by grep). The user submits an invalid country and sees **no message at all**.

---

## 6. Log-based verification used in this audit

| Log line | Drove which conclusion |
|---|---|
| `Unknown column 'billing_state' in 'field list'` | SEC-04, D-S1, F-36 — checkout is broken |
| `Unknown column 'product_image' in 'field list'` | Same — second missing column |
| `Class "Razorpay\Api\Api" not found` | DEP-01 — the manual `require_once` hack exists because of this |
| `Shiprocket Login Successful` | Shiprocket auth works |
| `Creating Shiprocket order {"order_id":"ORD-…"}` | A later run succeeded ⇒ the columns were **manually added** outside version control |
| `Attempt to read property "product_image" on null` | Soft-deleted products leave dangling wishlist rows |
| `Attempt to read property "name" on null` | Orphan cart lines ⇒ null `unit` relation |
| `Unknown column 'sub_categories.category_id'` | R-01 — broken Category↔SubCategory relationship |
| `cURL error 28: Operation timed out` | No HTTP timeout configured on Shiprocket calls |
| Failed INSERT with bound `pay_TQiWawBEhaCveJ` | Payment captured before the order failed; PII in logs |

---

## 7. Recommendations

| Priority | Action |
|---|---|
| **P0** | Never return `$e->getMessage()` to the client. Log the detail, return a generic message + a correlation id |
| **P0** | Fix the two missing columns so the order-persistence 500 disappears |
| **P0** | Add `shiprocket_status = 'FAILED'` on integration failure and surface it to staff |
| **P1** | Add a `reportable` closure to redact PII from `QueryException` context |
| **P1** | Add exception rendering for JSON requests (so AJAX gets JSON, not HTML) |
| **P1** | Global CSRF + error wrapper for `fetch` |
| **P1** | Disable "Place Order" while in flight (prevents the no-idempotency double charge) |
| **P1** | Add `SHIPROCKET_TIMEOUT` (10 s) and `->retry(2, 200)` |
| P2 | Rotate logs daily (`LOG_CHANNEL=daily`), set `LOG_LEVEL=info`, add a retention job |
| P2 | Add Sentry or a Slack webhook to `config/logging.php` |
| P2 | Render `session('error')` in `country-selection.blade.php` |
| P2 | Add friendly 419/404/500 Blade pages via `withExceptions` |
| P3 | Add `Model::preventLazyLoading(! app()->isProduction())` |
| P3 | Schedule `session:prune` in `routes/console.php` |

---

## 8. Related documents

- [13-security-audit.md](13-security-audit.md) — SEC-15 error leakage, SEC-18 PII
- [15-broken-features.md](15-broken-features.md) — the two missing columns
- [16-performance.md](16-performance.md) — the N+1 that silent logging hides
- [23-external-services.md](23-external-services.md) — timeout and retry gaps
- [22-database-integrity.md](22-database-integrity.md) — corrective migrations