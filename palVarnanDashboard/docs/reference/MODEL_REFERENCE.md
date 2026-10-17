# MODEL_REFERENCE.md

**Source:** 63 Eloquent models in `app/Models/*.php`.  
**Evidence:** `VERIFIED_FROM_SOURCE`, `INFERRED`, `UNVERIFIED`, `NOT_AVAILABLE`

> See also: [DATABASE_RELATIONSHIPS.md](./DATABASE_RELATIONSHIPS.md), [DATABASE_TABLE_REFERENCE.md](./DATABASE_TABLE_REFERENCE.md), [../06-data-model.md](../06-data-model.md)

## Overview

- **Total models:** 63 files in `app/Models/`. `VERIFIED_FROM_SOURCE` ([../audit/VERIFIED_FACTS.md](../audit/VERIFIED_FACTS.md#counts-verified_from_source)).
- **Only 5 models define `$casts`:** `DashboardUser`, `User`, `DeliveryPerson`, `Category`, `Brand`. `VERIFIED_FROM_SOURCE` ([../06-data-model.md](../06-data-model.md#casts--broken)).
- **2 models fully unguarded (`$guarded = []`):** `AppAlert`, `AppNotification`. `VERIFIED_FROM_SOURCE` ([../06-data-model.md](../06-data-model.md#mass-assignment)).
- **Only 1 model has query scopes:** `ProductStock`. `VERIFIED_FROM_SOURCE` ([../06-data-model.md](../06-data-model.md#query-scopes)).
- **32 models have no relationships defined.** `INFERRED`.

## Authenticatable models

| Model | File | Table | Auth wired? | Notes |
|---|---|---|---|---|
| `DashboardUser` | `app/Models/DashboardUser.php` | `dashboard_users` | Yes (web guard) | Uses `HasRoles` (Spatie), `protected $guard_name = 'web'`. Casts: `email_verified_at => datetime`, `country_id => array`. `VERIFIED_FROM_SOURCE` ([../06-data-model.md](../06-data-model.md#authenticatable-models), [../06-data-model.md](../06-data-model.md#multi-country-representation--verified)). |
| `User` | `app/Models/User.php` | `users` | No | Has `password`, `remember_token`, `enc_password` fillable; not wired to auth. Casts present. `VERIFIED_FROM_SOURCE`. |
| `DeliveryPerson` | `app/Models/DeliveryPerson.php` | `delivery_people` | No | Similar to User (password fields present), not wired to auth. Casts present. `VERIFIED_FROM_SOURCE`. |

## Major domain groups

### Catalogue
| Model | File | Table | Notes |
|---|---|---|---|
| `Product` | `Product.php` | `products` | Uses `SoftDeletes` (matches migration). No casts for prices/dates. `VERIFIED_FROM_SOURCE` ([../05-database-schema.md](../05-database-schema.md#soft-deletes), [../06-data-model.md](../06-data-model.md#soft-deletes--incomplete)). |
| `ProductVarient` | `ProductVarient.php` | `product_varient` | `$fillable` contains `product_gst` twice. Misspelled class name. `VERIFIED_FROM_SOURCE` ([../06-data-model.md](../06-data-model.md#mass-assignment), [../06-data-model.md](../06-data-model.md#naming-conventions--incomplete)). |
| `ProductChildImage` | `ProductChildImage.php` | `product_child_images` | `INFERRED`. |
| `WebImage` | `WebImage.php` | `web_images` | `INFERRED`. |
| `Category` | `Category.php` | `categories` | Casts `country_id => string`. `VERIFIED_FROM_SOURCE` ([../06-data-model.md](../06-data-model.md#casts--broken)). |
| `SubCategory` | `SubCategory.php` | `sub_categories` | `INFERRED`. |
| `Brand` | `Brand.php` | `brands` | Casts `country_id => string`. `VERIFIED_FROM_SOURCE`. |
| `Unit` | `Unit.php` | `units` | **Empty/minimal?** `INFERRED` (flagged as potentially empty). `UNVERIFIED`. |
| `GiftColor` | `GiftColor.php` | `gift_color` | `INFERRED`. |
| `GiftSize` | `GiftSize.php` | `gift_size` | `INFERRED`. |
| `TodayDeals` | `TodayDeals.php` | `today_deals` | `INFERRED`. |
| `HomePromotion` | `HomePromotion.php` | `home_promotions` | **Empty/minimal?** `INFERRED`. `UNVERIFIED`. |
| `OfferImage` | `OfferImage.php` | `offer_images` | `INFERRED`. |
| `BannerImage` | `BannerImage.php` | `banner_images` | `INFERRED`. |
| `Coupon` | `Coupon.php` | `coupons` | `INFERRED`. |
| `Newcoupon` | `Newcoupon.php` | `newcoupons` | `INFERRED`. |
| `ScrollingBar` | `ScrollingBar.php` | `scrolling_bars` | `INFERRED`. |

### Inventory/Warehouse/Purchase
| Model | File | Table | Notes |
|---|---|---|---|
| `ProductStock` | `ProductStock.php` | `productstocks` | Has 3 scopes (`withFilters`, `lowStock`, `deadStock`). **Defects:** `Productvarient()` joins on wrong FK (`id` vs `pro_ver_id`); `warehouse()` references `Warehouse` (class name case) vs `WareHouse`. `VERIFIED_FROM_SOURCE` ([../06-data-model.md](../06-data-model.md#defective-relationships--broken), [../06-data-model.md](../06-data-model.md#query-scopes)). |
| `ProductStockBatch` | `ProductStockBatch.php` | `product_stock_batches` | `INFERRED`. |
| `StockDistribution` | `StockDistribution.php` | `stock_distributions` | `INFERRED`. |
| `StockTransfer` | `StockTransfer.php` | `stock_transfers` | `INFERRED`. |
| `WareHouse` | `WareHouse.php` | `warehouse` | Class name case differs from common `Warehouse`. `INFERRED`. |
| `WareHouseBranch` | `WareHouseBranch.php` | `warehouse_branch` | `INFERRED`. |
| `SupplierDetail` | `SupplierDetail.php` | `supplier_details` | `INFERRED`. |
| `PurchaseOrder` | `PurchaseOrder.php` | `purchase_orders` | `INFERRED`. |
| `PurchaseOrderItem` | `PurchaseOrderItem.php` | `purchase_order_items` | `INFERRED`. |
| `GiftPurchaseOrder` | `GiftPurchaseOrder.php` | `gift_purchase_orders` | `INFERRED`. |
| `GiftPurchaseOrderItem` | `GiftPurchaseOrderItem.php` | `gift_purchase_order_items` | `INFERRED`. |

### Orders/Fulfillment
| Model | File | Table | Notes |
|---|---|---|---|
| `ProductOrder` | `ProductOrder.php` | `product_orders` | 13 relations. **Critical defects:** `items()` uses string `order_id` FK to int PK; `area()` dereferences `$this->customer->area()` without null guard; `product()` references non-header `product_id`. Appends derived `is_cancelled` (shadows column conceptually). `created_at`/`updated_at` in `$fillable`. `VERIFIED_FROM_SOURCE` ([../06-data-model.md](../06-data-model.md#defective-relationships--broken), [../06-data-model.md](../06-data-model.md#accessor-that-shadows-a-real-column--verified), [../06-data-model.md](../06-data-model.md#mass-assignment)). |
| `ProductOrderItem` | `ProductOrderItem.php` | `product_order_items` | Belongs to ProductOrder/Product/ProductVarient. `INFERRED`. |
| `ProductOrderUserAddress` | `ProductOrderUserAddress.php` | `product_order_user_addresses` | **Defect:** `country()` joins via `area_id`. `VERIFIED_FROM_SOURCE` ([../06-data-model.md](../06-data-model.md#defective-relationships--broken)). |
| `ProductSlot` | `ProductSlot.php` | `product_slots` | `INFERRED`. |
| `ProductRefund` | `ProductRefund.php` | `product_refunds` | `INFERRED`. |
| `ProductTracking` | `ProductTracking.php` | `product_tracking` | `INFERRED`. |
| `ProductTransactionLog` | `ProductTransactionLog.php` | `product_transaction_logs` | Written after stock deduction without transaction. `INFERRED` ([../07-data-integrity.md](../07-data-integrity.md#transaction-log-written-after-the-fact--incomplete)). |
| `CancelRequest` | *(no model file)* | `cancel_requests` | **No model file exists** — table only. `VERIFIED_FROM_SOURCE` ([../06-data-model.md](../06-data-model.md#model-inventory-by-domain)). |
| `BulkOrder` | `BulkOrder.php` | `bulkorders` | `INFERRED`. |
| `Cart` | `Cart.php` | `carts` | `$fillable` contains `ip_addresscart` (typo; no `ip_address` reachable). `VERIFIED_FROM_SOURCE` ([../06-data-model.md](../06-data-model.md#mass-assignment)). |
| `Review` | `Review.php` | `reviews` | `INFERRED`. |
| `Wishlist` | `Wishlist.php` | `wishlists` | `INFERRED`. |
| `Testimonial` | `Testimonial.php` | `testimonials` | `INFERRED`. |

### Milk/subscription
| Model | File | Table | Notes |
|---|---|---|---|
| `MilkOrder` | `MilkOrder.php` | `milk_orders` | `$fillable` includes `id`, `created_at`, `updated_at`. `VERIFIED_FROM_SOURCE` ([../06-data-model.md](../06-data-model.md#mass-assignment)). |
| `MilkSlot` | `MilkSlot.php` | `milk_slots` | `$fillable` includes `id`, `created_at`, `updated_at`. `VERIFIED_FROM_SOURCE`. |
| `MilkRefund` | `MilkRefund.php` | `milk_refunds` | `INFERRED`. |
| `MilkOrderUserAddress` | `MilkOrderUserAddress.php` | `milk_order_user_addresses` | `INFERRED`. |
| `MilkTransactionLog` | `MilkTransactionLog.php` | `milk_transaction_logs` | `INFERRED`. |
| `PlanType` | `PlanType.php` | `plan_types` | `INFERRED`. |

### Customers/Logistics/Geography
| Model | File | Table | Notes |
|---|---|---|---|
| `UserAddress` | `UserAddress.php` | `user_addresses` | `INFERRED`. |
| `Area` | `Area.php` | `areas` | `INFERRED`. |
| `AreaAssign` | `AreaAssign.php` | `area_assigns` | `INFERRED`. |
| `City` | `City.php` | `cities` | `$fillable` includes `id`, `created_at`, `updated_at`. `VERIFIED_FROM_SOURCE`. |
| `State` | `State.php` | `states` | `INFERRED`. |
| `District` | `District.php` | `districts` | `$fillable` includes `id`, `created_at`, `updated_at`. `VERIFIED_FROM_SOURCE`. |
| `Country` | `Country.php` | `countries` | `INFERRED`. |
| `AddressType` | `AddressType.php` | `address_types` | `INFERRED`. |
| `GenderType` | `GenderType.php` | `gender_types` | `INFERRED`. |
| `AllIndiaPincode` | `AllIndiaPincode.php` | `all_india_pincodes` | `INFERRED`. |
| `ContactDetail` | `ContactDetail.php` | `contact_details` | `INFERRED`. |
| `Otp` | `Otp.php` | `otps` | `INFERRED`. |
| `MailOtp` | `MailOtp.php` | `mail_otps` | `INFERRED`. |

### Content/Misc
| Model | File | Table | Notes |
|---|---|---|---|
| `Notification` | `Notification.php` | `notifications` | `INFERRED`. |
| `AppNotification` | `AppNotification.php` | `app_notifications` | `$guarded = []` (fully open). `VERIFIED_FROM_SOURCE`. |
| `AppAlert` | `AppAlert.php` | `app_alerts` | `$guarded = []` (fully open). **Accessor defect:** `getCreatedAtAttribute()` returns relative time string (not Carbon) — breaks ordering/serialization. `VERIFIED_FROM_SOURCE` ([../06-data-model.md](../06-data-model.md#overridden-created_at--verified)). |
| `UserReadNotification` | `UserReadNotification.php` | `user_read_notifications` | `INFERRED`. |
| `FreeShippingSetting` | `FreeShippingSetting.php` | `free_shipping_settings` | `INFERRED`. |
| `DeliveryChargeDetail` | `DeliveryChargeDetail.php` | `delivery_charge_details` | `INFERRED`. |

### Misc/Utility
| Model | File | Table | Notes |
|---|---|---|---|
| `Test` | `Test.php` | `tests` | `INFERRED`. |

## Fillable/guarded/casts summary (highlights)

- **No casts for money/quantities/dates/booleans** in most models. `VERIFIED_FROM_SOURCE`.
- **PK/timestamps in fillable:** `City`, `District`, `MilkOrder`, `MilkSlot`, `ProductOrder`. `VERIFIED_FROM_SOURCE`.
- **Unguarded:** `AppAlert`, `AppNotification`. `VERIFIED_FROM_SOURCE`.
- **Cart fillable typo:** `ip_addresscart`. `VERIFIED_FROM_SOURCE`.
- **ProductVarient duplicate:** `product_gst` listed twice. `VERIFIED_FROM_SOURCE`.
- **Country_id type asymmetry:** `DashboardUser` casts to `array` (JSON), `Category`/`Brand` cast to `string` (scalar). `VERIFIED_FROM_SOURCE`.

## Relations summary (defective)

- `ProductStock::Productvarient()` uses wrong FK (`id`) — should be `pro_ver_id`. `VERIFIED_FROM_SOURCE`.
- `ProductStock::warehouse()` references `Warehouse::class` but model is `WareHouse` (case). `VERIFIED_FROM_SOURCE`.
- `ProductOrderUserAddress::country()` joins via `area_id`. `VERIFIED_FROM_SOURCE`.
- `ProductOrder::product()` references non-header `product_id`. `VERIFIED_FROM_SOURCE`.
- `ProductOrder::area()` dereferences customer without null guard. `VERIFIED_FROM_SOURCE`.
- `ProductOrder::items()` has type mismatch: string `order_id` → int `ProductOrder.id`. `VERIFIED_FROM_SOURCE`.

## Empty models check

- `Unit.php`: `UNVERIFIED` — not read line-by-line here; flagged as potentially empty per task. `INFERRED`.
- `HomePromotion.php`: `UNVERIFIED` — flagged as potentially empty per task. `INFERRED`.

## Related docs

- [DATABASE_RELATIONSHIPS.md](./DATABASE_RELATIONSHIPS.md) — relation graph/defects
- [DATABASE_TABLE_REFERENCE.md](./DATABASE_TABLE_REFERENCE.md) — table details
- [../06-data-model.md](../06-data-model.md) — comprehensive model analysis