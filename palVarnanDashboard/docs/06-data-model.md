# 06 — Data Model

63 Eloquent models in `app/Models/`. This document covers structure, mass assignment, casts,
relationships, and the specific defects found.

## Authenticatable models

`VERIFIED` — three models implement `Illuminate\Contracts\Auth\Authenticatable`:

| Model | Table | Wired into `config/auth.php`? |
| --- | --- | --- |
| `DashboardUser` | `dashboard_users` | **Yes** — `guards.web.provider` |
| `User` | `users` | No |
| `DeliveryPerson` | `delivery_people` | No |

`VERIFIED` — `User` and `DeliveryPerson` are dead authentication code paths: nothing can log in as
them, but both carry `password` and `remember_token` fillable fields and both store an
`enc_password` column.

`VERIFIED` — `DashboardUser` uses `HasRoles` (Spatie) and sets `protected $guard_name = 'web'`.

## Model inventory by domain

**Catalogue** — `Product`, `ProductVarient`, `ProductChildImage`, `WebImage`, `Category`,
`SubCategory`, `Brand`, `Unit`, `GiftColor`, `GiftSize`, `TodayDeals`, `HomePromotion`,
`OfferImage`, `BannerImage`, `Coupon`, `Newcoupon`, `ScrollingBar`.

**Inventory** — `ProductStock`, `ProductStockBatch`, `StockDistribution`, `StockTransfer`,
`WareHouse`, `WareHouseBranch`, `SupplierDetail`.

**Orders** — `ProductOrder`, `ProductOrderItem`, `ProductOrderUserAddress`, `ProductSlot`,
`ProductRefund`, `ProductTracking`, `ProductTransactionLog`, `CancelRequest` *(table only, no
model file)*, `BulkOrder`, `Cart`, `PurchaseOrder`, `PurchaseOrderItem`, `GiftPurchaseOrder`,
`GiftPurchaseOrderItem`.

**Milk / subscription** — `MilkOrder`, `MilkSlot`, `MilkRefund`, `MilkOrderUserAddress`,
`MilkTransactionLog`, `PlanType`.

**Customers & logistics** — `User`, `UserAddress`, `DeliveryPerson`, `Area`, `AreaAssign`, `City`,
`State`, `District`, `Country`, `AddressType`, `GenderType`, `AllIndiaPincode`, `ContactDetail`,
`Otp`, `MailOtp`.

**Content / misc** — `Notification`, `AppNotification`, `AppAlert`, `UserReadNotification`,
`FreeShippingSetting`, `DeliveryChargeDetail`, `Review`, `Wishlist`, `Testimonial`.

## Casts — `BROKEN`

`VERIFIED` — only **5 of 63 models** define a `$casts` property:

| Model | Casts |
| --- | --- |
| `DashboardUser` | `email_verified_at => datetime`, `country_id => array` |
| `User` | see `app/Models/User.php:43` |
| `DeliveryPerson` | see `app/Models/DeliveryPerson.php:39` |
| `Category` | `country_id => string` |
| `Brand` | `country_id => string` |

**There are no casts anywhere for money, quantities, dates, or booleans.** Consequences:

- Money columns (`offer_price`, `mrp_price`, `total_amount`, `subtotal`, `gst_amount`,
  `shipping_charge`, `transfer_amount`) come back from MySQL as strings. Any arithmetic on them
  is string arithmetic; `round()`, `number_format()`, and comparison operators all behave
  differently than a developer would expect.
- Date columns (`manufacturing_date`, `expiry_date`, `start_date`, `end_date`, `flash_sale_date`,
  `last_stockupdate_date`) are strings, forcing manual `Carbon::parse()` at every use site.
- Boolean-ish columns (`is_gift`, `is_cancelled`, `hot_deals`, `Popular_products`, `pre_order`,
  `flash_sale`, `approval`) are returned as `0`/`1` integers, producing `if ($model->is_gift)` and
  `{{ $model->is_gift }}` bugs throughout the views.

See [07-data-integrity.md](07-data-integrity.md) for the concrete impact.

## Mass assignment

**`$guarded = []` (fully open) — `VERIFIED`, 2 models**

| Model | Risk |
| --- | --- |
| `AppAlert` | Every column is mass assignable, including `user_id`. |
| `AppNotification` | Every column is mass assignable. |

**Primary key and timestamps in `$fillable` — `VERIFIED`**

| Model | Fields exposed |
| --- | --- |
| `City` | `id`, `created_at`, `updated_at` |
| `District` | `id`, `created_at`, `updated_at` |
| `MilkOrder` | `id`, `created_at`, `updated_at` |
| `MilkSlot` | `id`, `created_at`, `updated_at` |
| `ProductOrder` | `created_at`, `updated_at` |

Any `create()`/`fill()` call on these models can overwrite the primary key and timestamps with
request input.

**Duplicate column in `$fillable` — `VERIFIED`**
`ProductVarient::$fillable` lists `product_gst` twice (`app/Models/ProductVarient.php:13-14`).
Harmless at runtime but indicates unreviewed edits.

**Naming drift in `$fillable` — `VERIFIED`**
`Cart::$fillable` contains the concatenated token `ip_addresscart`
(`app/Models/Cart.php:13`), which matches no column. The real `ip_address` field is unreachable
through mass assignment.

## Relationships

Relationship coverage is uneven: 32 of 63 models have no relationship methods at all, and the
models that carry the most relationships (`ProductOrder` with 13) also carry the most defects.

### Defective relationships — `BROKEN`

| Location | Defect |
| --- | --- |
| `ProductStock::Productvarient()` | `belongsTo(ProductVarient::class, 'id')` — the foreign key must be `pro_ver_id`. This relation joins stock to the variant **on the wrong column** (`app/Models/ProductStock.php:63`). |
| `ProductStock::warehouse()` | `belongsTo(Warehouse::class, ...)` — the model class is `WareHouse`, not `Warehouse` (`app/Models/ProductStock.php:38`). Resolves on Windows (case-insensitive filesystem); **breaks on Linux**. |
| `ProductOrderUserAddress::country()` | `belongsTo(Country::class, 'area_id')` — joins `countries.id` to `area_id` (`app/Models/ProductOrderUserAddress.php:28`). |
| `ProductOrder::product()` | `belongsTo(Product::class, "product_id")` but `product_id` is **not** in `ProductOrder::$fillable` and is not part of the order header; the product lives on `ProductSlot`. |
| `ProductOrder::area()` | `return $this->customer->area();` — dereferences `customer` with no null guard. Any order whose user row is missing produces a 500 on every `->area` access (`app/Models/ProductOrder.php`). |
| `ProductOrder::items()` | `hasMany(ProductOrderItem::class, 'order_id', 'id')` — `ProductOrderItem` extends `id` (an integer), while `order_id` is a string like `HG-ORD-000123`. This relation cannot match. |

### Accessor that shadows a real column — `VERIFIED`

`ProductOrder` declares
`protected $appends = ['order_id_display', 'delivery_status', 'is_cancelled', 'date_ordered_on', 'delivery_charge', 'grand_total_amount']`.
`is_cancelled` is also a genuine column on `product_slots` and `milk_slots`; on `ProductOrder` it is
purely derived from `status === 'cancelled'`. Code that writes `is_cancelled` on a `ProductOrder`
and code that reads it may disagree.

`getDeliveryStatusAttribute()` maps status strings to integers via an inline array — a status
literal in a model accessor, not a shared enum.

### Overridden `created_at` — `VERIFIED`

`AppAlert::getCreatedAtAttribute()` returns `Carbon::parse($value)->diffForHumans()` — a
**relative-time string**, not a date. Any code that sorts, filters, or serialises `AppAlert`
timestamps gets prose. This breaks `->orderBy('created_at')` semantics and JSON date handling.

## Query scopes

`VERIFIED` — only **one** model defines scopes. `ProductStock` has three
(`app/Models/ProductStock.php:47-64`):

- `scopeWithFilters($query, array $filters)` — country/warehouse/branch/category filtering.
- `scopeLowStock($query)` — `availablestock <= low_stocks`.
- `scopeDeadStock($query)` — `salestock = 0 AND overallstock > 0 AND last_stockupdate_date <= now-30d`.

`INCOMPLETE` — `scopeLowStock` and `scopeDeadStock` appear unused. The controller implements
low-stock detection with an inline `where('productstocks.availablestock', '<', DB::raw('product_varient.low_stock'))`
cross-table comparison instead (`app/Http/Controllers/StockController.php::lowstock()`), so the
scopes and the live query can disagree about what "low stock" means.

## Naming conventions — `INCOMPLETE`

`VERIFIED` — inconsistent naming is baked into class names, table names, and file names:

- Misspelled classes: `ProductVarient`, `ProductVarientControllet`, `ProductThum`,
  `OrderSummery`, `ProductSpecfication` (column).
- Inconsistent capitalisation of the same concept: `WareHouse` / `Warehouse`, `WareHouseBranch`,
  `Country` / `countries`.
- Plural/singular inconsistency: `Category` / `categories`, `SubCategory` / `sub_categories`,
  `Country` / `Countries`, `BannerImage` / `banner_images`.
- Non-standard FK names: `productstocks.productid` (not `product_id`),
  `product_varient.categoryid` / `subcategoryid`.

These are load-bearing — renaming any of them requires a coordinated change across models,
migrations, raw joins, and Blade views.

## Soft deletes — `INCOMPLETE`

`VERIFIED` — only `Product` uses `SoftDeletes`, matching migration
`2026_02_27_172014_add_deleted_at_to_products_table`. No other table has a `deleted_at` column.
Note that `sub_categories` (plural) is the model's table while the migration that added `is_gift`
targets `sub_categories` — consistent — but the model file is `SubCategory.php`. Confirm the
`deleted_at` column actually exists on `products` before relying on soft deletes.

## Multi-country representation — `VERIFIED`

`country_id` is stored inconsistently:

| Storage | Where |
| --- | --- |
| Scalar integer | `products`, `product_varient`, `categories`, `sub_categories`, `brands`, `units`, `supplier_details`, `home_promotions`, `productstocks` |
| JSON array | `dashboard_users` |

`VERIFIED` — `Category` and `Brand` cast `country_id` to `string`, which would break the JSON-array
semantics if those tables ever move to multi-country. `DashboardUser` casts to `array` and
`DashboardUserController` decodes it manually with a fallback (`app/Http/Controllers/DashboardUserController.php:14-32`),
treating both representations as possible — defensive code that should not be necessary.

## No accessors or mutators for money — `VERIFIED`

`VERIFIED` — zero `Attribute` mutators exist for price fields. There is no single place where a
price is normalised on write, which is how `product_mrp_price`, `product_regular_price`,
`offer_price`, `mrp_price`, and `offer_value` ended up as five overlapping price concepts across
`products`, `product_varient`, `today_deals`, and `product_stock_batches`.

## Policies — none exist

`VERIFIED` — `app/Policies/` is empty and `AuthServiceProvider::$policies` is an empty array with
only the commented scaffold example. There is no model-level authorisation anywhere. See
[11-authorization-rbac.md](11-authorization-rbac.md).