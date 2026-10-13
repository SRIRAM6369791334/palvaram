# 11 — Authorization (RBAC)

## Summary

`BROKEN` — The application has a complete role/permission **data model and UI**, but **zero
server-side enforcement**. Permissions hide menu items. They do not protect routes, controllers,
or queries.

## What exists

`VERIFIED`

| Component | Location |
| --- | --- |
| Permission tables | migration `2026_04_06_124404_create_permission_tables` (Spatie default schema) |
| Trait on user | `DashboardUser` uses `HasRoles` |
| Guard name | `DashboardUser::$guard_name = 'web'` |
| Role CRUD | `RoleController` — `index`, `store`, `update`, `destroy`, `permissions`, `updatePermissions` |
| User↔role assignment | `DashboardUserController::userss()` → `assignRole()`; `update()` → `syncRoles()` |
| Roles UI | `resources/views/pages/roles.blade.php`, `permissions.blade.php`, `users_single.blade.php` |

`VERIFIED` — permission names follow a `Module.action` convention with a literal space in the
module segment, e.g. `Banner Images.index`, `Subcategories.index`, `Country.selection`,
`Order Wise Report.index`, `Warehouse Branch.index`.

## What does not exist

`VERIFIED` — no server-side enforcement of any kind:

| Mechanism | Status |
| --- | --- |
| `can:` middleware on routes | **0 uses.** `app/Http/Kernel.php` registers the alias; `routes/web.php` never applies it. |
| `role:` / `permission:` middleware | Middleware aliases are **not even registered** in `$routeMiddleware`. |
| `@can` Blade directive in controllers | N/A. |
| `Gate::define` / `Gate::before` | **None.** `AuthServiceProvider::$policies` is an empty array with only the commented scaffold line. |
| Model policies | `app/Policies/` is **empty**. |
| `$this->authorize()` calls | **0 occurrences.** |
| `can` checks inside controllers | **0 occurrences.** |
| Route model binding authorisation | `SubstituteBindings` is registered but no policy is attached, so binding performs no authorisation. |

**Every one of the ~400 registered web routes is reachable by any authenticated `DashboardUser`,
regardless of role or permission.**

## The UI-only enforcement — `VERIFIED`

`VERIFIED` — 54 `@can(...)` directives exist, all in Blade views, all purely cosmetic:

| View | Guards |
| --- | --- |
| `layouts/sidebar.blade.php` | `Banner Images.index`, `Brands.index`, `Products.index`, `Subcategories.index` (commented out at line 106) |
| `layouts/topbar.blade.php` | `Country.selection` |
| `pages/products_single.blade.php` | `Categories.index`, `Subcategories.index`, `Brands.index`, `Products.index`, `Product Varient.index` |
| `pages/orders_single.blade.php` | `All Orders.index`, `New Orders.index`, `Package Orders.index`, `Dispatched Orders.index`, `Out For Delivery Orders.index`, `Delivered Orders.index`, `Purchase Orders.index`, `Other Orders.index`, `Supplier Details.index` |
| `pages/reports_single.blade.php` | `Product Wise Report.index`, `Order Wise Report.index`, `Inventory Report.index`, `Purchase Report.index` |
| `pages/stock_single.blade.php` | `Stocks.index`, `Stock Distribution.index`, `Stock Transfer.index`, `Stock Tracking.index`, `Stock Management.index` |
| `pages/users_single.blade.php` | `Customers.index`, `Users.index`, `Roles.index` |
| `pages/locations.blade.php` | `Countries.index`, `Warehouse.index`, `Warehouse Branch.index` |
| `pages/giftproducts_single.blade.php` | `Gift Categories.index`, `Gift Subcategories.index`, `Gift Products.index` |
| `pages/categories.blade.php`, `subcategory.blade.php`, `brands.blade.php`, `units.blade.php`, `supplier_details.blade.php`, `home_promotions.blade.php`, `warehouse.blade.php` | `Country.selection` |

`VERIFIED` — of the 75 admin screens in `resources/views/pages/`, only these ~10 use `@can` at
all. The remaining screens (stock detail, refunds, returns, milk orders, milk slots, coupons,
new coupons, offers, banners, scrolling bar, notifications, delivery persons, areas, purchase
orders, top customers, bulk orders, cancel requests, gift purchase orders, gift stock screens) have
**no permission check in the view or the controller**.

## Impact — concrete escalation paths

`VERIFIED` — because enforcement is absent, an account with the fewest permissions can perform every
administrative action:

| Action | Route | Guard applied |
| --- | --- | --- |
| Create / edit / delete **roles** | `POST /roles`, `POST /roles/{id}`, `POST /roles/{id}` (destroy) | `auth` only |
| Assign **any permission to any role** | `RoleController::updatePermissions()` → `syncPermissions($request->permissions)` | `auth` only |
| Change any user's **role** | `DashboardUserController::update()` → `syncRoles([$request->role])` | `auth` only |
| Create / edit / delete **dashboard users** | `userss()`, `update()`, `destroy()` | `auth` only |
| Reset any user's **password** | `DashboardUserController::update1()` | `auth` only |
| Adjust **stock levels** | `StockController::update()`, `reduceStock()`, `update1()` | `auth` only |
| Accept **stock transfers** | `StockController::acceptTransfer($id)` | `auth` only |
| Edit **pricing** | `ProductController::update()` | `auth` only |
| **Create orders** on behalf of customers | `POST /createMilkSubscription`, `POST /createProductSubscription` | `auth` only |
| Read **all customer PII** | `UserController`, `users_single`, order detail screens | `auth` only |

The most severe chain: an account with **no permissions at all** can call
`RoleController::updatePermissions()` to grant itself every permission, or
`DashboardUserController::update1()` to reset a super-admin's password outright. RBAC is not a
security boundary in this application.

## Role/permission management is itself unguarded — `BROKEN`

`VERIFIED` — `RoleController`:

```php
public function updatePermissions(Request $request, Role $role)
{
    $role->syncPermissions($request->permissions ?? []);
    return redirect()->route('roles.index')->with('success', 'Permissions updated');
}
```

- No `$this->authorize()`, no `can` middleware.
- `$request->permissions` is **not validated** — it is passed straight to `syncPermissions()`.
  Mass-assignment of the full permission set is unguarded.
- **No protection against privilege escalation on the caller's own role.** There is no check that
  the caller holds the permissions being granted, nor that they are not granting themselves
  permissions they lack.

`VERIFIED` — `RoleController::destroy($id)` deletes any role by id with no check that it is not in
use, and no cascade handling. Deleting a role in use by `model_has_roles` orphans the assignment
depending on the Spatie foreign-key constraint.

`VERIFIED` — `store()` validates `name` as `required|min:3|unique:roles,name` but **not**
`guard_name`, which is hardcoded to `'web'` — correct, but worth noting as the one place guard
consistency is maintained.

## Country scoping is the only partial control — `INCOMPLETE`

`VERIFIED` — a second, informal authorisation mechanism exists: `country_id` scoping.

```php
// app/Http/Controllers/DashboardUserController.php:14-32
private function getAllowedCountryIds()
{
    $user = auth()->user();
    $countryIds = is_array($user->country_id) ? $user->country_id : json_decode($user->country_id, true) ?? [];
    $countryIds = array_map('intval', $countryIds);
    $sessionCountryId = session('country_id');

    if (!$sessionCountryId) { return $countryIds; }
    if (in_array((int)$sessionCountryId, $countryIds)) { return [(int)$sessionCountryId]; }
    return $countryIds;
}
```

This helper is `private` and called **only from `DashboardUserController::index()`**. It scopes
which dashboard users are *listed*. It is not used by `userss()`, `update()`, `destroy()`, or
`update1()` — so a user can create, modify, or delete a `DashboardUser` in any country by supplying
a different id.

`VERIFIED` — elsewhere, country scoping is a *display filter* built from `session('country_id')`,
e.g. `StockController::index()` lines 33-37. The session value is set by whatever screen set it;
no middleware validates it against the user's permitted countries. A user can therefore set
`session('country_id')` to an arbitrary value via the country dropdown and read another country's
stock and catalogue data.

`BROKEN` — `session('country_id')` is trusted with no verification. There is no check anywhere that
the session country is in `DashboardUser::country_id`.

## Recommendations

1. Register `role` and `permission` aliases in `app/Http/Kernel.php::$routeMiddleware`
   (Spatie ships these; they are just not registered here).
2. Apply `->middleware('can:...')` or Spatie's `role:`/`permission:` to every mutating route,
   starting with roles, permissions, users, stock, and pricing.
3. Add `Gate::before` super-admin bypass in `AuthServiceProvider`, plus `Gate::define` for
   role-management operations, so a super-admin is not locked out.
4. Create policies for `DashboardUser`, `Role`, `ProductStock`, `Product`, and `ProductOrder`
   and register them in `AuthServiceProvider::$policies`.
5. Add `$this->authorize()` calls inside controllers for object-level checks — a role that may edit
   products must not be able to edit products in a country it is not assigned to.
6. Validate `permissions` in `RoleController::updatePermissions()` and forbid self-escalation.
7. Validate `session('country_id')` against the authenticated user's permitted countries in
   middleware, so the display filter cannot be widened from the client.
8. Remove the registration route: `Auth::routes(['register' => false])`.

See [26-refactoring-roadmap.md](26-refactoring-roadmap.md) for sequencing.