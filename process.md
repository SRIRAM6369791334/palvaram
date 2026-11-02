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

---

## 6. Unified Hybrid Catalog & Storefront End-to-End Flow (Completed Implementation)

### A. New Eloquent Models & Relationship Wiring
1. **[`GiftSize.php`](file:///c:/xampp/htdocs/pauls/palVarnan/app/Models/GiftSize.php):**
   - Created in Storefront mapping to `gift_size` table with `variants()` relationship.
2. **[`GiftColor.php`](file:///c:/xampp/htdocs/pauls/palVarnan/app/Models/GiftColor.php):**
   - Created in Storefront mapping to `gift_color` table with `variants()` relationship.
3. **[`ProductVarient.php`](file:///c:/xampp/htdocs/pauls/palVarnan/app/Models/ProductVarient.php):**
   - Added `size()` (`belongsTo(GiftSize::class, 'size_id')`) and `color()` (`belongsTo(GiftColor::class, 'color_id')`).

### B. Controller Multi-Country & Gift Support
1. **[`ProductController.php`](file:///c:/xampp/htdocs/pauls/palVarnan/app/Http/Controllers/ProductController.php):**
   - Eager loads `['variants.unit', 'variants.size', 'variants.color']`.
   - Updated country filter from strict `=` to `FIND_IN_SET(?, country_id)` to support comma-separated multi-country configurations (e.g., `'243,244'`).
   - Added `?gift=1` dedicated filter query support.
2. **[`CategoryController.php`](file:///c:/xampp/htdocs/pauls/palVarnan/app/Http/Controllers/CategoryController.php):**
   - Updated category product count subquery to use `FIND_IN_SET(?, country_id)` so gift products accurately reflect in category badge counts.

### C. Hybrid View Templates
1. **Categories Showcase ([`categories.blade.php`](file:///c:/xampp/htdocs/pauls/palVarnan/resources/views/pages/categories.blade.php)):**
   - Added `🎁 GIFT` badge to gift categories. Verified Soft Toy displays 2 items and 2 subcategory pills (`test toy 1`, `test toy 2`).
2. **Products Catalog ([`products.blade.php`](file:///c:/xampp/htdocs/pauls/palVarnan/resources/views/pages/products.blade.php)):**
   - Added `🎁 Gifts & Toys` sidebar filter.
   - Re-engineered variant dropdown into hybrid selector: shows `Size • Color` (e.g. `Small (S) • Pink`) when `is_gift = 1` or size/color are present, and `weight + unit` for grocery products.
3. **Product Detail ([`product-detail.blade.php`](file:///c:/xampp/htdocs/pauls/palVarnan/resources/views/pages/product-detail.blade.php)):**
   - Renders interactive selection pills: `Small (S) • Pink ₹300` and `Medium (M) • White ₹450`.
   - Dynamic SKU, price, discount, and stock reactivity.
   - Specifications table showcases `Available Size / Color` with badge styling.
4. **Cart View ([`cart.blade.php`](file:///c:/xampp/htdocs/pauls/palVarnan/resources/views/pages/cart.blade.php)):**
   - Line items display `Size: Small (S) | Color: Pink` with gift icon tag alongside grocery weight units.

### D. Objective Verification Receipts
1. **Automated Verification Script (`verify_gift_system.php`):** 100% PASS across Category count, Product index, `?gift=1` filtering, and Product detail rendering.
2. **Playwright Visual Verification:**
   - Categories: `http://127.0.0.1:8000/categories` verified.
   - Products Catalog: `http://127.0.0.1:8000/products?category=soft-toy` verified.
   - Product Detail: `http://127.0.0.1:8000/product/unicorn-toy-1779103452` verified (`unicorn_product_detail.png`).
   - Cart: `http://127.0.0.1:8000/cart` verified (`cart_gift_item.png`).

---

## 7. SMTP Mail Configuration Update

Configured Gmail SMTP credentials across both environments to enable transaction alerts, OTP, and customer order emails.

### A. Customer Storefront ([`palVarnan/.env`](file:///C:/xampp/htdocs/pauls/palVarnan/.env))
```env
MAIL_MAILER=smtp
MAIL_SCHEME=null
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=Kalaimern2005@gmail.com
MAIL_PASSWORD="tedv qvyz odfs uozp"
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="Kalaimern2005@gmail.com"
MAIL_FROM_NAME="${APP_NAME}"

EMAIL_USER=Kalaimern2005@gmail.com
EMAIL_PASS="tedv qvyz odfs uozp"
```

### B. Admin Dashboard ([`palVarnanDashboard/.env`](file:///C:/xampp/htdocs/pauls/palVarnanDashboard/.env))
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=Kalaimern2005@gmail.com
MAIL_PASSWORD="tedv qvyz odfs uozp"
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="Kalaimern2005@gmail.com"
MAIL_FROM_NAME="Pal Varnan"

EMAIL_USER=Kalaimern2005@gmail.com
EMAIL_PASS="tedv qvyz odfs uozp"
```

---

## 8. Customer Contact Inquiries End-to-End System

Seamless customer message handling connecting the storefront inquiry form to the admin backoffice with real-time notifications.

### A. Authentication Credentials
- **Admin Dashboard Login:** `admin@palvarnan.com` / `admin@2025`

### B. Database Schema Update
- Checked existing database tables: `contacts` table existed with `name`, `email`, `subject`, `message`, `created_at`, `updated_at`.
- Enhanced schema by adding the `phone` column:
  ```sql
  ALTER TABLE `contacts` ADD COLUMN `phone` VARCHAR(20) NULL AFTER `email`;
  ```

### C. Storefront Implementation (`palVarnan`)
1. **Model:** [`palVarnan/app/Models/Contact.php`](file:///c:/xampp/htdocs/pauls/palVarnan/app/Models/Contact.php)
   - Mapped to `contacts` table with `$fillable = ['name', 'email', 'phone', 'subject', 'message']`.
2. **Controller:** [`palVarnan/app/Http/Controllers/ContactController.php`](file:///c:/xampp/htdocs/pauls/palVarnan/app/Http/Controllers/ContactController.php)
   - `index()`: Serves `pages.contact`.
   - `store(Request $request)`: Validates inputs, saves to database, dispatches email notification safely in background/try-catch, returns JSON response.
3. **Routes:** [`palVarnan/routes/web.php`](file:///c:/xampp/htdocs/pauls/palVarnan/routes/web.php)
   - `Route::get('/contact', [ContactController::class, 'index'])->name('contact');`
   - `Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');`
4. **View:** [`palVarnan/resources/views/pages/contact.blade.php`](file:///c:/xampp/htdocs/pauls/palVarnan/resources/views/pages/contact.blade.php)
   - Replaced simulated submit handler with modern AJAX fetch POST with loading spinner and SweetAlert2 confirmation.

### D. Admin Dashboard Implementation (`palVarnanDashboard`)
1. **Model:** [`palVarnanDashboard/app/Models/Contact.php`](file:///c:/xampp/htdocs/pauls/palVarnanDashboard/app/Models/Contact.php)
2. **Controller:** [`palVarnanDashboard/app/Http/Controllers/ContactController.php`](file:///c:/xampp/htdocs/pauls/palVarnanDashboard/app/Http/Controllers/ContactController.php)
   - `index()`: Retrieves inquiries and computes real-time stats (`totalCount`, `todayCount`, `exportCount`).
   - `destroy($id)`: Deletes selected inquiry with flash confirmation.
3. **Routes:** [`palVarnanDashboard/routes/web.php`](file:///c:/xampp/htdocs/pauls/palVarnanDashboard/routes/web.php)
   - `Route::resource('contacts', ContactController::class)->only(['index', 'destroy']);`
4. **Sidebar:** [`palVarnanDashboard/resources/views/layouts/sidebar.blade.php`](file:///c:/xampp/htdocs/pauls/palVarnanDashboard/resources/views/layouts/sidebar.blade.php)
   - Added `Contact Inquiries` menu item with live dynamic badge count.
5. **View:** [`palVarnanDashboard/resources/views/pages/contacts.blade.php`](file:///c:/xampp/htdocs/pauls/palVarnanDashboard/resources/views/pages/contacts.blade.php)
   - Summary stat cards for quick business metrics.
   - Real-time client-side search input and subject topic filter dropdown.
   - Interactive Inquiry Details modal with one-click "Reply via Email" and "WhatsApp" buttons.
   - Secure deletion action.

### E. Objective Verification Receipts
1. **Storefront Submission:** Submitted test inquiry via Playwright (`Mani Kandan`, `Global Export`, `manikandan@example.com`, `+91 9443322110`). Received 200 OK and SweetAlert confirmation (`contact_form_submitted.png`).
2. **Dashboard Verification:** Logged in with `admin@palvarnan.com` / `admin@2025`, navigated to `http://127.0.0.1:8001/contacts`. Inquiry rendered in table with correct tags, date, and metrics (`dashboard_contacts_verified.png`).
3. **Modal Verification:** Opened Inquiry Details modal, verified message content and action links (`dashboard_modal_inquiry_view.png`).

---

## 9. Dual HTML Email Dispatch System (UI/UX Pro Max Standard)

### A. Admin Email Updated in Environment
- **Target Admin Email:** `ADMIN_EMAIL=sriramsri1234321@gmail.com`
- Updated in both:
  - [`palVarnan/.env`](file:///C:/xampp/htdocs/pauls/palVarnan/.env)
  - [`palVarnanDashboard/.env`](file:///C:/xampp/htdocs/pauls/palVarnanDashboard/.env)

### B. High-End Responsive HTML Email Templates
1. **Admin Notification Template:** [`palVarnan/resources/views/emails/contact_admin.blade.php`](file:///c:/xampp/htdocs/pauls/palVarnan/resources/views/emails/contact_admin.blade.php)
   - Pal Varnan brand header with gradient `#0c716a` to `#15803d` and inquiry ID badge.
   - Structured customer details grid (Name, Email, Phone, Topic, Timestamp).
   - High-contrast message card with left accent border.
   - One-click action buttons: "Reply via Email" (`mailto:`) and "WhatsApp Customer" (`wa.me`).
2. **Customer Confirmation Template:** [`palVarnan/resources/views/emails/contact_customer.blade.php`](file:///c:/xampp/htdocs/pauls/palVarnan/resources/views/emails/contact_customer.blade.php)
   - Personalized warm greeting ("Hello [Name], We Received Your Message!").
   - Service level commitment ("Our team typically responds within 12 to 24 business hours").
   - Complete recap of their submitted message with Reference ID.
   - Urgent support box with helpline (+91 8778509639, Mon-Sat 8AM-8PM).
   - Conversion CTA button ("Explore Fresh Products & Gifts").

### C. Resilient Dispatch Controller
- **File:** [`palVarnan/app/Http/Controllers/ContactController.php`](file:///c:/xampp/htdocs/pauls/palVarnan/app/Http/Controllers/ContactController.php)
- Added `isSmtpReachable()` non-blocking socket probe (1.2s timeout) so that local firewall/ISP SMTP blocks never stall browser form submissions, while ensuring instant live dispatch in production.

### D. Objective Verification Receipts
1. **Admin Email Rendered:** `c:/xampp/htdocs/pauls/rendered_email_admin.html` & preview captured (`email_preview_admin.png`).
2. **Customer Email Rendered:** `c:/xampp/htdocs/pauls/rendered_email_customer.html` & preview captured (`email_preview_customer.png`).
3. **Live Form Submission:** Verified in browser at `http://127.0.0.1:8000/contact` with `Kavitha Ramachandran` (`contact_dual_email_submitted.png`).
4. **Dashboard Updated:** Verified at `http://127.0.0.1:8001/contacts` displaying the newly submitted inquiry (`dashboard_updated_with_kavitha.png`).

---

## 10. Comprehensive Multi-Dimensional QA Testing & CRUD Verification

### A. Testing Scope & Methodology
Executed end-to-end verification across 5 major dimensions:
1. **Storefront Core Customer Journeys:** Verified routes `/`, `/products`, `/product/{slug}`, `/categories`, `/contact`, and `/cart`.
2. **Dynamic Hybrid Variant Behavior:** Tested simultaneous handling of grocery variants (`unit_id`, `value`) and gift variants (`size_id`, `color_id`) across catalog, product detail, and cart.
3. **Reactive UI State Transitions:** Checked variant pill switching, price recalculation, stock badge response, quantity stepper, and WhatsApp auto-message generation.
4. **Admin Dashboard CRUD & Workflow Integrity:** Tested Category, Product, Variant, and Contact Inquiry operations.
5. **Database Consistency & Schema Verification:** Verified column alignment (`categories`, `products`, `product_varient`, `contacts`) and validated state transitions directly via MySQL queries.

### B. QA Verification Matrix & Evidence Receipts

| Test ID | Module | Scenario / Test Case | Input / Action | Expected Result | Actual Result | Status | Proof Receipt |
|---|---|---|---|---|---|:---:|---|
| **TC-01** | Storefront Home | Country selection & home components load | Load `http://127.0.0.1:8000/` with session `country_id=243` | Navbar, Carousel, Category strip, Country badge render | Rendered 200 OK cleanly | **PASS** | `qa_proof_01_storefront_home.png` |
| **TC-02** | Product Catalog | Catalog grid & filtering | Navigate to `/products` | Category filters, brand list, product cards render | Rendered with active items | **PASS** | `qa_proof_02_storefront_products.png` |
| **TC-03** | Gift Toggle | Filter catalog for gifts only | Load `/products?gift=1` | Only gift items (toys, hampers) display | Filtered gift catalog rendered | **PASS** | `qa_proof_03_storefront_gift_products.png` |
| **TC-04** | Product Detail | Initial variant load | Load `/product/new-test-toy-1780660244` | Default variant (Small • White ₹700) selected | ₹700 displayed with active pill | **PASS** | `qa_proof_04_product_detail_initial.png` |
| **TC-05** | Variant Switching | Dynamic reactivity on click | Click "Medium (M) • Sky Blue ₹850" pill | Offer price changes to ₹850; active pill updates | Offer price changed to ₹850 dynamically | **PASS** | `qa_proof_05_product_variant_selected.png` |
| **TC-06** | Add to Cart | Detail page cart interaction | Click "Add To Cart" on selected variant | Button transforms to stepper `[- 1 +]` | Stepper rendered; session updated | **PASS** | `qa_proof_06_added_to_cart.png` |
| **TC-07** | Cart Co-existence | Grocery & Gift hybrid items | Navigate to `/cart` | Both gift variants & grocery units listed accurately | Both types co-exist without conflict | **PASS** | `qa_proof_07_cart_hybrid_variants.png` |
| **TC-08** | Category Search | Live search filter | Type "Toy" on `/categories` | Live filter hides non-matching categories | `🎁 GIFT Soft Toy` filtered instantly | **PASS** | `qa_proof_08_categories_filter.png` |
| **TC-09** | Contact Submit | Form validation & submission | Submit inquiry for Kavitha Priya | DB row created, SweetAlert2 success shown | Inserted ID #7, SweetAlert displayed | **PASS** | `qa_proof_09_contact_form_submitted_success.png` |
| **TC-10** | Admin Contacts | Inquiry list verification | Login to `http://127.0.0.1:8001/contacts` | Inquiry #7 listed at top with full metadata | Row #7 displayed with General Inquiry tag | **PASS** | `qa_proof_10_dashboard_contacts_list.png` |
| **TC-11** | Inquiry Modal | View details & action triggers | Click "View" on Row #7 | Modal displays message, WhatsApp & Mailto links | Modal rendered with accurate action links | **PASS** | `qa_proof_11_dashboard_inquiry_modal.png` |
| **TC-12** | Category CRUD | Admin category lifecycle | Create, Read, Update, Delete Category | Database records manipulated without error | All 4 CRUD operations verified | **PASS** | `qa_proof_12_dashboard_categories.png` |
| **TC-13** | Product & Variant CRUD | Gift product & variant lifecycle | Create product, add variant in `product_varient` | Full CRUD lifecycle & storefront query verified | Created ID #42, Variant #106, cleanup verified | **PASS** | Automated Script Log |

---

## 11. Complete Cart, Wishlist, and Checkout Flow Implementation

### A. Architectural Upgrades & System Parity
1. **Cart Optimization:**
   - Eliminated hardcoded totals in [`cart.blade.php`](file:///c:/xampp/htdocs/pauls/palVarnan/resources/views/pages/cart.blade.php). Implemented dynamic calculations for Subtotal, Estimated Delivery (₹50.00), Estimated Tax (16% GST), and Grand Total.
   - Added debounced AJAX quantity synchronization (`fetch('/cart/ajax-update')`) when pressing `+` or `-` buttons on the cart page.
   - Enhanced guest checkout conversion: clicking "Proceed to Checkout" as a guest triggers an intuitive modal directing the user to `/login?redirect=/checkout`.
   - Upgraded [`AuthController::login`](file:///c:/xampp/htdocs/pauls/palVarnan/app/Http/Controllers/AuthController.php) with automatic **Guest-to-User Cart Migration**, transferring any items added under `ip_addresscart` directly into the logged-in user's account and respecting the `redirect` query parameter.

2. **Wishlist Flow & Hybrid Gift Variant Support:**
   - Upgraded [`WishlistController.php`](file:///c:/xampp/htdocs/pauls/palVarnan/app/Http/Controllers/WishlistController.php) to eager-load `variant.size` and `variant.color` alongside grocery `variant.unit`.
   - Updated [`wishlist.blade.php`](file:///c:/xampp/htdocs/pauls/palVarnan/resources/views/pages/wishlist.blade.php) with polymorphic badge rendering (purple badge for gift dimensions/colors and teal badge for grocery weights/volumes).
   - Integrated AJAX "Add to Cart from Wishlist" with instant toast alerts and table row cleanup.

3. **Checkout, Delivery & Multi-Table Order Synchronization:**
   - Ensured `country_id` parity (`session('country_id') ?? $user->country_id ?? 243`) in [`CheckoutController::placeOrder`](file:///c:/xampp/htdocs/pauls/palVarnan/app/Http/Controllers/CheckoutController.php) so all storefront orders match the admin country filter criteria (`whereIn('country_id', $allowedCountryIds)`).
   - Live Shiprocket courier quotes with graceful Standard Delivery fallback.
   - Dual payment method support: Razorpay online card/UPI/netbanking and Cash on Delivery (COD).
   - Atomic database transactions synchronizing:
     - `product_orders` (Master order record)
     - `product_order_items` (Customer account history line items)
     - `product_slots` (Admin dashboard packing and dispatch table)
     - `product_tracking` & `product_transaction_logs`
     - Stock reduction in `productstocks` and `product_varient`
     - Cart table cleanup for the purchasing user.

4. **Dual HTML Order Email Notifications:**
   - Built [`order_customer.blade.php`](file:///c:/xampp/htdocs/pauls/palVarnan/resources/views/emails/order_customer.blade.php) and [`order_admin.blade.php`](file:///c:/xampp/htdocs/pauls/palVarnan/resources/views/emails/order_admin.blade.php).
   - Integrated non-blocking `isSmtpReachable()` socket probe (1.2s timeout) before dispatching confirmation emails to prevent checkout freezing on local network blocks.

### B. Verification Receipts & Proof Matrix

| Test ID | Module | Scenario / Flow Verified | Expected Result | Actual Result | Status | Proof Receipt |
|---|---|---|---|---|:---:|---|
| **TC-14** | Cart | Dynamic Subtotal, Tax, Delivery & Grand Total recalculation | Real-time calculation without hardcoding | Calculated ₹2,840 + ₹50 + ₹454.40 = ₹3,344.40 | **PASS** | `qa_proof_13_cart_dynamic_recalculated.png` |
| **TC-15** | Checkout | Authenticated user checkout with Live Couriers | Displays addresses, Shiprocket rates (Xpressbees ₹186.24, Ekart ₹213.12) | Loaded successfully with live courier rates | **PASS** | `qa_proof_14_checkout_page_active.png` |
| **TC-16** | Order Placement | Live browser COD order placement (`#ORD-6AE81A4E512E2`) | Inserts order, redirects to thank you page with order details | Order placed, redirected to `/checkout/thankyou/...` | **PASS** | `qa_proof_15_order_success_thankyou.png` |
| **TC-17** | Dashboard Sync | Storefront order appears in Admin Dashboard | Order #18 visible at top of `/productOrders` list | Rendered as Row #1 under India territory | **PASS** | `qa_proof_16_dashboard_order_received.png` |



