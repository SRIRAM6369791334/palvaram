# 07 — Database Schema

**93 tables.** Evidence: `C:\xampp\htdocs\pauls\palvarnan_db (2).sql` (mysqldump), verified by enumerating every `CREATE TABLE` statement, every `FOREIGN KEY` constraint (34), and every `UNIQUE KEY` (19).

**The storefront's `database/migrations/` folder contains only the 3 default Laravel migrations — it owns none of the domain schema.**

---

## 1. Schema ownership

| Table group | Count | Created by | In `palVarnan/database/migrations`? |
|---|---|---|---|
| Laravel default | 6 (`users`, `password_reset_tokens`, `sessions`, `cache`, `cache_locks`, `jobs`, `job_batches`, `failed_jobs`, `migrations`) | `palVarnan` | **Yes** |
| Domain (products, orders, carts…) | ~78 | `palVarnanDashboard` (~57 migrations) | **No** |
| Post-dump additions | — | `palVarnanDashboard/…/2026_08_17_000001_add_shiprocket_and_razorpay_columns_to_product_orders_table.php` | **No** |

**Consequence:** `php artisan migrate:fresh` on `palVarnan` produces a database of Laravel boilerplate tables and a storefront that cannot render a page. There is no bootstrap path for this app alone.

---

## 2. The complete table list (93, VERIFIED)

### Catalogue (7)
`products`, `product_varient`, `productstocks`, `categories`, `sub_categories`, `brands`, `units`

### Cart & wishlist (2)
`carts`, `wishlists`

### Orders & fulfilment (6)
`product_orders`, `product_order_items`, `product_order_user_addresses`, `product_transaction_logs`, `product_tracking`, `product_slots`

### Procurement & stock (7)
`purchase_orders`, `purchase_order_items`, `product_stock_batches`, `product_stock_batches` (batches), `stock_distributions`, `stock_transfers`, `product_refunds`

### Warehousing (2)
`warehouse`, `warehouse_branch`

### Geography (8)
`countries`, `states`, `districts`, `areas`, `area_assigns`, `cities`, `city_list`, `state_list`, `all_india_pincodes`, `country_tables`, `delivery_charge_details`, `free_shipping_settings`

### Users & addresses (3)
`users`, `user_addresses`, `address_types`, `gender_types`

### Auth & roles (7)
`dashboard_users`, `roles`, `permissions`, `role_has_permissions`, `model_has_roles`, `model_has_permissions`, `personal_access_tokens`

### Gifts (5)
`gift_purchase_orders`, `gift_purchase_order_items`, `gift_color`, `gift_size`, `is_gift` (column, not table)

### Promotions & content (8)
`home_promotions`, `coupons`, `newcoupons`, `offer_images`, `banner_images`, `web_images`, `scrolling_bars`, `today_deals`, `testimonials`, `reviews`

### Contact & enquiries (5)
`contacts`, `contact_details`, `bulkorders`, `cancel_requests`, `supplier_details`, `delivery_people`, `mail_otps`, `otps`, `onetimes`, `plan_types`, `newcoupons`

### Milk / subscription (5)
`milk_orders`, `milk_order_user_addresses`, `milk_slots`, `milk_refunds`, `milk_transaction_logs`

### Notifications (4)
`notifications`, `app_notifications`, `app_alerts`, `user_read_notifications`

### Observability (4)
`telescope_entries`, `telescope_entries_tags`, `telescope_monitoring`, `tests`

### Laravel infrastructure (9)
`migrations`, `cache`, `cache_locks`, `jobs`, `job_batches`, `failed_jobs`, `sessions`, `password_reset_tokens`, `password_resets`

### **Tables referenced in code but ABSENT from the dump**

| Referenced name | Where referenced | Consequence |
|---|---|---|
| `api_logs` | integration audit trail | **Table does not exist** |
| `shiprocket_orders` | integration mirror | **Table does not exist** |
| `shiprocket_pickup_locations` | `getPickupLocations()` | **Table does not exist** |
| `product_transactions` | referenced in docs/summaries | Actual table is `product_transaction_logs` |
| `product_order_status` | order history | Actual table is `product_tracking` |
| `order_tracking` | tracking page | Actual table is `product_tracking` |
| `productcategories`, `productpictures`, `product_varient_pictures` | pivot/image tables | **Do not exist** — `products` carries `category_id`, `subcategory_id`, `brand_id`, `product_image` inline |
| `productstocksprices` | pricing tiers | **Does not exist** |
| `product_offers`, `offers` | promotions | **Do not exist** |
| `compare`, `attributes`, `attr_values`, `variants_attributes` | features | **Do not exist** |
| `pincodes` | serviceability | Actual table is `all_india_pincodes` |
| `posts`, `post_categorie`, `categories_blog`, `pages` | blog/CMS | **Do not exist** |
| `shipping_methods`, `shipping_zones`, `country_shipping` | shipping config | **Do not exist** |
| `shipments`, `shipment_items`, `returns`, `return_items`, `order_status_logs` | logistics | **Do not exist** |
| `coupon__product`, `coupon__product_varient` | coupon pivots | **Do not exist** |
| `membership_plans`, `membership_plan_subscriptions`, `membership_plan_orders` | subscriptions | **Do not exist** (milk_* tables instead) |
| `user_role` | RBAC pivot | **Does not exist** |
| `product_transactions`, `product_invoice` | finance | **Do not exist** |
| `gift_token` | guest identity | **Does not exist** |

**There is no integration-audit table anywhere in the schema.** Shiprocket and Razorpay calls are logged to `storage/logs/laravel.log` only.

---

## 3. Critical schema defects

### D-S1 — Missing columns that break checkout — **CRITICAL, VERIFIED**

The INSERT at `CheckoutController.php:295-327` writes 14 columns to `product_orders`. Compared against the dump's DDL, **three do not exist**:

| Column | Exists in dump? | Log evidence |
|---|---|---|
| `billing_state` | **No** — `product_orders` has `billing_pincode` then `shipping_name` | `Unknown column 'billing_state' in 'field list'` |
| `shipping_state` | **No** | would be the next failure |
| `product_orders.courier_name` | **No** | written at `:321` |
| `product_order_items.product_image` | **No** — `product_order_items` has no image column | `Unknown column 'product_image' in 'field list'` |

Confirmed by reading the dump DDL directly:

```
`product_orders` columns: country_id, user_id, order_number, order_id,
  delivery_person_id, delivery_person_name, delivery_person_phone,
  is_delivery_assigned, billing_name … billing_pincode,
  shipping_name … shipping_pincode,
  subtotal, gst_amount, shipping_charge, total_amount,
  payment_status, payment_method, razorpay_order_id, razorpay_payment_id,
  status, created_at, updated_at, reason, unavailable_amount, unavailable_count
```

No `billing_state`. No `shipping_state`. No `courier_name`. No `discount` — **yet `ProductOrder::$fillable` lists `discount` and `courier_name`, and the dashboard's post-dump migration adds the `shiprocket_*` / `awb_code` / `pickup_scheduled` / `manifest_url` / `label_url` / `invoice_url` / `cancellation_reason` columns that `ProductOrder::$fillable` also expects.**

A log entry from the same day later shows a Shiprocket order created successfully, proving **someone ran unrecorded `ALTER TABLE`s** against one database. That state is in no repository.

### D-S2 — `products.product_price` does not exist — **VERIFIED**

`products` has `product_mrp_price` and `product_regular_price`. It has **no** `product_price`.

Six read sites:

| Location | Expression |
|---|---|
| `AppServiceProvider.php:41-43` | `($c->variant->offer_price ?? 0) * $c->product_quantity` |
| `CartController.php:27` | `$item->variant->offer_price * $item->product_quantity` (no `??`) |
| `CartController.php:142` | `($item->variant->offer_price ?? 0) * …` |
| `CheckoutController.php:60, 184, 279, 367, 447` | `$item->variant->offer_price ?? $item->product->product_price ?? 0` |

Because `??` on a non-existent Eloquent attribute returns `null` silently, the chain falls through to `0`. **No error is raised — the price is just wrong when `offer_price` is also null.** `Product::$fillable` also lists `offer_price` and `mrp_price`, neither of which is a `products` column.

### D-S3 — `product_varient.varient` is `int(11)` — **VERIFIED**

`SearchController.php:38` applies `LIKE '%…%'` to it. MySQL coerces the pattern to `0`; an integer column can never match. **Variant search is permanently dead, with no error.**

### D-S4 — Missing slug columns — **VERIFIED**

| Model | Column in dump | Column the code reads |
|---|---|---|
| `Brand` | `brand_slug` (unique) | `slug` |
| `Category` | **no slug column at all** | `slug` |
| `Product` | `slug` (unique) | correct |

Both brand and category URLs resolve to `/brand/` and `/category/`.

### D-S5 — `sub_categories` has no `category_id` — **VERIFIED**

The dump shows `sub_categories`: `country_id`, `subcategory_name`, `subcategory_image`, `category_name`, `category_display`, `status`, `is_gift`.

There is **no `category_id`**. Yet:
- `SubCategory::category()` → `belongsTo(Category::class, 'category_id')`
- `Category::subCategories()` → `hasMany(SubCategory::class)` (also defaults to `category_id`)

Log: `Unknown column 'sub_categories.category_id' in 'where clause'`. The link is by **name** (`sub_categories.category_name`), which no relationship declares.

### D-S6 — Foreign keys: 34 across 93 tables, concentrated elsewhere

The 34 declared FKs cover `warehouse`, `warehouse_branch`, `countries`, `users`, `areas`, `address_types`, `sub_categories`, `products`, `productstocks`, `product_varient`, `product_orders`, `purchase_orders`, `purchase_order_items`, `milk_*`, `area_assigns`, `app_alerts`, `app_notifications`, `telescope_*`, and the role/permission pivots.

**Notably absent from the money and cart tables:**

| Relationship | Enforcement |
|---|---|
| `carts.product_id` → `products.id` | **No FK** |
| `carts.product_varient_id` → `product_varient.id` | **No FK** |
| `carts.user_id` → `users.id` | **No FK** (`user_id` is `varchar(255)`) |
| `wishlists.user_id` → `users.id` | **No FK** (`varchar(255)`) |
| `wishlists.product_id` → `products.id` | **No FK** |
| `product_order_items.order_id` → `product_orders.id` | **No FK** |
| `product_order_items.product_id` → `products.id` | **No FK** |
| `product_order_user_addresses.user_id` | **No FK** |
| `product_transaction_logs.order_id` | **No FK** (`order_id` is `varchar`) |
| `product_tracking.order_id` | **No FK** (`varchar`) |
| `product_slots.order_id` | **No FK** (`varchar`) |

`product_orders.user_id` **does** have an FK to `users.id` with `ON DELETE CASCADE` — and because it cascades, **deleting a user deletes their order history and billing addresses**, which is itself a data-retention defect.

Also note `carts.user_id`, `wishlists.user_id`, and `product_order_user_addresses.user_id` are `varchar(255)` while `product_orders.user_id` is `bigint`. **The same logical identifier is typed two different ways across the schema.**

This is why soft-deleted products leave dangling cart/wishlist rows, producing the log's `Attempt to read property "product_image" on null`.

### D-S7 — `product_order_items` uses the *correct* spelling

| Table | Column |
|---|---|
| `carts`, `wishlists`, `product_slots` | `product_varient_id` (misspelled) |
| `product_order_items` | `product_variant_id` (correct) |

Same table, two spellings, within one order transaction. Also misspelled: `productstocks.pro_ver_id`, `products.product_specfication` (a duplicate of `product_specification`), `carts.ip_addresscart`, `wishlists.ip_address`, `states.name varchar(30)`, `user_addresses.state varchar(225)`.

### D-S8 — `carts.pre_order` is written but stripped

`CartController.php:89` passes `pre_order => 0`. `Cart::$fillable` omits it ⇒ silently dropped ⇒ the column stays `NULL`. Same for `pre_note` (`carts` has no `pre_note` column at all — it lives on `product_varient`).

### D-S9 — Variant stock columns used by checkout do not all exist

`CheckoutController.php:453-457` reads:

```php
$itemWeight = (float) ($item->variant->weight ?? 0.5);
$maxLength  = max($maxLength,  (float) ($item->variant->length  ?? 10));
$maxBreadth = max($maxBreadth, (float) ($item->variant->breadth ?? 10));
$maxHeight  = max($maxHeight,  (float) ($item->variant->height  ?? 10));
```

**`product_varient` has no `weight`, `length`, `breadth`, or `height` column** (verified against the DDL). Every lookup returns `null`, so:

- **total weight is always `0.5 × quantity`**, floored at `0.5` — the courier is quoted for a fixed guessed weight regardless of the product
- dimensions sent to Shiprocket are always `10 × 10 × 10`

**Shipping charges are therefore computed on fictional dimensions.** This is a live pricing defect, not a latent one.

`productstocks` also has no `sku`; the SKU comes from `product_varient.sku` (unique) — which the payload correctly uses at `:445`.

### D-S10 — `sessions` grows unbounded

Default Laravel session table, no scheduled `session:prune`.

### D-S11 — `countries.currency` / `ship_charge` never read

`Country::$fillable` is `['name', 'currency']`. `countries.ship_charge` exists but nothing reads it; the UI always renders `₹`.

---

## 4. Unique indexes that DO exist (19)

| Index | Table |
|---|---|
| `users_email_unique` | `users` |
| `users_phone_unique` | `users` |
| `users_phone_number_unique` | `users` |
| `users_user_id_unique` | `users` |
| `products_slug_unique` | `products` |
| `product_orders_order_number_unique` | `product_orders` |
| `product_varient_sku_unique` | `product_varient` |
| `brands_brand_slug_unique` | `brands` |
| `newcoupons_code_unique` | `newcoupons` |
| `dashboard_users_email_unique` | `dashboard_users` |
| `delivery_people_email_unique`, `delivery_people_phone_number_unique` | `delivery_people` |
| `roles_name_guard_name_unique`, `permissions_name_guard_name_unique` | RBAC |
| `failed_jobs_uuid_unique`, `personal_access_tokens_token_unique`, `telescope_entries_uuid_unique`, `purchase_orders_po_code_unique`, `gift_purchase_orders_po_code_unique` | infra |

### Corrections to earlier assumptions

| Claim | Actual |
|---|---|
| `users.phone_number` has no unique index | **It does** — `users_phone_number_unique` |
| `product_orders.order_number` is not unique | **It is** — `product_orders_order_number_unique` |
| `product_orders.razorpay_order_id` is not unique | Confirmed — **no unique index** |

So `order_number` uniqueness is enforced and `'ORD-' . strtoupper(uniqid())` (`:290`) cannot collide silently — a duplicate insert throws instead. **`razorpay_order_id` remains unconstrained, so the double-submit idempotency gap is real (BUG-21).**

### What is still missing

| Missing constraint | Consequence |
|---|---|
| `UNIQUE(razorpay_order_id)` on `product_orders` | duplicate orders on double-submit |
| `UNIQUE(user_id, product_id)` on `wishlists` | duplicate wishlist rows |
| `UNIQUE(user_id, product_varient_id)` on `carts` | duplicate cart lines |
| `INDEX(user_id)` on `carts`, `wishlists` | full scan on every page view |
| `INDEX(order_id)` on the six order tables | full scan per order lookup |
| `INDEX(country_id)` | moot — `country_id` is `varchar` in most tables |

---

## 5. Data-integrity rules that exist only in PHP

No CHECK constraints anywhere. Rules and where they actually live:

| Rule | Enforced where | Risk |
|---|---|---|
| Quantity ≥ 1 | **nowhere** | negative quantities persist |
| Quantity ≤ available stock | `max(..., 0)` at `:405, 413` | silent oversell |
| Price = `offer_price` | 6 divergent expressions | display/cart/order can disagree |
| Ownership on cart/wishlist writes | 4 of 6 write paths | IDOR |
| Total > 0 | `max(..., 1)` at `:193` only | `:288` has no floor |
| Variant weight matches the product | **nowhere** — column absent | flat 0.5 kg shipping |
| Coupon discount is valid | **nowhere** — no coupon lookup | client-set discount |
| `country_id` is a real country | **nowhere** — `varchar` CSV | `FIND_IN_SET` scans |

---

## 6. Migration status

| Path | Count | Domain tables |
|---|---|---|
| `palVarnan/database/migrations` | 3 | **0** |
| `palVarnanDashboard/database/migrations` | ~57 | ~78 |
| Post-dump additions (dashboard) | 1 | `shiprocket_*`, `razorpay_*`, `awb_code` on `product_orders` |

**No migration anywhere creates `billing_state`, `shipping_state`, `courier_name` on `product_orders`, or `product_image` on `product_order_items`.** These are the S1 checkout failures.

---

## 7. Related documents

- [02-architecture.md](02-architecture.md) — shared-database topology
- [08-business-logic.md](08-business-logic.md) — the rules implemented in PHP, not SQL
- [16-performance.md](16-performance.md) — index and N+1 costs
- [22-database-integrity.md](22-database-integrity.md) — corrective migrations
- [26-data-model-diagram.md](26-data-model-diagram.md) — ER diagrams