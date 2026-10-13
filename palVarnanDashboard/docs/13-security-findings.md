# 13 — Security Findings

Prioritised register. Every entry has file-level evidence. Severity reflects exploitability by an
attacker with the stated starting position.

---

## CRITICAL

### SEC-01 — Any authenticated user can reset any other user's password

**Severity:** Critical · **Status:** `VERIFIED` · **CVSS-equivalent:** full account takeover

`app/Http/Controllers/DashboardUserController.php:145-165`

```php
public function update1(Request $request, $id)
{
    $user = DashboardUser::findOrFail($id);
    $validated = $request->validate(['password' => 'required']);
    $user->update(['password' => Hash::make($validated['password'])]);
    $users = DashboardUser::with(['roles'])->get();
    return response()->json(['message' => 'User Password Successfully Updated', 'users' => $users]);
}
```

**Missing:** any ownership check, any role check, current-password verification, password policy,
country scoping.

**Exploit:** log in as the lowest-privilege dashboard account → `POST` to the update-password route
with an administrator's `id` → log in as that administrator. Combined with
[SEC-02](#sec-02--no-server-side-authorization-anywhere) this is a single-request path to full
administrative control, including the ability to grant oneself any role and then edit prices,
stock, and orders.

**Also leaks** the complete user/role directory in the response body.

---

### SEC-02 — No server-side authorization anywhere

**Severity:** Critical · **Status:** `VERIFIED`

| Control | Present? |
| --- | --- |
| `can:` middleware on routes | No — 0 uses |
| `role:` / `permission:` middleware aliases | Not even registered in `$routeMiddleware` |
| `Gate::define` / `Gate::before` | None |
| Model policies (`app/Policies/`) | Directory empty |
| `$this->authorize()` | 0 occurrences |
| `authorizeResource` / `authorizeForUser` | 0 occurrences |

All 54 `@can()` directives live in Blade views and affect **link visibility only**. Requesting any
URL directly bypasses every one of them.

**Exploit:** an authenticated user with zero permissions can call `RoleController::updatePermissions()`
to grant themselves every permission, `RoleController::store()`/`destroy()` to rewrite the role
model, `DashboardUserController::userss()`/`update()`/`destroy()` to manage accounts,
`StockController::update()`/`reduceStock()` to alter inventory, and `ProductController::update()` to
change pricing. See [11-authorization-rbac.md](11-authorization-rbac.md).

---

### SEC-03 — Unauthenticated database write and data dump via `GET /api/test`

**Severity:** Critical · **Status:** `VERIFIED`

`routes/api.php:25` → `app/Http/Controllers/TestController.php:14-44`

```php
public function test(Request $request)
{
    $orderId = "MV-ORD-000006";
    $user = User::query()->where("user_id", "MV-CUS-00002")->first();
    $defaultUserAddress = $user->defaultAddress->toArray();

    MilkOrderUserAddress::create([...$defaultUserAddress, "order_id" => $orderId]);
    // ...
    $deliveryPerson = DeliveryPerson::with(['areaAssigns', 'milkOrders'])
        ->whereHas('areaAssigns', fn($q) => $q->where('area_id', $areaId))
        ->withCount('milkOrders')->get();

    dd($deliveryPerson);
}
```

The route is registered **outside** any auth middleware (`RouteServiceProvider::boot()` mounts
`routes/api.php` under the `api` group, which contains only `throttle:api` and
`SubstituteBindings`).

**Impact, no credentials required:**

1. **Write** — inserts a `milk_order_user_addresses` row on every call, copying a real customer's
   address. Repeated calls produce unbounded duplicate rows.
2. **Read** — `dd()` dumps delivery-person records with `areaAssigns` and milk-order counts.
3. **Availability** — `dd()` throws, so the request always 500s after the write. Each hit mutates
   the database.
4. **Enumeration** — `config/cors.php` sets `'allowed_origins' => ['*']` for `api/*`, so the
   response is readable cross-origin from any website.

Rate limit is 60/minute per IP (`RouteServiceProvider::configureRateLimiting()`), which is ample.

**Fix:** delete the route and the controller immediately.

---

### SEC-04 — Shiprocket API credentials hardcoded in source

**Severity:** Critical · **Status:** `VERIFIED` — three distinct credential pairs

| File:line | Email | Password |
| --- | --- | --- |
| `app/Http/Controllers/ProductController.php:1010` | `[SECRET_REDACTED]` | `[SECRET_REDACTED]` |
| `app/Http/Controllers/ProductOrdersController.php:110` | `[SECRET_REDACTED]` — *a different account* | `[SECRET_REDACTED]` |
| `app/Http/Controllers/ProductSlotController.php:131` | `[SECRET_REDACTED]` | `[SECRET_REDACTED]` |
| `app/Http/Controllers/ProductSlotController.php:202` | `[SECRET_REDACTED]` | `[SECRET_REDACTED]` |
| `app/Http/Controllers/ProductController.php:869` | `[SECRET_REDACTED]` | `[SECRET_REDACTED]` — commented out, still in the file |

**The proper implementation already exists and is ignored.** `.env` contains
`SHIPROCKET_EMAIL`, `SHIPROCKET_PASSWORD`, `SHIPROCKET_BASE_URL`, and
`app/Services/ShiprocketService.php` reads `config('services.shiprocket.base_url')`. Yet four
controllers still authenticate with raw `curl` and inline literals, bypassing the service and the
environment configuration.

**Impact:** anyone with read access to the repository — including via the single public git commit
if this is ever pushed — can create shipments, cancel orders, and read tracking data through the
Shiprocket account. Two different accounts are in use, so rotating one will not cover the other.

**Required:** rotate **both** Shiprocket accounts now, then delete the inline credentials and route
all calls through `ShiprocketService`.

---

## HIGH

### SEC-05 — Extension-based upload naming enables PHP execution in the document root

**Severity:** High (currently unreachable) · **Status:** `VERIFIED` (dead code)

`app/Http/Controllers/HomeController.php:131-140`

```php
if ($request->file('avatar')) {
    $avatarName = time() . '.' . $avatar->getClientOriginalExtension();
    $avatarPath = public_path('/images/');
    $avatar->move($avatarPath, $avatarName);
    $user->avatar = '/images/' . $avatarName;
}
```

Validation is `avatar => nullable|image|mimes:jpg,jpeg,png|max:1024`. Laravel's `mimes` rule checks
the file's **guessed MIME-derived extension**, not the client-supplied filename. `getClientOriginalExtension()`
reads the **client filename**. A valid JPEG uploaded as `shell.php` passes validation and is written
to `public/images/<timestamp>.php` — remote code execution under Apache.

`VERIFIED` — the route is **commented out** (`routes/web.php:629`), so this is currently
unreachable. It must be fixed before the route is ever re-enabled, and the code should be deleted
rather than left as a trap.

Contrast with the rest of the codebase, which uses `->store()` (random name, extension from
detected MIME) — the correct approach.

---

### SEC-06 — Country-scoping trusts a client-controllable session value

**Severity:** High · **Status:** `VERIFIED`

Admin screens filter data by `session('country_id')`, e.g.
`app/Http/Controllers/StockController.php:33-37`:

```php
$countryId = session('country_id');
if (!empty($countryId)) { $query->where('productstocks.country_id', $countryId); }
```

No middleware validates that the session value is among the countries the authenticated
`DashboardUser` is assigned. `DashboardUser::country_id` holds the permitted list but is only
consulted by the **private** `DashboardUserController::getAllowedCountryIds()`, which is used only
by that controller's `index()`.

**Impact:** a user assigned to country A selects country B in the country dropdown and reads
country B's stock levels, catalogue, orders, and reports. The same session value gates
`ProductController`, `GiftController`, `InventoryReportController`, and others.

---

### SEC-07 — Unvalidated stock mutation endpoints

**Severity:** High · **Status:** `VERIFIED`

`app/Http/Controllers/StockController.php`:

| Method | Route | Validation |
| --- | --- | --- |
| `store()` | `POST /stocks` | **none** |
| `update()` | stock increase | **none** |
| `reduceStock()` | stock decrease | **none** |
| `update1()` | stock increase + low-stock list | **none** |
| `acceptTransfer()` | `GET`/`POST` by `$id` | **none** |

`reduceStock()` and `update()` cast with `(int) $request->stock_quantity`. A negative value is a
valid integer and **increases** stock rather than decreasing it. `store()` writes
`overallstock`/`availablestock` straight from request input. No permission check applies to any of
them (see SEC-02).

---

### SEC-08 — Business-logic methods exposed as independent endpoints

**Severity:** High · **Status:** `VERIFIED`

| Method | Route |
| --- | --- |
| `ProductController::createMilkSlot($orderId)` | `routes/web.php:220` |
| `ProductController::createProductSlot($orderId, $selettype, Request)` | `routes/web.php:226` |
| `ProductController::createMilkSubscription(Request)` | `routes/web.php:218` |
| `ProductController::createProductSubscription(Request)` | `routes/web.php:224` |
| `ProductController::viewProductInvoice($orderId = null)` | `routes/web.php:238` |
| `ProductController::upadetstatus(Request)` | `routes/web.php:241` |

These are the **second half** of order creation. They are also called internally by
`createMilkSubscription()` / `createProductSubscription()`. Because they are separately routable, an
authenticated user can:

- Call `createProductSlot()` directly for an order that already has slots, re-running
  `ProductSlot::insert()` and the stock decrement — **double-deducting inventory**.
- Call `createMilkSlot()` with any existing `order_id`, appending arbitrary delivery dates to a
  delivered order.
- Call `viewProductInvoice()` for any `orderId`, including null, since the parameter defaults to
  `null`.

`assignDeliverProductOrder()` and `addProductOrderDeliveryAddress()` are also `public` (though not
routed) and should be `protected`.

---

### SEC-09 — ZIP extraction with no entry validation

**Severity:** High · **Status:** `VERIFIED`

`app/Http/Controllers/ProductController.php:1211-1223`

```php
$zip = new \ZipArchive;
$res = $zip->open($request->file('zip_file')->path());
if ($res === TRUE) {
    $tempPath = storage_path('app/temp_bulk_upload_' . time());
    File::makeDirectory($tempPath, 0755, true);
    $zip->extractTo($tempPath);
    $zip->close();
}
```

Validation is `'zip_file' => 'nullable|mimes:zip'` — an extension check only, and it does not
inspect archive contents.

`VERIFIED` — no validation of entry names before extraction. `ZipArchive::extractTo()` has
mitigated basic `../` traversal since PHP 5.6.32/7.0.17/7.1.3, so classic path traversal is
largely blocked on a modern PHP. Remaining concerns:

- Entry names are never checked for absolute paths or symlinks.
- `$tempPath` is derived from `time()` and is therefore **predictable**, enabling a pre-created
  directory to be targeted if the extraction and cleanup race.
- On Windows, backslash-separated entry names behave differently from the POSIX case.
- Archive size and entry count are unbounded — a zip bomb consumes disk in `storage/app/`.

**Also:** `$request->validate(['excel_file' => 'required|mimes:xlsx,xls,csv,txt'])` allows a plain
`.txt` file through `maatwebsite/excel`, and `ProductImport::collection()` writes rows for whatever
it reads.

**Fix:** iterate `$zip->getNameIndex()` and reject any entry containing `..`, a leading `/`, or a
backslash before extracting to per-entry paths.

---

### SEC-10 — Debug mode enabled, and `dd()` used on a live path

**Severity:** High · **Status:** `VERIFIED`

| Setting | Value |
| --- | --- |
| `.env` `APP_ENV` | `local` |
| `.env` `APP_DEBUG` | **`true`** |
| `.env` `APP_URL` | `http://localhost` |
| `.env` `LOG_LEVEL` | `debug` |

`APP_DEBUG=true` renders the Ignition error page on any exception, disclosing stack traces,
file paths, environment values, and query text to whoever triggered the error.

`VERIFIED` — `dd()` appears in `TestController::test()` (see SEC-03). `UNVERIFIED` — whether `dd()`
appears on other request paths; the codebase uses `dd()` liberally during development and no
authoritative scan of call sites was completed.

`VERIFIED` — `barryvdh/laravel-debugbar` is installed with an unpinned `*` version and
`DEBUGBAR_ENABLED=false`. `laravel/telescope` is installed and disabled from auto-discovery but
`config/telescope.php` is present; `TelescopeServiceProvider` is registered.

---

### SEC-11 — Exception messages returned to the client

**Severity:** High · **Status:** `VERIFIED`

`StockController::transferStock()`, `warehouseTransfer()`, `acceptTransfer()`,
`cancelTransfer()`, and all four `ProductManagementController` actions:

```php
} catch (\Exception $e) {
    DB::rollBack();
    return response()->json(['message' => $e->getMessage()], 500);
}
```

`$e->getMessage()` from a PDO exception includes the SQL statement, table names, column names, and
constraint names. From a filesystem exception it includes absolute paths. This hands an attacker a
schema map.

`VERIFIED` — `app/Exceptions/Handler.php` is unmodified scaffold: `$dontReport` is empty, there is
no custom `reportable()` logic, and `render()` is not overridden. Every exception is reported and
rendered by framework defaults.

---

### SEC-12 — Committed debug and schema artifacts at the project root

**Severity:** High · **Status:** `VERIFIED`

`check_db_raw.php`, `check_db_schema.php`, `debug_query.php`, `dump_order.php`,
`seed_promotions.php`, `test.php`, `tmp_schema_check.php`, `schema_output.txt`.

These sit in the project root. `public/` is the document root under Apache, so they are not
web-reachable in a correct configuration — but they are committed to git, reveal schema and
credential details, and become immediately reachable if the document root is ever misconfigured to
the project root (a common XAMPP misconfiguration).

---

## MEDIUM

### SEC-13 — No rate limiting on authentication

**Severity:** Medium · **Status:** `VERIFIED`

`Auth::routes()` provides `GET/POST /login` with no `throttle` middleware. `app/Http/Kernel.php`
registers the `throttle` alias and `RouteServiceProvider` defines an `api` limiter of 60/min, but
that limiter is only applied to the `api` middleware group. No login-attempt tracking, lockout, or
CAPTCHA exists. Credential stuffing against `dashboard_users` is unthrottled.

The only throttled paths are `routes/api.php` (60/min per IP) and
`config/auth.php` `passwords.users.throttle = 60` (60 **seconds** between reset emails).

---

### SEC-14 — Sessions survive a password change

**Severity:** Medium · **Status:** `VERIFIED`

`app/Http/Kernel.php:39` has `\Illuminate\Session\Middleware\AuthenticateSession::class`
commented out. Without it, resetting a `DashboardUser`'s password does not invalidate that user's
existing sessions on other browsers or devices. Combined with SEC-01, a password reset does not
evict an attacker who is already logged in.

`SESSION_LIFETIME=120` (2 hours) with `SESSION_DRIVER=file` and no configured `secure` cookie
flag.

---

### SEC-15 — CSRF meta tag absent from the demo layout

**Severity:** Medium · **Status:** `VERIFIED`

`VerifyCsrfToken::$except` is empty, so all POST routes require a token. The `@csrf` meta tag is
present in `layouts/master.blade.php` but **absent** from `layouts/master-layouts.blade.php` (the
Symox template demo layout). Any form or `fetch()`/AJAX call composed in that layout fails token
verification — or a developer removes the token to make it work, introducing CSRF. See
[19-frontend-architecture.md](19-frontend-architecture.md).

---

### SEC-16 — Overly broad CORS on the API surface

**Severity:** Medium · **Status:** `VERIFIED`

`config/cors.php`:

```php
'paths' => ['api/*', 'sanctum/csrf-cookie'],
'allowed_methods' => ['*'],
'allowed_origins' => ['*'],
'allowed_headers' => ['*'],
'supports_credentials' => false,
```

Mitigated by `supports_credentials => false` (cookies are not sent) and by Sanctum's
`EnsureFrontendRequestsAreStateful` being commented out. The practical effect today is that the
output of the unauthenticated `GET /api/test` (SEC-03) is readable from any origin, and any future
token-authenticated API route would be.

---

### SEC-17 — Duplicate route name resolves to the wrong handler

**Severity:** Medium · **Status:** `VERIFIED`

`routes/web.php:144-145`

```php
Route::post("getProductDetail",  [...])->name("products.getProductDetail");
Route::post("getproductfilter", [...])->name("products.getProductDetail");
```

Laravel's route-name lookup resolves to the last registration. `route('products.getProductDetail')`
in any Blade view reaches the **filter** endpoint. Confusing, and a latent data-exposure path if
the two handlers return different data shapes.

---

### SEC-18 — Silent route shadowing on shared dropdown endpoints

**Severity:** Medium · **Status:** `VERIFIED`

| Path | First (live) | Second (dead) |
| --- | --- | --- |
| `GET /get-warehouses` | `InventoryReportController::getWarehouses` (line 500) | `StockDistributionController::getWarehouses` (line 528) |
| `GET /get-branches` | `InventoryReportController::getBranches` (line 501) | `StockDistributionController::getBranches` (line 529) |

Laravel uses the first registration. The stock-distribution screen's dropdown contract is silently
served by the inventory controller. Any query-parameter difference is ignored, which can produce
incorrect warehouse selection and therefore incorrect stock operations.

---

### SEC-19 — Mass assignment of keys and timestamps

**Severity:** Medium · **Status:** `VERIFIED`

| Model | `$fillable` includes |
| --- | --- |
| `City` | `id`, `created_at`, `updated_at` |
| `District` | `id`, `created_at`, `updated_at` |
| `MilkOrder` | `id`, `created_at`, `updated_at` |
| `MilkSlot` | `id`, `created_at`, `updated_at` |
| `ProductOrder` | `created_at`, `updated_at` |
| `AppAlert` | `$guarded = []` — everything, incl. `user_id` |
| `AppNotification` | `$guarded = []` — everything |

Request input reaching `create()`/`fill()` can overwrite primary keys and timestamps.

---

### SEC-20 — Unsafe relationship definitions reachable from Blade

**Severity:** Medium · **Status:** `VERIFIED`

| Location | Defect |
| --- | --- |
| `ProductOrder::area()` | `$this->customer->area()` — no null guard. Missing user row ⇒ 500 on every view rendering it. |
| `ProductOrder::items()` | `hasMany(ProductOrderItem::class, 'order_id', 'id')` — joins a string `order_id` to an integer `id`; the relation never matches. |
| `ProductStock::Productvarient()` | `belongsTo(ProductVarient::class, 'id')` — must be `pro_ver_id`. Joins on the wrong column. |
| `ProductOrderUserAddress::country()` | `belongsTo(Country::class, 'area_id')` — joins `countries.id` to `area_id`. |
| `ProductStock::warehouse()` | `Warehouse::class` — the class is `WareHouse`. Works on Windows, breaks on Linux. |
| `AppAlert::getCreatedAtAttribute()` | Overrides `created_at` to return `diffForHumans()`. Breaks sorting, filtering, and JSON date handling. |

The first three are reachable from order views by data condition, not by user action.

---

### SEC-21 — `dd()` and debug output on request paths

**Severity:** Medium · **Status:** `VERIFIED` (partial)

`VERIFIED` — `dd()` in `TestController::test()`. Code comments such as
`// dd( $totalAmount);` (`ProductController.php:739`) indicate `dd()` was used during development
and may remain elsewhere. `UNVERIFIED` — a complete inventory of `dd()`/`dump()`/`var_dump()` call
sites reachable from a route was not produced. Recommend a repository-wide grep as a remediation
step.

---

### SEC-22 — Predictable resource paths and unbounded work

**Severity:** Medium · **Status:** `VERIFIED`

- `$tempPath = storage_path('app/temp_bulk_upload_' . time())` — predictable, attacker-observable
  name (SEC-09).
- `createMilkSubscription()` expands a `from_date`..`to_date` range into a per-day array and then a
  single `MilkSlot::insert()`. A multi-year range is unbounded work in one request. No
  `diffInDays` cap.
- `ProductManagementController::index()` calls `->get()` on all `productstocks` and all cancellable
  orders with no pagination.
- `HomeController::root()` runs 22 aggregate queries per dashboard load.

---

## LOW / INFORMATIONAL

| ID | Finding | Status |
| --- | --- | --- |
| SEC-23 | `public/robots.txt` and no security headers configured. `TrustHosts` middleware is commented out of the global stack. | `VERIFIED` |
| SEC-24 | Framework scaffolding left in tree: `storage/framework/sessions` and `views` populated; `.gitignore` does not exclude `storage/` contents. | `VERIFIED` presence, `UNVERIFIED` commit status |
| SEC-25 | Minimum-stability `dev` in `composer.json` — dev-branch packages can be installed into production. | `VERIFIED` |
| SEC-26 | `admin-resources` npm dependency pinned to `master` on a GitHub URL — supply-chain risk, unpinned commit. | `VERIFIED` |
| SEC-27 | No `.env.testing`; the application has no isolated test environment. | `VERIFIED` |
| SEC-28 | Mojibake in several controller files means they are not valid UTF-8. | `VERIFIED` |
| SEC-29 | `Cart::$fillable` contains `ip_addresscart`, a malformed key matching no column — dead token. | `VERIFIED` |

---

## Remediation order

1. **Immediately:** delete `routes/api.php:25` and `TestController` (SEC-03).
2. **Immediately:** rotate both Shiprocket accounts; remove inline credentials (SEC-04).
3. **Immediately:** set `APP_DEBUG=false` in every non-local environment (SEC-10).
4. **This sprint:** fix `DashboardUserController::update1()` to require a current password and an
   administrative role (SEC-01).
5. **This sprint:** register `role`/`permission` middleware aliases and gate every mutating route
   (SEC-02).
6. **This sprint:** stop returning `$e->getMessage()` to clients (SEC-11).
7. **This sprint:** add `throttle` to the login route (SEC-13); re-enable `AuthenticateSession`
   (SEC-14).
8. **Next sprint:** delete the avatar upload path entirely (SEC-05); add validation to all
   `StockController` mutations (SEC-07); make the order-finalisation helpers `protected`
   (SEC-08); validate ZIP entries (SEC-09).
9. **Ongoing:** validate `session('country_id')` against the user's assigned countries (SEC-06);
   remove root debug artifacts (SEC-12); fix the duplicate route name (SEC-17) and the shadowed
   dropdown routes (SEC-18); narrow `$fillable` (SEC-19); repair the broken relationships (SEC-20).