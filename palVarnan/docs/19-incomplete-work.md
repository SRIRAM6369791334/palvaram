# 19 — Incomplete Work

Everything half-built, stubbed, unreachable, or visibly abandoned. Each entry names its evidence and the effort to finish it.

Effort: **S** (< 1 h) · **M** (1–4 h) · **L** (1–3 d) · **XL** (> 3 d)

---

## 1. Backend gaps

| ID | Item | Evidence | Effort |
|---|---|---|---|
| BW-01 | **Checkout cannot persist an order** | `Unknown column 'billing_state'` and `'product_image'` in `laravel.log`; no migration creates either | **S** |
| BW-02 | Password reset accepts a new password with no token | `AuthController::resetPassword()` reads no `token` | **M** |
| BW-03 | `forgotPassword()` is orphaned | `routes/web.php:29` exists; no view posts to `password.email` | **S** |
| BW-04 | No route accepts a reset token | no `GET /reset-password/{token}` | **S** |
| BW-05 | **Coupon engine absent** | `Coupon` imported at `CheckoutController.php:6`, never queried. `coupon_discount` is `floatval($request->coupon_discount ?? 0)` at `:192` and `:287` — an unchecked client number. `coupons` and `newcoupons` tables both exist | **L** |
| BW-06 | "Apply Coupon" button has no handler | `cart.blade.php`, `checkout.blade.php:94` | **S** |
| BW-07 | Guest → user cart merge missing | `AuthController::login()` migrates nothing | **M** |
| BW-08 | Guest → user wishlist merge missing | same | **S** |
| BW-09 | `guest_token` cookie is read but never set | 3 read sites (`CartController::index/subtotal`, `getCartItems()`), 0 write sites; `carts` has **no** `guest_token` column | **S** |
| BW-10 | Cart update/remove lack ownership scoping | `CartController.php:35-44, 46-60` | **S** |
| BW-11 | Cart quantity input unvalidated on every cart path | `update()`, `store()`, `ajaxUpdate()` | **S** |
| BW-12 | No stock check on add-to-cart | `store()` has no stock query | **S** |
| BW-13 | No stock reservation | `product_varient.low_stock` exists, unused | **M** |
| BW-14 | Oversell silently permitted | `max($stock - $qty, 0)` `:405, 413` floors at zero with no flag | **M** |
| BW-15 | No `lockForUpdate()` on stock | two concurrent orders can both pass the check | **S** |
| BW-16 | Stock never restored | no cancel/return path exists | **XL** |
| BW-17 | Shiprocket failure leaves no local trace | `catch (\Exception)` at `:525` logs only; `shiprocket_status` never set to `FAILED` | **S** |
| BW-18 | **Pickup never generated** | `generatePickup()` has no caller — orders are created but never collected | **S** |
| BW-19 | 7 of 11 Shiprocket methods unused | `generatePickup`, `generateManifest`, `printManifest`, `generateLabel`, `printInvoice`, `trackAWB`, `getPickupLocations` | **M** |
| BW-20 | No Shiprocket webhook receiver | no route | **L** |
| BW-21 | No Razorpay webhook receiver | no route | **L** |
| BW-22 | Razorpay amount never re-verified | `:252-268` verifies the signature only | **M** |
| BW-23 | No idempotency key on `placeOrder` | no key; `razorpay_order_id` has no unique index | **M** |
| BW-24 | No refund path anywhere | payment can be captured with no order row | **L** |
| BW-25 | No order-tracking page | `product_tracking` is written on every order, never read | **M** |
| BW-26 | Cross-app status mismatch | `SyncShiprocketStatus` excludes `'Order Placed'`; the storefront writes exactly that status | **S** |
| BW-27 | No order cancellation in the storefront | `cancel_requests` table exists | **L** |
| BW-28 | No returns/refunds in the storefront | `product_refunds` and `milk_refunds` tables exist | **XL** |
| BW-29 | No reviews | `reviews` and `testimonials` tables exist; no model, route, or UI | **L** |
| BW-30 | No bulk-order or enquiry capture | `bulkorders` table exists | **M** |
| BW-31 | Gift, milk, and purchase-order flows absent | `gift_purchase_orders`, `gift_purchase_order_items`, `gift_color`, `gift_size`, `milk_orders`, `milk_slots`, `milk_refunds`, `milk_order_user_addresses`, `milk_transaction_logs`, `purchase_orders`, `purchase_order_items`, `stock_transfers`, `stock_distributions` — **14 tables, 0 storefront code** | **XL** |
| BW-32 | Variant dimension and weight columns do not exist | `product_varient` has no `weight`, `length`, `breadth`, `height`; `:453-463` therefore always sends 0.5 kg and 10×10×10 | **M** |
| BW-33 | District/city cascade unused | `getCitiesByState()` `:659-670` has no caller | **S** |
| BW-34 | State shipping-charge endpoint unused | `getShippingCharge()` `:638-654` has no caller | **S** |
| BW-35 | No scheduled tasks | `routes/console.php` is stock — no `session:prune`, no reconciliation | **M** |
| BW-36 | No queue workers configured | `QUEUE_CONNECTION=sync`, `app/Jobs/` absent | **M** |
| BW-37 | No notifications or mailables | `app/Mail/`, `app/Notifications/` absent, despite `app_notifications`, `notifications`, `mail_otps`, `otps`, `user_read_notifications` tables | **L** |
| BW-38 | No policies or form requests | `app/Policies/`, `app/Http/Requests/` absent | **M** |
| BW-39 | No custom middleware | `app/Http/Middleware/` absent | **S** |
| BW-40 | Variant search is impossible | `product_varient.varient` is `int(11)`; `SearchController.php:38` applies `LIKE` | **M** |
| BW-41 | No search results page | `/search` returns JSON only; the header form is `action="#"` | **M** |
| BW-42 | Brand/category search URLs are invalid | `$item->slug` on `brands` (which has `brand_slug`) and on `categories` (which has no slug at all) | **S** |
| BW-43 | `SubCategory::category()` broken | `sub_categories` has `category_name`, not `category_id` | **S** |
| BW-44 | `ProductVarient::category()` broken | infers `category_id`; the column is `categoryid` | **S** |
| BW-45 | `UserAddress::country()` wrong FK | uses `area_id` | **S** |
| BW-46 | Duplicate relationships | `Product::variants()`/`productvari()`; `ProductOrder::user()`/`customer()` | **S** |
| BW-47 | `wishlists` has no unique constraint | `toggle()` guards in PHP only | **S** |
| BW-48 | No `/brand/{slug}` or `/category/{slug}` route | search results link to both | **M** |
| BW-49 | No country-aware currency, payment, or shipping | `₹` and `'INR'` hard-coded; Shiprocket payload pins `billing_country: "India"` | **XL** |
| BW-50 | `countries.ship_charge` never read | and `Country::$fillable` omits it | **M** |
| BW-51 | `addresses` / `address_types` / `cities` / `districts` / `country_tables` / `state_list` / `city_list` unreferenced | 7 lookup tables with no storefront code | **M** |
| BW-52 | `contacts` and `contact_details` exist; **the storefront has no ContactController, route, or view** | four `contact.html` links in the header and footer all 404 | **M** |

---

## 2. Frontend gaps

| ID | Item | Evidence | Effort |
|---|---|---|---|
| FW-01 | **~700 lines of template demo in `home.blade.php`** | Unsplash images, `wowtheme7.com` links, hard-coded prices | **M** |
| FW-02 | **Dead links in `header.blade.php`** | repeated `shop.html` targets that no route serves | **M** |
| FW-03 | `footer.blade.php` carries demo contacts | vendor email addresses and a placeholder phone number | **S** |
| FW-04 | No Blade components | `resources/views/components/` absent | **L** |
| FW-05 | No partials | no `@include` beyond the layout | **L** |
| FW-06 | All JS inline in Blade | dozens of `fetch()` calls, no `resources/js` modules | **L** |
| FW-07 | **jQuery loaded twice** — 3.7.1 local plus 3.6.0 CDN | `app.blade.php` | **S** |
| FW-08 | `boostrap.bundle.min.js` misspelled | filename on disk and in markup | **S** |
| FW-09 | Three CDN dependencies | cdnjs ×2, jsdelivr | **S** |
| FW-10 | Vite/Tailwind pipeline unused | no `@vite`; `node_modules` and `public/build` absent | **S** (delete) |
| FW-11 | `welcome.blade.php` unreachable | no route renders it | **S** (delete) |
| FW-12 | CSRF header missing on most `fetch()` calls | across `cart`, `myaccount`, `header`, `checkout` | **S** |
| FW-13 | Only the address form handles HTTP 419 | `checkout.blade.php:572-575` | **M** |
| FW-14 | No global fetch wrapper | every call hand-rolls headers | **M** |
| FW-15 | `favicon.ico` is 0 bytes | `public/favicon.ico` | **S** |
| FW-16 | No per-page SEO metadata | one `<title>` on every page | **M** |
| FW-17 | No OpenGraph or canonical URLs | — | **M** |
| FW-18 | No sitemap | stock `robots.txt` | **M** |
| FW-19 | No analytics or tracking pixels | none in any view | **M** |
| FW-20 | No `loading="lazy"` | all images eager | **S** |
| FW-21 | No `srcset` or WebP | — | **M** |
| FW-22 | Currency `₹` hard-coded in ~40 places | no `Intl.NumberFormat` | **M** |
| FW-23 | No error pages | `withExceptions()` body is an empty comment | **S** |
| FW-24 | `country-selection.blade.php` never renders `session('error')` | `CountryController::store()` flashes it | **S** |
| FW-25 | `country-selection.blade.php` doesn't extend the layout | bare page, no header or cart badge | **S** |
| FW-26 | Home page category branches are dead `<button>`s | no handlers | **M** |
| FW-27 | No product sort or price filters | only category, subcategory, brand | **M** |
| FW-28 | Search has no debounce or minimum length | fires per keystroke, four `LIKE '%…%'` queries each | **S** |
| FW-29 | Hidden `shipping_charge` / `coupon_discount` inputs are the client's own arithmetic | `checkout.blade.php:472-473`, written by `updateTotals()` at `:626`, read back by both server endpoints | **S** |

---

## 3. Infrastructure gaps

| ID | Item | Effort |
|---|---|---|
| IW-01 | **No domain migrations in this app** — the 93-table schema is owned by `palVarnanDashboard` | **XL** |
| IW-02 | **Razorpay absent from `composer.lock`** — present on disk only | **S** |
| IW-03 | `composer.lock` stale against `composer.json` | **S** |
| IW-04 | **The entire checkout feature is uncommitted** — `app/Services/`, 10 models, `thankyou.blade.php`, plus both `test_*.php` files are untracked | **S** |
| IW-05 | **No CI** — no `.github/workflows/` | **M** |
| IW-06 | No Docker | **M** |
| IW-07 | No deployment script or config | **L** |
| IW-08 | No `.env.example` | **S** |
| IW-09 | No backup or restore procedure for a shared database | **M** |
| IW-10 | No monitoring or alerting | **M** |
| IW-11 | `composer run setup` runs `migrate --force` then a failing `npm run build` | **S** |
| IW-12 | `composer run dev` fails at the Vite step | **S** |
| IW-13 | **Zero domain migrations for any of the 93 tables** — schema drift is invisible to git | **XL** |
| IW-14 | **No 5xx alerting** — checkout failures are invisible until a customer complains | **M** |
| IW-15 | **No money-loss alert** — `payment_status = 'Paid'` written at `:324` *inside* the transaction that rolls back, and `product_transaction_logs` records no `razorpay_payment_id`, so a captured payment with no order row is undetectable by SQL | **M** |
| IW-16 | **`palVarnan1/` duplicate app directory** beside the working copy | **M** |
| IW-17 | `storage/framework/sessions` and compiled views committed in the dashboard app | **S** |
| IW-18 | **Theme assets committed**, including vendor demo imagery and the extracted `marketpro-ecommerce-multivendor-html-template-*` directory | **M** |
| IW-19 | **The purchased template is a multi-vendor theme; the schema has no vendor concept** | **XL** |
| IW-20 | `tests` and `personal_access_tokens` tables exist with no test suite or token auth | **M** |
| IW-21 | **Laravel Telescope tables present** (`telescope_entries`, `telescope_entries_tags`, `telescope_monitoring`) — in production MySQL | **M** |
| IW-22 | `Spatie` permission tables present (`roles`, `permissions`, `model_has_roles`, `model_has_permissions`, `role_has_permissions`) with no `HasRoles` trait on any model | **M** |

---

## 4. Abandoned features

Present in the template or schema, with **zero** storefront implementation evidence:

| Feature | Leftover evidence |
|---|---|
| Blog / news | `blog.html`, `blog-details.html` links |
| **Contact page** | 4 `contact.html` links — **no controller, route, view, or file** |
| Become a seller | `become-seller.html` |
| **Multi-vendor** | 4 `vendor*.html` links; the template is a multivendor theme with **no vendor concept in code or schema** |
| Alternate homepages | `index-two.html`, `index-three.html` |
| Search results page | header form `action="#"` |
| Order tracking | `ShiprocketService::trackAWB()` exists with no caller; `product_tracking` written, never read |
| Gift, milk, and purchase-order flows | 14 tables, 0 storefront code (BW-31) |
| Areas / pincode serviceability | `areas`, `area_assigns`, `all_india_pincodes`; `getCitiesByState()` uncalled |
| Offers / flash sale | `product_varient.flash_sale`, `.flash_sale_date`, `.hot_deals`, `.Popular_products` — no storefront UI |
| Pre-order | `carts.pre_order` stripped by `$fillable`; `product_varient.pre_order`, `.pre_note` unused |
| Low-stock alerts | `product_varient.low_stock`, `productstocks.low_stocks` — both `varchar`; no notification logic |
| Schedule slots | `product_slots.delivery_date`, `.dispatch_date` — written, never scheduled |
| 404 / 500 / maintenance pages | `withExceptions()` empty |
| Multi-currency | `countries.currency` never read |
| Reviews and testimonials | `reviews`, `testimonials` tables |
| Coupons | `coupons`, `newcoupons`, `offer_images`, `free_shipping_settings`, `today_deals` — no storefront logic |
| Notifications | `app_notifications`, `notifications`, `user_read_notifications`, `app_alerts`, `mail_otps`, `otps` |
| Returns and cancellations | `cancel_requests`, `product_refunds`, `milk_refunds` |
| Warehouse / branch | `warehouse`, `warehouse_branch`, `supplier_details`, `stock_transfers`, `stock_distributions` |

**Notable:** `newcoupons` exists alongside `coupons`, and `product_stock_batches` sits beside `productstocks` — parallel tables suggest at least one abandoned schema branch.

---

## 5. Effort summary

| Effort | Count of actionable items (BW + FW + IW) |
|---|---|
| **S** (< 1 h) | 36 |
| **M** (1–4 h) | 44 |
| **L** (1–3 d) | 18 |
| **XL** (> 3 d) | 8 |

The **S** bucket alone resolves BW-01, the two cart IDORs, the pickup-scheduling gap, the cross-app status mismatch, the Razorpay lock defect, and the uncommitted-work risk.

---

## 6. Where the value is

### Fix first — blocks revenue or leaks money
BW-01, BW-05, BW-10, BW-11, BW-17, BW-18, BW-22, BW-23, BW-24, BW-26, BW-32, FW-07, FW-29, IW-02, IW-04

### Fix next — correctness and trust
BW-02, BW-04, BW-07, BW-12, BW-13, BW-14, BW-15, BW-19, BW-25, BW-27, BW-29, BW-30, BW-52, FW-01, FW-02, FW-12, FW-16

### Fix when scheduled — structural
IW-01, IW-13, IW-19, FW-04, FW-06, BW-31, BW-38, BW-20, BW-21, BW-28, BW-37, BW-49

---

## 7. Related documents

- [04-feature-inventory.md](04-feature-inventory.md) — the F-numbered view of the same material
- [07-database-schema.md](07-database-schema.md) — the 93-table inventory
- [13-security-audit.md](13-security-audit.md) — SEC-numbered security work
- [20-known-issues.md](20-known-issues.md) — the bug register
- [21-technical-debt.md](21-technical-debt.md) — refactoring items
- [29-remediation-plan.md](29-remediation-plan.md) — the sequenced plan