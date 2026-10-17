---
name: palvarnan-db-treemap
description: >-
  Comprehensive architectural tree map, schema definitions, relational keys (PK/FK),
  timestamps, and export utilities for the palvarnan_db MySQL database shared by
  palVarnan and palVarnanDashboard. Use whenever querying, extending, or debugging
  database tables, schema relationships, foreign keys, or temporal data in the Pauls projects.
metadata:
  version: 1.0.0
---

# Palvarnan Database Architecture & Tree Map (`palvarnan_db`)

This skill provides an authoritative, complete architectural reference for the `palvarnan_db` MySQL database shared by both **palVarnan** (e-commerce customer application) and **palVarnanDashboard** (admin operations application).

---

## 📌 Database Connection & Environment

The database configuration is synchronized across both applications:
- **palVarnan:** `C:\xampp\htdocs\pauls\palVarnan\.env`
- **palVarnanDashboard:** `C:\xampp\htdocs\pauls\palVarnanDashboard\.env`
- **Host / Port:** `127.0.0.1:3306` (MySQL / MariaDB via XAMPP)
- **Database:** `palvarnan_db`
- **Credentials:** User `root`, empty password
- **Apache Web Server:** Port `8080` (XAMPP `httpd`)

---

## 🌳 Architectural Tree Map (93 Tables Across 12 Domains)

```
palvarnan_db (93 tables | 165,056 rows)
│
├── 📦 Products & Catalog (17 tables | 209 rows)
│   ├── products (14 rows | 29 cols | PK: id | FK -> warehouse.id)
│   ├── product_varient (14 rows | 32 cols | PK: id | FK -> warehouse.id, warehouse_branch.id)
│   ├── productstocks (16 rows | 16 cols | PK: id | FK -> warehouse.id, warehouse_branch.id)
│   ├── product_stock_batches (6 rows | 10 cols | PK: id)
│   ├── product_orders (13 rows | 51 cols | PK: id | FK -> users.id, countries.id)
│   ├── product_order_items (21 rows | 14 cols | PK: id)
│   ├── product_order_user_addresses (13 rows | 19 cols | PK: id)
│   ├── product_slots (15 rows | 22 cols | PK: id)
│   ├── product_tracking (43 rows | 15 cols | PK: id)
│   ├── product_transaction_logs (1 row | 10 cols | PK: id)
│   ├── product_child_images (9 rows | 6 cols | PK: id)
│   ├── product_refunds (0 rows | 7 cols | PK: id)
│   ├── categories (15 rows | 10 cols | PK: id)
│   ├── sub_categories (11 rows | 10 cols | PK: id | FK -> countries.id)
│   ├── brands (8 rows | 8 cols | PK: id)
│   ├── units (10 rows | 6 cols | PK: id)
│   └── today_deals (0 rows | 7 cols | PK: id)
│
├── 🥛 Milk Delivery System (5 tables | 0 rows)
│   ├── milk_orders (18 cols | PK: id | FK -> users.user_id, delivery_people.delivery_person_id, plan_types.id)
│   ├── milk_slots (10 cols | PK: id | FK -> milk_orders.order_id)
│   ├── milk_order_user_addresses (14 cols | PK: id)
│   ├── milk_refunds (7 cols | PK: id)
│   └── milk_transaction_logs (10 cols | PK: id)
│
├── 👥 Users & Stakeholders (9 tables | 24 rows)
│   ├── users (6 rows | 29 cols | PK: id | FK -> countries.id, areas.id, address_types.id)
│   ├── dashboard_users (2 rows | 14 cols | PK: id)
│   ├── user_addresses (3 rows | 25 cols | PK: id)
│   ├── address_types (3 rows | 4 cols | PK: id)
│   ├── gender_types (3 rows | 4 cols | PK: id)
│   ├── delivery_people (0 rows | 11 cols | PK: id)
│   ├── supplier_details (4 rows | 9 cols | PK: id)
│   ├── otps (3 rows | 6 cols | PK: id)
│   └── mail_otps (0 rows | 6 cols | PK: id)
│
├── 🏢 Warehouse & Inventory (4 tables | 12 rows)
│   ├── warehouse (2 rows | 12 cols | PK: id | FK -> countries.id)
│   ├── warehouse_branch (6 rows | 13 cols | PK: id | FK -> warehouse.id, countries.id)
│   ├── stock_transfers (4 rows | 15 cols | PK: id)
│   └── stock_distributions (0 rows | 12 cols | PK: id)
│
├── 📑 Procurement & Purchase Orders (2 tables | 12 rows)
│   ├── purchase_orders (10 rows | 18 cols | PK: id | FK -> warehouse.id, warehouse_branch.id, countries.id, units.id)
│   └── purchase_order_items (2 rows | 14 cols | PK: id | FK -> purchase_orders.id, products.id, units.id)
│
├── 🎁 Gift System (4 tables | 6 rows)
│   ├── gift_purchase_orders (1 row | 10 cols | PK: id)
│   ├── gift_purchase_order_items (1 row | 17 cols | PK: id)
│   ├── gift_size (3 rows | 5 cols | PK: id)
│   └── gift_color (1 row | 5 cols | PK: id)
│
├── 🛒 Shopping & Cart Operations (8 tables | 182 rows)
│   ├── carts (20 rows | 10 cols | PK: id)
│   ├── wishlists (9 rows | 8 cols | PK: id)
│   ├── coupons (1 row | 11 cols | PK: id)
│   ├── newcoupons (0 rows | 10 cols | PK: id)
│   ├── onetimes (136 rows | 6 cols | PK: id)
│   ├── cancel_requests (7 rows | 6 cols | PK: id)
│   ├── reviews (9 rows | 10 cols | PK: id)
│   └── bulkorders (0 rows | 8 cols | PK: id)
│
├── 🗺️ Geography & Logistics (10 tables | 163,503 rows)
│   ├── all_india_pincodes (154,797 rows | 14 cols | PK: id)
│   ├── cities (7,692 rows | 3 cols | PK: id)
│   ├── city_list (899 rows | 4 cols | PK: id)
│   ├── districts (38 rows | 6 cols | PK: id)
│   ├── states (39 rows | 7 cols | PK: id)
│   ├── state_list (36 rows | 2 cols | PK: id)
│   ├── countries (2 rows | 8 cols | PK: id)
│   ├── country_tables (0 rows | 3 cols | PK: id)
│   ├── areas (0 rows | 5 cols | PK: id)
│   └── area_assigns (0 rows | 5 cols | PK: id | FK -> areas.id, delivery_people.id)
│
├── 🔐 Auth & Spatie RBAC Permissions (8 tables | 288 rows)
│   ├── permissions (136 rows | 5 cols | PK: id)
│   ├── roles (8 rows | 5 cols | PK: id)
│   ├── role_has_permissions (136 rows | 2 cols | PK: permission_id, role_id)
│   ├── model_has_roles (5 rows | 3 cols | PK: role_id, model_id, model_type)
│   ├── model_has_permissions (0 rows | 3 cols)
│   ├── personal_access_tokens (3 rows | 8 cols | PK: id)
│   ├── password_reset_tokens (0 rows | 3 cols | PK: email)
│   └── password_resets (0 rows | 3 cols)
│
├── 📢 CMS & Marketing Banners (8 tables | 13 rows)
│   ├── banner_images (0 rows | 5 cols)
│   ├── web_images (0 rows | 4 cols)
│   ├── offer_images (0 rows | 5 cols)
│   ├── home_promotions (4 rows | 11 cols)
│   ├── scrolling_bars (3 rows | 4 cols)
│   ├── testimonials (4 rows | 6 cols)
│   ├── contacts (1 row | 7 cols)
│   └── contact_details (1 row | 10 cols)
│
├── 🔔 Notifications & Alerts (4 tables | 1 row)
│   ├── notifications (1 row | 13 cols | PK: id)
│   ├── user_read_notifications (0 rows | 4 cols)
│   ├── app_notifications (0 rows | 9 cols | FK -> users.user_id)
│   └── app_alerts (0 rows | 9 cols | FK -> users.user_id)
│
└── ⚙️ System & Infrastructure (14 tables | 806 rows)
    ├── telescope_entries (657 rows | 11 cols | PK: sequence)
    ├── telescope_entries_tags (0 rows | 2 cols | FK -> telescope_entries.uuid)
    ├── telescope_monitoring (0 rows | 1 col)
    ├── migrations (132 rows | 3 cols)
    ├── jobs (8 rows | 7 cols)
    ├── failed_jobs (2 rows | 7 cols)
    ├── job_batches (0 rows | 10 cols)
    ├── cache & cache_locks (0 rows)
    ├── sessions (2 rows | 6 cols)
    ├── free_shipping_settings (1 row | 6 cols)
    ├── delivery_charge_details (1 row | 4 cols)
    ├── plan_types (3 rows | 4 cols)
    └── tests (0 rows | 3 cols)
```

---

## 🔗 Key Relational Foreign Key Integrity Map

| Source Table | Source Column | References Table | Target Column | Constraint Name |
|:---|:---|:---|:---|:---|
| `app_alerts` | `user_id` | `users` | `user_id` | `app_alerts_user_id_foreign` |
| `app_notifications` | `user_id` | `users` | `user_id` | `app_notifications_user_id_foreign` |
| `area_assigns` | `area_id` | `areas` | `id` | `area_assigns_ibfk_1` |
| `area_assigns` | `delivery_people_id`| `delivery_people` | `id` | `area_assigns_ibfk_2` |
| `milk_orders` | `delivery_person_id`| `delivery_people` | `delivery_person_id` | `milk_orders_ibfk_5` |
| `milk_orders` | `plan_type` | `plan_types` | `id` | `milk_orders_ibfk_3` |
| `milk_orders` | `user_id` | `users` | `user_id` | `milk_orders_ibfk_4` |
| `milk_slots` | `order_id` | `milk_orders` | `order_id` | `milk_slots_ibfk_1` |
| `products` | `warehouse_id` | `warehouse` | `id` | `products_warehouse_id_foreign` |
| `productstocks` | `warehouse_id` | `warehouse` | `id` | `productstocks_warehouse_id_foreign` |
| `productstocks` | `branch_id` | `warehouse_branch` | `id` | `productstocks_branch_id_foreign` |
| `product_orders` | `user_id` | `users` | `id` | `product_orders_user_id_foreign` |
| `product_orders` | `country_id` | `countries` | `id` | `fk_product_orders_country` |
| `product_varient` | `warehouse_id` | `warehouse` | `id` | `product_varient_warehouse_id_foreign` |
| `product_varient` | `branch_id` | `warehouse_branch` | `id` | `product_varient_branch_id_foreign` |
| `purchase_orders` | `warehouse_id` | `warehouse` | `id` | `purchase_orders_warehouse_id_foreign` |
| `purchase_orders` | `branch_id` | `warehouse_branch` | `id` | `purchase_orders_branch_id_foreign` |
| `purchase_orders` | `country_id` | `countries` | `id` | `purchase_orders_country_id_foreign` |
| `purchase_orders` | `unit_id` | `units` | `id` | `purchase_orders_unit_id_foreign` |
| `purchase_order_items` | `purchase_order_id`| `purchase_orders` | `id` | `purchase_order_items_purchase_order_id_foreign` |
| `purchase_order_items` | `product_id` | `products` | `id` | `purchase_order_items_product_id_foreign` |
| `purchase_order_items` | `unit_id` | `units` | `id` | `purchase_order_items_unit_id_foreign` |
| `sub_categories` | `country_id` | `countries` | `id` | `sub_categories_country_id_foreign` |
| `users` | `country_id` | `countries` | `id` | `fk_users_country` |
| `users` | `area_id` | `areas` | `id` | `users_area_id_foreign` |
| `users` | `address_type_id` | `address_types` | `id` | `users_address_type_id_foreign` |
| `warehouse` | `country_id` | `countries` | `id` | `warehouse_country_id_foreign` |
| `warehouse_branch` | `warehouse_id` | `warehouse` | `id` | `warehouse_branch_warehouse_id_foreign` |
| `warehouse_branch` | `country_id` | `countries` | `id` | `warehouse_branch_country_id_foreign` |

---

## 🕒 Timestamp Lifecycles

| Table | Count | Active Dates (Earliest to Latest) | Temporal Role |
|:---|:---:|:---|:---|
| `carts` | 20 | `2025-12-23 10:09:05` → **`2026-10-17 04:59:33`** | Real-time session cart modifications |
| `wishlists` | 9 | `2025-07-05 04:35:00` → `2026-10-13 07:10:03` | User saved items |
| `users` | 6 | `2026-01-12 10:24:55` → `2026-10-13 08:21:21` | Account registrations and logins |
| `products` | 14 | `2026-04-20 17:39:38` → `2026-10-12 13:03:34` | Catalog edits (includes `deleted_at` soft deletes) |
| `product_orders` | 13 | `2026-02-23 05:35:40` → `2026-08-17 05:18:20` | Customer purchases & Shiprocket delivery status |
| `product_stock_batches`| 6 | Mfg: `2026-05-11` → Expiry: **`2026-07-17`** | Lot number & expiration tracking |
| `onetimes` | 136 | `2024-02-28 11:09:17` → `2024-07-09 04:22:00` | SMS/Email OTP validation codes |

---

## 🚀 Useful Scripts & Workflows

### 1. Re-exporting Database Schema and Treemap
To extract the latest schema, keys, and timestamp metrics from MySQL, run:
```powershell
cd c:\xampp\htdocs\pauls
php export_database_complete.php
```

### 2. Viewing the Interactive Web Visualizer
Ensure Apache is running in XAMPP, then open:
```
http://localhost:8080/pauls/db_treemap_visualizer.html
```
- Click any table node to see all columns, nullable flags, defaults, and sample data.
- Search by table name or category to filter tables.
- View the Foreign Key matrix and Timestamp lifecycle timeline.
