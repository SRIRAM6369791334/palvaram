# MIGRATION_AND_SQL_AUDIT.md

**Sources:** `database/migrations/*.php` (60 files), SQL dump `../../palvarnan_db (2).sql` (93 tables), repo root diagnostic scripts.  
**Evidence:** `VERIFIED_FROM_SOURCE`, `VERIFIED_FROM_LIVE_SCHEMA`, `INFERRED`, `NOT_AVAILABLE`, `CONFLICTING_EVIDENCE`

> See also: [DATABASE_SCHEMA.md](./DATABASE_SCHEMA.md), [../05-database-schema.md](../05-database-schema.md)

## Migration vs SQL dump

- **Migrations:** **60** files (2023-05-17 through 2026-08-17). `VERIFIED_FROM_SOURCE` ([../05-database-schema.md](../05-database-schema.md#migration-inventory)).
- **Tables created by migrations:** **~25** (including Spatie permission tables). The rest are `Schema::table(...)` alterations. `VERIFIED_FROM_SOURCE`.
- **SQL dump:** **93** `CREATE TABLE` statements. `VERIFIED_FROM_LIVE_SCHEMA` (grep of dump).
- **Conclusion:** Only a subset of tables are migration-backed; 68+ tables exist in dump with **no corresponding `create_*` migration** in this repo — **schema drift**. `VERIFIED_FROM_SOURCE`, `VERIFIED_FROM_LIVE_SCHEMA`.

## Migration-backed tables (created)

Key create migrations:
- `2023_05_17_130506_create_districts_table.php` → `districts`
- `2024_02_13_150932_create_sub_categories_table.php` → `sub_categories`
- `2024_02_20_000000_create_home_promotions_table.php` → `home_promotions`
- `2024_02_27_115933_create_today_deals_table.php` → `today_deals`
- `2025_07_03_194813_create_scrolling_bars_table.php` → `scrolling_bars`
- `2026_02_06_105540_create_brands_table.php` → `brands`
- `2026_02_06_105614_create_units_table.php` → `units`
- `2026_03_07_113011_create_countries_table.php` → `countries`
- `2026_04_15_174839_create_warehouse_table.php` → `warehouse`
- `2026_04_15_183751_create_warehouse_branch_table.php` → `warehouse_branch`
- `2026_04_29_094831_create_stock_transfers_table.php` → `stock_transfers`
- `2026_04_29_101917_create_purchase_orders_table.php` → `purchase_orders`
- `2026_04_29_103520_create_purchase_order_items_table.php` → `purchase_order_items`
- `2026_05_06_115609_create_product_stock_batches_table.php` → `product_stock_batches`
- `2026_05_11_162832_create_stock_distributions_table.php` → `stock_distributions`
- `2026_05_18_095226_create_supplier_details_table.php` → `supplier_details`
- `2026_06_05_125439_create_gift_size_table.php` → `gift_size`
- `2026_06_05_144131_create_gift_color_table.php` → `gift_color`
- `2026_06_25_180528_create_gift_purchase_orders_table.php` → `gift_purchase_orders`
- `2026_06_25_180603_create_gift_purchase_order_items_table.php` → `gift_purchase_order_items`
- `2026_04_06_124404_create_permission_tables.php` → `permissions`, `roles`, `model_has_permissions`, `model_has_roles`, `role_has_permissions`
- `2019_12_14_000001_create_personal_access_tokens_table.php` exists in Laravel base; present in repo context as per typical Laravel 8 install? Check: `database/migrations/` has 60 files including Spatie; `personal_access_tokens` appears in dump/framework tables. `INFERRED`.

Full list: [../05-database-schema.md](../05-database-schema.md#tables-created-by-migrations).

## Tables NOT backed by create migrations (selected, representative)

From drift analysis: `products`, `product_varient`, `productstocks`, `product_orders`, `product_order_items`, `product_slots`, `product_order_user_addresses`, `product_tracking`, `product_refunds`, `cancel_requests`, `product_transaction_logs`, `carts`, `wishlists`, `bulkorders`, `onetimes`, `coupons`, `newcoupons`, `reviews`, `testimonials`, `banner_images`, `offer_images`, `free_shipping_settings`, `delivery_charge_details`, `milk_orders`, `milk_slots`, `milk_refunds`, `milk_order_user_addresses`, `milk_transaction_logs`, `plan_types`, `users`, `user_addresses`, `dashboard_users`, `delivery_people`, `areas`, `area_assigns`, `categories`, `sub_categories` (created but also heavily altered; created migration exists), `brands` (created), `units` (created), `cities`, `states`, `districts` (created), `countries` (created), `address_types`, `gender_types`, `contacts`, `contact_details`, `all_india_pincodes`, `state_list`, `city_list`, `country_tables`, `notifications`, `app_notifications`, `app_alerts`, `user_read_notifications`, `mail_otps`, `otps`, `sessions`, `cache`, `cache_locks`, `jobs`, `job_batches`, `failed_jobs`, `password_resets`, `password_reset_tokens`, `tests`, `telescope_*`. `VERIFIED_FROM_SOURCE`, `VERIFIED_FROM_LIVE_SCHEMA`.

## Multi-country & structural migrations (notable)

- `2026_03_07_114500_add_country_id_to_products_and_variants_table`
- `2026_04_15_123905_add_country_id_to_home_promotions`
- `2026_04_15_151424_add_country_id_to_dashboard_users_table` (makes country_id JSON array contextually)
- `2026_04_20_104250_add_country_id_to_categories_table`
- `2026_04_20_122401_add_country_id_to_sub_categories_table`
- `2026_04_20_130519_add_country_id_to_brands_table`
- `2026_04_20_171849_add_warehouse_and_branch_to_products_table`
- `2026_04_20_180937_add_warehouse_and_branch_to_productstocks_table`
- `2026_04_20_182539_add_warehouse_and_branch_to_product_varient_table`
- `2026_05_12_122252_make_branch_ids_nullable_in_stock_transfers`
- `2026_05_18_173119_add_country_id_to_units_table`
- `2026_06_25_115825_add_country_id_to_supplier_details_table`
- `2026_02_27_172014_add_deleted_at_to_products_table` (only soft delete)
- Spatie: `2026_04_06_124404_create_permission_tables`

`VERIFIED_FROM_SOURCE`.

## Implications for schema reproduction

- `php artisan migrate:fresh` on empty DB cannot reproduce 93-table schema. `VERIFIED_FROM_SOURCE`.
- Recommended: generate migrations from live DB (`kitloong/laravel-migrations-generator`) then review/squash, or apply SQL dump then run migrations and handle alter-migrations that may fail on already-present columns. `VERIFIED_FROM_SOURCE` ([../05-database-schema.md](../05-database-schema.md#consequence)).
- **Do not run `migrate:fresh` against any environment holding real data.** No reliable rollback path given drift. `VERIFIED_FROM_SOURCE`.

## Repo root diagnostic scripts (risk assessment)

Identified at repo root (per audit):  
`check_db_*.php`, `dump_order.php`, `seed_promotions.php`, `schema_output.txt`, `tmp_schema_check.php`, `test.php`  
`VERIFIED_FROM_SOURCE` ([../audit/VERIFIED_FACTS.md](../audit/VERIFIED_FACTS.md#rules-for-writers) context; also [../05-database-schema.md](../05-database-schema.md#committed-schema-artifact)).

### Specific artifacts found

- `schema_output.txt` (root): `var_dump` of `products` column list; includes duplicated pair `product_specification` and `product_specfication` (misspelling present in live schema). Committed artifact — **should not be in version control** (contains schema structure detail; low sensitivity but not needed). `VERIFIED_FROM_SOURCE` ([../05-database-schema.md](../05-database-schema.md#committed-schema-artifact)).
- Other root PHP scripts: diagnostic/one-off utilities (`check_db_*.php`, `dump_order.php`, `tmp_schema_check.php`, `test.php`, `seed_promotions.php`). `INFERRED` from audit text.

### Risk: credentials/PII exposure

- **Pattern risk:** diagnostic scripts may contain hardcoded DB credentials, API keys (Shiprocket/Razorpay), or print query results/PII. `INFERRED` (general risk). 
- **Known hardcoded credentials elsewhere (NOT in these root scripts per instruction — do not reproduce):** Shiprocket login payloads (email+password in plaintext JSON) in controllers: `ProductController.php` lines 869, 1010; `ProductOrdersController.php` line 110; `ProductSlotController.php` lines 131, 202. `VERIFIED_FROM_SOURCE` ([../audit/VERIFIED_FACTS.md](../audit/VERIFIED_FACTS.md#hardcoded-credentials--critical-values-not-reproduced)). Root scripts have not been read line-by-line in this pass; they are **tracked files** — treat as potential secrets/PII. `UNVERIFIED`.

### Recommendation

- **Do NOT reproduce values** from any root PHP diagnostic script. `VERIFIED_FROM_SOURCE` (rule).
- Review each: `check_db_*.php`, `dump_order.php`, `seed_promotions.php`, `schema_output.txt`, `tmp_schema_check.php`, `test.php` for hardcoded credentials/PII; consider removing from VCS or moving to local-only (gitignore) if they are ad-hoc diagnostics. `INFERRED`.
- Ensure no credentials end up in `docs/reference/` (this document follows that rule). `VERIFIED_FROM_SOURCE`.

## Migration coverage summary table

| Aspect | Count | Evidence |
|---|---|---|
| Migration files | 60 | `VERIFIED_FROM_SOURCE` |
| Tables created by migrations | ~25 | `VERIFIED_FROM_SOURCE` |
| Tables in SQL dump | 93 | `VERIFIED_FROM_LIVE_SCHEMA` |
| Migration-backed subset | ~27% | `INFERRED` |

## Related docs

- [DATABASE_SCHEMA.md](./DATABASE_SCHEMA.md)
- [../05-database-schema.md](../05-database-schema.md)
- [../13-security-findings.md](../13-security-findings.md) (for credential findings) — cross-link as appropriate