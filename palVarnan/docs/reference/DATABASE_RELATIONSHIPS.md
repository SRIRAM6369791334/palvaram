# DATABASE_RELATIONSHIPS — Eloquent relations, FK mapping, and ER diagram

> Reconciles model relations (from `app/Models/*`) with actual foreign keys present in the
> authoritative SQL dump `palvarnan_db (2).sql`. Documents defects including the
> `ProductOrder::items()` key mismatch and inconsistent relation keys across order tables.
>
> Evidence vocabulary: `VERIFIED_FROM_SOURCE`, `VERIFIED_FROM_LIVE_SCHEMA`, `INFERRED`,
> `UNVERIFIED`, `NOT_AVAILABLE`, `CONFLICTING_EVIDENCE`. **No credentials, connection
> strings, or PII are reproduced here.**
>
> See also: [`DATABASE_SCHEMA.md`](./DATABASE_SCHEMA.md), [`DATABASE_TABLE_REFERENCE.md`](./DATABASE_TABLE_REFERENCE.md),
> [`MIGRATION_AND_SQL_AUDIT.md`](./MIGRATION_AND_SQL_AUDIT.md), [`MODEL_REFERENCE.md`](./MODEL_REFERENCE.md),
> and pre-existing [`../11-models-and-relationships.md`](../11-models-and-relationships.md), [`../26-data-model-diagram.md`](../26-data-model-diagram.md).

---

## 1. Foreign keys from dump (authoritative)

Extracted from `palvarnan_db (2).sql` Constraints section ([168803-168934](../../palvarnan_db%20(2).sql#L168803-L168934)).
`VERIFIED_FROM_LIVE_SCHEMA`.

| Constraint (as named) | Child table | Child column(s) | Parent table | Parent column(s) | On actions |
|---|---|---|---|---|---|
| `fk_users_country` | `users` | `country_id` | `countries` | `id` | CASCADE/RESTRICT (as written) |
| `users_address_type_id_foreign` | `users` | `address_type_id` | `address_types` | `id` | SET NULL/… (per dump) |
| `users_area_id_foreign` | `users` | `area_id` | `areas` | `id` | SET NULL/… |
| `products_warehouse_id_foreign` | `products` | `warehouse_id` | `warehouse` | `id` | SET NULL |
| `sub_categories_country_id_foreign` | `sub_categories` | `country_id` | `countries` | `id` | CASCADE |
| `area_assigns_ibfk_1` | `area_assigns` | `area_id` | `areas` | `id` | CASCADE |
| `area_assigns_ibfk_2` | `area_assigns` | `delivery_people_id` | `delivery_people` | `id` | CASCADE |
| `productstocks_branch_id_foreign` | `productstocks` | `branch_id` | `warehouse_branch` | `id` | SET NULL |
| `productstocks_warehouse_id_foreign` | `productstocks` | `warehouse_id` | `warehouse` | `id` | SET NULL |
| `product_varient_branch_id_foreign` | `product_varient` | `branch_id` | `warehouse_branch` | `id` | SET NULL |
| `product_varient_warehouse_id_foreign` | `product_varient` | `warehouse_id` | `warehouse` | `id` | SET NULL |
| `fk_product_orders_country` | `product_orders` | `country_id` | `countries` | `id` | CASCADE |
| `product_orders_user_id_foreign` | `product_orders` | `user_id` | `users` | `id` | CASCADE |

**Notes:** Many tables used by storefront have **no FK constraints** in dump: `carts`, `wishlists`, `product_order_items`, `product_order_user_addresses`, `product_slots`, `product_tracking`, `product_transaction_logs`, `user_addresses`, `product_child_images`. Indexes named `*_foreign` may exist on some but without `CONSTRAINT ... FOREIGN KEY`. `VERIFIED_FROM_LIVE_SCHEMA`.

Also note: `sub_categories` has FK on `country_id` but **no `category_id` FK/column** in dump; `product_varient` has no FK on `categoryid`/`subcategoryid`. `CONFLICTING_EVIDENCE` vs model relations.

---

## 2. Model relation table (storefront models)

Table below lists each storefront model's relations with **actual Eloquent signatures** from source and **mapped FK/keys**. Empty models marked.

| Model | Table | Relations (Eloquent) | Notes / FK mapping | Tag |
|---|---|---|---|---|
| [Area](../../palVarnan/app/Models/Area.php) | `areas` | `hasMany(AreaAssign::class, 'area_id')` ([lines 13-16](../../palVarnan/app/Models/Area.php#L13-L16)) | Logical; `area_assigns.area_id` → `areas.id` (FK exists in dump). Model-only in app code. | `VERIFIED_FROM_SOURCE`+`VERIFIED_FROM_LIVE_SCHEMA` |
| [AreaAssign](../../palVarnan/app/Models/AreaAssign.php) | `area_assigns` | `belongsTo(Area::class, "area_id")` ([17-20](../../palVarnan/app/Models/AreaAssign.php#L17-L20)) | FK `area_id` → areas exists. `delivery_people_id` FK exists in dump but model has no relation to DeliveryPerson (class may not exist in app). | `VERIFIED_FROM_SOURCE`+`VERIFIED_FROM_LIVE_SCHEMA` |
| [Brand](../../palVarnan/app/Models/Brand.php) | `brands` | `hasMany(Product::class)` ([18-21](../../palVarnan/app/Models/Brand.php#L18-L21)); `belongsTo(Country::class)` ([23-26](../../palVarnan/app/Models/Brand.php#L23-L26)) | No FK `brands.country_id` shown in dump constraints list? (dump constraints section lists users/products/sub_categories/product_orders/productstocks/product_varient/area_assigns; brands not listed) — relation is logical. | `VERIFIED_FROM_SOURCE` |
| [Cart](../../palVarnan/app/Models/Cart.php) | `carts` | `belongsTo(Product::class, 'product_id')` ([17-20](../../palVarnan/app/Models/Cart.php#L17-L20)); `belongsTo(ProductVarient::class, 'product_varient_id')` ([22-25](../../palVarnan/app/Models/Cart.php#L22-L25)) | No FKs in dump. | `VERIFIED_FROM_SOURCE` |
| [Category](../../palVarnan/app/Models/Category.php) | `categories` | `hasMany(Product::class)` ([19-22](../../palVarnan/app/Models/Category.php#L19-L22)); `belongsTo(Country::class)` ([24-27](../../palVarnan/app/Models/Category.php#L24-L27)); `hasMany(SubCategory::class)` ([29-32](../../palVarnan/app/Models/Category.php#L29-L32)) | `sub_categories` has no `category_id` FK/column in dump; relation `SubCategory::belongsTo(Category::class,'category_id')` is **broken**. | `CONFLICTING_EVIDENCE` |
| [Country](../../palVarnan/app/Models/Country.php) | `countries` | none declared | Referenced by many relations; no relations defined in model. | `VERIFIED_FROM_SOURCE` |
| [Coupon](../../palVarnan/app/Models/Coupon.php) | `coupons` | none | Imported but unused in app code. | `VERIFIED_FROM_SOURCE` |
| [District](../../palVarnan/app/Models/District.php) | `districts` | none | No relations. | `VERIFIED_FROM_SOURCE` |
| [HomePromotion](../../palVarnan/app/Models/HomePromotion.php) | `home_promotions` | none (empty model) | Empty ([lines 1-10](../../palVarnan/app/Models/HomePromotion.php#L1-L10)). Queried directly. | `VERIFIED_FROM_SOURCE` |
| [Product](../../palVarnan/app/Models/Product.php) | `products` | `belongsTo(Country::class)` ([17-19](../../palVarnan/app/Models/Product.php#L17-L19)); `belongsTo(Category::class)` ([21-23](../../palVarnan/app/Models/Product.php#L21-L23)); `belongsTo(Brand::class)` ([25-27](../../palVarnan/app/Models/Product.php#L25-L27)); `hasMany(ProductVarient::class)` ([29-31](../../palVarnan/app/Models/Product.php#L29-L31)); `hasMany(ProductVarient::class)` (duplicate `productvari()`) ([33-35](../../palVarnan/app/Models/Product.php#L33-L35)); `hasMany(ProductChildImage::class,'product_id')` ([37-39](../../palVarnan/app/Models/Product.php#L37-L39)) | Duplicate relation names (`variants()` and `productvari()`) return same. `childImages()` exists but not mentioned in existing docs. FKs: `products.country_id`→countries (dump), `warehouse_id`→warehouse (dump). | `CONFLICTING_EVIDENCE` (duplicate) + `VERIFIED_FROM_SOURCE` |
| [ProductChildImage](../../palVarnan/app/Models/ProductChildImage.php) | `product_child_images` | `belongsTo(Product::class,'product_id')` ([15-17](../../palVarnan/app/Models/ProductChildImage.php#L15-L17)) | No FK in dump. Eager-loaded at [ProductController.php:118](../../palVarnan/app/Http/Controllers/ProductController.php#L118). | `VERIFIED_FROM_SOURCE` |
| [ProductOrder](../../palVarnan/app/Models/ProductOrder.php) | `product_orders` | `belongsTo(User::class,'user_id','id')` ([65-68](../../palVarnan/app/Models/ProductOrder.php#L65-L68)); duplicate `customer()` same ([70-73](../../palVarnan/app/Models/ProductOrder.php#L70-L73)); `hasMany(ProductOrderItem::class,'order_id')` ([75-78](../../palVarnan/app/Models/ProductOrder.php#L75-L78)); `hasMany(ProductSlot::class,'order_id','order_id')` ([80-83](../../palVarnan/app/Models/ProductOrder.php#L80-L83)); `hasOne(ProductOrderUserAddress::class,'order_id','order_id')` ([85-88](../../palVarnan/app/Models/ProductOrder.php#L85-L88)); `hasOne(ProductTransactionLog::class,'order_id','order_id')` ([90-93](../../palVarnan/app/Models/ProductOrder.php#L90-L93)); `hasMany(ProductTracking::class,'order_id','order_id')` ([95-98](../../palVarnan/app/Models/ProductOrder.php#L95-L98)) | **Key mismatch:** `items()` uses default local `id` (bigint) with `order_id` FK; `ProductOrderItem.order_id` stores bigint ([CheckoutController.php:374](../../palVarnan/app/Http/Controllers/CheckoutController.php#L374)) — matches. Other relations use `('order_id','order_id')` (varchar mirror). Also `product_orders.id` is bigint PK while sibling tables store varchar `order_id`. No FKs for siblings in dump. Duplicate `user()`/`customer()`. | `CONFLICTING_EVIDENCE` |
| [ProductOrderItem](../../palVarnan/app/Models/ProductOrderItem.php) | `product_order_items` | `belongsTo(Product::class,'product_id')` ([28-31](../../palVarnan/app/Models/ProductOrderItem.php#L28-L31)); `belongsTo(ProductVarient::class,'product_variant_id')` ([33-36](../../palVarnan/app/Models/ProductOrderItem.php#L33-L36)) | No FKs in dump. `order_id` is bigint in practice (written as `$order->id`). | `VERIFIED_FROM_SOURCE` |
| [ProductOrderUserAddress](../../palVarnan/app/Models/ProductOrderUserAddress.php) | `product_order_user_addresses` | `belongsTo(ProductOrder::class,"order_id","order_id")` ([32-35](../../palVarnan/app/Models/ProductOrderUserAddress.php#L32-L35)); `belongsTo(Area::class,"area_id","id")` ([37-40](../../palVarnan/app/Models/ProductOrderUserAddress.php#L37-L40)) | No FKs in dump. Links via varchar `order_id`. | `VERIFIED_FROM_SOURCE` |
| [ProductSlot](../../palVarnan/app/Models/ProductSlot.php) | `product_slots` | `belongsTo(ProductOrder::class,"order_id","order_id")` ([37-40](../../palVarnan/app/Models/ProductSlot.php#L37-L40)); `belongsTo(Product::class,'product_id')` ([42-45](../../palVarnan/app/Models/ProductSlot.php#L42-L45)); `belongsTo(ProductVarient::class,'product_varient_id')` ([47-50](../../palVarnan/app/Models/ProductSlot.php#L47-L50)) | No FKs in dump. Links via varchar `order_id`. | `VERIFIED_FROM_SOURCE` |
| [ProductStock](../../palVarnan/app/Models/ProductStock.php) | `productstocks` | `belongsTo(Product::class,'productid')` ([30-33](../../palVarnan/app/Models/ProductStock.php#L30-L33)); `belongsTo(ProductVarient::class,'pro_ver_id')` ([35-38](../../palVarnan/app/Models/ProductStock.php#L35-L38)) | FKs to warehouse/branch exist in dump. No FK to products/product_varient in dump constraints. | `VERIFIED_FROM_SOURCE`+`VERIFIED_FROM_LIVE_SCHEMA` |
| [ProductTracking](../../palVarnan/app/Models/ProductTracking.php) | `product_tracking` | none | No relations declared. Links to order via varchar `order_id`. | `VERIFIED_FROM_SOURCE` |
| [ProductTransactionLog](../../palVarnan/app/Models/ProductTransactionLog.php) | `product_transaction_logs` | none | No relations declared. | `VERIFIED_FROM_SOURCE` |
| [ProductVarient](../../palVarnan/app/Models/ProductVarient.php) | `product_varient` | `belongsTo(Country::class)` ([15-17](../../palVarnan/app/Models/ProductVarient.php#L15-L17)); `belongsTo(Product::class)` ([19-21](../../palVarnan/app/Models/ProductVarient.php#L19-L21)); `belongsTo(Category::class)` ([23-25](../../palVarnan/app/Models/ProductVarient.php#L23-L25)) — **uses default `category_id`** but actual column is `categoryid`; `belongsTo(Unit::class)` ([27-29](../../palVarnan/app/Models/ProductVarient.php#L27-L29)) | **Defects:** `category()` uses `category_id` (nonexistent); actual column `categoryid`. No FK in dump for categoryid. Unit relation exists; Unit model empty. FKs to warehouse/branch exist in dump. | `CONFLICTING_EVIDENCE` |
| [State](../../palVarnan/app/Models/State.php) | `states` | `hasMany(District::class,'state_name','name')` ([20-23](../../palVarnan/app/Models/State.php#L20-L23)) | Keyed by name (not id). No FK in dump for districts→states by id shown as constrained? districts has `state_id`, states has id; relation uses `state_name` as join key — unusual. | `CONFLICTING_EVIDENCE` |
| [SubCategory](../../palVarnan/app/Models/SubCategory.php) | `sub_categories` | `belongsTo(Category::class,'category_id')` ([11-14](../../palVarnan/app/Models/SubCategory.php#L11-L14)) | **Defect:** uses `category_id` but dump has **no `category_id` column** in `sub_categories` (has `category_name`). FK exists for `country_id` only. | `CONFLICTING_EVIDENCE` |
| [Unit](../../palVarnan/app/Models/Unit.php) | `units` | none (empty model) | Empty ([1-10](../../palVarnan/app/Models/Unit.php#L1-L10)). Referenced by `ProductVarient::unit()`. | `VERIFIED_FROM_SOURCE` |
| [User](../../palVarnan/app/Models/User.php) | `users` | none declared | Auth model. Relations to addresses/orders exist via other models. | `VERIFIED_FROM_SOURCE` |
| [UserAddress](../../palVarnan/app/Models/UserAddress.php) | `user_addresses` | `belongsTo(User::class,"user_id","user_id")` ([36-39](../../palVarnan/app/Models/UserAddress.php#L36-L39)); `belongsTo(Area::class,"area_id","id")` ([41-44](../../palVarnan/app/Models/UserAddress.php#L41-L44)); `belongsTo(Country::class,'area_id')` ([47-50](../../palVarnan/app/Models/UserAddress.php#L47-L50)) — **defect** (wrong FK target) | No FKs in dump. `country()` points to area_id → wrong. Targets `users.user_id` (non-standard). | `CONFLICTING_EVIDENCE` |
| [Wishlist](../../palVarnan/app/Models/Wishlist.php) | `wishlists` | `belongsTo(Product::class)` ([16-19](../../palVarnan/app/Models/Wishlist.php#L16-L19)); `belongsTo(ProductVarient::class,'product_varient_id')` ([21-24](../../palVarnan/app/Models/Wishlist.php#L21-L24)) | **No `user()` relation** in model. No FKs in dump. | `CONFLICTING_EVIDENCE` |

---

## 3. Critical relation defects (summary)

- **ProductOrder items vs siblings keying**: `ProductOrderItem.order_id` stores bigint (`order->id`) while `product_order_user_addresses`, `product_slots`, `product_tracking`, `product_transaction_logs` store varchar `order_id` (set to `$orderNumber`). `ProductOrder::items()` uses local `id` (bigint) with `order_id` FK — consistent for items; others use explicit pair `('order_id','order_id')` pointing to varchar mirror. This is a **dual-identifier design** (both `id` bigint and `order_id`/`order_number` varchar exist). `CONFLICTING_EVIDENCE`.
- **ProductOrder::items() signature vs design**: The relation is `hasMany(ProductOrderItem::class, 'order_id')` ([75-78](../../palVarnan/app/Models/ProductOrder.php#L75-L78)) with no explicit local key — so Eloquent uses `product_orders.id`. That matches code writing bigint. The "string-vs-int key mismatch" mentioned in requirements refers to this cross-table inconsistency (siblings keyed by varchar). Marked as defect. `CONFLICTING_EVIDENCE`.
- **SubCategory::category()** references `category_id` column that **does not exist** in `sub_categories` (dump has `category_name`). `CONFLICTING_EVIDENCE`.
- **ProductVarient::category()** defaults to `category_id` but actual column is `categoryid`. `CONFLICTING_EVIDENCE`.
- **UserAddress::country()** uses `area_id` as foreign key (should be country id). `CONFLICTING_EVIDENCE`.
- **State::districts()** joins on `state_name` (not `state_id`). `CONFLICTING_EVIDENCE`.
- **Product has duplicate relation** `variants()` and `productvari()` same target. `CONFLICTING_EVIDENCE`.
- **Wishlist has no `user()` relation** despite having `user_id`. `CONFLICTING_EVIDENCE`.
- **No FKs for order child tables** in dump — relations are application-level only. `VERIFIED_FROM_LIVE_SCHEMA`.

---

## 4. Mermaid ER diagram (logical)

This is a **logical ER** reflecting model relations and keying; it does not imply all FKs exist in DB. Empty/defective relations noted.

```mermaid
erDiagram
    countries {
        bigint id PK
        varchar name
        varchar iso_code
        varchar phone_code
        decimal ship_charge
        timestamp created_at
        timestamp updated_at
        varchar currency
    }

    users {
        bigint id PK
        bigint country_id FK->countries(id)
        varchar name
        varchar email
        varchar phone_number
        timestamp created_at
        timestamp updated_at
    }

    categories {
        bigint id PK
        varchar category_name
        bigint country_id
        timestamp created_at
        timestamp updated_at
    }

    sub_categories {
        bigint id PK
        varchar category_name  "note: no category_id column in dump"
        varchar subcategory_name
        bigint country_id FK->countries(id)
        timestamp created_at
        timestamp updated_at
    }

    brands {
        bigint id PK
        varchar brand_name
        varchar brand_slug
        bigint country_id
        timestamp created_at
        timestamp updated_at
    }

    products {
        bigint id PK
        bigint country_id FK->countries(id)
        bigint category_id FK->categories(id)
        bigint brand_id FK->brands(id)
        varchar product_name
        varchar slug
        timestamp created_at
        timestamp updated_at
        timestamp deleted_at
    }

    product_varient {
        bigint id PK
        bigint country_id
        int categoryid  "note: model uses category_id"
        int subcategoryid
        bigint product_id FK->products(id)
        varchar sku
        varchar varient_name
        int varient
        int offer_price
        int mrp_price
        int product_qty
        bigint warehouse_id FK->warehouse(id)
        bigint branch_id FK->warehouse_branch(id)
        timestamp created_at
        timestamp updated_at
    }

    product_child_images {
        bigint id PK
        bigint product_id FK->products(id)
        bigint variant_id
        varchar product_child_image
        timestamp created_at
        timestamp updated_at
    }

    units {
        bigint id PK
        bigint country_id
        varchar unit_name
        varchar short_name
    }

    productstocks {
        bigint id PK
        int productid  "logical->products.id"
        bigint pro_ver_id  "logical->product_varient.id"
        bigint warehouse_id FK->warehouse(id)
        bigint branch_id FK->warehouse_branch(id)
        int availablestock
        int salestock
        int overallstock
        timestamp created_at
        timestamp updated_at
    }

    states {
        int id PK
        varchar name
        int country_id
        varchar code
        timestamp created_at
        timestamp updated_at
    }

    districts {
        int id PK
        varchar name
        int state_id
        varchar state_name
        timestamp created_at
        timestamp updated_at
    }

    areas {
        bigint id PK
        varchar area_name
        varchar area_pincode
        timestamp created_at
        timestamp updated_at
    }

    area_assigns {
        bigint id PK
        bigint area_id FK->areas(id)
        bigint delivery_people_id FK->delivery_people(id)
        timestamp created_at
        timestamp updated_at
    }

    user_addresses {
        bigint id PK
        bigint user_id
        varchar address_username
        varchar city
        varchar state
        varchar pincode
        bigint area_id FK->areas(id)
        bigint address_phone_number
        timestamp created_at
        timestamp updated_at
    }

    carts {
        bigint id PK
        varchar ip_addresscart
        varchar user_id
        bigint product_id FK->products(id)
        bigint product_varient_id FK->product_varient(id)
        int product_quantity
        timestamp created_at
        timestamp updated_at
    }

    wishlists {
        bigint id PK
        varchar ip_address
        varchar user_id
        bigint product_id FK->products(id)
        int product_varient_id FK->product_varient(id)
        int product_quantity
        timestamp created_at
        timestamp updated_at
    }

    coupons {
        bigint id PK
        varchar codename
        decimal discount
        varchar discounttype
        decimal mini_amt
        date start_date
        date end_date
        timestamp created_at
        timestamp updated_at
    }

    home_promotions {
        bigint id PK
        bigint country_id
        varchar title
        varchar image
        tinyint status
        int sort_order
        timestamp created_at
        timestamp updated_at
    }

    product_orders {
        bigint id PK
        bigint user_id FK->users(id)
        varchar order_number UK
        varchar order_id  "mirror"
        varchar billing_name
        varchar billing_email
        varchar billing_phone
        varchar billing_state
        varchar billing_pincode
        varchar shipping_name
        varchar shipping_state
        varchar shipping_pincode
        decimal subtotal
        decimal gst_amount
        decimal shipping_charge
        decimal total_amount
        varchar payment_status
        varchar payment_method
        varchar status
        varchar courier_name
        timestamp created_at
        timestamp updated_at
    }

    product_order_items {
        bigint id PK
        bigint order_id  "FK->product_orders.id (bigint)"
        bigint product_id FK->products(id)
        bigint product_variant_id FK->product_varient(id)
        varchar product_name
        varchar product_image  "absent from dump DDL"
        int quantity
        decimal price
        decimal gst_rate
        decimal gst_amount
        decimal total
        timestamp created_at
        timestamp updated_at
    }

    product_order_user_addresses {
        bigint id PK
        bigint user_id
        varchar order_id  "FK->product_orders.order_id (varchar)"
        varchar firstname
        varchar city
        varchar state
        varchar pincode
        varchar address_phone_number
        timestamp created_at
        timestamp updated_at
    }

    product_slots {
        bigint id PK
        date delivery_date
        varchar order_id  "FK->product_orders.order_id (varchar)"
        bigint product_id FK->products(id)
        bigint product_varient_id FK->product_varient(id)
        int quantity
        varchar product_name
        decimal product_rate
        decimal product_total
        timestamp created_at
        timestamp updated_at
    }

    product_tracking {
        bigint id PK
        bigint user_id
        varchar order_id  "FK->product_orders.order_id (varchar)"
        tinyint delivery_status
        timestamp created_at
        timestamp updated_at
    }

    product_transaction_logs {
        bigint id PK
        varchar order_id  "FK->product_orders.order_id (varchar)"
        bigint slot_id
        date order_date
        decimal order_amount
        decimal amount_credited
        decimal amount_debited
        bigint user_id
        timestamp created_at
        timestamp updated_at
    }

    %% Logical relations
    countries ||--o{ users : has
    countries ||--o{ categories : has
    countries ||--o{ sub_categories : has
    countries ||--o{ brands : has
    countries ||--o{ products : has
    categories ||--o{ products : has
    categories ||--o{ sub_categories : "defective (no category_id)"
    brands ||--o{ products : has
    products ||--o{ product_varient : has
    products ||--o{ product_child_images : has
    products ||--o{ product_order_items : has
    products ||--o{ product_slots : has
    products ||--o{ carts : has
    products ||--o{ wishlists : has
    product_varient ||--o{ product_child_images : has
    product_varient ||--o{ product_order_items : has
    product_varient ||--o{ product_slots : has
    product_varient ||--o{ carts : has
    product_varient ||--o{ wishlists : has
    product_varient ||--o{ productstocks : has
    units ||--o{ product_varient : has
    users ||--o{ product_orders : has
    users ||--o{ user_addresses : has
    users ||--o{ wishlists : has (logical)
    users ||--o{ carts : has (logical)
    areas ||--o{ area_assigns : has
    areas ||--o{ user_addresses : has
    states ||--o{ districts : "by name (defective)"
    product_orders ||--o{ product_order_items : has (via id)
    product_orders ||--|| product_order_user_addresses : hasOne (via order_id mirror)
    product_orders ||--o{ product_slots : has (via order_id mirror)
    product_orders ||--o{ product_tracking : has (via order_id mirror)
    product_orders ||--|| product_transaction_logs : hasOne (via order_id mirror)
```

> Diagram notes: dashed/defective relations marked in text. Dual keying (bigint id vs varchar order_id mirror) is shown on child tables. FKs may not exist in DB even if shown logically. `CONFLICTING_EVIDENCE` where noted.

---

## 5. Related documents

- [`DATABASE_SCHEMA.md`](./DATABASE_SCHEMA.md) — overview and ownership.
- [`DATABASE_TABLE_REFERENCE.md`](./DATABASE_TABLE_REFERENCE.md) — column-level detail.
- [`MIGRATION_AND_SQL_AUDIT.md`](./MIGRATION_AND_SQL_AUDIT.md) — migrations vs 93 tables.
- [`MODEL_REFERENCE.md`](./MODEL_REFERENCE.md) — per-model subsections.
- Pre-existing: [`../11-models-and-relationships.md`](../11-models-and-relationships.md), [`../26-data-model-diagram.md`](../26-data-model-diagram.md), [`../22-database-integrity.md`](../22-database-integrity.md).
