# 📚 Lessons Learned & Technical Insights (`learnlesson.md`)

## [SEO_Slug_Routing]
- **Backward Compatibility via Canonical Redirects**: When transitioning an existing e-commerce platform from numeric primary key queries (e.g. `?category=21`) to human-readable slugs (e.g. `?category=daily-essentials`), never break old URLs. Implement a lightweight check (`is_numeric($param)`) at the start of the controller. If an integer is detected, look up the record and issue an immediate redirect to the slug URL. This keeps search engine indexes healthy, preserves user bookmarks, and ensures smooth UX.

## [Database_Schema_Edge_Case]
- **Column Naming vs Eloquent Assumptions**: In `sub_categories`, the parent category reference is named `category_name` rather than `category_id`, and stores the integer category ID (e.g. "36", "1"). Always inspect the actual database schema (using our `db_export_full.json` or PDO query) before defining Eloquent relationships; otherwise default `hasMany` relationships will attempt to query non-existent columns.

## [UI_UX_Anti_Clutter]
- **Eliminating AI / Template Placeholders**: Generic phrases such as "Certified Fresh & Direct Farm Staples", "Catalog Directory", and "Single department catalog without sub-branches" reduce perceived interface quality. Replacing them with purpose-driven microcopy (e.g., "Find products by category or subcategory", interactive subcategory pill tags, or clean direct links) creates an agency-grade, premium shopping experience.

## [Laravel_Active_State_Tolerant_Comparison]
- **Multi-Format Active Detection in Views**: When query strings can accept either numeric IDs or slugs during transitional phases, the view's active state check must evaluate both:
  ```php
  $isCatActive = (isset($selectedCategory) && $selectedCategory->id == $category->id)
      || request('category') == $category->id
      || request('category') == Str::slug($category->category_name);
  ```
  This eliminates subtle UI bugs where products are correctly filtered, but the sidebar accordion fails to highlight the active branch.

## [ECommerce_Gift_Subsystem_Partitioning]
- **Shared Tables with Domain Flags (`is_gift = 1`)**: Rather than creating duplicate database structures for seasonal or distinct domains (e.g., `gift_products`, `gift_categories`), the system uses a shared database table strategy with an `is_gift` TINYINT flag on `products`, `categories`, `sub_categories`, and `supplier_details`.
- **Hybrid Domain-Specific Attribute Isolation**: While catalog entity tables are shared, domain-specific physical attributes (such as toy/gift sizes and toy colors) are separated into specialized tables (`gift_size`, `gift_color`), which are then linked inside the shared `product_varient` table via `size_id` and `color_id`. Procurement is also partitioned via dedicated `gift_purchase_orders` and `gift_purchase_order_items`.

