# MODELS_AND_RELATIONSHIPS.md

**Purpose:** The 25 Eloquent models, fillables, and relations.

---

## 1. Models inventory (25)

Files in [`app/Models/`](../../app/Models): 25. `VERIFIED_FROM_SOURCE`.

| Model | Table | Traits | Key notes | Evidence |
|---|---|---|---|---|
| `Area.php` | `areas` (default) | `HasFactory` | fillable limited; relations vary | [`app/Models/Area.php`](../../app/Models/Area.php) |
| `AreaAssign.php` | `area_assigns` (default) | — | simple model | [`app/Models/AreaAssign.php`](../../app/Models/AreaAssign.php) |
| `Brand.php` | `brands` (default) | `HasFactory` | `products()` relation | [`app/Models/Brand.php`](../../app/Models/Brand.php) |
| `Cart.php` | `carts` (default) | — | `$fillable` includes ip/user/product/variant/quantity ([10-16](../../app/Models/Cart.php#L10-L16)); relations to Product, ProductVarient | [`app/Models/Cart.php`](../../app/Models/Cart.php) |
| `Category.php` | `categories` (default) | `HasFactory` | relations to products/country/subCategories (some broken by schema) | [`app/Models/Category.php`](../../app/Models/Category.php) |
| `Country.php` | `countries` | — | fillable minimal | [`app/Models/Country.php`](../../app/Models/Country.php) |
| `Coupon.php` | `coupons` | `HasFactory` | fillable 7 fields; **never queried** in code | [`app/Models/Coupon.php`](../../app/Models/Coupon.php); [`CheckoutController.php:6`](../../app/Http/Controllers/CheckoutController.php#L6) (import only) |
| `District.php` | `districts` | — | name join used | [`app/Models/District.php`](../../app/Models/District.php) |
| `HomePromotion.php` | `home_promotions` (default) | — | **empty model** (no fillable/relations) | [`app/Models/HomePromotion.php`](../../app/Models/HomePromotion.php) |
| `Product.php` | `products` (default) | `HasFactory`, `SoftDeletes` | fillable 24+; relations: country, category, brand, variants, productvari, childImages | [`app/Models/Product.php`](../../app/Models/Product.php) |
| `ProductChildImage.php` | `product_child_images` | — | belongs to product | [`app/Models/ProductChildImage.php`](../../app/Models/ProductChildImage.php) |
| `ProductOrder.php` | `product_orders` | `HasFactory` | **large fillable (46)**; relations: user, customer, items, slots, orderAddress, transactionLog, trackings; uses `order_id` joins | [`app/Models/ProductOrder.php`](../../app/Models/ProductOrder.php) |
| `ProductOrderItem.php` | `product_order_items` | `HasFactory` | fillable includes `product_image`, `variant_name`; belongs to product/variant | [`app/Models/ProductOrderItem.php`](../../app/Models/ProductOrderItem.php) |
| `ProductOrderUserAddress.php` | `product_order_user_addresses` | `HasFactory` | order/address snapshot model | [`app/Models/ProductOrderUserAddress.php`](../../app/Models/ProductOrderUserAddress.php) |
| `ProductSlot.php` | `product_slots` | `HasFactory` | operational packing table | [`app/Models/ProductSlot.php`](../../app/Models/ProductSlot.php) |
| `ProductStock.php` | `productstocks` | `HasFactory` | keyed by `pro_ver_id` to variant | [`app/Models/ProductStock.php`](../../app/Models/ProductStock.php) |
| `ProductTracking.php` | `product_tracking` | `HasFactory` | tracking | [`app/Models/ProductTracking.php`](../../app/Models/ProductTracking.php) |
| `ProductTransactionLog.php` | `product_transaction_logs` | `HasFactory` | transaction log | [`app/Models/ProductTransactionLog.php`](../../app/Models/ProductTransactionLog.php) |
| `ProductVarient.php` | `product_varient` | — | fillable includes pricing/gst/qty/weight dims; relations to country/product/category/unit | [`app/Models/ProductVarient.php`](../../app/Models/ProductVarient.php) |
| `State.php` | `states` | `HasFactory` | fillable includes `shipping_charge` | [`app/Models/State.php`](../../app/Models/State.php) |
| `SubCategory.php` | `sub_categories` (default) | — | relation `category()` may be misconfigured vs actual columns | [`app/Models/SubCategory.php`](../../app/Models/SubCategory.php); see [`../11-models-and-relationships.md`](../11-models-and-relationships.md) |
| `Unit.php` | `units` (default) | — | **empty model** | [`app/Models/Unit.php`](../../app/Models/Unit.php) |
| `User.php` | `users` (default) | `HasFactory`, `Notifiable` | fillable includes name/phone/country/password; casts password hashed, email_verified_at datetime | [`app/Models/User.php`](../../app/Models/User.php) |
| `UserAddress.php` | `user_addresses` | `HasFactory` | address book | [`app/Models/UserAddress.php`](../../app/Models/UserAddress.php) |
| `Wishlist.php` | `wishlists` | — | toggle/index/remove; no explicit `user()` relation defined in code shown | [`app/Models/Wishlist.php`](../../app/Models/Wishlist.php) |

**Notes:** 25 models total. `Unit` and `HomePromotion` empty. `VERIFIED_FROM_SOURCE`; also cross-reference [`../11-models-and-relationships.md`](../11-models-and-relationships.md).

---

## 2. Key relations (highlights)

- `ProductOrder` links via `order_id` to `ProductOrderItem` (uses PK in some cases), `ProductSlot`, `ProductOrderUserAddress`, `ProductTracking`, `ProductTransactionLog` ([`ProductOrder.php:75-98`](../../app/Models/ProductOrder.php#L75-L98)).
- `Cart` belongs to `Product` and `ProductVarient` ([`Cart.php:17-26`](../../app/Models/Cart.php#L17-L26)).
- `Wishlist` relates to `product` and `variant` ([`WishlistController`](../../app/Http/Controllers/WishlistController.php) uses eager loads; model relations present conceptually).
- `State` hasMany `District` via `state_name` join ([`State.php:20-24`](../../app/Models/State.php#L20-L24)).
- `Product` has `variants()` and `productvari()` (both `hasMany(ProductVarient::class)`) — redundant naming. `SoftDeletes` used. `VERIFIED_FROM_SOURCE`.

For full relation matrix (including known broken relations), see [`../11-models-and-relationships.md`](../11-models-and-relationships.md).

---

## 3. Related

- [`../11-models-and-relationships.md`](../11-models-and-relationships.md)
- [`DATABASE_SCHEMA.md`](./DATABASE_SCHEMA.md)
- [`CONTROLLERS.md`](./CONTROLLERS.md)