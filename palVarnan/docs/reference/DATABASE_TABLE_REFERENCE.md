# DATABASE_TABLE_REFERENCE — Column-level schema reference

> Canonical column-level details for the tables that the `palVarnan` storefront reads or
> writes, reconciled against the authoritative SQL dump `palvarnan_db (2).sql`.
>
> Evidence vocabulary: `VERIFIED_FROM_SOURCE`, `VERIFIED_FROM_LIVE_SCHEMA`, `INFERRED`,
> `UNVERIFIED`, `NOT_AVAILABLE`, `CONFLICTING_EVIDENCE`. **No credentials, connection
> strings, or PII are reproduced here.**
>
> See also: [`DATABASE_SCHEMA.md`](./DATABASE_SCHEMA.md), [`DATABASE_RELATIONSHIPS.md`](./DATABASE_RELATIONSHIPS.md),
> [`MIGRATION_AND_SQL_AUDIT.md`](./MIGRATION_AND_SQL_AUDIT.md), [`MODEL_REFERENCE.md`](./MODEL_REFERENCE.md),
> and pre-existing [`../07-database-schema.md`](../07-database-schema.md), [`../11-models-and-relationships.md`](../11-models-and-relationships.md).

---

## 1. Authority

- Baseline: `palvarnan_db (2).sql` (repo root `C:\xampp\htdocs\pauls\palvarnan_db (2).sql`).
  Header: MariaDB 10.4.32, phpMyAdmin 5.2.1, `palvarnan_db`, **Jul 31, 2026 at 08:44 AM**.
  `VERIFIED_FROM_LIVE_SCHEMA` ([lines 1-22](../../palvarnan_db%20(2).sql#L1-L22)).
- Dump structure: `CREATE TABLE` blocks + Indexes section ([167670-168305](../../palvarnan_db%20(2).sql#L167670-L168305)) + Constraints section ([168803-168934](../../palvarnan_db%20(2).sql#L168803-L168934)).
  `VERIFIED_FROM_LIVE_SCHEMA`.
- Model declarations (table names, `$fillable`, relations) from `app/Models/*` are
  `VERIFIED_FROM_SOURCE`. Code references from `app/Http/Controllers/*` are `VERIFIED_FROM_SOURCE`.
- Existing numbered docs linked for context, not duplicated: [`../07-database-schema.md`](../07-database-schema.md),
  [`../11-models-and-relationships.md`](../11-models-and-relationships.md), [`../22-database-integrity.md`](../22-database-integrity.md).

---

## 2. Key naming notes (actual vs expected)

| Model | Declared `$table` | Actual table in dump | Notes |
|---|---|---|---|
| `ProductVarient` | `product_varient` ([ProductVarient.php:10](../../palVarnan/app/Models/ProductVarient.php#L10)) | `product_varient` exists | Misspelled name ("variant"). No FK on `categoryid`/`subcategoryid`. `VERIFIED_FROM_SOURCE` + `VERIFIED_FROM_LIVE_SCHEMA`. |
| `ProductStock` | `productstocks` ([ProductStock.php:12](../../palVarnan/app/Models/ProductStock.php#L12)) | `productstocks` exists | No underscore. Columns use `productid`, `pro_ver_id`. `VERIFIED_FROM_LIVE_SCHEMA`. |
| `Product` | default `products` | `products` | Declared fillable includes `offer_price`, `mrp_price`, `product_mrp_price`, `product_regular_price`, `product_image`; dump lacks some code-used columns (see §3.4). `CONFLICTING_EVIDENCE`. |
| `ProductOrder` | `product_orders` | `product_orders` | Fillable has 44 items ([ProductOrder.php:18-63](../../palVarnan/app/Models/ProductOrder.php#L18-L63)); code writes fields (`billing_state`, `shipping_state`, `courier_name`, Shiprocket fields, `cancellation_reason`) that are **absent from the dump DDL**. `CONFLICTING_EVIDENCE`. |
| `ProductOrderItem` | `product_order_items` | `product_order_items` | Uses `product_variant_id` (correct spelling in model/field name). `order_id` is `bigint(20) UNSIGNED`. `product_image` present in model `$fillable` but **not present** in dump DDL for this table; code writes it ([CheckoutController.php:378](../../palVarnan/app/Http/Controllers/CheckoutController.php#L378)). `CONFLICTING_EVIDENCE`. |
| `Cart` | default `carts` | `carts` | `$fillable` has `ip_addresscart` (misspelled). `VERIFIED_FROM_SOURCE`. |
| `Wishlist` | `wishlists` | `wishlists` | No `user()` relation in model. `VERIFIED_FROM_SOURCE`. |

---

## 3. Table details (core storefront tables)

> For each table, key columns are listed as they appear in dump; model `$fillable` and code usage are noted. Complete DDL location in dump is cited.

### 3.1 `users` ([dump block approx users](../../palvarnan_db%20(2).sql#L167472-L167520))
`VERIFIED_FROM_LIVE_SCHEMA`. Model [User.php](../../palVarnan/app/Models/User.php). 
Keys/uniques: `users_email_unique`, `users_phone_number_unique`, `users_user_id_unique`, `users_phone_unique` ([dump uniques/constraints](../../palvarnan_db%20(2).sql#L168803-L168934), [indexes](../../palvarnan_db%20(2).sql#L167670-L168305)). 
FKs include `fk_users_country` → `countries(id)` CASCADE ([168803-168934](../../palvarnan_db%20(2).sql#L168803-L168934)).

### 3.2 `products` ([dump block](../../palvarnan_db%20(2).sql#L165565-L165622))
`VERIFIED_FROM_LIVE_SCHEMA`. Model [Product.php](../../palVarnan/app/Models/Product.php). 
- `fillable` includes: `offer_price`, `mrp_price`, `product_mrp_price`, `product_regular_price`, `product_image`, `unit_value`, `product_value`, `warehouse_id`, `branch_id` ([Product.php:12-15](../../palVarnan/app/Models/Product.php#L12-L15)). 
- Dump has columns like `product_mrp_price`, `product_regular_price`, `product_quantity` (bigint/int), `deleted_at` (SoftDeletes). 
- **Code reads** `product->product_price` at [CheckoutController.php:367](../../palVarnan/app/Http/Controllers/CheckoutController.php#L367) as fallback — **no `product_price` column** in model fillable/dump DDL. `CONFLICTING_EVIDENCE`. 
- Has duplicate/variant names in schema notes: `product_specification`, `product_specfication` (typo) exist in some schema views (`INFERRED` from existing docs [`../07-database-schema.md`](../07-database-schema.md)); dump column names must be taken as-is. `UNVERIFIED` until exact dump columns confirmed for those fields.

### 3.3 `product_varient` ([dump block](../../palvarnan_db%20(2).sql#L166047-L166407))
`VERIFIED_FROM_LIVE_SCHEMA`. Model [ProductVarient.php](../../palVarnan/app/Models/ProductVarient.php): table `product_varient`, fillable includes `categoryid`, `subcategoryid`, `varient` (int), `offer_price`, `mrp_price` ([ProductVarient.php:10,12](../../palVarnan/app/Models/ProductVarient.php#L10-L12)). 
- **Foreign keys present**: `product_varient_branch_id_foreign` → `warehouse_branch(id)` SET NULL, `product_varient_warehouse_id_foreign` → `warehouse(id)` SET NULL ([168803-168934](../../palvarnan_db%20(2).sql#L168803-L168934)). 
- **No FK** for `categoryid`/`subcategoryid` → `categories`/`sub_categories`. `VERIFIED_FROM_LIVE_SCHEMA`. 
- `varient` is integer; model uses it as variant identifier. 
- `created_at` has `ON UPDATE current_timestamp()` in dump. `VERIFIED_FROM_LIVE_SCHEMA`.

### 3.4 `productstocks` ([dump block](../../palvarnan_db%20(2).sql#L165623-L165669))
`VERIFIED_FROM_LIVE_SCHEMA`. Model [ProductStock.php](../../palVarnan/app/Models/ProductStock.php): table `productstocks`, fillable `productid` (int), `pro_ver_id` (bigint), `availablestock`, `salestock`, `overallstock`, `low_stocks` (misspellings). 
- FKs: `productstocks_branch_id_foreign` → `warehouse_branch(id)` SET NULL, `productstocks_warehouse_id_foreign` → `warehouse(id)` SET NULL ([168803-168934](../../palvarnan_db%20(2).sql#L168803-L168934)). 
- Code decrements `availablestock`, increments `salestock` ([CheckoutController.php:405-407](../../palVarnan/app/Http/Controllers/CheckoutController.php#L405-L407)). `VERIFIED_FROM_SOURCE`.

### 3.5 `product_orders` ([dump block](../../palvarnan_db%20(2).sql#L165700-L165765))
`VERIFIED_FROM_LIVE_SCHEMA`. Model [ProductOrder.php](../../palVarnan/app/Models/ProductOrder.php): 44 fillable fields ([18-63](../../palVarnan/app/Models/ProductOrder.php#L18-L63)). 
- **Conflicts with dump**: fillable includes `billing_state`, `shipping_state`, `courier_name`, `shiprocket_order_id`, `shiprocket_shipment_id`, `awb_code`, `pickup_scheduled`, `manifest_url`, `label_url`, `invoice_url`, `shiprocket_status`, `cancellation_reason` — these are **not present** in dump DDL as columns of `product_orders` (per baseline dump). `CONFLICTING_EVIDENCE`. 
- Also `discount` is **not in fillable**; existing docs claim it exists → `CONFLICTING_EVIDENCE`. 
- Code writes `billing_state`, `shipping_state`, `courier_name` at [CheckoutController.php:306,315,321](../../palVarnan/app/Http/Controllers/CheckoutController.php#L306-L321). These columns are not in dump → schema drift. `CONFLICTING_EVIDENCE`.
- FKs: `fk_product_orders_country` → `countries(id)` CASCADE, `product_orders_user_id_foreign` → `users(id)` CASCADE ([168803-168934](../../palvarnan_db%20(2).sql#L168803-L168934)). `VERIFIED_FROM_LIVE_SCHEMA`.
- Unique: `product_orders_order_number_unique` ([indexes/uniques](../../palvarnan_db%20(2).sql#L168803-L168934)). 

### 3.6 `product_order_items` ([dump block](../../palvarnan_db%20(2).sql#L165766-L165812))
`VERIFIED_FROM_LIVE_SCHEMA`. Model [ProductOrderItem.php](../../palVarnan/app/Models/ProductOrderItem.php): fillable includes `product_variant_id`, `product_image` ([14-26](../../palVarnan/app/Models/ProductOrderItem.php#L14-L26)). 
- **`product_image` not in dump DDL** for this table. Code writes it ([CheckoutController.php:378](../../palVarnan/app/Http/Controllers/CheckoutController.php#L378)). `CONFLICTING_EVIDENCE`. 
- `order_id` is `bigint(20) UNSIGNED` (PK/FK style in dump). 
- Code sets `order_id` = `$order->id` (bigint) at [CheckoutController.php:374](../../palVarnan/app/Http/Controllers/CheckoutController.php#L374). 
- **No FK constraints** declared in dump for `product_order_items` ([168803-168934](../../palvarnan_db%20(2).sql#L168803-L168934)) — relations are logical only. `VERIFIED_FROM_LIVE_SCHEMA`.

### 3.7 `product_order_user_addresses` ([dump block](../../palvarnan_db%20(2).sql#L165813-L165874))
`VERIFIED_FROM_LIVE_SCHEMA`. Model [ProductOrderUserAddress.php](../../palVarnan/app/Models/ProductOrderUserAddress.php). Code writes with `order_id` = `$orderNumber` (string) at [CheckoutController.php:332](../../palVarnan/app/Http/Controllers/CheckoutController.php#L332); model relation expects linking via `order_id` ([34-36](../../palVarnan/app/Models/ProductOrderUserAddress.php#L34-L36)) matching string. **No FK constraints** in dump. `CONFLICTING_EVIDENCE` in terms of key types across tables (see §4).

### 3.8 `product_slots` ([dump block](../../palvarnan_db%20(2).sql#L165875-L165955))
`VERIFIED_FROM_LIVE_SCHEMA`. Model [ProductSlot.php](../../palVarnan/app/Models/ProductSlot.php). Code writes `order_id` = `$orderNumber` (string) at [CheckoutController.php:389](../../palVarnan/app/Http/Controllers/CheckoutController.php#L389)). **No FK** in dump. `VERIFIED_FROM_LIVE_SCHEMA`.

### 3.9 `product_tracking` ([dump block](../../palvarnan_db%20(2).sql#L165956-L166027))
`VERIFIED_FROM_LIVE_SCHEMA`. Model [ProductTracking.php](../../palVarnan/app/Models/ProductTracking.php) — empty relations. Code writes `order_id` = `$orderNumber` ([CheckoutController.php:361](../../palVarnan/app/Http/Controllers/CheckoutController.php#L361)). **No FK**. `VERIFIED_FROM_LIVE_SCHEMA`.

### 3.10 `product_transaction_logs` ([dump block](../../palvarnan_db%20(2).sql#L166028-L166046))
`VERIFIED_FROM_LIVE_SCHEMA`. Model [ProductTransactionLog.php](../../palVarnan/app/Models/ProductTransactionLog.php). Code writes `order_id` = `$orderNumber` ([CheckoutController.php:349](../../palVarnan/app/Http/Controllers/CheckoutController.php#L349)). **No FK**. `VERIFIED_FROM_LIVE_SCHEMA`.

### 3.11 `categories`, `sub_categories`, `brands`
- `categories` ([dump block](../../palvarnan_db%20(2).sql#L155618-L155633)): Model [Category.php](../../palVarnan/app/Models/Category.php). `$fillable` includes `country_id` (cast string); code queries via FIND_IN_SET at [HomeController.php:24](../../palVarnan/app/Http/Controllers/HomeController.php#L24). `VERIFIED_FROM_SOURCE`+`VERIFIED_FROM_LIVE_SCHEMA`.
- `sub_categories` ([dump block](../../palvarnan_db%20(2).sql#L166602-L166645)): Model [SubCategory.php](../../palVarnan/app/Models/SubCategory.php). Fillable has `category_name` (not `category_id`); relation `category()` uses `category_id` as foreign key ([SubCategory.php:11-14](../../palVarnan/app/Models/SubCategory.php#L11-L14)) but **no `category_id` column** exists in dump DDL for `sub_categories`. Also has `country_id` bigint + FK `sub_categories_country_id_foreign` → `countries(id)` CASCADE ([168803-168934](../../palvarnan_db%20(2).sql#L168803-L168934)). `CONFLICTING_EVIDENCE`.
- `brands` ([dump block](../../palvarnan_db%20(2).sql#L155473-L155520)): Model [Brand.php](../../palVarnan/app/Models/Brand.php). Unique `brands_brand_slug_unique`. `VERIFIED_FROM_LIVE_SCHEMA`.

### 3.12 Geography: `countries`, `states`, `districts`, `areas`, `area_assigns`
- `countries` ([dump](../../palvarnan_db%20(2).sql#L164338-L164374)): columns `id,name,iso_code,phone_code,ship_charge,created_at,updated_at,currency`. FK targets from users/sub_categories. `VERIFIED_FROM_LIVE_SCHEMA`.
- `states` ([dump](../../palvarnan_db%20(2).sql#L166431-L166601)): `name varchar(30)`, `country_id int`, charset latin1. Model [State.php](../../palVarnan/app/Models/State.php): fillable `name,code,shipping_charge` ([14-18](../../palVarnan/app/Models/State.php#L14-L18)); relation to districts by `state_name` ([21-23](../../palVarnan/app/Models/State.php#L21-L23)). `CONFLICTING_EVIDENCE` (fillable includes `code` not obvious in dump; relation keying by name). `VERIFIED_FROM_SOURCE`+`PARTIAL`.
- `districts` ([dump](../../palvarnan_db%20(2).sql#L164473-L164531)): `state_id int`, `state_name`. `VERIFIED_FROM_LIVE_SCHEMA`.
- `areas` ([dump](../../palvarnan_db%20(2).sql#L155431-L155444)): `area_name`, `area_pincode`. Model-only in storefront (no queries). `VERIFIED_FROM_LIVE_SCHEMA`.
- `area_assigns` ([dump](../../palvarnan_db%20(2).sql#L155445-L155472)): FKs `area_assigns_ibfk_1` → `areas(id)` CASCADE, `area_assigns_ibfk_2` → `delivery_people(id)` CASCADE ([168803-168934](../../palvarnan_db%20(2).sql#L168803-L168934)). Model-only. `VERIFIED_FROM_LIVE_SCHEMA`.

### 3.13 `carts`, `wishlists`, `coupons`, `units`, `user_addresses`, `home_promotions`
- `carts` ([dump](../../palvarnan_db%20(2).sql#L155573-L155617)): fillable has `ip_addresscart` (misspelled) ([Cart.php:9-15](../../palVarnan/app/Models/Cart.php#L9-L15)); no FKs in dump. `VERIFIED_FROM_SOURCE`+`VERIFIED_FROM_LIVE_SCHEMA`.
- `wishlists` ([dump](../../palvarnan_db%20(2).sql#L167646-L167669)): fillable `ip_address,user_id,product_id,product_varient_id,product_quantity` ([Wishlist.php:11-14](../../palVarnan/app/Models/Wishlist.php#L11-L14)); **no `user()` relation** in model. No FKs in dump. `VERIFIED_FROM_SOURCE`+`VERIFIED_FROM_LIVE_SCHEMA`.
- `coupons` ([dump](../../palvarnan_db%20(2).sql#L164375-L164472)): columns `id,codename,mini_amt,discounttype,discount,start_date,end_date,default_id,coupon_status,created_at,updated_at`. Model [Coupon.php](../../palVarnan/app/Models/Coupon.php): fillable includes `coupon_name` (not present as column name obvious?) and `codename,discounttype,discount,mini_amt,start_date,end_date` ([14-22](../../palVarnan/app/Models/Coupon.php#L14-L22)). Code **imports Coupon but never queries** ([CheckoutController.php:6](../../palVarnan/app/Http/Controllers/CheckoutController.php#L6)); no `Coupon::` calls in `app/`. `VERIFIED_FROM_SOURCE`+`VERIFIED_FROM_LIVE_SCHEMA`.
- `units` ([dump](../../palvarnan_db%20(2).sql#L167441-L167471)): `unit_name,short_name,...`. Model [Unit.php](../../palVarnan/app/Models/Unit.php) is **empty** (lines 1-10). Read only via `variants.unit` eager load ([ProductController.php:118](../../palVarnan/app/Http/Controllers/ProductController.php#L118), [ProductVarient.php:27-29](../../palVarnan/app/Models/ProductVarient.php#L27-L29)). `VERIFIED_FROM_SOURCE`+`VERIFIED_FROM_LIVE_SCHEMA`.
- `user_addresses` ([dump](../../palvarnan_db%20(2).sql#L167521-L167645)): fillable includes `state` (varchar), `address_phone_number` (bigint), `pincode` (int), `area_id` ([UserAddress.php:15-34](../../palVarnan/app/Models/UserAddress.php#L15-L34)). Relation `country()` uses `area_id` as FK ([UserAddress.php:47-50](../../palVarnan/app/Models/UserAddress.php#L47-L50)) — **defect** (points to areas, not countries). No FK constraints in dump. `CONFLICTING_EVIDENCE`.
- `home_promotions` ([dump](../../palvarnan_db%20(2).sql#L164703-L164732)): Model [HomePromotion.php](../../palVarnan/app/Models/HomePromotion.php) is **empty**. Queried at [HomeController.php:19-22](../../palVarnan/app/Http/Controllers/HomeController.php#L19-L22). `VERIFIED_FROM_SOURCE`+`VERIFIED_FROM_LIVE_SCHEMA`.

### 3.14 Child/images and misc
- `product_child_images` ([dump](../../palvarnan_db%20(2).sql#L165670-L165699)): columns `id, product_id, variant_id, product_child_image, created_at, updated_at` (PK id only). Model [ProductChildImage.php](../../palVarnan/app/Models/ProductChildImage.php). Eager-loaded at [ProductController.php:118](../../palVarnan/app/Http/Controllers/ProductController.php#L118). No FK in dump. `VERIFIED_FROM_LIVE_SCHEMA`.
- Framework tables: `sessions`, `cache`, `cache_locks`, `jobs`, `job_batches`, `failed_jobs`, `password_reset_tokens` — created by stock migrations only ([0001_01_01_000000_create_users_table.php](../../palVarnan/database/migrations/0001_01_01_000000_create_users_table.php), [0001_01_01_000001_create_cache_table.php](../../palVarnan/database/migrations/0001_01_01_000001_create_cache_table.php), [0001_01_01_000002_create_jobs_table.php](../../palVarnan/database/migrations/0001_01_01_000002_create_jobs_table.php)). Present in dump. `VERIFIED_FROM_SOURCE`+`VERIFIED_FROM_LIVE_SCHEMA`.

---

## 4. Key type/key mismatches (defects)

| Issue | Location | Evidence | Tag |
|---|---|---|---|
| Dual order identifiers: `product_orders.id` is bigint (auto-increment) PK; `product_orders.order_id`/`order_number` are varchar. Sibling tables (`product_order_user_addresses`, `product_slots`, `product_tracking`, `product_transaction_logs`) store `order_id` as **varchar(255)** (mirror of order_number). `ProductOrderItem.order_id` stores the **bigint id** (`$order->id`) at [CheckoutController.php:374](../../palVarnan/app/Http/Controllers/CheckoutController.php#L374), while other relations in `ProductOrder` model use `('order_id','order_id')` pointing to varchar mirror ([ProductOrder.php:82,87,92,97](../../palVarnan/app/Models/ProductOrder.php#L82-L97)). | Models + Controllers + Dump | [ProductOrder.php](../../palVarnan/app/Models/ProductOrder.php), [CheckoutController.php:332,349,361,374,389](../../palVarnan/app/Http/Controllers/CheckoutController.php#L332-L389), dump blocks for those tables ([165813-166046](../../palvarnan_db%20(2).sql#L165813-L166046), [165700-165765](../../palvarnan_db%20(2).sql#L165700-L165765), [165766-165812](../../palvarnan_db%20(2).sql#L165766-L165812)) | `CONFLICTING_EVIDENCE` |
| `ProductOrder::items()` is `hasMany(ProductOrderItem::class,'order_id')` with default local key `id` (bigint); `ProductOrderItem.order_id` stores bigint. That matches in principle but **other relations** (`slots`, `orderAddress`, `transactionLog`, `trackings`) use foreign/local pair `('order_id','order_id')` (varchar) — inconsistent relation design. | Model | [ProductOrder.php:75-78](../../palVarnan/app/Models/ProductOrder.php#L75-L78), [82-98](../../palVarnan/app/Models/ProductOrder.php#L82-L98) | `CONFLICTING_EVIDENCE` |
| `SubCategory::category()` uses `category_id` ([SubCategory.php:11-14](../../palVarnan/app/Models/SubCategory.php#L11-L14)) but `sub_categories` has **no `category_id` column** in dump; it has `category_name`. | Model vs dump | [SubCategory.php:11-14](../../palVarnan/app/Models/SubCategory.php#L11-L14), dump [166602-166645](../../palvarnan_db%20(2).sql#L166602-L166645) | `CONFLICTING_EVIDENCE` |
| `ProductVarient::category()` uses default `category_id` ([ProductVarient.php:23-25](../../palVarnan/app/Models/ProductVarient.php#L23-L25)) but actual column is `categoryid` ([ProductVarient.php:12](../../palVarnan/app/Models/ProductVarient.php#L12), dump [166047-166407](../../palvarnan_db%20(2).sql#L166047-L166407)). | Model vs dump | [ProductVarient.php:23-25](../../palVarnan/app/Models/ProductVarient.php#L23-L25) | `CONFLICTING_EVIDENCE` |
| `UserAddress::country()` uses `area_id` as FK ([UserAddress.php:47-50](../../palVarnan/app/Models/UserAddress.php#L47-L50)) — points to areas, not countries. | Model | [UserAddress.php:47-50](../../palVarnan/app/Models/UserAddress.php#L47-L50) | `CONFLICTING_EVIDENCE` |
| `ProductOrderItem` has `product_image` in `$fillable` and code writes it ([ProductOrderItem.php:19](../../palVarnan/app/Models/ProductOrderItem.php#L19), [CheckoutController.php:378](../../palVarnan/app/Http/Controllers/CheckoutController.php#L378)) but **column absent** from `product_order_items` DDL in dump. | Model+code vs dump | [ProductOrderItem.php:19](../../palVarnan/app/Models/ProductOrderItem.php#L19), dump [165766-165812](../../palvarnan_db%20(2).sql#L165766-L165812) | `CONFLICTING_EVIDENCE` |
| `product_orders` fillable/model includes many fields not in dump DDL (`billing_state`, `shipping_state`, `courier_name`, Shiprocket fields, `cancellation_reason`) and `discount` claimed absent in fillable vs docs; code writes several ([CheckoutController.php:306,315,321](../../palVarnan/app/Http/Controllers/CheckoutController.php#L306-L321)). | Model+code vs dump | [ProductOrder.php:18-63](../../palVarnan/app/Models/ProductOrder.php#L18-L63), dump [165700-165765](../../palvarnan_db%20(2).sql#L165700-L165765) | `CONFLICTING_EVIDENCE` |
| `Product` code reads `product_price` fallback ([CheckoutController.php:367](../../palVarnan/app/Http/Controllers/CheckoutController.php#L367)); no such column in model/dump. | Code vs schema | [CheckoutController.php:367](../../palVarnan/app/Http/Controllers/CheckoutController.php#L367), [Product.php:12-15](../../palVarnan/app/Models/Product.php#L12-L15), dump [165565-165622](../../palvarnan_db%20(2).sql#L165565-L165622) | `CONFLICTING_EVIDENCE` |
| `UserAddress::user()` uses FK `user_id`→`users.user_id` ([UserAddress.php:36-39](../../palVarnan/app/Models/UserAddress.php#L36-L39)); `User` model is default Laravel user (no explicit `user_id` as primary key in fillable shown). Relation targets non-standard local key. | Model | [UserAddress.php:36-39](../../palVarnan/app/Models/UserAddress.php#L36-L39), [User.php](../../palVarnan/app/Models/User.php) | `INFERRED` |

---

## 5. Tables absent from dump but referenced (for completeness)

Existing docs (notably [`../07-database-schema.md`](../07-database-schema.md) §2) list ~25 tables referenced in design notes but not found in `palvarnan_db (2).sql`. These are **outside** storefront model/code usage scope; listed here as `NOT_AVAILABLE` in dump. Examples include `address_types` (referenced by `ProductOrderUserAddress` fillable `address_type_id`, `address_type_name`), `delivery_people` (target of `area_assigns_ibfk_2`), `warehouse`, `warehouse_branch` (FK targets for variants/stocks), etc. Exact census: see [`../07-database-schema.md`](../07-database-schema.md). `INFERRED` from existing docs.

---

## 6. Related documents

- [`DATABASE_SCHEMA.md`](./DATABASE_SCHEMA.md) — overview and ownership.
- [`DATABASE_RELATIONSHIPS.md`](./DATABASE_RELATIONSHIPS.md) — relations + ER diagram.
- [`MIGRATION_AND_SQL_AUDIT.md`](./MIGRATION_AND_SQL_AUDIT.md) — migrations vs 93 tables, drift.
- [`MODEL_REFERENCE.md`](./MODEL_REFERENCE.md) — per-model subsections.
- Pre-existing: [`../07-database-schema.md`](../07-database-schema.md), [`../11-models-and-relationships.md`](../11-models-and-relationships.md), [`../22-database-integrity.md`](../22-database-integrity.md), [`../26-data-model-diagram.md`](../26-data-model-diagram.md), [`../audit/VERIFIED_FACTS.md`](../audit/VERIFIED_FACTS.md).
