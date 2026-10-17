# DATABASE_SCHEMA.md

**Shared schema:** `palvarnan_db`  
**Evidence:** `VERIFIED_FROM_LIVE_SCHEMA`, `VERIFIED_FROM_SOURCE`

## Overview

- **Database name:** `palvarnan_db` — same MySQL schema used by both Project A (`palVarnanDashboard`) and Project B.  
  `VERIFIED_FROM_LIVE_SCHEMA` ([SQL dump](../../palvarnan_db%20(2).sql)), `CONFIRMED` cross-project usage ([../05-database-schema.md](../05-database-schema.md#consequence), [../audit/VERIFIED_FACTS.md](../audit/VERIFIED_FACTS.md#cross-project-integration-confirmed)).
- **Tables in SQL dump:** **93** `CREATE TABLE` statements — `VERIFIED_FROM_LIVE_SCHEMA` (grep of dump).
- **Migration files in Project A:** **60** files (`database/migrations/`) — `VERIFIED_FROM_SOURCE` ([../05-database-schema.md](../05-database-schema.md#migration-inventory)).
- **Coverage:** migrations create **~25** tables; the remaining tables exist in the dump with **no corresponding `create_*` migration** in this repository — this is **schema drift**. `VERIFIED_FROM_SOURCE`, `VERIFIED_FROM_LIVE_SCHEMA`.

## Schema ownership & cross-project usage

- The canonical schema for shared entities (`products`, `product_varient`, `product_orders`, `product_order_items`, `product_slots`, `carts`, `wishlists`, `users`, etc.) is effectively owned/defined by Project A’s migration history and the live dump; Project B reads/writes the same schema. `CONFIRMED` ([../audit/VERIFIED_FACTS.md](../audit/VERIFIED_FACTS.md#cross-project-integration-confirmed)).
- Project A runs the scheduler (`shiprocket:sync` via `app/Console/Kernel.php`); Project B runs with `queue=sync` and has no scheduler — operational split, shared data model. `INFERRED` from audit notes.

## Migration-backed vs dump-only (summary)

- **Migration-created tables (subset):** `districts`, `sub_categories`, `home_promotions`, `today_deals`, `scrolling_bars`, `brands`, `units`, `countries`, `warehouse`, `warehouse_branch`, `stock_transfers`, `purchase_orders`, `purchase_order_items`, `product_stock_batches`, `stock_distributions`, `supplier_details`, `gift_size`, `gift_color`, `gift_purchase_orders`, `gift_purchase_order_items` plus Spatie permission tables (`permissions`, `roles`, `model_has_permissions`, `model_has_roles`, `role_has_permissions`) and `personal_access_tokens`, framework tables like `migrations`, etc. `VERIFIED_FROM_SOURCE` ([../05-database-schema.md](../05-database-schema.md#tables-created-by-migrations)).
- **Dump-only tables (many):** core catalogue/customers (`products`, `product_varient`, `product_child_images`, `web_images`, `categories`, `users`, `user_addresses`, `dashboard_users`, `delivery_people`, `areas`, `area_assigns`, `cities`, `states`, `countries`, `address_types`, `gender_types`, `contacts`, `contact_details`, `all_india_pincodes`, `state_list`, `city_list`, `country_tables`), orders/fulfillment (`product_orders`, `product_order_items`, `product_order_user_addresses`, `product_slots`, `product_tracking`, `product_refunds`, `cancel_requests`, `carts`, `bulkorders`, `onetimes`, `coupons`, `newcoupons`, `reviews`, `wishlists`, `testimonials`, `free_shipping_settings`, `delivery_charge_details`, `product_transaction_logs`), milk/subscription (`milk_orders`, `milk_slots`, `milk_refunds`, `milk_order_user_addresses`, `milk_transaction_logs`, `plan_types`), promotions/content (`banner_images`, `offer_images`, `app_alerts`, `app_notifications`, `notifications`, `user_read_notifications`, `mail_otps`, `otps`), inventory (`productstocks`), and framework tables (`sessions`, `cache`, `cache_locks`, `jobs`, `job_batches`, `failed_jobs`, `password_resets`, `password_reset_tokens`, `tests`, `telescope_*`). `VERIFIED_FROM_LIVE_SCHEMA`, `VERIFIED_FROM_SOURCE`.

## Consequence of drift

- `php artisan migrate:fresh` cannot reproduce this schema. Recovery requires either generating migrations from live DB (`kitloong/laravel-migrations-generator`) or applying the SQL dump and then running migrations; both have caveats. `VERIFIED_FROM_SOURCE` ([../05-database-schema.md](../05-database-schema.md#consequence)).
- Do not run `migrate:fresh` against any environment with real data; rollback path is not reliable. `VERIFIED_FROM_SOURCE`.

## Key schema notes

- **Multi-country evolution:** `country_id` added to multiple tables via migrations (Mar–Jun 2026). `dashboard_users.country_id` is stored as **JSON array** (not scalar) and consumed via `json_decode`; this is the only country-scoping mechanism on write paths. `VERIFIED_FROM_SOURCE` ([../05-database-schema.md](../05-database-schema.md#multi-country-schema-evolution), [../06-data-model.md](../06-data-model.md#multi-country-representation--verified)).
- **Warehouse/branch expansion:** warehouse/branch columns threaded through catalogue and `stock_transfers` branch IDs made nullable in some cases. `VERIFIED_FROM_SOURCE` ([../05-database-schema.md](../05-database-schema.md#warehouse--branch-expansion)).
- **Soft deletes:** only `products` has `deleted_at` column/migration. `VERIFIED_FROM_SOURCE` ([../05-database-schema.md](../05-database-schema.md#soft-deletes)).
- **Data type asymmetry:** money columns returned as strings; `country_id` is scalar in most tables but JSON array in `dashboard_users`. `VERIFIED_FROM_SOURCE`, `INFERRED` ([../05-database-schema.md](../05-database-schema.md#data-types-worth-flagging), [../06-data-model.md](../06-data-model.md#casts--broken)).

## Related docs

- [../05-database-schema.md](../05-database-schema.md) — detailed schema notes (drift list, multi-country, warehouse)
- [../06-data-model.md](../06-data-model.md) — models/casts/relations
- [../07-data-integrity.md](../07-data-integrity.md) — transactional/integrity issues
- [../16-external-integrations.md](../16-external-integrations.md) — external integrations (Shiprocket) context
