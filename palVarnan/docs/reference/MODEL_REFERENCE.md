# MODEL_REFERENCE — Per-model canonical reference (25 models)

> One subsection per model in `app/Models/` (25 files total). For each: class, namespace,
> `$table`, `$fillable`, `$hidden`/casts if present, relations with line citations, notes on
> defects (empty models, table-name mismatches), and usage evidence from `app/`.
>
> Empty models: [`Unit`](../../palVarnan/app/Models/Unit.php) and [`HomePromotion`](../../palVarnan/app/Models/HomePromotion.php).
> Table-name mismatches: `ProductVarient` → table `product_varient` (misspelled); `ProductStock` → table `productstocks` (no underscore).
>
> Evidence vocabulary: `VERIFIED_FROM_SOURCE`, `VERIFIED_FROM_LIVE_SCHEMA`, `INFERRED`,
> `UNVERIFIED`, `NOT_AVAILABLE`, `CONFLICTING_EVIDENCE`. **No credentials, connection
> strings, or PII are reproduced here.**
>
> See also: [`DATABASE_SCHEMA.md`](./DATABASE_SCHEMA.md), [`DATABASE_TABLE_REFERENCE.md`](./DATABASE_TABLE_REFERENCE.md),
> [`DATABASE_RELATIONSHIPS.md`](./DATABASE_RELATIONSHIPS.md), [`MIGRATION_AND_SQL_AUDIT.md`](./MIGRATION_AND_SQL_AUDIT.md).

---

## Model list (25)

1. [Area](#1-area)
2. [AreaAssign](#2-areaassign)
3. [Brand](#3-brand)
4. [Cart](#4-cart)
5. [Category](#5-category)
6. [Country](#6-country)
7. [Coupon](#7-coupon)
8. [District](#8-district)
9. [HomePromotion](#9-homepromotion)
10. [Product](#10-product)
11. [ProductChildImage](#11-productchildimage)
12. [ProductOrder](#12-productorder)
13. [ProductOrderItem](#13-productorderitem)
14. [ProductOrderUserAddress](#14-productorderuseraddress)
15. [ProductSlot](#15-productslot)
16. [ProductStock](#16-productstock)
17. [ProductTracking](#17-producttracking)
18. [ProductTransactionLog](#18-producttransactionlog)
19. [ProductVarient](#19-productvarient)
20. [State](#20-state)
21. [SubCategory](#21-subcategory)
22. [Unit](#22-unit)
23. [User](#23-user)
24. [UserAddress](#24-useraddress)
25. [Wishlist](#25-wishlist)

---

## 1. Area

- **Class/namespace:** `App\Models\Area` ([Area.php:1-17](../../palVarnan/app/Models/Area.php#L1-L17)) `VERIFIED_FROM_SOURCE`
- **Uses:** `HasFactory`, `HasMany` ([5-8](../../palVarnan/app/Models/Area.php#L5-L8))
- **Table:** default `areas` `VERIFIED_FROM_SOURCE` + `VERIFIED_FROM_LIVE_SCHEMA` ([dump 155431-155444](../../palvarnan_db%20(2).sql#L155431-L155444))
- **$fillable:** `["area_name", "area_pincode"]` ([line 12](../../palVarnan/app/Models/Area.php#L12))
- **Relations:**
  - `areaAssigns(): HasMany` → `AreaAssign::class, 'area_id'` ([13-16](../../palVarnan/app/Models/Area.php#L13-L16)) `VERIFIED_FROM_SOURCE`
- **Notes:** Model-only in storefront (no direct queries). FK `area_assigns.area_id`→`areas.id` exists in dump ([168803-168934](../../palvarnan_db%20(2).sql#L168803-L168934)). `VERIFIED_FROM_LIVE_SCHEMA`
- **Usage:** Referenced by `UserAddress::area()` ([UserAddress.php:41-44](../../palVarnan/app/Models/UserAddress.php#L41-L44)), `ProductOrderUserAddress::area()` ([ProductOrderUserAddress.php:37-40](../../palVarnan/app/Models/ProductOrderUserAddress.php#L37-L40)). No controller queries Area directly. `VERIFIED_FROM_SOURCE`

---

## 2. AreaAssign

- **Class/namespace:** `App\Models\AreaAssign` ([AreaAssign.php:1-24](../../palVarnan/app/Models/AreaAssign.php#L1-L24)) `VERIFIED_FROM_SOURCE`
- **Uses:** `HasFactory`, `BelongsTo`
- **Table:** default `area_assigns` `VERIFIED_FROM_LIVE_SCHEMA` ([155445-155472](../../palvarnan_db%20(2).sql#L155445-L155472))
- **$fillable:** `["area_id", "delivery_people_id"]` ([14](../../palVarnan/app/Models/AreaAssign.php#L14))
- **Relations:**
  - `area(): BelongsTo` → `Area::class, "area_id"` ([17-20](../../palVarnan/app/Models/AreaAssign.php#L17-L20)) `VERIFIED_FROM_SOURCE`
- **Notes:** No relation to `DeliveryPerson` defined (model may not exist in app). FKs: `area_assigns_ibfk_1`→`areas(id)`, `area_assigns_ibfk_2`→`delivery_people(id)` in dump ([168803-168934](../../palvarnan_db%20(2).sql#L168803-L168934)). `VERIFIED_FROM_LIVE_SCHEMA`
- **Usage:** Model-only. `VERIFIED_FROM_SOURCE`

---

## 3. Brand

- **Class/namespace:** `App\Models\Brand` ([Brand.php:1-27](../../palVarnan/app/Models/Brand.php#L1-L27)) `VERIFIED_FROM_SOURCE`
- **Uses:** `HasFactory`
- **Table:** default `brands` `VERIFIED_FROM_LIVE_SCHEMA` ([155473-155520](../../palvarnan_db%20(2).sql#L155473-L155520))
- **$fillable:** `['brand_name', 'brand_slug', 'brand_image', 'status', 'country_id']` ([12](../../palVarnan/app/Models/Brand.php#L12))
- **Casts:** `country_id => 'string'` ([14-16](../../palVarnan/app/Models/Brand.php#L14-L16))
- **Relations:**
  - `products(): HasMany` → `Product::class` ([18-21](../../palVarnan/app/Models/Brand.php#L18-L21))
  - `country(): BelongsTo` → `Country::class` ([23-26](../../palVarnan/app/Models/Brand.php#L23-L26))
- **Notes:** Unique `brands_brand_slug_unique` in dump. No FK for `brands.country_id` listed in constraints (logical only). `VERIFIED_FROM_LIVE_SCHEMA`
- **Usage:** Referenced in `ProductController` ([ProductController.php:5](../../palVarnan/app/Http/Controllers/ProductController.php#L5)), `Product::brand()` ([Product.php:25-27](../../palVarnan/app/Models/Product.php#L25-L27)). `VERIFIED_FROM_SOURCE`

---

## 4. Cart

- **Class/namespace:** `App\Models\Cart` ([Cart.php:1-26](../../palVarnan/app/Models/Cart.php#L1-L26)) `VERIFIED_FROM_SOURCE`
- **Uses:** none (Model only)
- **Table:** default `carts` `VERIFIED_FROM_LIVE_SCHEMA` ([155573-155617](../../palvarnan_db%20(2).sql#L155573-L155617))
- **$fillable:** `['ip_addresscart','user_id','product_id','product_varient_id','product_quantity']` ([9-15](../../palVarnan/app/Models/Cart.php#L9-L15))
- **Relations:**
  - `product(): BelongsTo` → `Product::class,'product_id'` ([17-20](../../palVarnan/app/Models/Cart.php#L17-L20))
  - `variant(): BelongsTo` → `ProductVarient::class,'product_varient_id'` ([22-25](../../palVarnan/app/Models/Cart.php#L22-L25))
- **Notes:** `$fillable` has `ip_addresscart` (misspelled). No FKs in dump. `CONFLICTING_EVIDENCE`
- **Usage:** `CartController` ([CartController.php:5](../../palVarnan/app/Http/Controllers/CartController.php#L5)), cleared in `CheckoutController` ([CheckoutController.php:419](../../palVarnan/app/Http/Controllers/CheckoutController.php#L419)). `VERIFIED_FROM_SOURCE`

---

## 5. Category

- **Class/namespace:** `App\Models\Category` ([Category.php:1-34](../../palVarnan/app/Models/Category.php#L1-L34)) `VERIFIED_FROM_SOURCE`
- **Uses:** `HasFactory`
- **Table:** default `categories` `VERIFIED_FROM_LIVE_SCHEMA` ([155618-155633](../../palvarnan_db%20(2).sql#L155618-L155633))
- **$fillable:** `["category_name","category_image","category_banner","category_icon","is_gift","country_id"]` ([12](../../palVarnan/app/Models/Category.php#L12))
- **Casts:** `country_id => 'string'` ([14-16](../../palVarnan/app/Models/Category.php#L14-L16))
- **Relations:**
  - `products(): HasMany` → `Product::class` ([19-22](../../palVarnan/app/Models/Category.php#L19-L22))
  - `country(): BelongsTo` → `Country::class` ([24-27](../../palVarnan/app/Models/Category.php#L24-L27))
  - `subCategories(): HasMany` → `SubCategory::class` ([29-32](../../palVarnan/app/Models/Category.php#L29-L32))
- **Notes:** `subCategories()` uses default FK `category_id` but `sub_categories` has **no `category_id` column** in dump (has `category_name`) — relation is **broken**. `CONFLICTING_EVIDENCE`. No FK for `categories.country_id` in constraints. `VERIFIED_FROM_LIVE_SCHEMA`
- **Usage:** `ProductController` ([ProductController.php:7](../../palVarnan/app/Http/Controllers/ProductController.php#L7)), queried via FIND_IN_SET in `HomeController` ([HomeController.php:24](../../palVarnan/app/Http/Controllers/HomeController.php#L24)). `VERIFIED_FROM_SOURCE`

---

## 6. Country

- **Class/namespace:** `App\Models\Country` ([Country.php:1-14](../../palVarnan/app/Models/Country.php#L1-L14)) `VERIFIED_FROM_SOURCE`
- **Uses:** `Model`
- **Table:** `countries` ([line 10](../../palVarnan/app/Models/Country.php#L10)) `VERIFIED_FROM_LIVE_SCHEMA` ([164338-164374](../../palvarnan_db%20(2).sql#L164338-L164374))
- **$fillable:** `['name','currency']` ([line 12](../../palVarnan/app/Models/Country.php#L12))
- **Relations:** none declared
- **Notes:** Referenced as FK target (users, products, sub_categories, product_orders). `VERIFIED_FROM_LIVE_SCHEMA`
- **Usage:** `CountryController` ([CountryController.php:5](../../palVarnan/app/Http/Controllers/CountryController.php#L5)), relations from others. `VERIFIED_FROM_SOURCE`

---

## 7. Coupon

- **Class/namespace:** `App\Models\Coupon` ([Coupon.php:1-23](../../palVarnan/app/Models/Coupon.php#L1-L23)) `VERIFIED_FROM_SOURCE`
- **Uses:** `HasFactory`
- **Table:** `coupons` ([line 12](../../palVarnan/app/Models/Coupon.php#L12)) `VERIFIED_FROM_LIVE_SCHEMA` ([164375-164472](../../palvarnan_db%20(2).sql#L164375-L164472))
- **$fillable:** `['coupon_name','codename','discounttype','discount','mini_amt','start_date','end_date']` ([14-22](../../palVarnan/app/Models/Coupon.php#L14-L22))
- **Relations:** none
- **Notes:** `coupon_name` in fillable — not obvious as column in dump (dump has `codename`, `discount`, etc.). **Imported but never queried** in app code. `CONFLICTING_EVIDENCE`
- **Usage:** `use App\Models\Coupon;` in `CheckoutController` ([CheckoutController.php:6](../../palVarnan/app/Http/Controllers/CheckoutController.php#L6)); no `Coupon::` calls found in `app/`. `VERIFIED_FROM_SOURCE`

---

## 8. District

- **Class/namespace:** `App\Models\District` ([District.php:1-19](../../palVarnan/app/Models/District.php#L1-L19)) `VERIFIED_FROM_SOURCE`
- **Uses:** `HasFactory`
- **Table:** `districts` ([line 12](../../palVarnan/app/Models/District.php#L12)) `VERIFIED_FROM_LIVE_SCHEMA` ([164473-164531](../../palvarnan_db%20(2).sql#L164473-L164531))
- **$fillable:** `['name','state_name','state_id']` ([14-18](../../palVarnan/app/Models/District.php#L14-L18))
- **Relations:** none
- **Notes:** Dump has `state_id int`, `state_name`. No FK in constraints shown. `VERIFIED_FROM_LIVE_SCHEMA`
- **Usage:** Queried in `CheckoutController` ([CheckoutController.php:662](../../palVarnan/app/Http/Controllers/CheckoutController.php#L662)). `VERIFIED_FROM_SOURCE`

---

## 9. HomePromotion

- **Class/namespace:** `App\Models\HomePromotion` ([HomePromotion.php:1-10](../../palVarnan/app/Models/HomePromotion.php#L1-L10)) `VERIFIED_FROM_SOURCE`
- **Uses:** `Model`
- **Table:** default `home_promotions` `VERIFIED_FROM_LIVE_SCHEMA` ([164703-164732](../../palvarnan_db%20(2).sql#L164703-L164732))
- **$fillable:** none (empty)
- **Relations:** none
- **Notes:** **Empty model** (only `//`). No fillable/relations defined. `VERIFIED_FROM_SOURCE`
- **Usage:** Queried directly in `HomeController` ([HomeController.php:19-22](../../palVarnan/app/Http/Controllers/HomeController.php#L19-L22)). `VERIFIED_FROM_SOURCE`

---

## 10. Product

- **Class/namespace:** `App\Models\Product` ([Product.php:1-40](../../palVarnan/app/Models/Product.php#L1-L40)) `VERIFIED_FROM_SOURCE`
- **Uses:** `HasFactory`, `SoftDeletes`
- **Table:** default `products` `VERIFIED_FROM_LIVE_SCHEMA` ([165565-165622](../../palvarnan_db%20(2).sql#L165565-L165622))
- **$fillable:** `['country_id','category_id','brand_id','product_name','slug','product_quantity','offer_price','mrp_price','product_mrp_price','product_regular_price','product_description','product_image','product_specification','unit_value','product_value','subcategory_id','cate_name','subcate_name','brand_name','brand_material','brand_type','approval_days','is_gift','warehouse_id','branch_id']` ([12-15](../../palVarnan/app/Models/Product.php#L12-L15))
- **Relations:**
  - `country(): BelongsTo` ([17-19](../../palVarnan/app/Models/Product.php#L17-L19))
  - `category(): BelongsTo` ([21-23](../../palVarnan/app/Models/Product.php#L21-L23))
  - `brand(): BelongsTo` ([25-27](../../palVarnan/app/Models/Product.php#L25-L27))
  - `variants(): HasMany` → `ProductVarient::class` ([29-31](../../palVarnan/app/Models/Product.php#L29-L31))
  - `productvari(): HasMany` → `ProductVarient::class` ([33-35](../../palVarnan/app/Models/Product.php#L33-L35)) **duplicate**
  - `childImages(): HasMany` → `ProductChildImage::class,'product_id'` ([37-39](../../palVarnan/app/Models/Product.php#L37-L39))
- **Notes:** Duplicate relation `variants()` and `productvari()` same target. `childImages()` exists but not mentioned in existing docs. **Code reads** `product->product_price` fallback in `CheckoutController` ([CheckoutController.php:367](../../palVarnan/app/Http/Controllers/CheckoutController.php#L367)) but no such column in fillable/dump. `CONFLICTING_EVIDENCE`. FKs: `products_warehouse_id_foreign`→warehouse, `fk_product_orders_country`/others as noted. `VERIFIED_FROM_LIVE_SCHEMA`
- **Usage:** `ProductController` ([ProductController.php:8,118](../../palVarnan/app/Http/Controllers/ProductController.php#L8-L118)), relations used throughout. `VERIFIED_FROM_SOURCE`

---

## 11. ProductChildImage

- **Class/namespace:** `App\Models\ProductChildImage` ([ProductChildImage.php:1-18](../../palVarnan/app/Models/ProductChildImage.php#L1-L18)) `VERIFIED_FROM_SOURCE`
- **Uses:** `HasFactory`
- **Table:** `product_child_images` ([line 11](../../palVarnan/app/Models/ProductChildImage.php#L11)) `VERIFIED_FROM_LIVE_SCHEMA` ([165670-165699](../../palvarnan_db%20(2).sql#L165670-L165699))
- **$fillable:** `['product_id','product_child_image','variant_id']` ([line 13](../../palVarnan/app/Models/ProductChildImage.php#L13))
- **Relations:**
  - `product(): BelongsTo` → `Product::class,'product_id'` ([15-17](../../palVarnan/app/Models/ProductChildImage.php#L15-L17))
- **Notes:** No FK in dump. `variant_id` present (no relation declared to ProductVarient). PK is `id` only. `VERIFIED_FROM_LIVE_SCHEMA`
- **Usage:** Eager-loaded in `ProductController` ([ProductController.php:118](../../palVarnan/app/Http/Controllers/ProductController.php#L118)). `VERIFIED_FROM_SOURCE`

---

## 12. ProductOrder

- **Class/namespace:** `App\Models\ProductOrder` ([ProductOrder.php:1-99](../../palVarnan/app/Models/ProductOrder.php#L1-L99)) `VERIFIED_FROM_SOURCE`
- **Uses:** `HasFactory`, relations traits
- **Table:** `product_orders` ([line 16](../../palVarnan/app/Models/ProductOrder.php#L16)) `VERIFIED_FROM_LIVE_SCHEMA` ([165700-165765](../../palvarnan_db%20(2).sql#L165700-L165765))
- **$fillable:** 44 items including `user_id,order_number,order_id,billing_name,...,billing_state,...,shipping_state,...,courier_name,...,shiprocket_* ,awb_code,pickup_scheduled,manifest_url,label_url,invoice_url,shiprocket_status,cancellation_reason,created_at,updated_at` ([18-63](../../palVarnan/app/Models/ProductOrder.php#L18-L63))
- **Relations:**
  - `user(): BelongsTo` → `User::class,'user_id','id'` ([65-68](../../palVarnan/app/Models/ProductOrder.php#L65-L68))
  - `customer(): BelongsTo` same as user ([70-73](../../palVarnan/app/Models/ProductOrder.php#L70-L73)) **duplicate**
  - `items(): HasMany` → `ProductOrderItem::class,'order_id'` (default local key `id`, bigint) ([75-78](../../palVarnan/app/Models/ProductOrder.php#L75-L78))
  - `slots(): HasMany` → `ProductSlot::class,'order_id','order_id'` ([80-83](../../palVarnan/app/Models/ProductOrder.php#L80-L83))
  - `orderAddress(): HasOne` → `ProductOrderUserAddress::class,'order_id','order_id'` ([85-88](../../palVarnan/app/Models/ProductOrder.php#L85-L88))
  - `transactionLog(): HasOne` → `ProductTransactionLog::class,'order_id','order_id'` ([90-93](../../palVarnan/app/Models/ProductOrder.php#L90-L93))
  - `trackings(): HasMany` → `ProductTracking::class,'order_id','order_id'` ([95-98](../../palVarnan/app/Models/ProductOrder.php#L95-L98))
- **Notes:** **Fillables absent from dump:** `billing_state`, `shipping_state`, `courier_name`, `shiprocket_order_id`, `shiprocket_shipment_id`, `awb_code`, `pickup_scheduled`, `manifest_url`, `label_url`, `invoice_url`, `shiprocket_status`, `cancellation_reason`. **`discount` not in fillable** (claimed in docs). **Keying:** `items()` uses local `id` (bigint) matching `ProductOrderItem.order_id` written as bigint; siblings use varchar mirror `order_id`. Dual identifier (`id` bigint + `order_number`/`order_id` varchar). FKs in dump: `fk_product_orders_country`→countries, `product_orders_user_id_foreign`→users. `CONFLICTING_EVIDENCE`. Unique `product_orders_order_number_unique`. `VERIFIED_FROM_LIVE_SCHEMA`
- **Usage:** Created in `CheckoutController` ([CheckoutController.php:295-327](../../palVarnan/app/Http/Controllers/CheckoutController.php#L295-L327)). `VERIFIED_FROM_SOURCE`

---

## 13. ProductOrderItem

- **Class/namespace:** `App\Models\ProductOrderItem` ([ProductOrderItem.php:1-37](../../palVarnan/app/Models/ProductOrderItem.php#L1-L37)) `VERIFIED_FROM_SOURCE`
- **Uses:** `HasFactory`
- **Table:** `product_order_items` ([line 12](../../palVarnan/app/Models/ProductOrderItem.php#L12)) `VERIFIED_FROM_LIVE_SCHEMA` ([165766-165812](../../palvarnan_db%20(2).sql#L165766-L165812))
- **$fillable:** `['order_id','product_id','product_variant_id','product_name','product_image','variant_name','quantity','price','gst_rate','gst_amount','total']` ([14-26](../../palVarnan/app/Models/ProductOrderItem.php#L14-L26))
- **Relations:**
  - `product(): BelongsTo` → `Product::class,'product_id'` ([28-31](../../palVarnan/app/Models/ProductOrderItem.php#L28-L31))
  - `variant(): BelongsTo` → `ProductVarient::class,'product_variant_id'` ([33-36](../../palVarnan/app/Models/ProductOrderItem.php#L33-L36))
- **Notes:** **`product_image` not in dump DDL** for this table; code writes it ([CheckoutController.php:378](../../palVarnan/app/Http/Controllers/CheckoutController.php#L378)). `order_id` stored as bigint (`$order->id`) at [CheckoutController.php:374](../../palVarnan/app/Http/Controllers/CheckoutController.php#L374). No FKs in dump. `CONFLICTING_EVIDENCE`. `VERIFIED_FROM_LIVE_SCHEMA`
- **Usage:** Created in `CheckoutController` ([CheckoutController.php:373-385](../../palVarnan/app/Http/Controllers/CheckoutController.php#L373-L385)). `VERIFIED_FROM_SOURCE`

---

## 14. ProductOrderUserAddress

- **Class/namespace:** `App\Models\ProductOrderUserAddress` ([ProductOrderUserAddress.php:1-41](../../palVarnan/app/Models/ProductOrderUserAddress.php#L1-L41)) `VERIFIED_FROM_SOURCE`
- **Uses:** `HasFactory`, `BelongsTo`
- **Table:** `product_order_user_addresses` ([line 13](../../palVarnan/app/Models/ProductOrderUserAddress.php#L13)) `VERIFIED_FROM_LIVE_SCHEMA` ([165813-165874](../../palvarnan_db%20(2).sql#L165813-L165874))
- **$fillable:** includes `user_id,order_id,firstname,secondname,address_line_one,address_line_two,landmark,area_id,phonecode,city,address_phone_number,address_type_id,address_type_name,address_type_others_name` ([15-30](../../palVarnan/app/Models/ProductOrderUserAddress.php#L15-L30))
- **Relations:**
  - `productOrder(): BelongsTo` → `ProductOrder::class,"order_id","order_id"` ([32-35](../../palVarnan/app/Models/ProductOrderUserAddress.php#L32-L35))
  - `area(): BelongsTo` → `Area::class,"area_id","id"` ([37-40](../../palVarnan/app/Models/ProductOrderUserAddress.php#L37-L40))
- **Notes:** Code writes `order_id` as `$orderNumber` (varchar) at [CheckoutController.php:332](../../palVarnan/app/Http/Controllers/CheckoutController.php#L332). No FKs in dump. Links via varchar mirror. `VERIFIED_FROM_LIVE_SCHEMA`
- **Usage:** Created in `CheckoutController` ([CheckoutController.php:330-345](../../palVarnan/app/Http/Controllers/CheckoutController.php#L330-L345)). `VERIFIED_FROM_SOURCE`

---

## 15. ProductSlot

- **Class/namespace:** `App\Models\ProductSlot` ([ProductSlot.php:1-51](../../palVarnan/app/Models/ProductSlot.php#L1-L51)) `VERIFIED_FROM_SOURCE`
- **Uses:** `HasFactory`
- **Table:** `product_slots` ([line 12](../../palVarnan/app/Models/ProductSlot.php#L12)) `VERIFIED_FROM_LIVE_SCHEMA` ([165875-165955](../../palvarnan_db%20(2).sql#L165875-L165955))
- **$fillable:** extensive operational fields including `delivery_date,order_id,product_id,product_varient_id,quantity,product_name,product_rate,product_total,gst_amt,gst_per,product_value,delivery_status,order_delivered_time,deliver_person_id,preorder,dispatch_date,is_cancelled,cancel_reason,created_at,updated_at` ([14-35](../../palVarnan/app/Models/ProductSlot.php#L14-L35))
- **Relations:**
  - `productOrder(): BelongsTo` → `ProductOrder::class,"order_id","order_id"` ([37-40](../../palVarnan/app/Models/ProductSlot.php#L37-L40))
  - `product(): BelongsTo` → `Product::class,'product_id'` ([42-45](../../palVarnan/app/Models/ProductSlot.php#L42-L45))
  - `productVarient(): BelongsTo` → `ProductVarient::class,'product_varient_id'` ([47-50](../../palVarnan/app/Models/ProductSlot.php#L47-L50))
- **Notes:** Code writes `order_id` as `$orderNumber` (varchar) at [CheckoutController.php:389](../../palVarnan/app/Http/Controllers/CheckoutController.php#L389). No FKs in dump. `VERIFIED_FROM_LIVE_SCHEMA`
- **Usage:** Created in `CheckoutController` ([CheckoutController.php:388-400](../../palVarnan/app/Http/Controllers/CheckoutController.php#L388-L400)). `VERIFIED_FROM_SOURCE`

---

## 16. ProductStock

- **Class/namespace:** `App\Models\ProductStock` ([ProductStock.php:1-39](../../palVarnan/app/Models/ProductStock.php#L1-L39)) `VERIFIED_FROM_SOURCE`
- **Uses:** `HasFactory`
- **Table:** `productstocks` ([line 12](../../palVarnan/app/Models/ProductStock.php#L12)) **table-name mismatch (no underscore)** `VERIFIED_FROM_LIVE_SCHEMA` ([165623-165669](../../palvarnan_db%20(2).sql#L165623-L165669))
- **$fillable:** `['productid','category_id','subcategory_id','pro_ver_id','productname','overallstock','availablestock','salestock','low_stocks','last_stockupdate_date','country_id','warehouse_id','branch_id']` ([14-28](../../palVarnan/app/Models/ProductStock.php#L14-L28))
- **Relations:**
  - `product(): BelongsTo` → `Product::class,'productid'` ([30-33](../../palVarnan/app/Models/ProductStock.php#L30-L33))
  - `variant(): BelongsTo` → `ProductVarient::class,'pro_ver_id'` ([35-38](../../palVarnan/app/Models/ProductStock.php#L35-L38))
- **Notes:** Columns use `productid`, `pro_ver_id` (not snake_case). Misspellings `availablestock`, `salestock`, `low_stocks`. FKs: `productstocks_branch_id_foreign`→warehouse_branch, `productstocks_warehouse_id_foreign`→warehouse in dump. No FKs to products/product_varient. `VERIFIED_FROM_LIVE_SCHEMA`
- **Usage:** Stock decrement in `CheckoutController` ([CheckoutController.php:403-407](../../palVarnan/app/Http/Controllers/CheckoutController.php#L403-L407)). `VERIFIED_FROM_SOURCE`

---

## 17. ProductTracking

- **Class/namespace:** `App\Models\ProductTracking` ([ProductTracking.php:1-19](../../palVarnan/app/Models/ProductTracking.php#L1-L19)) `VERIFIED_FROM_SOURCE`
- **Uses:** `HasFactory`
- **Table:** `product_tracking` ([line 12](../../palVarnan/app/Models/ProductTracking.php#L12)) `VERIFIED_FROM_LIVE_SCHEMA` ([165956-166027](../../palvarnan_db%20(2).sql#L165956-L166027))
- **$fillable:** `["user_id","order_id","delivery_status"]` ([14-18](../../palVarnan/app/Models/ProductTracking.php#L14-L18))
- **Relations:** none declared
- **Notes:** No relations. Code writes `order_id` as `$orderNumber` (varchar) at [CheckoutController.php:361](../../palVarnan/app/Http/Controllers/CheckoutController.php#L361). No FKs in dump. `VERIFIED_FROM_LIVE_SCHEMA`
- **Usage:** Created in `CheckoutController` ([CheckoutController.php:359-363](../../palVarnan/app/Http/Controllers/CheckoutController.php#L359-L363)). `VERIFIED_FROM_SOURCE`

---

## 18. ProductTransactionLog

- **Class/namespace:** `App\Models\ProductTransactionLog` ([ProductTransactionLog.php:1-23](../../palVarnan/app/Models/ProductTransactionLog.php#L1-L23)) `VERIFIED_FROM_SOURCE`
- **Uses:** `HasFactory`
- **Table:** `product_transaction_logs` ([line 12](../../palVarnan/app/Models/ProductTransactionLog.php#L12)) `VERIFIED_FROM_LIVE_SCHEMA` ([166028-166046](../../palvarnan_db%20(2).sql#L166028-L166046))
- **$fillable:** `['order_id','slot_id','order_date','order_amount','amount_credited','amount_debited','user_id']` ([14-22](../../palVarnan/app/Models/ProductTransactionLog.php#L14-L22))
- **Relations:** none declared
- **Notes:** No relations. Code writes `order_id` as `$orderNumber` (varchar) at [CheckoutController.php:349](../../palVarnan/app/Http/Controllers/CheckoutController.php#L349). No FKs in dump. `VERIFIED_FROM_LIVE_SCHEMA`
- **Usage:** Created in `CheckoutController` ([CheckoutController.php:348-356](../../palVarnan/app/Http/Controllers/CheckoutController.php#L348-L356)). `VERIFIED_FROM_SOURCE`

---

## 19. ProductVarient

- **Class/namespace:** `App\Models\ProductVarient` ([ProductVarient.php:1-30](../../palVarnan/app/Models/ProductVarient.php#L1-L30)) `VERIFIED_FROM_SOURCE`
- **Uses:** `Model`
- **Table:** `product_varient` ([line 10](../../palVarnan/app/Models/ProductVarient.php#L10)) **table-name mismatch (misspelled)** `VERIFIED_FROM_LIVE_SCHEMA` ([166047-166407](../../palvarnan_db%20(2).sql#L166047-L166407))
- **$fillable:** `['country_id','categoryid','subcategoryid','product_id','sku','barcode','unit_id','varient','value','offer_price','mrp_price','product_qty','product_gst','low_stock','hot_deals','Popular_products','pre_order','pre_note','product_gst','subcatename','varient_img','varient_name','size_value','flash_sale','flash_sale_date','color_id','size_id']` ([line 12](../../palVarnan/app/Models/ProductVarient.php#L12)) — note duplicate `product_gst`
- **Relations:**
  - `country(): BelongsTo` → `Country::class` ([15-17](../../palVarnan/app/Models/ProductVarient.php#L15-L17))
  - `product(): BelongsTo` → `Product::class` ([19-21](../../palVarnan/app/Models/ProductVarient.php#L19-L21))
  - `category(): BelongsTo` → `Category::class` ([23-25](../../palVarnan/app/Models/ProductVarient.php#L23-L25)) **uses default `category_id` but actual column is `categoryid`**
  - `unit(): BelongsTo` → `Unit::class` ([27-29](../../palVarnan/app/Models/ProductVarient.php#L27-L29))
- **Notes:** **`categoryid` column name** (not `category_id`); relation is **broken** (defaults to `category_id`). `subcategoryid` also non-standard. `varient` is `int(11)`. `created_at` has `ON UPDATE current_timestamp()` in dump. FKs: `product_varient_branch_id_foreign`→warehouse_branch, `product_varient_warehouse_id_foreign`→warehouse. No FK for categoryid/subcategoryid. Unique `product_varient_sku_unique`. `CONFLICTING_EVIDENCE`. `VERIFIED_FROM_LIVE_SCHEMA`
- **Usage:** Referenced in `ProductController` ([ProductController.php:118](../../palVarnan/app/Http/Controllers/ProductController.php#L118)), stock sync in `CheckoutController` ([CheckoutController.php:411-415](../../palVarnan/app/Http/Controllers/CheckoutController.php#L411-L415)), relations from Cart/Wishlist/ProductOrderItem/ProductSlot/ProductStock. `VERIFIED_FROM_SOURCE`

---

## 20. State

- **Class/namespace:** `App\Models\State` ([State.php:1-24](../../palVarnan/app/Models/State.php#L1-L24)) `VERIFIED_FROM_SOURCE`
- **Uses:** `HasFactory`
- **Table:** `states` ([line 12](../../palVarnan/app/Models/State.php#L12)) `VERIFIED_FROM_LIVE_SCHEMA` ([166431-166601](../../palvarnan_db%20(2).sql#L166431-L166601))
- **$fillable:** `['name','code','shipping_charge']` ([14-18](../../palVarnan/app/Models/State.php#L14-L18))
- **Relations:**
  - `districts(): HasMany` → `District::class,'state_name','name'` ([20-23](../../palVarnan/app/Models/State.php#L20-L23)) **joins by name, not id**
- **Notes:** Dump columns `name varchar(30)`, `country_id int`, charset latin1. Fillable includes `code` not obvious in dump columns shown. Relation keyed by `state_name` is **defective** vs `districts.state_id`. `CONFLICTING_EVIDENCE`. `VERIFIED_FROM_LIVE_SCHEMA` (partial)
- **Usage:** Queried in `CheckoutController` ([CheckoutController.php:71](../../palVarnan/app/Http/Controllers/CheckoutController.php#L71)), `ProfileController` ([ProfileController.php:94](../../palVarnan/app/Http/Controllers/ProfileController.php#L94)). `VERIFIED_FROM_SOURCE`

---

## 21. SubCategory

- **Class/namespace:** `App\Models\SubCategory` ([SubCategory.php:1-15](../../palVarnan/app/Models/SubCategory.php#L1-L15)) `VERIFIED_FROM_SOURCE`
- **Uses:** `Model`
- **Table:** default `sub_categories` `VERIFIED_FROM_LIVE_SCHEMA` ([166602-166645](../../palvarnan_db%20(2).sql#L166602-L166645))
- **$fillable:** `['category_name','subcategory_image','subcategory_name','category_display','is_gift','country_id']` ([line 9](../../palVarnan/app/Models/SubCategory.php#L9))
- **Relations:**
  - `category(): BelongsTo` → `Category::class,'category_id'` ([11-14](../../palVarnan/app/Models/SubCategory.php#L11-L14)) **uses `category_id` but column doesn't exist**
- **Notes:** **No `category_id` column** in dump; dump has `category_name`. Relation is **broken**. FK exists for `country_id` only: `sub_categories_country_id_foreign`→`countries(id)` CASCADE ([168803-168934](../../palvarnan_db%20(2).sql#L168803-L168934)). `CONFLICTING_EVIDENCE`. `VERIFIED_FROM_LIVE_SCHEMA`
- **Usage:** `ProductController` ([ProductController.php:9](../../palVarnan/app/Http/Controllers/ProductController.php#L9)). `VERIFIED_FROM_SOURCE`

---

## 22. Unit

- **Class/namespace:** `App\Models\Unit` ([Unit.php:1-10](../../palVarnan/app/Models/Unit.php#L1-L10)) `VERIFIED_FROM_SOURCE`
- **Uses:** `Model`
- **Table:** default `units` `VERIFIED_FROM_LIVE_SCHEMA` ([167441-167471](../../palvarnan_db%20(2).sql#L167441-L167471))
- **$fillable:** none (empty model)
- **Relations:** none
- **Notes:** **Empty model** — only `//`. Referenced by `ProductVarient::unit()` ([ProductVarient.php:27-29](../../palVarnan/app/Models/ProductVarient.php#L27-L29)). `VERIFIED_FROM_SOURCE`
- **Usage:** Eager-loaded via `variants.unit` in `ProductController` ([ProductController.php:118](../../palVarnan/app/Http/Controllers/ProductController.php#L118)). `VERIFIED_FROM_SOURCE`

---

## 23. User

- **Class/namespace:** `App\Models\User` ([User.php:1-44](../../palVarnan/app/Models/User.php#L1-L44)) `VERIFIED_FROM_SOURCE`
- **Uses:** `HasFactory`, `Notifiable`, `Authenticatable`
- **Table:** default `users` `VERIFIED_FROM_LIVE_SCHEMA` ([167472-167520](../../palvarnan_db%20(2).sql#L167472-L167520))
- **$fillable:** `['country_id','name','email','phone_number','first_name','last_name','password']` ([line 20](../../palVarnan/app/Models/User.php#L20))
- **$hidden:** `['password','remember_token']` ([27-30](../../palVarnan/app/Models/User.php#L27-L30))
- **Casts:** `email_verified_at => 'datetime'`, `password => 'hashed'` ([37-43](../../palVarnan/app/Models/User.php#L37-L43))
- **Relations:** none declared in model
- **Notes:** Auth model. Uniques: `users_email_unique`, `users_phone_number_unique`, `users_user_id_unique`, `users_phone_unique`. FK `fk_users_country`→countries. Other models reference User via relations (ProductOrder has `user()`/`customer()`; UserAddress has `user()` targeting `user_id` as local key). `VERIFIED_FROM_LIVE_SCHEMA`
- **Usage:** Auth (`AuthController`), profile (`ProfileController`). `VERIFIED_FROM_SOURCE`

---

## 24. UserAddress

- **Class/namespace:** `App\Models\UserAddress` ([UserAddress.php:1-52](../../palVarnan/app/Models/UserAddress.php#L1-L52)) `VERIFIED_FROM_SOURCE`
- **Uses:** `HasFactory`, `BelongsTo`
- **Table:** `user_addresses` ([line 13](../../palVarnan/app/Models/UserAddress.php#L13)) `VERIFIED_FROM_LIVE_SCHEMA` ([167521-167645](../../palvarnan_db%20(2).sql#L167521-L167645))
- **$fillable:** `['user_id','address_username','address_first_name','address_last_name','phone_code','address_line_one','address_line_two','landmark','pincode','area_id','city','area_name','state','address_phone_number','address_type_id','address_type_name','address_type_others_name','email']` ([15-34](../../palVarnan/app/Models/UserAddress.php#L15-L34))
- **Relations:**
  - `user(): BelongsTo` → `User::class,"user_id","user_id"` ([36-39](../../palVarnan/app/Models/UserAddress.php#L36-L39)) — targets non-standard local key
  - `area(): BelongsTo` → `Area::class,"area_id","id"` ([41-44](../../palVarnan/app/Models/UserAddress.php#L41-L44))
  - `country(): BelongsTo` → `Country::class,'area_id'` ([47-50](../../palVarnan/app/Models/UserAddress.php#L47-L50)) **defect: uses area_id as FK to countries**
- **Notes:** **`country()` relation is broken** (points to areas via area_id, not countries). `user()` relation targets `users.user_id` as local key (non-standard). Dump columns include `state varchar(225)`, `address_phone_number bigint`, `pincode int`. No FKs in dump. `CONFLICTING_EVIDENCE`. `VERIFIED_FROM_LIVE_SCHEMA`
- **Usage:** `CheckoutController` ([CheckoutController.php:42,558](../../palVarnan/app/Http/Controllers/CheckoutController.php#L42-L558)), `ProfileController` ([ProfileController.php:90](../../palVarnan/app/Http/Controllers/ProfileController.php#L90)). `VERIFIED_FROM_SOURCE`

---

## 25. Wishlist

- **Class/namespace:** `App\Models\Wishlist` ([Wishlist.php:1-26](../../palVarnan/app/Models/Wishlist.php#L1-L26)) `VERIFIED_FROM_SOURCE`
- **Uses:** `Model`
- **Table:** `wishlists` ([line 9](../../palVarnan/app/Models/Wishlist.php#L9)) `VERIFIED_FROM_LIVE_SCHEMA` ([167646-167669](../../palvarnan_db%20(2).sql#L167646-L167669))
- **$fillable:** `['ip_address','user_id','product_id','product_varient_id','product_quantity']` ([11-14](../../palVarnan/app/Models/Wishlist.php#L11-L14))
- **Relations:**
  - `product(): BelongsTo` → `Product::class` ([16-19](../../palVarnan/app/Models/Wishlist.php#L16-L19))
  - `variant(): BelongsTo` → `ProductVarient::class,'product_varient_id'` ([21-24](../../palVarnan/app/Models/Wishlist.php#L21-L24))
- **Notes:** **No `user()` relation** defined despite having `user_id`. No FKs in dump. `CONFLICTING_EVIDENCE`. `VERIFIED_FROM_LIVE_SCHEMA`
- **Usage:** `WishlistController` ([WishlistController.php:4](../../palVarnan/app/Http/Controllers/WishlistController.php#L4)). `VERIFIED_FROM_SOURCE`

---

## Summary of notable items

- **Empty models (2):** `Unit` ([Unit.php:1-10](../../palVarnan/app/Models/Unit.php#L1-L10)), `HomePromotion` ([HomePromotion.php:1-10](../../palVarnan/app/Models/HomePromotion.php#L1-L10)). `VERIFIED_FROM_SOURCE`
- **Table-name mismatches:** `ProductVarient` → `product_varient` (misspelled); `ProductStock` → `productstocks` (no underscore). `VERIFIED_FROM_SOURCE`+`VERIFIED_FROM_LIVE_SCHEMA`
- **Defective relations:** SubCategory→Category (no `category_id` column), ProductVarient→Category (uses `category_id` vs `categoryid`), UserAddress→Country (uses `area_id`), State→District (by name), Wishlist missing `user()`. `CONFLICTING_EVIDENCE`
- **Duplicate relations:** `Product::variants()` and `productvari()` same; `ProductOrder::user()` and `customer()` same. `CONFLICTING_EVIDENCE`
- **Key mismatch (order items vs siblings):** `ProductOrderItem.order_id` stores bigint (`order->id`), siblings store varchar (`order_id` mirror); `ProductOrder::items()` matches bigint local key. `CONFLICTING_EVIDENCE`
- **ChildImages on Product:** declared at [Product.php:37-39](../../palVarnan/app/Models/Product.php#L37-L39); eager-loaded at [ProductController.php:118](../../palVarnan/app/Http/Controllers/ProductController.php#L118)). `VERIFIED_FROM_SOURCE`

---

## Related documents

- [`DATABASE_SCHEMA.md`](./DATABASE_SCHEMA.md) — overview and ownership.
- [`DATABASE_TABLE_REFERENCE.md`](./DATABASE_TABLE_REFERENCE.md) — column-level detail.
- [`DATABASE_RELATIONSHIPS.md`](./DATABASE_RELATIONSHIPS.md) — relations + ER diagram.
- [`MIGRATION_AND_SQL_AUDIT.md`](./MIGRATION_AND_SQL_AUDIT.md) — migrations vs 93 tables.
- Pre-existing: [`../11-models-and-relationships.md`](../11-models-and-relationships.md), [`../07-database-schema.md`](../07-database-schema.md), [`../audit/VERIFIED_FACTS.md`](../audit/VERIFIED_FACTS.md).
