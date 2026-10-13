# 10 — Authentication

## Guards and providers — `VERIFIED`

`config/auth.php`:

```php
'defaults' => ['guard' => 'web', 'passwords' => 'users'],

'guards' => [
    'web' => ['driver' => 'session', 'provider' => 'users'],
],

'providers' => [
    'users' => ['driver' => 'eloquent', 'model' => App\Models\DashboardUser::class],
],
```

| Setting | Value |
| --- | --- |
| Guard | `web` (session driver) |
| Provider | `users` → `App\Models\DashboardUser` |
| Default password broker | `users` |
| Password reset table | `password_resets` |
| Reset token expiry | 60 minutes |
| Reset throttle | 60 seconds |
| `password_timeout` | 10800s (3 hours) |

`VERIFIED` — three models implement `Authenticatable` (`User`, `DashboardUser`, `DeliveryPerson`)
but only `DashboardUser` is registered. `User` and `DeliveryPerson` are unreachable as login
identities.

`INCOMPLETE` — the `users` **provider key** is `DashboardUser`'s table (`dashboard_users`), while
`passwords.users.table` points at `password_resets`. That table exists in the SQL dump but has **no
create migration** in this repository. Password reset therefore depends on a table the application
cannot provision. See [05-database-schema.md](05-database-schema.md).

`VERIFIED` — `guards` defines only `web`. There is **no `sanctum` guard**, yet `routes/api.php`
declares `auth:sanctum` on `GET /api/user`. With Sanctum 2.x the guard is auto-registered at
runtime, so this works, but it is not represented in `config/auth.php`.

## Login flow

`VERIFIED` — `Auth::routes()` at `routes/web.php:89` generates:

| Method | URI | Action |
| --- | --- | --- |
| GET | `/login` | `Auth\LoginController@showLoginForm` |
| POST | `/login` | `Auth\LoginController@login` |
| POST | `/logout` | `Auth\LogoutController@logout` |
| GET | `/register` | `Auth\RegisterController@showRegistrationForm` |
| POST | `/register` | `Auth\RegisterController@register` |

`RouteServiceProvider::HOME = '/'`, so successful login redirects to the KPI dashboard.

`INCOMPLETE` — `app/Http/Controllers/Auth/` contains no application controllers; the scaffolded
`laravel/ui` controllers are used unmodified. Consequences:

- No rate limiting or lockout on failed logins beyond what `laravel/ui` provides by default
  (none). `throttle` is defined in the kernel but applied to no auth route.
- No CAPTCHA, no MFA, no forced password change.
- `password_timeout` of 3 hours is configured but no route in `routes/web.php` uses the
  `password.confirm` middleware.

`VERIFIED` — `public/index.php` and the `web` route group are unmodified; a manual logout route is
commented out (`routes/web.php:90`).

## Registration — `BROKEN` (exposed surface)

`VERIFIED` — `Auth::routes()` registers a public `GET/POST /register` backed by
`laravel/ui`'s `RegisterController`, which creates a **`User`** row in the `users` table.

`DashboardUser` is the guard's model, so a newly registered `User` **cannot log in** to this
dashboard. The registration endpoint is therefore a public write into a customer table that serves
no dashboard purpose.

`UNVERIFIED` — whether `RegisterController` is overridden or restricted by middleware discovered at
runtime. No override file exists in `app/Http/Controllers/Auth/`, so this is likely live.

**Recommendation:** remove `/register` from `Auth::routes()` via `->except(['register'])`.

## Password reset — `BROKEN`

`VERIFIED` — `DashboardUserController::update1(Request $request, $id)`:

```php
public function update1(Request $request, $id)
{
    $user = DashboardUser::findOrFail($id);

    $validated = $request->validate([
        'password' => 'required',
    ]);

    $user->update([
        'password' => Hash::make($validated['password']),
    ]);

    $users = DashboardUser::with(['roles'])->get();

    return response()->json([
        'message' => 'User Password Successfully Updated',
        'users' => $users,
    ]);
}
```

This is routed (`routes/web.php`, in the users block) and:

- Accepts **any** `$id` — there is no check that the target is the authenticated user, that the
  caller is an administrator, or even that `$id` belongs to the caller's country scope.
- Has **no password policy**: `'required'` only — no `min:8`, no `confirmed`, no strength check.
- Requires **no current password**.
- Returns **all** `DashboardUser` records with their roles, leaking the employee directory and role
  assignments to any authenticated user.

**Any authenticated dashboard account — including the lowest-privilege one — can reset any other
account's password, including a super-admin's, and log in as them.** This is a complete
authentication-bypass escalation path. Severity: critical. See
[13-security-findings.md](13-security-findings.md).

The method name `update1` also collides with `StockController::update1()`, an unrelated endpoint.

## Session handling

`VERIFIED` — `app/Http/Kernel.php`:

- `AuthenticateSession` middleware is **commented out** (line 39).
- `StartSession`, `ShareErrorsFromSession`, and `VerifyCsrfToken` are active in the `web` group.

Consequence of the disabled `AuthenticateSession`: changing a user's password does **not**
invalidate that user's existing sessions elsewhere. A hijacked session survives a password reset.

`VERIFIED` — `.env`: `SESSION_DRIVER=file`, `SESSION_LIFETIME=120` (minutes), `CACHE_DRIVER=file`.

| Risk | Detail |
| --- | --- |
| 2-hour session lifetime | Long for an admin panel holding stock and pricing data. |
| File sessions | Sessions live under `storage/framework/sessions`. `storage/` is **not** in `.gitignore` beyond `/storage/*.key`, so session payloads may be committed. `UNVERIFIED` — requires checking git history. |
| No secure cookie config found | `config/session.php` unmodified from scaffold; `secure`/`http_only`/`same_site` take framework defaults. `UNVERIFIED` — depends on `SESSION_SECURE_COOKIE`, absent from `.env`. |

## CSRF

`VERIFIED` — `app/Http/Middleware/VerifyCsrfToken::$except` is **empty**. All POST routes require a
CSRF token.

`BROKEN` — the CSRF meta tag is present in `layouts/master.blade.php` but **absent** from
`layouts/master-layouts.blade.php` (the template demo layout). Any form or AJAX call rendered inside
that layout will fail token verification, or a developer may add a `@csrf`-free partial to work
around it. See [19-frontend-architecture.md](19-frontend-architecture.md).

## Token storage — `INCOMPLETE`

`VERIFIED` — `DashboardUser` and `User` both store an `enc_password` column (present in
`$fillable`) alongside Laravel's `password`. `DashboardUser::$hidden` lists both, so they are not
serialised.

`UNVERIFIED` — what writes `enc_password`, and with what algorithm. No controller in this codebase
sets it; it is likely written by the companion `palVarnan/` application. If it is populated with a
reversible cipher, it is a stored-credential risk.

## Remember-me

`VERIFIED` — no route posts to Laravel's `remember` field; no `remember_token` handling is
implemented beyond the model column. Login is session-only.

## Multi-factor and lockout — absent

`VERIFIED` — no `two-factor`, `laravel-two-factor-auth`, or equivalent package in
`composer.json`. No failed-login tracking table. No account lockout.

## Middleware stack summary

`VERIFIED` — global middleware:

```
TrustProxies
Fruitcake\Cors\HandleCors          <-- CORS handled globally, see config/cors.php
PreventRequestsDuringMaintenance
ValidatePostSize
TrimStrings
ConvertEmptyStringsToNull
```

`TrustHosts` exists in `app/Http/Middleware/` but is commented out of the stack
(`app/Http/Kernel.php:19`) — the Laravel scaffold default; not a defect in a single-tenant admin app.

`web` group adds `EncryptCookies`, `AddQueuedCookiesToResponse`, `StartSession`,
`ShareErrorsFromSession`, `VerifyCsrfToken`, `SubstituteBindings`, `Localization`.

`api` group: `throttle:api`, `SubstituteBindings`. `EnsureFrontendRequestsAreStateful` is
commented out (line 47), so Sanctum SPA cookie auth is not available.