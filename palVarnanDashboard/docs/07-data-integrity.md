# 07 — Data Integrity

This document covers transaction safety, quantity/money consistency, and concurrency in the
order and stock write paths.

## Transaction coverage — `BROKEN`

`VERIFIED` — across 84 controllers, only **two** use database transactions:

| Controller | `beginTransaction`/`commit`/`rollBack` occurrences |
| --- | --- |
| `ProductManagementController` | 12 |
| `StockController` | 5 |

The remaining **82 controllers** perform multi-row writes with no transactional boundary. The two
most consequential — order creation with immediate stock deduction, and order cancellation with
stock restoration — are both outside that set.

## Order creation: stock is decremented without an availability check

`VERIFIED` — `ProductController::createProductSlot()`
(`app/Http/Controllers/ProductController.php:702-829`).

The flow for a multi-variant order (`selettype == "2"`, lines 724-748):

1. `$totalAmount` accumulates `ProductVarient::findOrFail($value)->offer_price * $quantity[$key]`.
2. `ProductOrder::where('order_id', $orderId)->update(["total_amount" => $totalAmount])`.
3. `ProductSlot::insert($slots)`.
4. A loop then decrements stock per slot.

The decrement loop (lines 749-770 and the equivalent 794-815):

```php
$productStock = ProductStock::where('pro_ver_id', $productVarientId)->first();
$productVarient = ProductVarient::where('id', $productVarientId)->first();

$availableStock = $productStock->availablestock - $quantity;
$saleStock = $productStock->salestock + $quantity;

ProductStock::where('pro_ver_id', $productVarientId)->update([...]);
ProductVarient::where('id', $productVarientId)->update(['product_qty' => $productVarientAvailQty]);
```

Defects:

| Issue | Detail |
| --- | --- |
| No availability check | Nothing compares `$quantity` against `$productStock->availablestock`. Stock can go **negative** without error. |
| No null guard | `->first()` may return `null` if the variant has no stock row; `$productStock->availablestock` then throws a 500 **after** `ProductSlot::insert()` already committed. The order exists, the slot exists, the stock is not decremented. |
| No transaction | Steps 2, 3 and 4 are three independent commits. A failure in step 4 leaves an order whose stock was never deducted. |
| Warehouse-agnostic | The query filters only on `pro_ver_id`, ignoring `warehouse_id`/`branch_id`. With multi-warehouse stock this decrements an arbitrary one of several rows. |
| Total overwritten | `total_amount` is **replaced** with a slot-derived figure, discarding any shipping charge, GST, or discount computed earlier. |

## Order ID generation — `BROKEN`

`VERIFIED`

| Location | Expression | Prefix |
| --- | --- | --- |
| `ProductController.php:457-458` | `MilkOrder::max('id') + 1` | `HG-ORD-%06d` |
| `ProductController.php:633-634` | `ProductOrder::max('id') + 1` | `HG-ORD-PR-%06d` |
| `TestController.php` | hardcoded | `MV-ORD-000006`, `MV-CUS-00002` |

`MAX(id) + 1` is a classic race: two concurrent order creations compute the same identifier. No
unique constraint is declared on `milk_orders.order_id` or `product_orders.order_id` in any
migration, so a duplicate `order_id` would be silently accepted and then produce order/slot rows
that cannot be distinguished.

`UNVERIFIED` — whether the deployed database has unique indexes on these columns added outside
migrations. See [05-database-schema.md](05-database-schema.md).

## Orders are created as already-paid — `INCOMPLETE`

`VERIFIED` — `createMilkSubscription()` writes `payment_status => 1` **at creation time**
(`app/Http/Controllers/ProductController.php:478`), before any payment is attempted.
`createProductSlot()` does the same (`app/Http/Controllers/ProductController.php:709-711`). No
Razorpay verification call is made anywhere in this controller; the payment columns exist
(`razorpay_order_id`, `razorpay_payment_id` in `ProductOrder::$fillable`) but are never populated by
the dashboard.

These are back-office "create order on behalf of a customer" endpoints, so a pre-set paid status
may be intentional. It does mean payment state cannot be trusted as evidence of money movement, and
`MilkTransactionLog::create()` writes `amount_credited => $totalAmount` in the same request.

## Money is computed as strings — `BROKEN`

`VERIFIED`

| Location | Expression |
| --- | --- |
| `ProductController.php:485` | `Product::findOrFail($productId)->product_mrp_price * $quantity * $noOfdays` |
| `ProductController.php:726`, `773` | `ProductVarient::findOrFail($value)->offer_price * $quantity[$key]` |
| `ProductController.php:825` | `'order_amount' => $totalAmount` |

`VERIFIED` — no model in `app/Models` casts price columns to `decimal` or `integer`. MySQL returns
`DECIMAL` columns as strings, so PHP performs numeric-string coercion. Consequences:

- Values like `0.1 + 0.2` accumulate binary floating-point error into `total_amount` and into
  `product_transaction_logs.order_amount`, with no rounding step anywhere.
- Line 485 uses **`product_mrp_price`** as the billed amount while line 726 uses **`offer_price`**.
  The same business flow bills different columns depending on which branch executes.
- Five overlapping price concepts exist across tables (`product_mrp_price`,
  `product_regular_price`, `mrp_price`, `offer_price`, `offer_value`) with no single source of
  truth. See [06-data-model.md](06-data-model.md).

## Order cancellation / stock restoration — `BROKEN`

`VERIFIED` — `app/Http/Controllers/ProductController.php:1122-1134` restores stock using raw
`DB::table()` calls:

```php
$getstock = DB::table('productstocks')
    ->where("productid", $ord->product_id)
    ->where('pro_ver_id', $ord->product_varient_id)
    ->first();

DB::table('productstocks')
    ->where("productid", $ord->product_id)
    ->where('pro_ver_id', $ord->product_varient_id)
    ->update([
        "overallstock" => $getstock->overallstock + $ord->quantity,
        "availablestock" => $getstock->availablestock + $ord->quantity,
        "salestock" => $getstock->salestock - $ord->quantity,
    ]);
```

Issues:

- **Read-then-write, no transaction, no locking.** Two concurrent cancellations read the same
  `availablestock` and both write `old + qty`, losing one increment.
- **Bypasses Eloquent entirely** — no model events, no `updated_at` maintenance, and
  `last_stockupdate_date` is not refreshed, so `ProductStock::scopeDeadStock()` will keep flagging
  the row as stagnant.
- `salestock` is decremented, which can go negative if the original sale was never recorded.
- Filtering on `productid` + `pro_ver_id` without `warehouse_id` matches multiple stock rows;
  `update()` affects all of them while the increment was computed from only one `first()`.

## Two competing definitions of "low stock" — `BROKEN`

`VERIFIED`

| Source | Rule |
| --- | --- |
| `product_varient.low_stock` | Cross-table: `productstocks.availablestock < product_varient.low_stock` (`StockController::lowstock()`) |
| `productstocks.low_stocks` | Same-table: `availablestock <= low_stocks` (`ProductStock::scopeLowStock()`) |

Both columns exist and are fillable. The live controller uses the variant column; the model scope
uses the stock column; neither references the other. Two admins can see different low-stock lists
depending on which code path renders the number.

`VERIFIED` — `StockController::update1()` hardcodes a third threshold: `where('availablestock', '<', 6)`.

## `ProductVarient.product_qty` is maintained inconsistently

`VERIFIED` — three code paths assign this column differently:

| Operation | Behaviour |
| --- | --- |
| `StockController::update()` | `$productVariant->product_qty = $productStock->availablestock;` — **absolute assignment** of a single warehouse's available stock. |
| `StockController::update1()` | `$productVariant->product_qty += $stockQty;` — **relative increment**. |
| `StockController::reduceStock()` | `$productVariant->product_qty = max($productVariant->product_qty - $stockQty, 0);` — decrement, clamped at 0. |
| `StockController::acceptTransfer()` | Does **not** touch `product_qty` at all when moving stock between warehouses. |

`VERIFIED` — `acceptTransfer()` moves `availablestock` and `overallstock` between warehouse rows
without updating `product_varient.product_qty`. Since `update()` assigns the field from one
warehouse row, `product_qty` reflects "whatever warehouse was last edited" rather than total
network availability. It cannot be relied upon.

## `reduceStock()` clamps instead of rejecting — `INCOMPLETE`

`VERIFIED` — `max($productStock->overallstock - $stockQty, 0)`. A request to remove more stock than
exists silently sets the value to `0` and returns HTTP 200 with the updated list. Callers cannot
distinguish "reduced as requested" from "silently floored", and no warning is recorded.

By contrast `StockController::transferStock()` and `warehouseTransfer()` **do** validate
(`if ($transferQty > $available) return 422`). The inconsistency means over-reduction is reachable
through the manual stock screen.

## Stock existence check ignores the variant — `BROKEN`

`VERIFIED` — `StockController::store()`:

```php
$exists = ProductStock::where('productid', $request->product_id)
    ->where('warehouse_id', $request->warehouse_id)
    ->where('branch_id', $request->branch_id)
    ->first();
```

`pro_ver_id` is absent from the existence check. Two variants of the same product in the same
warehouse and branch are treated as one stock row, so the second variant is rejected with
`{"status":"exists"}` while the response returns the **first variant's** `stock_id`.

`VERIFIED` — `store()` also does not update `ProductVarient.product_qty`, so the new stock row and
the variant's quantity field diverge immediately.

## Transfer lifecycle: pending rows hold no reservation

`VERIFIED` — `transferStock()` and `warehouseTransfer()` both contain the comment
`// Just log, NO stock mutation yet`. They validate available quantity, then insert a
`StockTransfer` row with `status => 'pending'`.

Stock only moves in `acceptTransfer()`, which is a **separate HTTP request** (`$id` route
parameter). Between creation and acceptance:

- The same units can be promised to several pending transfers, because each creation re-checks
  availability against a quantity that has not yet been reserved.
- `acceptTransfer()` re-checks availability, which is the correct backstop — but it uses
  `DB::beginTransaction()` with **no row locking** (`lockForUpdate` is absent), so two simultaneous
  accepts can both pass the check and both commit.

`VERIFIED` — `acceptTransfer()` wraps its work in `DB::beginTransaction()` / `commit()` /
`rollBack()`, and the store→store branch is correct in structure. However its `catch` block calls
`DB::rollBack()` unconditionally. `StockTransfer::findOrFail($id)` executes **before**
`DB::beginTransaction()`, so a request for a non-existent transfer id throws
`ModelNotFoundException`, is caught, and then `DB::rollBack()` runs with no active transaction —
raising a second exception that replaces the intended 404 with a 500.

## Store→store transfer checks `sum()` but updates one row — `BROKEN`

`VERIFIED` — `acceptTransfer()`, store branch:

- Availability is computed as `StockDistribution::...->sum('quantity')` across **all** matching rows.
- The deduction then targets a single row: `->orderBy('distributed_at', 'desc')->first()`, reducing
  `quantity` to `max(0, $senderDist->quantity - $qty)`.

If stock was distributed across several rows, `sum()` reports enough units to accept the transfer
while the single `first()` row is smaller than the requested quantity. The `max(0, ...)` clamp then
zeroes the row, and units are **silently lost** rather than spread across the remaining rows.

## Transaction log written after the fact — `INCOMPLETE`

`VERIFIED` — `ProductTransactionLog::create()` is the last statement of
`createProductSlot()` (lines 822-828), after stock has already been decremented, and it is not in a
transaction. A failure there leaves stock deducted with no corresponding ledger row. The same
pattern appears in `createMilkSubscription()`.

## Gift inventory is a separate, unguarded path — `INCOMPLETE`

`VERIFIED` — `GiftController` (557 lines) and the `gift*` controllers run parallel stock logic
with their own queries. `StockController` filters on `products.is_gift` in both directions
(`index()` for `0`, `giftIndex()` for `1`), so the flag is respected at the list level, but
`BROKEN`-class defects such as the missing availability check are replicated rather than avoided.
Whether gift stock decrement shares the retail decrement code path is `UNVERIFIED`.