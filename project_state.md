# 📌 Project State Memory (`project_state.md`)

## 1. Current State & Active Environment
- **Workspace:** `c:\xampp\htdocs\pauls`
- **Customer Storefront (`palVarnan`):** `http://127.0.0.1:8000/` (Laravel 10, Port 8000)
- **Admin Dashboard (`palVarnanDashboard`):** `http://127.0.0.1:8001/` (Laravel 10, Port 8001)
- **Database:** `palvarnan_db` on MySQL `127.0.0.1:3306` (93 tables, 34 FKs)

---

## 2. Completed Milestones
1. **Database Schema Extraction & Treemap:**
   - Generated `db_export_full.json`, `db_treemap_data.json`, and `db_treemap_visualizer.html`.
   - Created `palvarnan-db-treemap` skill.
2. **Categories Page & UX Overhaul:**
   - Built modern `/categories` showcase with instant search and subcategory chips.
   - Cleaned up non-essential template labels.
3. **SEO-Friendly Slug Routing & Canonical Redirection:**
   - Migrated category and subcategory filtering from query IDs (`category=2`) to clean slugs (`category=savouries`).
   - Backward compatible with 302 redirects.
4. **Gift Products Architecture (`/giftproducts`):**
   - Documented the Master Hub architecture in `palVarnanDashboard` and its mapped database tables.
