# 26 — Data Model Diagram

Mermaid diagrams derived from `palvarnan_db (2).sql` (93 tables, 34 FKs, 19 unique indexes — all enumerated directly) and the 24 models in `app/Models/`.

Render in any Mermaid-capable viewer: GitHub, VS Code, mermaid.live.

---

## 1. ER overview — the live commerce path

Solid arrows are **real** foreign keys or working Eloquent relations. Dashed arrows are **logical only** — no constraint, no relation, or a broken one.

```mermaid
erDiagram
    USERS ||--o{ PRODUCT_ORDERS : "FK, ON DELETE CASCADE"
    COUNTRIES ||--o{ USERS : "FK"
    COUNTRIES ||--o{ PRODUCT_ORDERS : "FK"
    COUNTRIES ||--o{ SUBCATEGORIES : "FK"

    PRODUCT_ORDERS ||--o{ PRODUCT_ORDER_ITEMS : "no FK"
    PRODUCT_ORDERS ||--o{ PRODUCT_ORDER_USER_ADDRESSES : "no FK, varchar key"
    PRODUCT_ORDERS ||--o{ PRODUCT_TRANSACTION_LOGS : "no FK, varchar key"
    PRODUCT_ORDERS ||--o{ PRODUCT_TRACKING : "no FK, varchar key"
    PRODUCT_ORDERS ||--o{ PRODUCT_SLOTS : "no FK, varchar key"

    USERS ||--o{ CARTS : "no FK, varchar"
    USERS ||--o{ WISHLISTS : "no FK, varchar"
    USERS ||--o{ USER_ADDRESSES : "no FK, varchar"

    PRODUCTS ||--o{ PRODUCT_VARIENT : "no FK"
    PRODUCTS ||--o{ PRODUCTSTOCKS : "no FK, via pro_ver_id"
    CATEGORIES ||--o{ PRODUCTS : "no FK, via category_id"
    BRANDS ||--o{ PRODUCTS : "no FK, via brand_id"

    PRODUCT_ORDER_ITEMS }o--|| PRODUCTS : "logical only"
    PRODUCT_SLOTS }o--|| PRODUCT_VARIENT : "logical only"
    CARTS }o--|| PRODUCT_VARIENT : "logical only"
    WISHLISTS }o--|| PRODUCT_VARIENT : "logical only"

    COUNTRIES {
        bigint id PK
        varchar name
        varchar iso_code "unused"
        varchar ship_charge "never read"
        varchar currency "never read"
    }
    USERS {
        bigint id PK
        varchar user_id UK "second identifier"
        varchar name
        varchar email UK
        varchar phone UK
        varchar phone_number UK
        bigint country_id FK
        varchar password
        text enc_password "unused"
        varchar user_token "unused"
        bigint area_id FK
        timestamp email_verified_at "never set"
    }
    PRODUCT_ORDERS {
        bigint id PK
        bigint country_id FK
        bigint user_id FK
        varchar order_number UK
        varchar order_id "mirror of order_number"
        varchar billing_name "PII"
        varchar billing_email "PII"
        varchar billing_phone "PII"
        varchar billing_pincode
        varchar shipping_name "PII"
        varchar shipping_email "PII"
        decimal subtotal
        decimal gst_amount
        decimal shipping_charge "client-set"
        decimal total_amount "client-influenced"
        varchar payment_status
        varchar payment_method
        varchar razorpay_order_id "NO UNIQUE INDEX"
        varchar razorpay_payment_id
        varchar status
        varchar reason
        decimal unavailable_amount
        int unavailable_count
    }
    CARTS {
        bigint id PK
        varchar ip_addresscart "misspelled"
        varchar user_id "varchar, no FK"
        bigint product_id "no FK"
        bigint product_varient_id "no FK"
        int product_quantity "signed"
        varchar pre_order "stripped by fillable"
        date dispatch_date
    }
    WISHLISTS {
        int id PK
        varchar ip_address
        varchar user_id "varchar, no FK"
        int product_id "no FK"
        int product_varient_id "no FK"
        int product_quantity "always 1"
    }
```

### What the diagram shows

**Every arrow on the money path is dashed.** `product_orders` is the only order table with a real FK — and it cascades to `users`, so deleting a user destroys order history.

---

## 2. Catalogue

```mermaid
erDiagram
    PRODUCTS {
        bigint id PK
        bigint category_id "NOT NULL, no FK"
        bigint brand_id "no FK"
        varchar country_id "CSV"
        bigint warehouse_id FK
        bigint branch_id FK
        varchar subcategory_id "varchar"
        varchar product_name
        varchar slug UK
        bigint product_quantity
        bigint product_mrp_price "int, not decimal"
        bigint product_regular_price "int, not decimal"
        longtext product_description
        varchar product_image
        longtext product_specification
        longtext product_specfication "DUPLICATE"
        varchar brand_name
        varchar brand_material
        varchar brand_type
        int approval_days
        tinyint is_gift
        varchar unit_value
        varchar product_value
        varchar cate_name
        varchar subcate_name
        int size_value
        timestamp deleted_at "SoftDeletes"
    }
    PRODUCT_VARIENT {
        bigint id PK
        varchar country_id "CSV"
        bigint categoryid "NOT category_id"
        varchar subcategoryid
        bigint product_id "no FK"
        varchar sku UK
        varchar barcode
        int varient "MISSPELLED int(11) - searched with LIKE"
        bigint unit_id
        bigint size_id "no relation"
        bigint color_id "no relation"
        varchar varient_img
        varchar varient_name
        varchar value "varchar(50)"
        int offer_price
        int mrp_price
        int product_qty
        varchar low_stock "never read"
        int hot_deals
        int Popular_products
        int pre_order
        text pre_note
        int product_gst "1=5 2=12 3=18 4=28"
        int flash_sale
        datetime flash_sale_date
        varchar item_code
        bigint warehouse_id FK
        bigint branch_id FK
    }
    PRODUCTSTOCKS {
        int id PK
        int productid "MISSPELLED, no FK"
        int category_id
        varchar subcategory_id
        varchar country_id "CSV"
        bigint warehouse_id FK
        bigint branch_id FK
        bigint pro_ver_id "MISSPELLED, no FK"
        varchar productname
        int overallstock
        int availablestock "MISSPELLED"
        int salestock "MISSPELLED"
        varchar low_stocks
        varchar last_stockupdate_date
    }
    CATEGORIES {
        bigint id PK
        varchar country_id "CSV, NOT NULL"
        varchar category_name
        varchar category_banner
        varchar category_image
        varchar category_icon
        int status
        tinyint is_gift
    }
    SUBCATEGORIES {
        bigint id PK
        bigint country_id FK "single id, unlike others"
        varchar subcategory_name
        varchar subcategory_image
        varchar category_name "relation is by NAME"
        varchar category_display
        varchar status
        tinyint is_gift
    }
    BRANDS {
        bigint id PK
        varchar country_id "CSV, NOT NULL"
        varchar brand_name
        varchar brand_slug UK "code reads slug"
        varchar brand_image
        tinyint status
    }
    UNITS {
        bigint id PK
    }
    PRODUCTS ||--o{ PRODUCT_VARIENT : "1:N, no FK"
    PRODUCTS ||--|| PRODUCTSTOCKS : "1:1 via pro_ver_id"
    CATEGORIES ||--o{ PRODUCTS : "1:N via category_id"
    BRANDS ||--o{ PRODUCTS : "1:N via brand_id"
```

### Defects marked inline

| Marker | Defect | ID |
|---|---|---|
| `offer_price` / `mrp_price` in `Product::$fillable` | **not `products` columns** — writes are silently dropped | DB-17 |
| `products.product_price` read at 6 sites | **does not exist**; chain falls to `0` | DB-16 |
| `product_mrp_price bigint` | money stored as an integer column | DB-27 |
| no `weight`/`length`/`breadth`/`height` | **shipping quoted on a fictional 12 kg parcel** | DB-04 |
| `varient int(11)` | `LIKE` search can never match | D-S3 |
| `categoryid` vs `category_id` | `ProductVarient::category()` broken | DB-09 |
| `category_name` on `sub_categories` | `SubCategory::category()` broken | DB-08 |
| `country_id` CSV on 5 tables, FK on 5 others | two incompatible designs | DB-18 |
| `product_specfication` | duplicate of `product_specification` | DB-22 |
| `productstocks.productid` + `pro_ver_id` | two misspelled links to the same parents | — |

---

## 3. Order transaction — what `placeOrder()` actually writes

Six tables, one transaction, one rollback. This is the sequence that fails.

```mermaid
sequenceDiagram
    participant S as Shopper
    participant C as placeOrder()
    participant DB as MySQL
    participant R as Razorpay

    S->>C: POST /checkout/place-order
    C->>R: verifyPaymentSignature()
    Note over C,R: payment already captured<br/>amount never re-checked
    C->>DB: BEGIN
    C->>DB: INSERT product_orders (billing_state, shipping_state, courier_name)
    Note over DB: Unknown column 'billing_state'
    DB-->>C: exception
    C->>DB: ROLLBACK
    C-->>S: 500 "Order failed to save: ..."
    Note over S,R: Customer is charged.<br/>No order exists.
```

```mermaid
flowchart TD
    A["1. product_orders<br/>user_id, order_number, order_id,<br/>billing_*, shipping_*,<br/>subtotal, gst_amount, shipping_charge,<br/>total_amount, courier_name, status,<br/>payment_method, payment_status,<br/>razorpay_order_id, razorpay_payment_id"] --> A1["FAILS<br/>billing_state<br/>shipping_state<br/>courier_name absent"]
    A1 -.-> B
    B["2. product_order_user_addresses<br/>order_id varchar = ORD-XXXX<br/>firstname, address_line_one/two,<br/>landmark, city, state, pincode,<br/>phonecode, address_phone_number"] --> C
    C["3. product_transaction_logs<br/>order_id, slot_id null,<br/>order_date now()-toDateString(),<br/>order_amount, amount_credited"] --> D
    D["4. product_tracking<br/>user_id, order_id,<br/>delivery_status 0"] --> E
    E["5. per cart item:<br/>product_order_items<br/>product_slots<br/>UPDATE productstocks<br/>UPDATE product_varient.product_qty"] --> F
    F["6. DELETE FROM carts WHERE user_id"] --> G
    G["COMMIT"] --> H["7. Shiprocket createOrder<br/>OUTSIDE the transaction<br/>failure swallowed"]
```

### The three insert points that fail

| Step | Table | Absent columns | Evidence |
|---|---|---|---|
| 1 | `product_orders` | `billing_state`, `shipping_state`, `courier_name` | `Unknown column 'billing_state'` |
| 5 | `product_order_items` | `product_image` | `Unknown column 'product_image'` |
| — | — | never reached | — |

### Identifier split across the six writes

| Table | `order_id` type | Value written |
|---|---|---|
| `product_order_items` | `bigint` | `$order->id` |
| `product_order_user_addresses` | `varchar(255)` | `'ORD-XXXX'` |
| `product_transaction_logs` | `varchar(255)` | `'ORD-XXXX'` |
| `product_tracking` | `varchar(255)` | `'ORD-XXXX'` |
| `product_slots` | `varchar(255)` | `'ORD-XXXX'` |

**One order has two identifiers, and only one is joinable.** `ProductOrder::items()` works because the code writes `$order->id`; `ProductOrder::trackings()` works because it writes the string form. No FK enforces either.

---

## 4. Stock flow

```mermaid
erDiagram
    PRODUCTSTOCKS {
        int id PK
        bigint pro_ver_id "no FK"
        int availablestock "MISSPELLED"
        int salestock "MISSPELLED"
        int overallstock "never read"
        varchar low_stocks "never read"
        varchar last_stockupdate_date "never read"
    }
    PRODUCT_VARIENT {
        bigint id PK
        int product_qty "second stock counter"
    }
    PRODUCTSTOCKS ||--|| PRODUCT_VARIENT : "no FK, loose coupling"
```

`CheckoutController.php:403-415` decrements **two independent counters**:

```php
$productStock->availablestock = max($productStock->availablestock - $qty, 0);
$productStock->salestock      = ($productStock->salestock ?? 0) + $qty;

$variant->product_qty = max($variant->product_qty - $qty, 0);
```

| Defect | Detail |
|---|---|
| no `lockForUpdate()` | two concurrent checkouts both read the same stock |
| `max(..., 0)` | **oversell is silent** — stock reads 0 while units keep shipping |
| `product_qty` is never initialised | a new variant defaults to `null`; `null - 5` is `-5` |
| no stock restore | no cancel or return path exists |
| `overallstock`, `low_stocks`, `last_stockupdate_date` | never written |
| `ProductStock::where('pro_ver_id', …)->first()` | picks one row arbitrarily where a variant spans warehouses |

---

## 5. Address and geography

```mermaid
erDiagram
    USER_ADDRESSES {
        bigint id PK
        varchar user_id "varchar, no FK"
        varchar address_username "never written"
        varchar address_first_name
        varchar address_last_name "varchar(20)"
        varchar address_phone_number bigint "phone stored in int column"
        varchar email
        longtext address_line_one
        longtext address_line_two
        longtext landmark
        bigint area_id "no FK, used as country FK in code"
        varchar area_name "never written"
        longtext city
        int city_id "never written"
        int state_id "never written"
        int pincode "no format validation"
        int pincode_id "never written"
        varchar district "never written"
        varchar state "varchar(225)"
        varchar phone_code "never written"
        bigint address_type_id
        varchar address_type_name
        varchar address_type_others_name
    }
    COUNTRIES {
        bigint id PK
        varchar name
        varchar iso_code
        varchar phone_code
        varchar ship_charge "never read"
        varchar currency "never read"
    }
    STATES {
        int id PK
        varchar name "varchar(30)"
        int country_id
        varchar shipping_charge "read only by dead endpoint"
        decimal express_shipping_charge "never read"
    }
    DISTRICTS {
        bigint id PK
        varchar name
        int state_id
        varchar state_name "relation is by NAME"
    }
    AREAS {
        bigint id PK
    }
    COUNTRIES ||--o{ USER_ADDRESSES : "no FK - country() uses area_id"
    DISTRICTS ||--o{ USER_ADDRESSES : "no FK"
    AREAS ||--o{ USER_ADDRESSES : "no FK declared"
    COUNTRIES ||--o{ STATES : "no FK"
    STATES ||--o{ DISTRICTS : "no FK"
```

| Defect | Detail |
|---|---|
| `UserAddress::country()` uses `area_id` | points at `areas`, not `countries` |
| `address_phone_number bigint` | a phone number in an integer column |
| `state varchar(225)` | a typo for `255`; wider than `states.name varchar(30)` |
| `address_last_name varchar(20)` | truncates |
| `getCitiesByState()` | `District::where('state_name', …)` — joins by name, no FK |
| `getShippingCharge()` | `State::where('name', …)` — no caller |
| `countries.currency`, `ship_charge` | never read; the UI hard-codes `₹` |
| `is_default` | **does not exist**; no default-address concept |
| `user_default_address_id` on `users` | never written |

---

## 6. Warehousing (referenced, never used)

```mermaid
erDiagram
    COUNTRIES ||--o{ WAREHOUSE : "FK"
    COUNTRIES ||--o{ WAREHOUSE_BRANCH : "FK"
    WAREHOUSE ||--o{ WAREHOUSE_BRANCH : "FK"
    WAREHOUSE ||--o{ PRODUCTS : "FK, SET NULL"
    WAREHOUSE ||--o{ PRODUCT_VARIENT : "FK, SET NULL"
    WAREHOUSE ||--o{ PRODUCTSTOCKS : "FK, SET NULL"
    WAREHOUSE_BRANCH ||--o{ PRODUCTS : "no FK declared"
    WAREHOUSE_BRANCH ||--o{ PRODUCT_VARIENT : "FK, SET NULL"
    WAREHOUSE_BRANCH ||--o{ PRODUCTSTOCKS : "FK, SET NULL"
```

**The only tables in the schema with real, cascading warehouse constraints — and the storefront reads none of them.** `warehouse_id` and `branch_id` sit in `$fillable` on three models and are never populated. So `ProductStock::where('pro_ver_id', …)->first()` always resolves against rows with `warehouse_id = NULL`.

---

## 7. Tables with no storefront usage (~55)

| Group | Tables | Present in schema as |
|---|---|---|
| Milk / subscription | `milk_orders`, `milk_order_user_addresses`, `milk_slots`, `milk_refunds`, `milk_transaction_logs`, `plan_types`, `onetimes` | a second business, unimplemented |
| Gifts | `gift_purchase_orders`, `gift_purchase_order_items`, `gift_color`, `gift_size` | template leftovers |
| Procurement | `purchase_orders`, `purchase_order_items`, `product_stock_batches`, `stock_distributions`, `stock_transfers`, `supplier_details` | dashboard domain |
| Delivery | `delivery_people`, `delivery_charge_details` | dashboard domain |
| Promotions | `coupons`, `newcoupons`, `offer_images`, `today_deals`, `testimonials`, `reviews`, `home_promotions` | no storefront UI |
| Content | `banner_images`, `web_images`, `scrolling_bars`, `contacts`, `contact_details`, `bulkorders` | dashboard domain |
| Auth | `dashboard_users`, `roles`, `permissions`, `role_has_permissions`, `model_has_roles`, `model_has_permissions`, `personal_access_tokens`, `otps`, `mail_otps` | admin only |
| Notifications | `notifications`, `app_notifications`, `app_alerts`, `user_read_notifications` | no mailers exist |
| Geography extras | `cities`, `city_list`, `state_list`, `all_india_pincodes`, `country_tables`, `free_shipping_settings`, `address_types`, `gender_types` | partially referenced |
| Observability | `telescope_entries`, `telescope_entries_tags`, `telescope_monitoring`, `tests` | Telescope not installed |
| Warehouse | `warehouse`, `warehouse_branch` | FKs only, never read |
| Coupons | `cancel_requests`, `product_refunds` | no code path |

### Tables that do **not** exist, contrary to earlier design notes

`api_logs`, `shiprocket_orders`, `shiprocket_pickup_locations`, `product_transactions`, `product_order_status`, `order_tracking`, `productcategories`, `productpictures`, `product_varient_pictures`, `productstocksprices`, `product_offers`, `offers`, `compare`, `attributes`, `attr_values`, `variants_attributes`, `pincodes`, `posts`, `pages`, `banners`, `shipping_methods`, `shipping_zones`, `country_shipping`, `shipments`, `shipment_items`, `returns`, `return_items`, `order_status_logs`, `coupon__product`, `coupon__product_varient`, `membership_plans`, `user_role`, `product_invoice`, `product_return_requests`, `gift_token`.

**There is no integration-audit table.** Shiprocket and Razorpay traffic exists only in `storage/logs/laravel.log`.

---

## 8. Model-to-table map (24 models, 24 tables)

| Model | Table | `$table` declared? | Defects |
|---|---|---|---|
| `User` | `users` | default | `MustVerifyEmail` commented out; `enc_password`, `user_token` unused |
| `UserAddress` | `user_addresses` | yes | `country()` uses `area_id` — broken |
| `Country` | `countries` | yes | `ship_charge`, `iso_code` never read |
| `State` | `states` | yes | `name` only, `shipping_charge` read by dead code |
| `District` | `districts` | yes | `state_name` used as a join key |
| `Area` | `areas` | default | unused |
| `AreaAssign` | `area_assigns` | default | unused |
| `Cart` | `carts` | default | `pre_order` stripped |
| `Wishlist` | `wishlists` | yes | no `user()` relation; `ip_address` vs `ip_addresscart` |
| `Product` | `products` | default | `SoftDeletes`; `offer_price`/`mrp_price` not columns; `variants()` + `productvari()` |
| `ProductVarient` | `product_varient` | yes | `category()` uses `category_id` instead of `categoryid` |
| `ProductStock` | `productstocks` | yes | misspelled attribute access |
| `ProductOrder` | `product_orders` | yes | 14 `$fillable` columns absent from the dump; timestamps fillable; `user()` + `customer()` |
| `ProductOrderItem` | `product_order_items` | yes | `product_image` written, column absent |
| `ProductOrderUserAddress` | `product_order_user_addresses` | yes | keyed on the varchar `order_id` |
| `ProductTransactionLog` | `product_transaction_logs` | yes | no gateway reference — not a payment record |
| `ProductTracking` | `product_tracking` | default | keyed on the varchar `order_id` |
| `ProductSlot` | `product_slots` | yes | keyed on the varchar `order_id` |
| `Category` | `categories` | default | `subCategories()` uses a non-existent FK; no `slug` |
| `SubCategory` | `sub_categories` | default | `category()` FK does not exist |
| `Brand` | `brands` | default | code reads `slug`, column is `brand_slug` |
| `Unit` | `units` | default | fine |
| `Coupon` | `coupons` | yes | never queried |
| `HomePromotion` | `home_promotions` | default | never queried |

---

## 9. Fix order for the model

```mermaid
flowchart TD
    A["1. Guarded migrations:<br/>billing_state, shipping_state,<br/>courier_name, discount,<br/>product_order_items.product_image"] --> B["2. Add variant weight/length/breadth/height"]
    B --> C["3. composer require razorpay/razorpay"]
    C --> D["4. Commit the untracked checkout code"]
    D --> E["5. UNIQUE razorpay_order_id,<br/>wishlists, carts"]
    E --> F["6. FKs on carts/wishlists/user_addresses.<br/>user_id; fix ON DELETE CASCADE on orders"]
    F --> G["7. Index user_id, order_id columns"]
    G --> H["8. Fix the 3 broken relationships"]
    H --> I["9. Normalise user_id and order_id types"]
    I --> J["10. Extract OrderTotalsCalculator"]
    J --> K["11. Write the 11 Tier-1 tests"]
```

Steps 1-2 are revenue-blocking. Step 2 in particular fixes shipping quotes that are currently wrong for every order.

---

## 10. Related documents

- [07-database-schema.md](07-database-schema.md) — table-by-table reference
- [11-models-and-relationships.md](11-models-and-relationships.md) — Eloquent detail
- [22-database-integrity.md](22-database-integrity.md) — constraints and migrations
- [26-data-model-diagram.md](26-data-model-diagram.md) — this document
- [29-remediation-plan.md](29-remediation-plan.md) — the fix sequence