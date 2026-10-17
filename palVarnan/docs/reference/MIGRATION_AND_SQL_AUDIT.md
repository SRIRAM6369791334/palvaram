# MIGRATION_AND_SQL_AUDIT — Migrations vs SQL dump drift

> Audits the three stock Laravel migrations in `palVarnan/database/migrations/` against the
> 93-table baseline `palvarnan_db (2).sql`, identifies schema drift (columns/models vs dump),
> documents risk from root scripts `test_apis.php` and `test_services.php`, and traces
> migration bookkeeping.
>
> Evidence vocabulary: `VERIFIED_FROM_SOURCE`, `VERIFIED_FROM_LIVE_SCHEMA`, `INFERRED`,
> `UNVERIFIED`, `NOT_AVAILABLE`, `CONFLICTING_EVIDENCE`. **No credentials, connection
> strings, or PII are reproduced here.**
>
> See also: [`DATABASE_SCHEMA.md`](./DATABASE_SCHEMA.md), [`DATABASE_TABLE_REFERENCE.md`](./DATABASE_TABLE_REFERENCE.md),
> [`DATABASE_RELATIONSHIPS.md`](./DATABASE_RELATIONSHIPS.md), [`MODEL_REFERENCE.md`](./MODEL_REFERENCE.md),
> pre-existing [`../22-database-integrity.md`](../22-database-integrity.md), [`../audit/VERIFIED_FACTS.md`](../audit/VERIFIED_FACTS.md).

---

## 1. Scope and authority

- Baseline dump: `palvarnan_db (2).sql` — **93 `CREATE TABLE` statements**, **34 FK constraints**, **19 unique keys** ([header 1-22](../../palvarnan_db%20(2).sql#L1-L22), [indexes/constraints 167670-168934](../../palvarnan_db%20(2).sql#L167670-L168934)). `VERIFIED_FROM_LIVE_SCHEMA`.
- Storefront migrations: only **3 files** in `palVarnan/database/migrations/`. `VERIFIED_FROM_SOURCE`.
- The dump's `migrations` table lists migration filenames that do **not** exist in this repository ([164807-164835](../../palvarnan_db%20(2).sql#L164807-L164835)). `VERIFIED_FROM_LIVE_SCHEMA`.
- Domain schema (~84 tables) is owned elsewhere (per [`../22-database-integrity.md`](../22-database-integrity.md) §2, the dashboard project owns migrations). `INFERRED`.

---

## 2. The three stock migrations

| File | Purpose | Tables created | Evidence |
|---|---|---|---|
| [`0001_01_01_000000_create_users_table.php`](../../palVarnan/database/migrations/0001_01_01_000000_create_users_table.php) | Laravel auth scaffolding | `users` (core auth fields), `password_reset_tokens`, `sessions` | [lines 14-37](../../palVarnan/database/migrations/0001_01_01_000000_create_users_table.php#L14-L37) `VERIFIED_FROM_SOURCE` |
| [`0001_01_01_000001_create_cache_table.php`](../../palVarnan/database/migrations/0001_01_01_000001_create_cache_table.php) | Framework cache | `cache`, `cache_locks` | [lines 14-24](../../palVarnan/database/migrations/0001_01_01_000001_create_cache_table.php#L14-L24) `VERIFIED_FROM_SOURCE` |
| [`0001_01_01_000002_create_jobs_table.php`](../../palVarnan/database/migrations/0001_01_01_000002_create_jobs_table.php) | Queue | `jobs`, `job_batches`, `failed_jobs` | [lines 14-45](../../palVarnan/database/migrations/0001_01_01_000002_create_jobs_table.php#L14-L45) `VERIFIED_FROM_SOURCE` |

**Total tables created by these 3 migrations:** 9 (plus `migrations` bookkeeping table which Laravel manages). **Zero commerce/domain tables** (`products`, `product_orders`, `product_varient`, `productstocks`, `categories`, `sub_categories`, `brands`, carts/wishlists, etc.). `VERIFIED_FROM_SOURCE`.

**Conclusion:** `php artisan migrate:fresh` in this repository **cannot rebuild the commerce schema**. `INFERRED` (direct consequence of the above).

---

## 3. Drift between code/models and dump (snapshot vs live shape)

The dump is a snapshot (2026-07-31). Code writes columns that do **not** exist in dump DDL; models declare `$fillable` for columns not present in dump. These are treated as `CONFLICTING_EVIDENCE`.

| Table | Drift | Evidence | Tag |
|---|---|---|---|
| `product_orders` | Model fillable includes `billing_state`, `shipping_state`, `courier_name`, `shiprocket_order_id`, `shiprocket_shipment_id`, `awb_code`, `pickup_scheduled`, `manifest_url`, `label_url`, `invoice_url`, `shiprocket_status`, `cancellation_reason` — **absent from dump DDL**. Code writes `billing_state`, `shipping_state`, `courier_name` ([CheckoutController.php:306,315,321](../../palVarnan/app/Http/Controllers/CheckoutController.php#L306-L321)). `discount` not in fillable but claimed in docs. | [ProductOrder.php:18-63](../../palVarnan/app/Models/ProductOrder.php#L18-L63), [CheckoutController.php:306,315,321](../../palVarnan/app/Http/Controllers/CheckoutController.php#L306-L321), dump [165700-165765](../../palvarnan_db%20(2).sql#L165700-L165765) | `CONFLICTING_EVIDENCE` |
| `product_order_items` | Model fillable has `product_image` ([ProductOrderItem.php:19](../../palVarnan/app/Models/ProductOrderItem.php#L19)); code writes it ([CheckoutController.php:378](../../palVarnan/app/Http/Controllers/CheckoutController.php#L378)); **column absent from dump DDL**. | [ProductOrderItem.php:19](../../palVarnan/app/Models/ProductOrderItem.php#L19), [CheckoutController.php:378](../../palVarnan/app/Http/Controllers/CheckoutController.php#L378), dump [165766-165812](../../palvarnan_db%20(2).sql#L165766-L165812) | `CONFLICTING_EVIDENCE` |
| `products` | Code reads fallback `product_price` ([CheckoutController.php:367](../../palVarnan/app/Http/Controllers/CheckoutController.php#L367)); no such column in model fillable or dump. Model fillable includes `offer_price`, `mrp_price`, `product_mrp_price`, `product_regular_price`, `product_image` etc. | [CheckoutController.php:367](../../palVarnan/app/Http/Controllers/CheckoutController.php#L367), [Product.php:12-15](../../palVarnan/app/Models/Product.php#L12-L15), dump [165565-165622](../../palvarnan_db%20(2).sql#L165565-L165622) | `CONFLICTING_EVIDENCE` |
| `sub_categories` | Model relation uses `category_id` ([SubCategory.php:11-14](../../palVarnan/app/Models/SubCategory.php#L11-L14)) but **no `category_id` column** in dump (has `category_name`). | [SubCategory.php:11-14](../../palVarnan/app/Models/SubCategory.php#L11-L14), dump [166602-166645](../../palvarnan_db%20(2).sql#L166602-L166645) | `CONFLICTING_EVIDENCE` |
| `product_varient` | Model relation `category()` defaults to `category_id` ([ProductVarient.php:23-25](../../palVarnan/app/Models/ProductVarient.php#L23-L25)) but actual column is `categoryid`. | [ProductVarient.php:23-25](../../palVarnan/app/Models/ProductVarient.php#L23-L25), dump [166047-166407](../../palvarnan_db%20(2).sql#L166047-L166407) | `CONFLICTING_EVIDENCE` |
| `user_addresses` | Model `country()` relation uses `area_id` ([UserAddress.php:47-50](../../palVarnan/app/Models/UserAddress.php#L47-L50)) — wrong FK target. | [UserAddress.php:47-50](../../palVarnan/app/Models/UserAddress.php#L47-L50) | `CONFLICTING_EVIDENCE` |

**Implication:** The live schema may have been altered (e.g. added Shiprocket/Razorpay columns, added `product_image` to `product_order_items`, added states to `product_orders`) since the dump (2026-07-31). The dump remains the **authoritative baseline** for this reference set; conflicts are explicitly tagged. `CONFLICTING_EVIDENCE`.

---

## 4. Migration bookkeeping (`migrations` table)

Dump contains `migrations` table ([164807-164835](../../palvarnan_db%20(2).sql#L164807-L164835)) listing migration filenames (e.g. `2014_10_12_000000_create_users_table`, `2023_04_04_162840_create_otps_table`, `2023_04_15_072341_create_categories_table`, `2023_04_17_044323_create_products_table`, etc.) with batch numbers. **Many of these files do not exist in `palVarnan/database/migrations/`**. `VERIFIED_FROM_LIVE_SCHEMA`.

This confirms:
- Commerce migrations were created/run in another project/environment (dashboard) against the same DB.
- This repo’s 3 migrations are the only Laravel framework migrations present here.
- The `migrations` table is shared state in the same database.

See [`../22-database-integrity.md`](../22-database-integrity.md) for dashboard-owned migrations context. `INFERRED`.

---

## 5. Root scripts risk (`test_apis.php`, `test_services.php`)

**Location (actual):** `C:\xampp\htdocs\pauls\palVarnan\test_apis.php` and `C:\xampp\htdocs\pauls\palVarnan\test_services.php` (inside the app root). **Not** at git repo root `C:\xampp\htdocs\pauls\`. `VERIFIED_FROM_SOURCE` (filesystem inspection via read of directories).

These files are **not migrations** and are **not part of the application code paths** (not referenced by routes/controllers in `app/`). They are diagnostic/test scripts located at app root. 
- Treat as **operational risk** if executed in production/shared environment: may call external APIs, write/read DB, or mutate state. 
- **Do not reproduce contents** here (per requirements). 
- Note the nuance vs the brief’s “repo root” wording — they reside under the `palVarnan` app directory. `INFERRED`.

Recommendation: exclude from deploy, add to `.gitignore`/deployment denylist, or move to local dev-only location. `UNVERIFIED` (no execution observed).

---

## 6. Cross-references to existing docs

- [`../22-database-integrity.md`](../22-database-integrity.md) — constraints, corrective DDL, notes on dashboard-owned migrations (`VERIFIED_FROM_SOURCE` in that doc).
- [`../11-models-and-relationships.md`](../11-models-and-relationships.md) — `$fillable`/relation audit.
- [`../07-database-schema.md`](../07-database-schema.md) — full table census and defects.
- [`../audit/VERIFIED_FACTS.md`](../audit/VERIFIED_FACTS.md) — audit rules and working notes.

---

## 7. Summary

- **3 stock migrations** create only 9 framework tables; **93 tables** exist in dump → **schema drift/ownership split**. `VERIFIED_FROM_SOURCE`+`VERIFIED_FROM_LIVE_SCHEMA`.
- Dump is authoritative baseline but **code/models include fields absent from dump** (Shiprocket/Razorpay fields on orders, `product_image` on order items, `product_price` fallback) → treat as live schema evolution. `CONFLICTING_EVIDENCE`.
- Root test scripts exist at `palVarnan/` (app root), not repo root — **documented as risk, contents not reproduced**. `VERIFIED_FROM_SOURCE`.
- Migration bookkeeping confirms commerce migrations live in another project. `VERIFIED_FROM_LIVE_SCHEMA`+`INFERRED`.
