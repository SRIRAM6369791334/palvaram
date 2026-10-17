# DATABASE_TABLE_REFERENCE.md

**Scope:** tables touched heavily by dashboard; column-level details derived from SQL dump.  
**Evidence:** `VERIFIED_FROM_LIVE_SCHEMA`, `VERIFIED_FROM_SOURCE`, `INFERRED`, `NOT_AVAILABLE`

> See also: [DATABASE_SCHEMA.md](./DATABASE_SCHEMA.md), [../05-database-schema.md](../05-database-schema.md)

## How to read this

- **Dump source:** `../../palvarnan_db (2).sql` contains 93 `CREATE TABLE` statements — read directly for precision. `VERIFIED_FROM_LIVE_SCHEMA`.
- **Migration-backed:** tables that have corresponding `create_*` migration in `database/migrations/`. `VERIFIED_FROM_SOURCE`.
- **Schema drift:** referenced by code/models but no create migration. `VERIFIED_FROM_SOURCE`, `INFERRED`.

## Core product/catalog tables

### `products` (dump-only; altered by migrations) — `VERIFIED_FROM_LIVE_SCHEMA`, `VERIFIED_FROM_SOURCE`
- Key fields (from dump): `id`, `product_name`, `slug`, `product_sku`, `item_code` (added), `category_id`, `sub_category_id`, `brand_id`, `unit_id`, `product_mrp_price`, `product_regular_price`, `offer_price`, `product_specfication` (note misspelling), `product_specification` (dup), `description`, `country_id`, `warehouse`, `branch`, `deleted_at`, timestamps.
- Altered by: `2026_02_06_105439_add_slug_and_sku_to_products_table`, `2026_02_27_172014_add_deleted_at_to_products_table`, `2026_03_07_114500_add_country_id_to_products_and_variants_table`, `2026_04_20_171849_add_warehouse_and_branch_to_products_table`, `2026_06_25_154915_add_item_code_to_products_table`. `VERIFIED_FROM_SOURCE`.

### `product_varient` (dump-only) — `VERIFIED_FROM_LIVE_SCHEMA`
- Key fields: `id`, `product_id`, `product_sku`, `item_code`, `product_qty`, `low_stock`, `offer_price`, `mrp_price`, `product_gst`, `country_id`, `warehouse`, `branch`, `size_id`/color fields added later (see migration). `INFERRED` (matches model usage [../06-data-model.md](../06-data-model.md)).
- Altered by: `2026_03_07_114500_add_country_id_to_products_and_variants_table`, `2026_04_20_182539_add_warehouse_and_branch_to_product_varient_table`, `2026_06_05_155733_add_size_color_to_product_varient_table`, `2026_06_25_165655_add_item_code_to_product_varient_table`. `VERIFIED_FROM_SOURCE`.

### `productstocks` (dump-only) — `VERIFIED_FROM_LIVE_SCHEMA`
- Fields: `stock_id`, `productid`, `pro_ver_id`, `availablestock`, `overallstock`, `salestock`, `low_stocks`, `last_stockupdate_date`, `country_id`, `warehouse_id`, `branch_id`. `VERIFIED_FROM_LIVE_SCHEMA` (dump). 
- Note FK naming: `productid` (not `product_id`), `pro_ver_id`. `INFERRED`.
- Altered by: `2026_04_20_180937_add_warehouse_and_branch_to_productstocks_table`. `VERIFIED_FROM_SOURCE`.

### `product_child_images`, `web_images` (dump-only) — `VERIFIED_FROM_LIVE_SCHEMA`

## Orders & fulfillment (heavily touched)

### `product_orders` (dump-only) — `VERIFIED_FROM_LIVE_SCHEMA`
- Core: `id`, `order_id` (string e.g. HG-ORD-PR-XXXXXX), `user_id`, `total_amount`, `order_amount`, `payment_status`, `status`, `razorpay_order_id`, `razorpay_payment_id`, `shiprocket_order_id`, `shipment_id`, `awb_code`, `shipping_charge`, `coupon_code`, `discount_amount`, `is_gift`, timestamps and delivery fields. `VERIFIED_FROM_LIVE_SCHEMA`.
- ID generation uses `MAX(id)+1` (race condition noted). `VERIFIED_FROM_SOURCE` ([../07-data-integrity.md](../07-data-integrity.md#order-id-generation--broken)).
- No unique constraint on `order_id` visible from migration set. `INFERRED`.

### `product_order_items` (dump-only) — `VERIFIED_FROM_LIVE_SCHEMA`
- `id`, `order_id` (string FK to order), `product_id`, `product_varient_id`, `quantity`, `price`, `total`, timestamps. `VERIFIED_FROM_LIVE_SCHEMA`.
- Relation: `ProductOrder::items()` uses `order_id` (string) → `ProductOrderItem.order_id` matching `ProductOrder.id`? Model relation is `hasMany(ProductOrderItem::class, 'order_id', 'id')` but `ProductOrder.id` is integer PK while `order_id` is string — mismatch. `VERIFIED_FROM_SOURCE` ([../06-data-model.md](../06-data-model.md#defective-relationships--broken)).

### `product_order_user_addresses` (dump-only) — `VERIFIED_FROM_LIVE_SCHEMA`
- Address snapshot for order; has fields including `area_id`, `country_id`. Note model relation bug: `country()` joins via `area_id`. `VERIFIED_FROM_SOURCE` ([../06-data-model.md](../06-data-model.md#defective-relationships--broken)).

### `product_slots` (dump-only) — `VERIFIED_FROM_LIVE_SCHEMA`
- Slots for products in order context; fields include `order_id`, `product_id`, `product_varient_id`, `quantity`, `is_cancelled`, `status`, etc. `VERIFIED_FROM_LIVE_SCHEMA`.

### `product_tracking`, `product_refunds`, `cancel_requests`, `product_transaction_logs` (dump-only) — `VERIFIED_FROM_LIVE_SCHEMA`
- Tracking, refunds, cancellations, ledger rows. `product_transaction_logs` written after stock deduction (no transaction). `VERIFIED_FROM_SOURCE` ([../07-data-integrity.md](../07-data-integrity.md#transaction-log-written-after-the-fact--incomplete)).

## Milk/subscription

### `milk_orders`, `milk_order_user_addresses`, `milk_slots`, `milk_refunds`, `milk_transaction_logs`, `plan_types` (dump-only) — `VERIFIED_FROM_LIVE_SCHEMA`
- Milk subscription domain; creation sets `payment_status => 1` pre-payment in controller. `VERIFIED_FROM_SOURCE` ([../07-data-integrity.md](../07-data-integrity.md#orders-are-created-as-already-paid--incomplete)).

## Customers/users/addresses

### `users`, `user_addresses` (dump-only) — `VERIFIED_FROM_LIVE_SCHEMA`
- Frontend customer users; `user_addresses` has `email` added via migration `2026_07_17_101613_add_email_to_user_addresses_table`. `VERIFIED_FROM_SOURCE`.

### `dashboard_users` (dump-only) — `VERIFIED_FROM_LIVE_SCHEMA`
- Authenticatable admin/provider; `country_id` is **JSON array** (cast to array in model), consumed via `json_decode`. `VERIFIED_FROM_SOURCE` ([../05-database-schema.md](../05-database-schema.md#multi-country-schema-evolution), [../06-data-model.md](../06-data-model.md#multi-country-representation--verified)).
- Wired to `config/auth.php` as web provider. `VERIFIED_FROM_SOURCE` ([../06-data-model.md](../06-data-model.md#authenticatable-models)).

### `delivery_people` (dump-only) — `VERIFIED_FROM_LIVE_SCHEMA`
- Delivery persons; not wired for auth despite having password fields. `VERIFIED_FROM_SOURCE` ([../06-data-model.md](../06-data-model.md#authenticatable-models)).

### `areas`, `area_assigns`, `cities`, `states`, `districts`, `countries`, `address_types`, `gender_types`, `all_india_pincodes`, `state_list`, `city_list`, `country_tables` (mixed) — `VERIFIED_FROM_LIVE_SCHEMA`, `VERIFIED_FROM_SOURCE`
- Geography/address lookup tables; `districts`, `countries` have create migrations; others mostly dump-only. `VERIFIED_FROM_SOURCE`.

## Inventory/warehouse/purchase

### `warehouse`, `warehouse_branch` (migration-created) — `VERIFIED_FROM_SOURCE`
- Created by migrations; address fields added later. `VERIFIED_FROM_SOURCE`.

### `stock_transfers`, `stock_distributions`, `purchase_orders`, `purchase_order_items`, `product_stock_batches`, `supplier_details`, `gift_*` purchase orders/items, `gift_color`, `gift_size` — `VERIFIED_FROM_SOURCE` (most created by migrations; some altered). `VERIFIED_FROM_LIVE_SCHEMA`.

## Promotions/content

### `banners/banner_images`, `offer_images`, `coupons`, `newcoupons`, `today_deals`, `home_promotions`, `scrolling_bars`, `reviews`, `wishlists`, `testimonials`, `free_shipping_settings`, `delivery_charge_details` (mixed) — `VERIFIED_FROM_LIVE_SCHEMA`, `VERIFIED_FROM_SOURCE`

## Notifications/OTP

### `app_alerts`, `app_notifications`, `notifications`, `user_read_notifications`, `mail_otps`, `otps` (dump-only) — `VERIFIED_FROM_LIVE_SCHEMA`
- Note: `AppAlert::getCreatedAtAttribute()` overrides to return relative time string. `VERIFIED_FROM_SOURCE` ([../06-data-model.md](../06-data-model.md#overridden-created_at--verified)).

## Framework/Spatie/telescope/cache

### `permissions`, `roles`, `model_has_permissions`, `model_has_roles`, `role_has_permissions`, `personal_access_tokens`, `sessions`, `cache`, `cache_locks`, `jobs`, `job_batches`, `failed_jobs`, `password_resets`, `password_reset_tokens`, `migrations`, `telescope_*`, `tests` (some migration-created) — `VERIFIED_FROM_SOURCE`, `VERIFIED_FROM_LIVE_SCHEMA`

## Schema drift flags

- **Tables with no create migration in repo (examples):** `products`, `product_varient`, `productstocks`, `product_orders`, `product_order_items`, `product_slots`, `users`, `dashboard_users`, `carts`, `wishlists`, `categories`, `brands`, `units`, `areas`, `states`, `cities`, `coupons`, etc. — full list in [../05-database-schema.md](../05-database-schema.md#schema-drift--broken). `VERIFIED_FROM_SOURCE`.
- **FK naming inconsistencies:** `productstocks.productid` (not `product_id`), `product_varient.categoryid`/`subcategoryid` appear in some contexts; model FKs use mixed naming. `INFERRED` ([../06-data-model.md](../06-data-model.md#naming-conventions--incomplete)).
- **Money/quantities as strings:** no casts; arithmetic is string-coerced. `VERIFIED_FROM_SOURCE` ([../06-data-model.md](../06-data-model.md#casts--broken), [../07-data-integrity.md](../07-data-integrity.md#money-is-computed-as-strings--broken)).
- **order_id string vs PK int:** `ProductOrderItem.order_id` string references `ProductOrder` (int id) in relation definition. `VERIFIED_FROM_SOURCE` ([../06-data-model.md](../06-data-model.md#defective-relationships--broken)).
- **country_id type asymmetry:** scalar vs JSON array (`dashboard_users`). `VERIFIED_FROM_SOURCE`.

## Related docs

- [DATABASE_SCHEMA.md](./DATABASE_SCHEMA.md)
- [DATABASE_RELATIONSHIPS.md](./DATABASE_RELATIONSHIPS.md)
- [MODEL_REFERENCE.md](./MODEL_REFERENCE.md)
- [../05-database-schema.md](../05-database-schema.md)