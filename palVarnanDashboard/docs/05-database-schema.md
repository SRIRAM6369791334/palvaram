# 05 — Database Schema

## Sources consulted

`VERIFIED`

| Source | Notes |
| --- | --- |
| `database/migrations/` | 60 migration files, dated 2023-05-17 through 2026-08-17. |
| `schema_output.txt` | 3,886-byte `var_dump` of the `products` table columns only. Committed to the repo root. |
| `..\palvarnan_db (2).sql` | 26.05 MB dump with **93 `CREATE TABLE` statements**. Out-of-tree but the most complete schema reference. |

No live database connection was used; the audit did not execute queries.

## Migration inventory

`VERIFIED` — 60 files. Only **20** of them create a table; the remaining 40 are `Schema::table(...)`
alterations of tables that migrations never create.

### Tables created by migrations

| Table | Migration |
| --- | --- |
| `districts` | `2023_05_17_130506_create_districts_table` |
| `sub_categories` | `2024_02_13_150932_create_sub_categories_table` |
| `home_promotions` | `2024_02_20_000000_create_home_promotions_table` |
| `today_deals` | `2024_02_27_115933_create_today_deals_table` |
| `scrolling_bars` | `2025_07_03_194813_create_scrolling_bars_table` |
| `brands` | `2026_02_06_105540_create_brands_table` |
| `units` | `2026_02_06_105614_create_units_table` |
| `countries` | `2026_03_07_113011_create_countries_table` |
| `warehouse` | `2026_04_15_174839_create_warehouse_table` |
| `warehouse_branch` | `2026_04_15_183751_create_warehouse_branch_table` |
| `stock_transfers` | `2026_04_29_094831_create_stock_transfers_table` |
| `purchase_orders` | `2026_04_29_101917_create_purchase_orders_table` |
| `purchase_order_items` | `2026_04_29_103520_create_purchase_order_items_table` |
| `product_stock_batches` | `2026_05_06_115609_create_product_stock_batches_table` |
| `stock_distributions` | `2026_05_11_162832_create_stock_distributions_table` |
| `supplier_details` | `2026_05_18_095226_create_supplier_details_table` |
| `gift_size` | `2026_06_05_125439_create_gift_size_table` |
| `gift_color` | `2026_06_05_144131_create_gift_color_table` |
| `gift_purchase_orders` | `2026_06_25_180528_create_gift_purchase_orders_table` |
| `gift_purchase_order_items` | `2026_06_25_180603_create_gift_purchase_order_items_table` |

Plus the Spatie permission tables from `2026_04_06_124404_create_permission_tables`:
`permissions`, `roles`, `model_has_permissions`, `model_has_roles`, `role_has_permissions`.

## Schema drift — `BROKEN`

`VERIFIED` — the SQL dump declares **93 tables**; migrations create **~25**. The following tables
exist in the database dump, are queried by controllers and models, and have **no create migration**
in this repository:

**Core catalogue & customers**
`products`, `product_varient`, `product_thum`/`web_images`, `categories`, `brands`, `units`,
`users`, `user_addresses`, `dashboard_users`, `delivery_people`, `areas`, `area_assigns`,
`countries`, `states`, `cities`, `districts`, `address_types`, `gender_types`, `contact_details`,
`contacts`, `all_india_pincodes`, `state_list`, `city_list`, `country_tables`.

**Orders & fulfilment**
`product_orders`, `product_order_items`, `product_order_user_addresses`, `product_slots`,
`product_tracking`, `product_refunds`, `cancel_requests`, `carts`, `bulkorders`, `onetimes`,
`coupons`, `newcoupons`, `reviews`, `wishlists`, `testimonials`, `free_shipping_settings`,
`delivery_charge_details`, `product_transaction_logs`.

**Milk / subscription**
`milk_orders`, `milk_slots`, `milk_refunds`, `milk_order_user_addresses`,
`milk_transaction_logs`, `plan_types`, `product_child_images`.

**Promotions & content**
`banner_images`, `offer_images`, `app_alerts`, `app_notifications`, `notifications`,
`user_read_notifications`, `mail_otps`, `otps`.

**Inventory (partially missing)**
`productstocks`, `products` — both are the target of **40** `Schema::table` migrations yet are never
created by one.

**Permissions & framework**
`permissions`, `roles`, `model_has_permissions`, `model_has_roles`, `role_has_permissions`,
`personal_access_tokens`, `sessions`, `cache`, `cache_locks`, `jobs`, `job_batches`, `failed_jobs`,
`password_resets`, `password_reset_tokens`, `tests`, `migrations`, `telescope_entries`,
`telescope_entries_tags`, `telescope_monitoring`.

**Unused / template tables**
`plan_types`, `onetimes`, `country_tables`, `tests`.

### Consequence

`php artisan migrate:fresh` on an empty database **cannot** reproduce this schema. A new developer
following the standard Laravel bootstrap will get a broken application. Recovery paths, in order of
preference:

1. `INCOMPLETE` — Generate migrations from the live database with the already-installed
   `kitloong/laravel-migrations-generator`, then review and squash.
2. `UNVERIFIED` — Apply `..\palvarnan_db (2).sql` directly, then run `php artisan migrate` and check
   which alter-migrations fail against already-present columns.

**Do not run `migrate:fresh` against any environment holding real data.** There is no reliable
rollback path given the drift.

## Multi-country schema evolution

`VERIFIED` — a `country_id` column was added to core tables across five migrations in April–June
2026:

| Migration | Target |
| --- | --- |
| `2026_03_07_114500_add_country_id_to_products_and_variants_table` | `products`, `product_varient` |
| `2026_04_15_123905_add_country_id_to_home_promotions` | `home_promotions` |
| `2026_04_15_151424_add_country_id_to_dashboard_users_table` | `dashboard_users` |
| `2026_04_20_104250_add_country_id_to_categories_table` | `categories` |
| `2026_04_20_122401_add_country_id_to_sub_categories_table` | `sub_categories` |
| `2026_04_20_130519_add_country_id_to_brands_table` | `brands` |
| `2026_05_18_173119_add_country_id_to_units_table` | `units` |
| `2026_06_25_115825_add_country_id_to_supplier_details_table` | `supplier_details` |

`VERIFIED` — `dashboard_users.country_id` stores a **JSON array**, not a scalar. Consumed via
`json_decode(..., true)` in `DashboardUserController::getAllowedCountryIds()`. This is the
multi-country authorisation primitive, and it is the only country-scoping mechanism found on a
write path.

## Warehouse / branch expansion

`VERIFIED` — warehouse and branch columns were threaded through the catalogue in three migrations:

| Migration | Target |
| --- | --- |
| `2026_04_20_171849_add_warehouse_and_branch_to_products_table` | `products` |
| `2026_04_20_180937_add_warehouse_and_branch_to_productstocks_table` | `productstocks` |
| `2026_04_20_182539_add_warehouse_and_branch_to_product_varient_table` | `product_varient` |

`VERIFIED` — `2026_05_12_122252_make_branch_ids_nullable_in_stock_transfers` makes branch IDs
nullable on `stock_transfers`, encoding the warehouse↔store duality that
`StockController::transferStock()` / `warehouseTransfer()` branch on.

## Soft deletes

`VERIFIED` — `2026_02_27_172014_add_deleted_at_to_products_table` adds `deleted_at` to `products`.
No other table has soft deletes. Whether `Product` actually uses `SoftDeletes` is covered in
[06-data-model.md](06-data-model.md).

## Data types worth flagging

`UNVERIFIED` from the dump's `var_dump` output, `VERIFIED` from model/controller usage:

- Money columns (`product_mrp_price`, `offer_price`, `total_amount`, `transfer_amount`) are read
  and written without casts — see [06-data-model.md](06-data-model.md) and
  [07-data-integrity.md](07-data-integrity.md).
- `country_id` is polymorphic across scalar integer (most tables) and JSON array
  (`dashboard_users`). This asymmetry is a recurring source of bugs.

## Committed schema artifact

`BROKEN` — `schema_output.txt` at the repo root contains a `var_dump` of the `products` column
list, including the duplicated pair `product_specification` and `product_specfication` (note the
misspelling, which is in the live schema, not the dump). It should not be in version control.