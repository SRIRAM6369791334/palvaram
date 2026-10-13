# 22 — Database Integrity

Evidence: `palvarnan_db (2).sql` (93 tables, 34 FKs, 19 unique indexes — all enumerated directly), `database/migrations/` (3 files), `storage/logs/laravel.log`, and every model in `app/Models/`.

---

## 1. Headline

| Measure | Value |
|---|---|
| Tables in dump | **93** |
| Foreign keys declared | **34** |
| Unique indexes declared | **19** |
| CHECK constraints | **0** |
| Domain migrations in this app | **0** |
| Migration files in `database/migrations/` | **3** (Laravel defaults only) |
| Domain migrations owned by | `palVarnanDashboard` (~57 files) |
| Columns code writes that do not exist | **4** |
| `$fillable` columns with no matching column | **14** |
| Columns code reads that do not exist | **6** (4 variant dimensions + 2 price fields) |
| Broken relationship FKs | **3** |
| Tables referenced in code that do not exist | **~25** |
| Tables with zero storefront usage | **~55** |

**The schema is not reproducible.** `php artisan migrate:fresh` yields 9 Laravel tables and no commerce schema at all.

---

## 2. What the storefront actually owns

```
palVarnan/database/migrations/
  0001_01_01_000000_create_users_table.php      → users, password_reset_tokens, sessions
  0001_01_01_000001_create_cache_table.php       → cache, cache_locks
  0001_01_01_000002_create_jobs_table.php        → jobs, job_batches, failed_jobs
```

Three files. Zero domain tables. Everything else — 84 tables — was created by migrations that live in a **different repository**.

---

## 3. Missing columns that code writes (the S1s)

`CheckoutController::placeOrder()` writes four columns that exist in no migration:

| # | Column | Table | Written at | Log evidence |
|---|---|---|---|---|
| 1 | `billing_state` | `product_orders` | `:306` | `Unknown column 'billing_state' in 'field list'` |
| 2 | `shipping_state` | `product_orders` | `:315` | would be next |
| 3 | `courier_name` | `product_orders` | `:321` | would be next |
| 4 | `product_image` | `product_order_items` | `:378` | `Unknown column 'product_image' in 'field list'` |

Verified against the dump DDL — `product_orders` runs `… billing_pincode, shipping_name … shipping_pincode, subtotal, gst_amount, shipping_charge, total_amount …` with no state columns and no courier name. `product_order_items` is `order_id, product_id, product_variant_id, product_name, variant_name, quantity, price, gst_rate, gst_amount, total` — no image.

**Because these writes are inside `DB::beginTransaction()`, the rollback discards the order, its items, addresses, transaction log, tracking row, slots, and the stock decrement — but Razorpay has already captured the payment.** The customer is charged for nothing.

### Corrective migrations

```php
// 2026_09_01_000001_add_shipping_state_to_product_orders_table.php
Schema::table('product_orders', function (Blueprint $table) {
    $table->string('billing_state')->nullable()->after('billing_pincode');
    $table->string('shipping_state')->nullable()->after('shipping_pincode');
    $table->string('courier_name')->nullable()->after('payment_method');
    $table->decimal('discount', 10, 2)->nullable()->after('shipping_charge');
    $table->boolean('pickup_scheduled')->default(false)->after('shiprocket_status');
    $table->string('awb_code')->nullable()->after('shiprocket_shipment_id');
});

// 2026_09_01_000002_add_product_image_to_product_order_items_table.php
Schema::table('product_order_items', function (Blueprint $table) {
    $table->string('product_image')->nullable()->after('variant_name');
});
```

**Guard every column with `Schema::hasColumn()`.** The log shows a later successful Shiprocket order, so these columns were added by hand to one database. A migration without a guard fails there.

```php
if (! Schema::hasColumn('product_orders', 'billing_state')) {
    $table->string('billing_state')->nullable()->after('billing_pincode');
}
```

---

## 4. Columns code reads that do not exist

### 4.1 `products.product_price`

`products` has `product_mrp_price` and `product_regular_price`. No `product_price`.

Six read sites, all guarded by `??` so they fail **silently**:

| Site | Expression | Guard |
|---|---|---|
| `AppServiceProvider.php:41-43` | `($c->variant->offer_price ?? 0) * …` | yes |
| `CartController.php:27` | `$item->variant->offer_price * …` | **no** |
| `CartController.php:142` | `($item->variant->offer_price ?? 0) * …` | yes |
| `CheckoutController.php:60` | `offer_price ?? product->product_price ?? 0` | yes |
| `CheckoutController.php:184` | same | yes |
| `CheckoutController.php:279` | same | yes |
| `CheckoutController.php:367` | same | yes |
| `CheckoutController.php:447` | same | yes |

The chain only reaches the missing column when `offer_price` is itself null — then it falls to `0`. **A free item, silently.**

### 4.2 Variant dimensions — the shipping defect

```php
// CheckoutController.php:453-463
$itemWeight = (float) ($item->variant->weight ?? 0.5);
$maxLength  = max($maxLength,  (float) ($item->variant->length  ?? 10));
$maxBreadth = max($maxBreadth, (float) ($item->variant->breadth ?? 10));
$maxHeight  = max($maxHeight,  (float) ($item->variant->height  ?? 10));
$totalWeight = max($totalWeight, 0.5);
```

**`product_varient` has no `weight`, `length`, `breadth`, or `height` column.** Verified against the DDL — it has `sku`, `barcode`, `varient`, `value`, `offer_price`, `mrp_price`, `product_qty`, `product_gst`, `unit_id`, `size_id`, `color_id`, and no dimensional fields.

Consequences:

| Field sent to Shiprocket | Actual value |
|---|---|
| `weight` | `0.5 × total_quantity`, floored at `0.5` — **always fictional** |
| `length`, `breadth`, `height` | `10`, `10`, `10` — **hard-coded** |
| billable weight | `max(weight, length×breadth×height/5000)` = **12 kg** for every order |

**Shiprocket computes shipping on a 12 kg volumetric parcel regardless of contents.** Bulky goods ship at parcel rates; small jewellery ships at parcel rates too, because `weight` is a guess and the volume divisor dominates.

The same `?? 0.5` pattern appears at `:67` and `:104`, so the courier-quote path is equally fictional.

**Fix:** add `weight`, `length`, `breadth`, `height` to `product_varient` (or to `productstocks`, which is per-warehouse), populate them, and fail loudly when they are null rather than defaulting.

---

## 5. `$fillable` columns with no matching column (14)

`$fillable` is not validated against the schema, so these are **silently stripped** — no error, no log, no effect.

### 5.1 `ProductOrder::$fillable`

The model lists 46 columns. Ten have no counterpart in the dump's `product_orders` DDL:

| Column | Status |
|---|---|
| `billing_state` | **missing** — S1 |
| `shipping_state` | **missing** — S1 |
| `courier_name` | **missing** |
| `discount` | **missing** |
| `shiprocket_order_id` | added post-dump by the dashboard |
| `shiprocket_shipment_id` | added post-dump |
| `shiprocket_status` | added post-dump |
| `awb_code` | added post-dump |
| `pickup_scheduled` | **never written by any code path** |
| `manifest_url`, `label_url`, `invoice_url`, `cancellation_reason` | added post-dump; the Shiprocket methods that return them are never called |

`created_at` and `updated_at` are in `$fillable` — **timestamps are client-settable** (BUG-65).

### 5.2 Other models

| Model | Column | Status |
|---|---|---|
| `Cart` | `pre_order` | column exists; **stripped** (BUG-45) |
| `Cart` | `pre_note` | column does not exist |
| `Product` | `offer_price`, `mrp_price` | **no such columns** |
| `ProductVarient` | `low_stock` | exists as `varchar(255)`; never read |
| `ProductVarient` | `flash_sale`, `flash_sale_date` | exist; stripped, never read |
| `ProductVarient` | `Popular_products` | exists; **not in `$fillable`** |
| `Wishlist` | `product_quantity` | exists; always `1` |
| `UserAddress` | `phone_code`, `city_id`, `state_id`, `pincode_id`, `district` | exist; never written |

**`Product::$fillable` lists `offer_price` and `mrp_price`, which are `product_varient` columns.** Any code path that tries to save a product price writes nothing at all.

---

## 6. Tables referenced in code that do not exist (~25)

| Name | Referenced by | Reality |
|---|---|---|
| `api_logs` | integration audit design | **absent** — no integration audit table exists |
| `shiprocket_orders` | integration mirror design | **absent** |
| `shiprocket_pickup_locations` | `getPickupLocations()` | **absent** — the method's results are never persisted |
| `product_transactions` | — | actual table is **`product_transaction_logs`** |
| `product_order_status` | order history design | actual table is **`product_tracking`** |
| `order_tracking` | tracking page design | actual table is **`product_tracking`** |
| `productcategories`, `productpictures`, `product_varient_pictures`, `product_child_images`(exists) | pivot/image design | **absent** — `products` holds `category_id`, `subcategory_id`, `brand_id`, `product_image` inline |
| `productstocksprices` | price tiers | **absent** |
| `product_offers`, `offers`, `offer_images`(exists) | promotions | **absent** |
| `compare`, `attributes`, `attr_values`, `variants_attributes` | features | **absent** |
| `pincodes` | serviceability | actual table is **`all_india_pincodes`** |
| `posts`, `post_categorie`, `categories_blog`, `pages` | blog/CMS | **absent** |
| `shipping_methods`, `shipping_zones`, `country_shipping` | shipping config | **absent** |
| `shipments`, `shipment_items`, `returns`, `return_items`, `order_status_logs` | logistics | **absent** |
| `coupon__product`, `coupon__product_varient` | coupon pivots | **absent** — `coupons` and `newcoupons` exist with no product link |
| `membership_plans`, `membership_plan_subscriptions`, `membership_plan_orders` | subscriptions | **absent** — `milk_*` tables instead |
| `user_role` | RBAC pivot | **absent** — `model_has_roles` serves that role |
| `product_invoice`, `product_return_requests` | finance/returns | **absent** — `product_refunds` exists |

**`reviews` does exist** (and `bulkorders`, `cancel_requests`, `testimonials`, `today_deals`, `scrolling_bars`, `web_images`, `banner_images`) — none are referenced by storefront code.

---

## 7. Missing constraints

### 7.1 Foreign keys absent from the money path (34 FKs exist, none here)

| Relationship | FK? |
|---|---|
| `product_orders.user_id` → `users.id` | **YES** — `ON DELETE CASCADE` |
| `carts.user_id` → `users.id` | no |
| `carts.product_id` → `products.id` | no |
| `carts.product_varient_id` → `product_varient.id` | no |
| `wishlists.user_id` → `users.id` | no |
| `wishlists.product_id` → `products.id` | no |
| `product_order_items.order_id` → `product_orders.id` | no |
| `product_order_items.product_id` → `products.id` | no |
| `product_order_user_addresses.user_id` | no |
| `product_transaction_logs.order_id` | no |
| `product_tracking.order_id` | no |
| `product_slots.order_id` | no |

**`product_orders.user_id` cascades to `users.id`, but the other five tables that hold `user_id` have no constraint at all.** The delete-the-user story is therefore inconsistent: orders vanish, carts and wishlist rows survive as orphans.

### 7.2 Type inconsistency on the same identifier

| Table | `user_id` type |
|---|---|
| `product_orders` | `bigint(20) UNSIGNED` |
| `carts` | `varchar(255)` |
| `wishlists` | `varchar(255)` |
| `product_order_user_addresses` | `varchar(255)` |
| `product_transaction_logs` | `varchar(255)` |
| `product_tracking` | `varchar(255)` |
| `users.user_id` | `varchar(255)` |

**Two different types for one logical key.** `UserAddress::user()` and `ProductOrderUserAddress` bind against `users.user_id` (varchar) while `ProductOrder::user()` binds against `users.id` (bigint). Both work; they cannot be joined against each other.

Similarly, `order_id` is `bigint` on `product_orders` and `product_order_items` but `varchar(255)` on `product_transaction_logs`, `product_tracking`, `product_slots`, and `product_order_user_addresses`. The code writes `'ORD-XXXXXXXXXX'` into the varchar tables and `$order->id` into the bigint table — **the same logical order has two identifiers**, and only `$order->id` is an FK-able key.

### 7.3 Unique indexes

Present (19): `users_email`, `users_phone`, `users_phone_number`, `users_user_id`, `products_slug`, `product_orders_order_number`, `product_varient_sku`, `brands_brand_slug`, `newcoupons_code`, plus RBAC, delivery, and infra indexes.

Absent and needed:

| Missing | Consequence |
|---|---|
| `product_orders.razorpay_order_id` | duplicate orders on double-submit |
| `wishlists(user_id, product_id)` | duplicate wishlist rows |
| `carts(user_id, product_varient_id)` | duplicate cart lines |
| index on `carts.user_id`, `wishlists.user_id` | full scan on every page view |
| index on the five `order_id` varchar columns | full scan per order |
| index on `products.category_id`, `brand_id` | listing queries scan |

**`order_number` IS unique** — `'ORD-' . strtoupper(uniqid())` cannot collide silently; a duplicate raises a constraint violation instead. **`razorpay_order_id` is not**, so the idempotency gap (BUG-21) stands.

---

## 8. Broken relationships (3)

| # | Relationship | Declared | Actual | Evidence |
|---|---|---|---|---|
| 1 | `SubCategory::category()` | `belongsTo(Category, 'category_id')` | `sub_categories` has **no `category_id`** — only `category_name` | `Unknown column 'sub_categories.category_id' in 'where clause'` |
| 2 | `ProductVarient::category()` | `belongsTo(Category)` ⇒ `category_id` | the column is **`categoryid`** | source read |
| 3 | `UserAddress::country()` | `belongsTo(Country, 'area_id')` | `user_addresses.area_id` → `areas.id`, a different table | source read |

### Why #1 is silent

`belongsTo` on a missing column does not throw. Eloquent builds `where countries.id = sub_categories.category_id`; MySQL raises `Unknown column` and the query returns nothing — so `category()` is `null`, and any `?->` chain is also `null`. **The error only surfaces if the caller dereferences without `?->`.**

### Relationship-count defects

| Model | Issue |
|---|---|
| `Category::subCategories()` | `hasMany(SubCategory::class)` with no FK ⇒ also `category_id`, which does not exist |
| `Category::products()` / `Brand::products()` | no FK given ⇒ defaults to `categories.id` / `brands.id`, which **is** correct |
| `Product` | `variants()` and `productvari()` are identical |
| `ProductOrder` | `user()` and `customer()` are identical |
| `ProductOrder::items()` | `hasMany(ProductOrderItem, 'order_id')` — correct |
| `ProductOrder::slots()` / `orderAddress()` / `transactionLog()` / `trackings()` | keyed on the **varchar** `order_id` while the parent's PK is bigint — these resolve only because the code writes the string form into children |
| `Wishlist` | no `user()` relation at all |

---

## 9. `country_id` type conflict

| Table | Type | Semantics |
|---|---|---|
| `products` | `varchar(255)` | CSV |
| `product_varient` | `varchar(255)` | CSV |
| `productstocks` | `varchar(255)` | CSV |
| `categories` | `varchar(255) NOT NULL` | CSV |
| `brands` | `varchar(255) NOT NULL` | CSV |
| `sub_categories` | `bigint UNSIGNED` **+ FK to `countries.id`** | single id |
| `users`, `product_orders`, `warehouse`, `warehouse_branch` | `bigint UNSIGNED` + FK | single id |
| `countries` | PK | — |

**Every catalogue table uses CSV; every identity table uses a real FK.** That is why `FIND_IN_SET(?, country_id)` appears in four product/category queries — and it is unindexable by construction.

Also: `sub_categories.country_id` and `products.country_id` mean **different things** (a country vs a country list), and `Brand`/`Category` cast `country_id` to `string` in the model.

---

## 10. Multi-tenant residue

The schema carries a substantial second business that is not implemented:

| Group | Tables |
|---|---|
| Milk / subscription | `milk_orders`, `milk_order_user_addresses`, `milk_slots`, `milk_refunds`, `milk_transaction_logs`, `plan_types`, `onetimes` |
| Gifts | `gift_purchase_orders`, `gift_purchase_order_items`, `gift_color`, `gift_size` |
| Procurement | `purchase_orders`, `purchase_order_items`, `product_stock_batches`, `stock_distributions`, `stock_transfers`, `supplier_details` |
| Warehousing | `warehouse`, `warehouse_branch` |
| Delivery | `delivery_people`, `delivery_charge_details` |
| Auth | `otps`, `mail_otps` |

`Gift` has no storefront UI. `PurchaseOrder` has no storefront code. `Warehouse`/`Branch` are referenced in `$fillable` on `products`, `product_varient`, and `productstocks` and carry real FKs, but nothing in the storefront reads them.

---

## 11. Integrity defects register

| ID | Severity | Defect | Fix |
|---|---|---|---|
| DB-01 | **S1** | `product_orders.billing_state` / `.shipping_state` / `.courier_name` do not exist | guarded migration |
| DB-02 | **S1** | `product_order_items.product_image` does not exist | guarded migration |
| DB-03 | **S1** | Zero domain migrations in this app | adopt a shared schema package |
| DB-04 | **S2** | Variant `weight`/`length`/`breadth`/`height` do not exist ⇒ shipping quoted on a fictional 12 kg parcel | add columns, populate, fail loudly on null |
| DB-05 | **S2** | No `UNIQUE(razorpay_order_id)` ⇒ double-submit duplicates | unique index |
| DB-06 | **S2** | No `UNIQUE(user_id, product_id)` on `wishlists` | unique index |
| DB-07 | **S2** | No `UNIQUE(user_id, product_varient_id)` on `carts` | unique index |
| DB-08 | **S3** | `SubCategory::category()` FK absent | bind on `category_name`, or add the column |
| DB-09 | **S3** | `ProductVarient::category()` should use `categoryid` | fix the relation |
| DB-10 | **S3** | `UserAddress::country()` uses `area_id` | fix the relation |
| DB-11 | **S3** | No FK on `carts`/`wishlists`/`product_order_user_addresses`.`user_id` | add FKs |
| DB-12 | **S3** | `user_id` is `bigint` on `product_orders`, `varchar` on six other tables | normalise to `bigint` |
| DB-13 | **S3** | `order_id` is `bigint` on 2 tables, `varchar` on 4 | add a real `parent_order_id` FK |
| DB-14 | **S3** | `product_orders.user_id` cascades — deleting a user destroys order history and billing addresses | `ON DELETE RESTRICT` |
| DB-15 | **S3** | 14 `$fillable` columns silently stripped | align with the schema |
| DB-16 | **S3** | `products.product_price` read at 6 sites, never exists | read `product_regular_price` / `product_mrp_price` |
| DB-17 | **S3** | `products.offer_price`, `products.mrp_price` in `$fillable` | remove |
| DB-18 | **S3** | `country_id` is CSV in 5 tables, FK in 5 others | normalise |
| DB-19 | **S3** | No index on `carts.user_id`, `wishlists.user_id`, or the `order_id` columns | add indexes |
| DB-20 | **S3** | ~25 tables referenced in design docs do not exist | delete the references |
| DB-21 | **S3** | No integration audit table (`api_logs` absent) | create one |
| DB-22 | **S4** | `product_specfication` duplicates `product_specification` | drop one |
| DB-23 | **S4** | `states.name varchar(30)`, `user_addresses.state varchar(225)` — truncating widths | widen |
| DB-24 | **S4** | `carts.product_quantity` has no `UNSIGNED` | add it |
| DB-25 | **S4** | `sessions` unbounded, no prune | schedule `session:prune` |
| DB-26 | **S4** | ~55 tables with zero storefront usage | drop or document |
| DB-27 | **S4** | No CHECK constraints anywhere | add on money columns |
| DB-28 | **S4** | `product_order_items` uses `product_variant_id`; three other tables use `product_varient_id` | normalise |
| DB-29 | **S4** | Dump, lock file, and dashboard migrations are three different schema snapshots | one source of truth |

---

## 12. Verification queries

```sql
-- Confirm the S1 columns are genuinely absent
SELECT table_name, column_name FROM information_schema.columns
 WHERE table_schema = DATABASE()
   AND ( (table_name = 'product_orders'      AND column_name IN
          ('billing_state','shipping_state','courier_name','discount','pickup_scheduled'))
      OR (table_name = 'product_order_items' AND column_name = 'product_image') );
-- Expect: zero rows. Any row returned means the migration already ran.

-- Confirm the variant dimensions are absent (shipping defect)
SELECT column_name FROM information_schema.columns
 WHERE table_schema = DATABASE() AND table_name = 'product_varient'
   AND column_name IN ('weight','length','breadth','height');
-- Expect: zero rows.

-- Money-loss detector: payment captured upstream, no local order
SELECT t.razorpay_payment_id, o.total_amount
  FROM product_orders o
 WHERE o.payment_status = 'Paid'
   AND o.id NOT IN (SELECT order_id FROM product_order_items WHERE order_id IS NOT NULL);
-- Better, once product_transactions exists, reconcile against Razorpay directly.

-- Duplicate wishlist lines
SELECT user_id, product_id, COUNT(*) c FROM wishlists
 GROUP BY user_id, product_id HAVING c > 1;

-- Orphaned cart/wishlist rows
SELECT COUNT(*) FROM carts WHERE user_id IS NOT NULL
  AND user_id NOT IN (SELECT CAST(id AS CHAR) FROM users);

-- Orders with no line items (a rolled-back transaction survivor)
SELECT COUNT(*) FROM product_orders o
 WHERE NOT EXISTS (SELECT 1 FROM product_order_items i WHERE i.order_id = o.id);

-- Variant stock drifted below zero or diverging from line items
SELECT ps.pro_ver_id, ps.availablestock, ps.salestock
  FROM productstocks ps WHERE ps.availablestock < 0;
```

**Note on the reconciliation query:** `product_transaction_logs` is a *log*, not a payment record — it stores `order_amount`, `amount_credited`, `amount_debited` and no gateway reference. There is **no table that records a successful Razorpay capture independently of the order.** That is why BUG-04's failure mode is undetectable from the database alone: the only proof of payment lives in `storage/logs/laravel.log` and in Razorpay's dashboard.

---

## 13. Related documents

- [07-database-schema.md](07-database-schema.md) — table-by-table schema reference
- [11-models-and-relationships.md](11-models-and-relationships.md) — Eloquent mapping
- [16-performance.md](16-performance.md) — index and query costs
- [22-database-integrity.md](22-database-integrity.md) — this document
- [26-data-model-diagram.md](26-data-model-diagram.md) — ER diagrams
- [29-remediation-plan.md](29-remediation-plan.md) — the fix sequence