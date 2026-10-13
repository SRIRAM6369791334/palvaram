# 14 — Input Validation

## Coverage measurement

`VERIFIED` — count of `$request->validate(...)` calls per controller file in
`app/Http/Controllers/` (70 files at the top level):

| Controllers with **zero** validation | Count |
| --- | --- |
| — | **34 of 70** |

Controllers with no `validate()` call at all:

```
MilkRefundController            MilkSlotController             NotificationController
MilkOrdersController            OrderSummeryController         OrderwiseController
PackingDeliveryController       PackingDispatchController       PackingOrderController
ProductRefundController         ProductReturnController        ProductSlotController
ProductwiseController           PurchaseReportController        ReportsController
ScrollingBarController          ShiprocketController            TopCustomerController
CancelProductController         CompoStockController            GiftProductwiseController
GiftInventoryReportController   GiftPurchaseReportController    AreaAssignController
AdminController                 BulkOrderController             LocationsController
InventoryReportController       IndexContrller                 GiftOrderWiseController
sendsms                         Controller                     packingCompleteController
```

`VERIFIED` — controllers with validation, and how much:

| Calls | Controllers |
| --- | --- |
| 1 | `PurchaseOrderController`, `ProductOrdersController`, `StockDistributionController`, `ShippingController`, `ScrollingBarController`, `GiftPurchaseOrderController`, `StockTrackingController` |
| 2 | `StockController`, `SubCategoryController`, `RoleController`, `WareHouseController`, `WareHouseBranchController`, `UnitController`, `ProductThumController`, `BrandController`, `CouponController`, `DeliveryPersonController`, `GiftColorController`, `GiftSizeController`, `GiftSupplierController`, `GiftVariantController`, `OfferImageController`, `AreaController`, `NewcouponController`, `HomePromotionController`, `TodayDealsController`, `ProductVarientControllet` |
| 3 | `SupplierDetailController`, `CountryController`, `DashboardUserController`, `ProductController` |
| 4 | `UserController`, `ProductManagementController`, `NotificationController` |
| 5 | `BannerImagesController`, `CategoryController` |
| 6 | `GiftController` |

**Nearly half the request surface accepts unvalidated input.**

## The `validate()` result is often discarded — `BROKEN`

`VERIFIED` — even where validation runs, the validated array is frequently ignored in favour of raw
request access. Example, `ProductController::store()`:

```php
$validated = $request->validate([
    'country_id' => "required|array",
    'category_id' => "required",
    'product_name' => "required",
    'product_specification' => "nullable",
    'product_image' => "required|mimes:png,jpg,webp,jpeg",
    'product_description' => "nullable",
    'brand_id' => "nullable",
    'subcategory_id' => "nullable",
    'slug' => "nullable|unique:products,slug",
]);

// ...
$subcate   = $request->subcategory_id;   // not $validated
$cate      = $request->category_id;      // not $validated
$itemCode  = $request->item_code;        // never validated at all
$sizecheck = $request->size_check;       // never validated at all
```

Only the spread `...$validated` at line 177 uses the validated data. Three fields
(`item_code`, `size_check`, and the country normalisation at lines 131-135, which **merges
unsanitised** comma-separated input into the request) bypass validation entirely.

`VERIFIED` — `country_id` is declared `required|array`, but line 131 rewrites a string value into
an array before validation runs, and line 154 then does `implode(',', $request->country_id)`. An
input of `country_id=1,2,abc` reaches the database as `1,2,abc` in a column the models cast as
`string` or treat as scalar.

## Missing validation on the most sensitive endpoints

`VERIFIED`

| Endpoint | Reads without validation |
| --- | --- |
| `ProductController::createMilkSubscription()` | `plan_type`, `user_id`, `product_id`, `quantity`, `from_date`, `to_date` |
| `ProductController::createProductSubscription()` / `createProductSlot()` | `product_id[]`, `productvar_id[]`, `product_quantity[]`, `from_date`, `user_id`, `selettype` |
| `ProductController::upadetstatus()` | `order_id`, `select_status`, `user_id`, `phone_number` |
| `ProductController::viewProductInvoice()` | `order_id` (defaults to `null`) |
| `StockController::store()` | `product_id`, `pro_ver_id`, `category_id`, `subcategory_id`, `country_id`, `warehouse_id`, `branch_id`, `stock_quantity` |
| `StockController::update()` / `reduceStock()` / `update1()` | `productid`, `stock_quantity` |
| `StockController::acceptTransfer()` / `cancelTransfer()` | route `$id` |
| `ProductSlotController` (0 validate calls) | all of its ~10 endpoints |
| `MilkOrdersController`, `MilkSlotController`, `MilkRefundController` | all endpoints |
| All packing/dispatch controllers | all endpoints |

## Integer coercion without validation

`VERIFIED` — `StockController` casts instead of validating:

```php
$stockQty = (int) $request->stock_quantity;
```

`(int)` on absent, null, array, or non-numeric input yields `0`; on a negative string it yields a
negative number. `reduceStock()` and `update()` then apply that value directly:

```php
$productStock->overallstock   = max($productStock->overallstock   - $stockQty, 0);
$productStock->availablestock = max($productStock->availablestock - $stockQty, 0);
$productVariant->product_qty  = max($productVariant->product_qty  - $stockQty, 0);
```

A negative `stock_quantity` **increases** stock on all three fields. The `max(…, 0)` clamp then
prevents a negative result, so the endpoint silently accepts nonsense and returns HTTP 200.

Contrast `StockController::transferStock()`, which validates `'transfer_quantity' => 'required|integer|min:1'`.
The two endpoints that move stock have opposite validation standards.

## Missing business-rule validation

`VERIFIED` — validation covers types and presence but almost never business constraints:

| Rule that should exist | Current state |
| --- | --- |
| Order quantity must not exceed available stock | Not checked anywhere in `createProductSlot()` |
| `status` transition must be legal | Not checked in `upadetstatus()`; `cancelOrder()` alone guards on `['pending','processing']` |
| Order must belong to the caller's country | Not checked |
| `productvar_id[]` must reference variants of `product_id[]` | Not checked — arrays are zipped positionally (`$productId[$key]`, `$quantity[$key]`) |
| Damaged quantity must not exceed stock | **Checked** in `ProductManagementController::logDamaged()` — the one good example |
| Write-off quantity must not exceed batch remaining | **Checked** in `writeOffExpired()` |
| Self-transfer must be rejected | **Checked** in `transferStock()` (`$fromBranchId == $toBranchId` → 422) |
| Password minimum length | Not checked (`'password' => 'required'`) |
| `country_id.*` must exist | **Checked** in `DashboardUserController` |

`VERIFIED` — positional zipping in `createProductSlot()` is the sharpest edge:

```php
foreach ($productvarid as $key => $value) {
    $slots[] = [
        "product_id"       => $productId[$key],
        "product_varient_id" => $value,
        "quantity"         => $quantity[$key],
    ];
}
```

If `productvar_id` has 3 entries and `product_quantity` has 1, PHP emits an undefined-index notice
and stores `null` quantities. Nothing verifies that a supplied variant actually belongs to the
supplied product, so stock can be decremented against an arbitrary variant.

## SQL injection — not found

`VERIFIED` — no raw SQL string concatenation with request input was found in the audited
controllers. Eloquent and the query builder parameterise values. The only `DB::raw()` uses are
static expressions:

| Location | Expression | Risk |
| --- | --- | --- |
| `HomeController::root()` | `DB::raw('SELECT (SELECT COUNT(*) FROM users) AS user_count, ...')` | Static |
| `StockController::lowstock()` | `where('availablestock', '<', DB::raw('product_varient.low_stock'))` | Static |
| `StockController::transferIndex()` | `\DB::raw('SUM(quantity) as total_distributed')` | Static |

`UNVERIFIED` — a complete audit of all 84 controllers for concatenated SQL was not performed. The
34 zero-validation controllers warrant a follow-up grep for `whereRaw`, `DB::select`, and
`DB::statement`.

## XSS — partial exposure

`VERIFIED` — Blade's `{{ }}` auto-escapes and the codebase predominantly uses it. Risk is
concentrated in:

| Location | Risk |
| --- | --- |
| `resources/views/**` | `UNVERIFIED` — not exhaustively scanned for `{!! !!}`, `v-html`, or inline `innerHTML` |
| `resources/views/layouts/master-layouts.blade.php` | `BROKEN` — omits `@csrf`; see [19-frontend-architecture.md](19-frontend-architecture.md) |

`VERIFIED` — stored HTML fields exist by design (`product_description`, `product_specification`,
notification `content`), which are rendered somewhere. `UNVERIFIED` — whether they pass through
`{!! !!}`.

## Recommendations

1. Adopt a `FormRequest` per endpoint so validation lives beside authorisation, not inside a
   1000-line controller.
2. Use `$request->validated()` exclusively; never spread `$request->all()` or read raw fields after
   validating.
3. Add `min:8|confirmed` to every password field.
4. Replace `(int) $request->x` with `integer|min:1` validation rules.
5. Validate array alignment for `product_id[]`/`productvar_id[]`/`product_quantity[]` — same count,
   and each variant belongs to its product.
6. Add the missing stock-availability check to `createProductSlot()` (see
   [07-data-integrity.md](07-data-integrity.md)).
7. Introduce a status enum and validate transitions centrally.
8. Grep the 34 zero-validation controllers for `whereRaw`/`DB::select` and for `{!! !!}` in views.