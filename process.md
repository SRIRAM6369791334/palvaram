# 🚀 Development Process & Architecture Log (`process.md`)

## 1. Overview & Objective
Transform the Pal Varnan E-Commerce product catalog navigation from raw ID-based query strings into clean, SEO-friendly **Slugs**, create a dedicated high-end Categories/Subcategories showcase, and enforce canonical URL redirects.

---

## 2. URL Transformation Matrix (ID to Slug Mapping)

| Legacy / ID URL | Clean Slug URL | Targeted Department & Sub-branch | Status |
|:---|:---|:---|:---:|
| `http://127.0.0.1:8000/products?category=2` | `http://127.0.0.1:8000/products?category=savouries` | Savouries | ✅ Verified 302/Canonical |
| `http://127.0.0.1:8000/products?category=21&subcategory=13` | `http://127.0.0.1:8000/products?category=daily-essentials&subcategory=cheese` | Daily Essentials → Cheese | ✅ Verified 302/Canonical |
| `http://127.0.0.1:8000/products?category=1&subcategory=17` | `http://127.0.0.1:8000/products?category=biscuts&subcategory=wheat-biscuts` | Biscuits → Wheat Biscuits | ✅ Verified 302/Canonical |
| `http://127.0.0.1:8000/products?category=36&subcategory=20` | `http://127.0.0.1:8000/products?category=soft-toy&subcategory=test-toy-1` | Soft Toy → Test Toy 1 | ✅ Verified 302/Canonical |

---

## 3. Architecture & Code Implementation

### A. Slug Resolution & Canonical Redirection
- **File:** [`palVarnan/app/Http/Controllers/ProductController.php`](file:///C:/xampp/htdocs/pauls/palVarnan/app/Http/Controllers/ProductController.php)
  - Intercepts incoming `category` and `subcategory` parameters.
  - If numeric IDs are passed, it looks up the record, constructs clean slug query parameters via `Str::slug(...)`, and issues an immediate canonical redirect.
  - Resolves slugs back to relational database IDs:
    - Queries `categories` via `category_name` slug comparison.
    - Queries `sub_categories` via `subcategory_name` slug comparison.
  - Passes `$selectedCategory` and `$selectedSubcategory` down to the blade templates for accurate highlighting.

### B. Eloquent Accessor Support
- **Model:** [`Category.php`](file:///C:/xampp/htdocs/pauls/palVarnan/app/Models/Category.php)
  - Added `getSlugAttribute()` returning `Str::slug($this->category_name)`.
- **Model:** [`SubCategory.php`](file:///C:/xampp/htdocs/pauls/palVarnan/app/Models/SubCategory.php)
  - Added `getSlugAttribute()` returning `Str::slug($this->subcategory_name)`.

### C. Frontend Template Links Updated
All internal application links now generate clean slugs instead of database IDs:
1. **Catalog View:** [`products.blade.php`](file:///C:/xampp/htdocs/pauls/palVarnan/resources/views/pages/products.blade.php)  
   - Sidebar links generated with `category={{ $catSlug }}` and `subcategory={{ $subSlug }}`.
   - Active state detects both ID and Slug matches seamlessly.
2. **Categories Showcase:** [`categories.blade.php`](file:///C:/xampp/htdocs/pauls/palVarnan/resources/views/pages/categories.blade.php)  
   - Pill tags and "View Products" buttons use clean slugs.
3. **Header Dropdown:** [`header.blade.php`](file:///C:/xampp/htdocs/pauls/palVarnan/resources/views/layouts/header.blade.php)  
   - Quick-access menu links use clean slugs.
4. **Home Carousel:** [`home.blade.php`](file:///C:/xampp/htdocs/pauls/palVarnan/resources/views/pages/home.blade.php)  
   - Popular category circles route to `products?category={{ $catSlug }}`.

---

## 4. Verification Receipts (Playwright Automated Testing)
1. **Test 1:** Visited `http://127.0.0.1:8000/products?category=2`  
   - Result: Automatically redirected to `http://127.0.0.1:8000/products?category=savouries` with Savouries filter active.
2. **Test 2:** Visited `http://127.0.0.1:8000/products?category=21&subcategory=13`  
   - Result: Automatically redirected to `http://127.0.0.1:8000/products?category=daily-essentials&subcategory=cheese` with Cheese subcategory highlighted.
3. **Test 3:** Categories Showcase at `http://127.0.0.1:8000/categories`  
   - Verified real-time search filtering, responsive 4-column cards, and clean typography.

---

## 5. Gift Products Management Hub (`http://127.0.0.1:8001/giftproducts`)
- **Route:** `Route::view('/giftproducts', 'pages.giftproducts_single');`
- **Location:** [`palVarnanDashboard/resources/views/pages/giftproducts_single.blade.php`](file:///C:/xampp/htdocs/pauls/palVarnanDashboard/resources/views/pages/giftproducts_single.blade.php)
- **Role:** Central Master Navigation Hub for all Gift operations (Categories, Subcategories, Products, Variants, Sizes, Colors, Suppliers, POs, Stock Distribution).
- **Core Database Tables:**
  1. `categories` (`is_gift = 1`)
  2. `sub_categories` (`is_gift = 1`)
  3. `products` (`is_gift = 1`)
  4. `product_varient` (links `size_id` -> `gift_size.id`, `color_id` -> `gift_color.id`)
  5. `gift_size`
  6. `gift_color`
  7. `supplier_details` (`is_gift = 1`)
  8. `gift_purchase_orders` & `gift_purchase_order_items`
  9. `productstocks`, `stock_distributions`, `stock_transfers`

