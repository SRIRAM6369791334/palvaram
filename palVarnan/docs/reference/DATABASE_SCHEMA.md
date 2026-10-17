# DATABASE_SCHEMA.md

**Purpose:** Tables, keys, and the shared-database ownership story.

---

## 1. Database overview

- **Database:** `palvarnan_db` (MySQL)
- **Shared:** Used by `palVarnan` (storefront) and `palVarnanDashboard` (admin)
- **Tables:** **93** tables total (`VERIFIED_FROM_LIVE_SCHEMA`) per SQL dump `palvarnan_db (2).sql` and audit
  ([`../audit/VERIFIED_FACTS.md:29`](../audit/VERIFIED_FACTS.md#counts-verified_from_source))
- **Storefront migrations:** **0 domain migrations**; only 3 stock Laravel migrations ([`database/migrations/`](../../database/migrations))
  — schema is owned by dashboard app

`VERIFIED_FROM_SOURCE`/`VERIFIED_FROM_LIVE_SCHEMA`.

---

## 2. Key storefront tables (referenced by code)

Core domain tables used by controllers:

| Table | Model | Purpose | Key fields used |
|---|---|---|---|
| `users` | [`User`](../../app/Models/User.php) | Customers | id, name/email/phone/password, country_id |
| `user_addresses` | [`UserAddress`](../../app/Models/UserAddress.php) | Address book | user_id, address fields, city/state/pincode |
| `countries` | [`Country`](../../app/Models/Country.php) | Country gate | id, name |
| `states` | [`State`](../../app/Models/State.php) | Shipping charge lookup | name, shipping_charge |
| `districts` | [`District`](../../app/Models/District.php) | Cities by state | state_name, name |
| `categories` | [`Category`](../../app/Models/Category.php) | Catalog | id, category_name, slug, country_id |
| `sub_categories` | [`SubCategory`](../../app/Models/SubCategory.php) | Subcategories | id, subcategory_name, country_id |
| `brands` | [`Brand`](../../app/Models/Brand.php) | Brands | id, brand_name |
| `products` | [`Product`](../../app/Models/Product.php) | Products | id, slug, product_name, product_image, category_id, brand_id, country_id |
| `product_varient` | [`ProductVarient`](../../app/Models/ProductVarient.php) | Variants (table name: product_varient) | id, product_id, offer_price, mrp_price, product_gst, product_qty, weight, length, breadth, height, sku, varient_img, varient_name, unit_id |
| `productstocks` | [`ProductStock`](../../app/Models/ProductStock.php) | Stock by variant | pro_ver_id, availablestock, salestock |
| `product_child_images` | [`ProductChildImage`](../../app/Models/ProductChildImage.php) | Gallery | product_id, image |
| `carts` | [`Cart`](../../app/Models/Cart.php) | Cart lines | id, user_id, ip_addresscart, product_id, product_varient_id, product_quantity |
| `wishlists` | [`Wishlist`](../../app/Models/Wishlist.php) | Wishlist | id, user_id, product_id, product_varient_id |
| `product_orders` | [`ProductOrder`](../../app/Models/ProductOrder.php) | Master order | id, order_number, order_id, user_id, billing/shipping fields, subtotal/gst/shipping/total, status, payment_status/method, razorpay_ids, shiprocket_ids, awb_code, courier_name, shiprocket_status |
| `product_order_items` | [`ProductOrderItem`](../../app/Models/ProductOrderItem.php) | Line items | order_id, product_id, product_variant_id, product_name, product_image, variant_name, quantity, price, gst_rate, gst_amount, total |
| `product_order_user_addresses` | [`ProductOrderUserAddress`](../../app/Models/ProductOrderUserAddress.php) | Order address snapshot | order_id, user_id, address fields |
| `product_slots` | [`ProductSlot`](../../app/Models/ProductSlot.php) | Operational slots | order_id, product_id, product_varient_id, quantity, product_name, rates/totals, gst, delivery_status |
| `product_tracking` | [`ProductTracking`](../../app/Models/ProductTracking.php) | Tracking | order_id, user_id, delivery_status |
| `product_transaction_logs` | [`ProductTransactionLog`](../../app/Models/ProductTransactionLog.php) | Transaction log | order_id, user_id, order_date, order_amount, amount_credited, amount_debited |
| `home_promotions` | [`HomePromotion`](../../app/Models/HomePromotion.php) | Banners | country_id, status, sort_order, image etc. |
| `units` | [`Unit`](../../app/Models/Unit.php) | Units | short_name/name |
| `coupons` | [`Coupon`](../../app/Models/Coupon.php) | Coupons (not used) | coupon_name, discount, etc. |

`VERIFIED_FROM_SOURCE`.

---

## 3. Keys and relations (highlights)

- `product_orders`: `order_number` and `order_id` set to same string at creation ([`CheckoutController.php:298`](../../app/Http/Controllers/CheckoutController.php#L298)); related tables link via `order_id` (model relations use `order_id`).
- `product_order_items.order_id` links to `product_orders.id` in code ([`CheckoutController.php:374`](../../app/Http/Controllers/CheckoutController.php#L374)).
- `product_slots.order_id` links to `order_number` value in creation ([`CheckoutController.php:389`](../../app/Http/Controllers/CheckoutController.php#L389)); model relation also defined on `order_id` ([`ProductOrder.php:81-84`](../../app/Models/ProductOrder.php#L81-L84)).
- `productstocks.pro_ver_id` links to `product_varient.id`.
- `product_order_items.product_variant_id` links to `product_varient.id`.

For full FK map (93 tables), see the palvarnan-db-treemap skill reference and [`../07-database-schema.md`](../07-database-schema.md).

---

## 4. Shared-database ownership

- **Single source of truth:** `palvarnan_db` is shared. Dashboard owns migrations/schema evolution.
- **No domain migrations in storefront:** only 3 stock migrations → storefront cannot create domain schema via `php artisan migrate`.
- **Implications:** schema drift possible; cross-app coupling exists. Deployments affecting schema must coordinate.
- **Verification:** both `.env` files set `DB_DATABASE=palvarnan_db`. `VERIFIED_FROM_SOURCE`.

---

## 5. Related

- [`../07-database-schema.md`](../07-database-schema.md)
- [`MODELS_AND_RELATIONSHIPS.md`](./MODELS_AND_RELATIONSHIPS.md)
- [`../22-database-integrity.md`](../22-database-integrity.md)