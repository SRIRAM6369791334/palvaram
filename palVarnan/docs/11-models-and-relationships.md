# 11 — Models and Relationships

**24 Eloquent models** in `app/Models/`. Every relationship, every `$fillable`, every defect — verified against model source and the `palvarnan_db (2).sql` DDL.

---

## 1. Model inventory

| Model | `$table` | Traits | `$fillable` | Relationships | Declared vs schema |
|---|---|---|---|---|---|
| `Product` | default → `products` | `HasFactory`, **`SoftDeletes`** | 24 | `country`, `category`, `brand`, `variants`, `productvari` | **2 columns don't exist** |
| `ProductVarient` | `product_varient` | — | 26 | `country`, `product`, `category` (**broken**), `unit` | ok |
| `ProductStock` | `productstocks` | `HasFactory` | 13 | `product` (`productid`), `variant` (`pro_ver_id`) | ok |
| `Category` | default → `categories` | `HasFactory` | 6 | `products`, `country`, `subCategories` (**broken**) | ok |
| `SubCategory` | default → `sub_categories` | — | 6 | `category` (**broken**) | ok |
| `Brand` | default → `brands` | `HasFactory` | 5 | `products`, `country` | ok |
| `Country` | `countries` | — | 2 | **none** | ok |
| `State` | `states` | `HasFactory` | 3 (`code` absent) | `districts` (name join) | 1 column doesn't exist |
| `District` | `districts` | — | 3 | none | ok |
| `Area` | default → `areas` | `HasFactory` | 2 | `areaAssigns` | ok |
| `AreaAssign` | default → `area_assigns` | — | 2 | `area` | ok |
| `Unit` | default → `units` | — | **0** | **none** | ok |
| `HomePromotion` | default → `home_promotions` | — | **0** | **none** | ok |
| `Coupon` | `coupons` | `HasFactory` | 7 | **none** | ok — never queried |
| `Cart` | default → `carts` | — | 5 | `product`, `variant` | `pre_order` stripped |
| `Wishlist` | `wishlists` | — | 5 | `product`, `variant` | **no `user()`** |
| `User` | default → `users` | `HasFactory`, `Notifiable` | 7 | **none** | ok |
| `UserAddress` | `user_addresses` | `HasFactory` | 17 | `user`, `area`, `country` (**broken**) | ok |
| `ProductOrder` | `product_orders` | `HasFactory` | **46** | `user`, `customer`, `items`, `slots`, `orderAddress`, `transactionLog`, `trackings` | **14 columns absent** |
| `ProductOrderItem` | `product_order_items` | `HasFactory` | 11 | `product`, `variant` | `product_image` absent |
| `ProductOrderUserAddress` | `product_order_user_addresses` | `HasFactory` | 14 | `productOrder`, `area` | ok |
| `ProductTracking` | `product_tracking` | `HasFactory` | 3 | **none** | ok |
| `ProductTransactionLog` | `product_transaction_logs` | `HasFactory` | 7 | **none** | ok |
| `ProductSlot` | `product_slots` | `HasFactory` | 19 | `productOrder`, `product`, `productVarient` | ok |

**Only `Product`, `Country`, `User`, and `UserAddress` declare `$hidden`.** No order or address model hides anything.

---

## 2. Relationship defects

### R-01 — `SubCategory::category()` points at a non-existent column — **VERIFIED IN LOG**

```php
return $this->belongsTo(Category::class, 'category_id');
```

`sub_categories` columns are: `country_id`, `subcategory_name`, `subcategory_image`, **`category_name`**, `category_display`, `status`, `is_gift`.

**There is no `category_id`.** Log: `Unknown column 'sub_categories.category_id' in 'where clause'`.

The real link is by **name** — `sub_categories.category_name`. No relationship declares it. `Category::subCategories()` has the same defect, since `hasMany(SubCategory::class)` also infers `category_id`.

### R-02 — `ProductVarient::category()` infers `category_id`

```php
return $this->belongsTo(Category::class);
```

The column is **`categoryid`**. Broken; silently `null`.

### R-03 — `UserAddress::country()` uses `area_id`

```php
return $this->belongsTo(Country::class, 'area_id');
```

`user_addresses.area_id` → `areas.id`, a different table. Resolves a `Country` row whose `id` coincidentally equals an area id. **Silently wrong data, no exception.**

### R-04 — `Product` declares two identical relationships

```php
public function variants()   { return $this->hasMany(ProductVarient::class); }
public function productvari() { return $this->hasMany(ProductVarient::class); }
```

Identical. Both spellings are used across the codebase.

### R-05 — `ProductOrder` declares two identical user relationships

```php
public function user()     { return $this->belongsTo(User::class, 'user_id', 'id'); }
public function customer() { return $this->belongsTo(User::class, 'user_id', 'id'); }
```

Both target `users.id` (`bigint`). Note `UserAddress::user()` targets `users.user_id` (`varchar`) — **the same logical key, two different columns.** See §6.

### R-06 — `State::districts()` joins on a string

```php
return $this->hasMany(District::class, 'state_name', 'name');
```

Matches `states.name` (varchar) to `districts.state_name`. No FK, no index guarantee. `getCitiesByState()` uses the same name join: `District::where('state_name', …)`.

### R-07 — Four spellings of the variant key

| Where | Spelling |
|---|---|
| `carts.product_varient_id` | `product_varient_id` |
| `wishlists.product_varient_id` | `product_varient_id` |
| `product_slots.product_varient_id` | `product_varient_id` |
| **`product_order_items.product_variant_id`** | **`product_variant_id`** (correct) |
| `productstocks.pro_ver_id` | `pro_ver_id` |
| `productstocks.productid` | `productid` |

The correct spelling appears on exactly one table, **inside the order transaction**, where the other three tables it writes alongside use the misspelling.

### R-08 — Order children bind on a non-PK string key

```php
// ProductOrder
return $this->hasMany(ProductSlot::class, 'order_id', 'order_id');
return $this->hasOne(ProductTransactionLog::class, 'order_id', 'order_id');
return $this->hasOne(ProductOrderUserAddress::class, 'order_id', 'order_id');
return $this->hasMany(ProductTracking::class, 'order_id', 'order_id');

// ProductOrder::items() — the exception
return $this->hasMany(ProductOrderItem::class, 'order_id');
```

`product_orders.id` is the `bigint` PK; `product_orders.order_id` is the `varchar` `'ORD-…'` mirror.

| Child | Local key column | Type | Value written by `placeOrder()` |
|---|---|---|---|
| `product_order_items` | `order_id` | `bigint` | `$order->id` |
| `product_slots` | `order_id` | `varchar` | `'ORD-…'` |
| `product_transaction_logs` | `order_id` | `varchar` | `'ORD-…'` |
| `product_tracking` | `order_id` | `varchar` | `'ORD-…'` |
| `product_order_user_addresses` | `order_id` | `varchar` | `'ORD-…'` |

**One order, two identifiers.** Neither is FK-enforced. The dashboard reads by the string form, which is why both exist.

### R-09 — `ProductOrderUserAddress` and `ProductTracking` have no parent relation

`ProductTracking` declares **no relationships at all**, yet is written with `user_id`, `order_id`, `delivery_status` on every order and read by nobody.

### R-10 — `Wishlist` has no `user()` relation

Every wishlist query filters on `user_id` by hand. A `user()` relation would let Laravel scope it, and a policy would prevent the omissions that caused BUG-12.

---

## 3. `$fillable` audit — what gets silently dropped

Laravel does not validate `$fillable` against the schema. A mismatch is **invisible**: no exception, no log, no effect.

### DEF-1 — `Product::$fillable` — 2 columns that do not exist

```php
protected $fillable = [ 'country_id', 'category_id', 'brand_id', 'product_name', 'slug',
  'product_quantity', 'offer_price', 'mrp_price', 'product_mrp_price', … ];
```

`offer_price` and `mrp_price` are **`product_varient`** columns, not `products` columns. Any attempt to save a product price writes nothing. Meanwhile `product_mrp_price` and `product_regular_price` are `bigint`, not `decimal` — **prices stored as integers**.

Six read sites reference `products.product_price`, which **does not exist at all**; the `??` chain falls through to `0`.

### DEF-2 — `ProductOrder::$fillable` — 14 columns absent from the dump

| Column | Status |
|---|---|
| `billing_state` | **absent — S1** |
| `shipping_state` | **absent — S1** |
| `courier_name` | **absent** |
| `discount` | **absent** |
| `shiprocket_order_id`, `shiprocket_shipment_id`, `shiprocket_status`, `awb_code` | added post-dump by the dashboard |
| `pickup_scheduled` | **never written by any code path** |
| `manifest_url`, `label_url`, `invoice_url`, `cancellation_reason` | added post-dump; the methods returning them are never called |

The model is being written against a schema that exists in **one production database and no repository**.

### DEF-3 — `ProductOrderItem::$fillable` — 1 column absent

`product_image`. The dump's `product_order_items` has `order_id, product_id, product_variant_id, product_name, variant_name, quantity, price, gst_rate, gst_amount, total` — no image. **S1.**

### DEF-4 — `Cart::$fillable` omits `pre_order`

```php
protected $fillable = ['ip_addresscart','user_id','product_id','product_varient_id','product_quantity'];
```

`CartController.php:89` passes `pre_order => 0`. **Silently dropped**; `carts.pre_order` stays `NULL`. `pre_note` is passed nowhere and the `carts` table has no such column.

### DEF-5 — `State::$fillable` includes `code`, which does not exist

`states` has `id, name, country_id, shipping_charge, express_shipping_charge, created_at, updated_at`. No `code`.

### DEF-6 — `created_at` / `updated_at` in `$fillable`

Present on `ProductOrder` **and `ProductSlot`**. Timestamps become client-settable — a forgeable audit trail on the two tables that matter most for reconciliation.

### DEF-7 — `Country::$fillable` omits what exists

Only `['name','currency']`, while `countries` has `iso_code`, `phone_code`, `ship_charge`. `iso_code` and `ship_charge` are unreachable by mass assignment.

---

## 4. Casting and type-safety gaps

### DEF-8 — `country_id` has two incompatible designs

`Category` and `Brand` cast to string so `FIND_IN_SET` works:

```php
protected $casts = ['country_id' => 'string'];
```

| Table | Type | Design |
|---|---|---|
| `products`, `product_varient`, `productstocks`, `categories`, `brands` | `varchar(255)` | CSV list |
| `sub_categories`, `users`, `product_orders`, `warehouse`, `warehouse_branch` | `bigint` + FK | single id |

**Same column name, two designs, 10 tables.** Root cause of the unindexable queries in [16-performance.md](16-performance.md).

### DEF-9 — No money casts

No model casts `offer_price`, `mrp_price`, `subtotal`, `gst_amount`, `shipping_charge`, `total_amount`, `price`, `total` to `decimal:2`. All arithmetic is PHP `float`:

```php
$subtotal += $price * $qty;
$total = ($subtotal + $gstAmount + $shipping) - $discount;
```

Rounding drift accumulates. Compounding it: `product_varient.offer_price` and `.mrp_price` are `int(11)`, and `products.product_mrp_price` / `.product_regular_price` are `bigint`.

### DEF-10 — No boolean casts

`is_gift`, `low_stock`, `hot_deals`, `Popular_products`, `pre_order`, `flash_sale`, `is_cancelled`, `preorder` are `tinyint(1)` or `int` and come back as raw integers. In Blade, `if ($p->is_gift)` treats `'0'` as **truthy** — every product with `is_gift = 0` evaluates as a gift.

Note `product_varient.low_stock` is `varchar(255)` while `productstocks.low_stocks` is also `varchar(255)` — string-typed thresholds compared numerically anywhere.

### DEF-11 — `User` casts `password` to `hashed`, but controllers also hash

```php
protected function casts(): array {
    return ['email_verified_at' => 'datetime', 'password' => 'hashed'];
}
```

`AuthController::registerStore()` and `ProfileController::update()` call `Hash::make()` themselves. **Laravel's `hashed` cast detects an already-hashed value and passes it through**, so this happens to be safe — but it depends on knowing the cast's internal check. Any caller that hashes with a different algorithm would double-hash.

### DEF-12 — `Product` uses `SoftDeletes`; nothing else does

Only `Product` soft-deletes. Consequences:

- Cart and wishlist rows survive their product ⇒ the log's `Attempt to read property "product_image" on null` and `"name" on null`
- `ProductVarient`, `ProductOrder`, `ProductStock` have **no** soft delete ⇒ deleting a product cascades destructively into order history
- `ProductOrder` has **no** soft delete ⇒ no cancellation state, only a `status` string

---

## 5. Timestamps

All 24 models inherit `$timestamps = true`. All 24 tables have `created_at` / `updated_at` in the dump — verified.

Two exceptions worth noting:

| Table | Behaviour |
|---|---|
| `product_varient` | `created_at` and `updated_at` both have `DEFAULT current_timestamp()`, and `created_at` additionally has **`ON UPDATE current_timestamp()`** — so the creation time silently changes on every update |
| `productstocks` | both `NOT NULL DEFAULT current_timestamp()` — fine |

**`ProductOrder` and `ProductSlot` also list them in `$fillable`** (DEF-6).

---

## 6. Accessors / mutators

**None exist.** No `getXAttribute`, no `Attribute` objects, no value objects, no casts beyond the three in §4.

All formatting is inline in Blade:

```blade
₹{{ number_format($item->variant->offer_price, 2) }}
{{ $order->total_amount }}
```

Consequences:

| Missing accessor | Result |
|---|---|
| price formatting | `₹` hard-coded in ~40 Blade locations; no `Intl.NumberFormat` |
| image URL | `MAIN_URL` / `/images/` logic duplicated in Blade and `SearchController.php:29` |
| status label | raw `status` strings compared inline against `'Order Placed'`, `'AWB Assigned'`, etc. |
| money → minor units | Razorpay conversion `round($total * 100)` done inline at `:202` |
| boolean | `'0'` treated as truthy (DEF-10) |

---

## 7. Model-level security gaps

| # | Gap | Consequence |
|---|---|---|
| M-01 | **No policies** — `app/Policies/` does not exist | Authorization is inline and inconsistent. `CheckoutController::updateAddress/destroyAddress` do it **correctly** with `abort_unless($address->user_id === Auth::id(), 403)`; `CartController::update/remove` and `WishlistController::remove` do not |
| M-02 | **No `$hidden` on 20 of 24 models** | `ProductOrder` serialises `billing_name`, `billing_email`, `billing_phone`, `billing_door_no`, `billing_street`, `razorpay_payment_id`. Any `->toArray()` or JSON response leaks customer PII |
| M-03 | **No global scope on `ProductOrder`** | `ProductOrder::all()` returns every customer's orders. `thankyou()` does scope by `user_id` (`:544`) — the only place that does |
| M-04 | **No `preventLazyLoading`** | N+1 queries are silent |
| M-05 | **No encryption cast** on address PII | Full customer address history in plaintext across `user_addresses` and `product_order_user_addresses` |
| M-06 | **`ProductTransactionLog` stores no gateway reference** | It records `order_amount`, `amount_credited`, `amount_debited` and **no `razorpay_payment_id`**. There is no way to reconcile captured payments against orders from the database — see §8 |

---

## 8. The reconciliation gap

The order transaction writes six tables. Only one of them records anything payment-related:

| Table | Records |
|---|---|
| `product_orders` | `payment_status`, `payment_method`, `razorpay_order_id`, `razorpay_payment_id` |
| `product_transaction_logs` | `order_amount`, `amount_credited`, `amount_debited` — **no payment id** |
| `product_order_user_addresses` | address |
| `product_tracking` | `delivery_status = 0` |
| `product_order_items` | line items |
| `product_slots` | line items (duplicate) |

`payment_status` is set optimistically at `:324` to `'Paid'` whenever `payment_method === 'Online Payment'` — **before the transaction opens and before the inserts that fail.** So:

> A rolled-back transaction leaves **no** local trace of the payment. The only evidence is `storage/logs/laravel.log` and Razorpay's dashboard.

This is why the money-loss detector in [22-database-integrity.md](22-database-integrity.md) §12 cannot be implemented as a SQL join today. It needs a webhook receiver (BW-21) or an independent payment table.

---

## 9. Recommendations, in priority order

| Priority | Action |
|---|---|
| **P0** | Add `billing_state`, `shipping_state`, `courier_name`, `discount` to a guarded migration (DEF-2) |
| **P0** | Add `product_image` to `product_order_items` via a guarded migration (DEF-3) |
| **P0** | Set `payment_status` **after** the commit succeeds, not before the insert |
| **P1** | Fix `SubCategory::category()`, `Category::subCategories()`, `ProductVarient::category()`, `UserAddress::country()` (R-01, R-02, R-03) |
| **P1** | Scope `CartController::update/remove` and `WishlistController::remove` to the owner |
| **P1** | Add `$hidden` to `ProductOrder`, `ProductOrderItem`, `ProductOrderUserAddress`, `ProductTransactionLog`, `ProductTracking` (M-02) |
| **P1** | Remove `created_at`/`updated_at` from `$fillable` on `ProductOrder` and `ProductSlot` (DEF-6) |
| **P1** | Add a `payments` table with a unique `razorpay_payment_id`, or a Razorpay webhook receiver (M-06) |
| **P2** | Remove `offer_price`/`mrp_price` from `Product::$fillable`; add `product_regular_price` (DEF-1) |
| **P2** | Add `pre_order` to `Cart::$fillable`, or remove it from the controller (DEF-4) |
| **P2** | Cast all money columns to `decimal:2`; change `offer_price`/`mrp_price` to `decimal(10,2)` (DEF-9) |
| **P2** | Add boolean casts for every `is_*` / `*_sale` / `low_stock` column (DEF-10) |
| **P2** | Fix `product_varient.created_at ON UPDATE current_timestamp()` |
| **P2** | Normalise `user_id` to `bigint` on all six tables that use `varchar` |
| **P3** | Collapse the duplicate relationships (R-04, R-05) |
| **P3** | Standardise the variant key name across 6 tables (R-07) |
| **P3** | Add price/image/status accessors to remove Blade duplication |

---

## 10. Related documents

- [07-database-schema.md](07-database-schema.md) — column-level truth
- [03-module-map.md](03-module-map.md) — module grouping
- [16-performance.md](16-performance.md) — N+1 and lazy-loading costs
- [13-security-audit.md](13-security-audit.md) — M-02/M-03 PII exposure
- [22-database-integrity.md](22-database-integrity.md) — corrective migrations
- [26-data-model-diagram.md](26-data-model-diagram.md) — ER diagrams