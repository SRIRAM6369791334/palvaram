# 12 — Business Logic

## Order creation — milk subscription

`VERIFIED` — `ProductController::createMilkSubscription(Request)`
(`app/Http/Controllers/ProductController.php:450-...`).

```php
$user    = User::query()->where("user_id", $userId)->first();
$lastId  = MilkOrder::max('id');
$orderId = sprintf('HG-ORD-%06d', $lastId + 1);
```

For `plan_type == "1"` (lines 460-481) it:

1. Expands `from_date`..`to_date` into an array of daily delivery dates.
2. `MilkOrder::create([... 'payment_status' => 1, 'date_to_delivery' => json_encode($dates) ...])`.
3. Calls `createMilkSlot($orderId)`.

Defects:

| Issue | Detail |
| --- | --- |
| No validation | `plan_type`, `user_id`, `product_id`, `quantity`, `from_date`, `to_date` are read straight from the request. |
| Order born "paid" | `payment_status => 1` at creation. No Razorpay call exists. |
| `MAX(id) + 1` race | Two concurrent requests produce the same `order_id`; no unique constraint declared. |
| Amount uses MRP | `Product::findOrFail($productId)->product_mrp_price * $quantity * $noOfdays` (line 485) — string arithmetic, and MRP rather than offer price. |
| Money without rounding | No `round()` or integer cast; accumulated into `MilkTransactionLog`. |
| Unbounded date loop | A `from_date`/`to_date` range of years generates millions of rows and one giant `MilkSlot::insert()`. |

`VERIFIED` — `createMilkSlot()` (lines 679-698) contains a broken guard:

```php
$orderedDates = json_decode($order->date_to_delivery);
...
if (!$orderedDates && !count($orderedDates)) {
    return errorResponse("Order Not Fount", 404);
}
```

`&&` should be `||`. With `$orderedDates === null`, `!$orderedDates` is `true`, so PHP then evaluates
`!count(null)` → **`TypeError` on PHP 8** / warning-and-`null` on PHP 7. Either way the intended
"no dates" check is not what executes.

`VERIFIED` — `MilkSlot::insert($slots)` is not wrapped in a transaction, and `payment_status` was
already set to `1` on the order one statement earlier.

## Order creation — product subscription

`VERIFIED` — `createProductSubscription()` → `createProductSlot($orderId, $selettype, $request)`
(lines 643, 702-829). Covered in detail in [07-data-integrity.md](07-data-integrity.md); the
structural points:

- `selettype == "2"` marks the order `delivery_status => 4` (delivered) at creation.
- Slot insert and the stock decrement are separate, untransacted commits.
- No availability check before decrementing.

## Delivery-person assignment

`VERIFIED` — `assignDeliverProductOrder($orderId, $user)` (lines 652-676):

```php
$deliveryPerson = DeliveryPerson::with(['areaAssigns', 'productOrders'])
    ->whereHas('areaAssigns', fn($q) => $q->where('area_id', $areaId))
    ->withCount('productOrders')
    ->orderBy('product_orders_count', 'asc')
    ->first();
```

Assigns the delivery person with the **fewest** product orders in the customer's area. Reasonable
load-balancing intent. Issues:

- `with(['areaAssigns', 'productOrders'])` eagerly loads **every** `productOrders` row for every
  candidate delivery person just to compute a count that `withCount()` already provides. On a
  delivery person with thousands of orders this is a large, unnecessary result set.
- `->first()` is applied after eager loading, so the full result set is hydrated first.
- `ProductOrder::query()->where("order_id", $orderId)->first()` then `->update(...)` — no null
  guard; a missing order produces a 500.
- `$productSlot->update([...])` is a **mass update on the whole slot set** — fine here, but it runs
  after the order update without a transaction.

## Order status update and embedded cURL — `BROKEN`

`VERIFIED` — `ProductController::upadetstatus(Request $request)`
(`app/Http/Controllers/ProductController.php:849+`), routed at `POST /updatestatus`
(`routes/web.php:503`).

A 20-line **commented-out** cURL block (lines 858-878) sits inside the method, containing a
Shiprocket login call with a hardcoded email and password. See
[16-external-integrations.md](16-external-integrations.md) — this is a credential leak in source,
not merely dead code.

The method reads `$request->order_id`, `$request->select_status`, `$request->user_id`,
`$request->phone_number` with no validation.

## Product management: the newest, best-structured code — and its own bugs

`VERIFIED` — `ProductManagementController` (232 lines) is the only controller that consistently
pairs `DB::beginTransaction()` / `commit()` / `rollBack()` with `$request->validate()`.
Four actions: `resolveReturn()`, `cancelOrder()`, `logDamaged()`, `writeOffExpired()`.

It is written entirely with `DB::table()` — no Eloquent, no model events.

### Cross-warehouse stock corruption — `BROKEN`

`VERIFIED` — `cancelOrder()` re-stocks with:

```php
DB::table('productstocks')
    ->where('productid', $item->product_id)
    ->increment('availablestock', $item->quantity);
```

`warehouse_id` and `branch_id` are **not** filtered. With stock recorded per warehouse and branch
(as the schema does), `increment()` applies to **every row** for that product. Cancelling an order
for one unit of milk at one warehouse inflates available stock at *all* warehouses simultaneously.

The identical defect appears in `resolveReturn()` (`decrement('availablestock', $qty)` filtered
only by `productid`).

### Double decrement in damage logging — `BROKEN`

`VERIFIED` — `logDamaged()` deducts the damaged quantity, then deducts it again:

```php
DB::table('productstocks')->where('id', $request->stock_id)->decrement('availablestock', $request->damaged_qty);
DB::table('productstocks')->where('id', $request->stock_id)->decrement('overallstock', $request->damaged_qty);
...
if (in_array($request->resolution, ['same', 'different', 'composite'])) {
    $replaceStockId = $request->resolution === 'same' ? $request->stock_id : null;
    if ($replaceStockId) {
        DB::table('productstocks')->where('id', $replaceStockId)->decrement('availablestock', $request->damaged_qty);
    }
}
```

When `resolution === 'same'`, `availablestock` is decremented **twice** by `damaged_qty` while
`overallstock` is decremented once. `resolution` is also read but **not validated** — it is absent
from `$request->validate(['stock_id', 'damaged_qty', 'reason'])`.

`VERIFIED` — no damage is recorded anywhere. `index()` explicitly sets
`'damaged_items' => 0, // extend if you have a damage_logs table`. Damage write-offs are invisible
to reporting and to stock reconciliation.

### Early returns inside an open transaction — `BROKEN`

`VERIFIED` — in all four methods, guard clauses `return response()->json(...)` occur **after**
`DB::beginTransaction()` and **without** `commit()` or `rollBack()`:

```php
DB::beginTransaction();
try {
    $order = DB::table('product_orders')->where('order_id', $request->order_id)->first();
    if (!$order) return response()->json(['success' => false, 'message' => 'Order not found']);
```

The transaction is left dangling. MySQL rolls it back when the connection closes, so the visible
effect is usually benign — but the connection is held with an open transaction for the remainder of
the request, and any code later on the same connection inherits it. The `catch` block also calls
`DB::rollBack()` unconditionally, which throws "There is no active transaction" if the failure
occurred before `beginTransaction()`.

### N+1 query in the dashboard load

`VERIFIED` — `index()` runs one query per returned return-order:

```php
->get()->map(function ($order) {
    $order->items = DB::table('product_order_items')->where('order_id', $order->order_id)->get();
    return $order;
});
```

Plus unbounded `->get()` on all `productstocks` and all `cancelledOrders`. The stats block then
re-counts already-materialised collections with `->count()` in PHP rather than in SQL. See
[24-performance.md](24-performance.md).

## Refund flows — `INCOMPLETE`

`VERIFIED` — routes exist for `MilkRefundController::refundMilkSlot()` and
`ProductRefundController::refundProductSlot()`. Neither controller was fully traced.

`UNVERIFIED` — whether a refund actually calls Razorpay, or only flips `refund_status`. No payment
gateway service class exists in `app/Services` (only `ShiprocketService`), so a local-only status
update is the likely behaviour. **Confirm before relying on refunds for financial reconciliation.**

`VERIFIED` — `cancelOrder()` inserts into `cancel_requests` with a comment:
`// Log refund (extend with actual Razorpay API call or wallet logic as needed)`. No refund is
initiated on cancellation.

## Packing / dispatch pipeline — `INCOMPLETE`

`VERIFIED` — five controllers form a status pipeline: `PackingOrderController`,
`packingCompleteController`, `PackingDispatchController`, `PackingDeliveryController`,
`ProductSlotController`. Statuses used across the codebase are string literals:
`pending`, `processing`, `shipped`, `out_for_delivery`, `delivered`, `cancelled`, `return`,
`return_resolved`, plus `packing`/`dispatched` variants.

`BROKEN` — there is no shared status constant, enum, or state machine. `ProductOrder::getDeliveryStatusAttribute()`
maps seven of them to integers in a model accessor; `HomeController::root()` hardcodes five more as
query filters; `cancelOrder()` guards on `['pending','processing']`. Adding a status requires
finding every literal.

`UNVERIFIED` — whether status transitions are validated (i.e. whether `delivered` can be set
directly from `pending`).

## Gift business — `INCOMPLETE`

`VERIFIED` — gift flows duplicate the retail ones across `GiftController` (557 lines),
`GiftVariantController`, `GiftPurchaseOrderController`, `GiftInventoryReportController`,
`GiftProductwiseController`, `GiftPurchaseReportController`, `GiftSupplierController`,
`GiftSizeController`, `GiftColorController`, and 16 views under `resources/views/pages/gifts/`.

Separation is by `is_gift` flag on `products`, `categories`, `sub_categories`, and
`supplier_details`. `VERIFIED` — whether gift stock decrement shares code with retail stock
decrement is unresolved; if it does not, the defects in
[07-data-integrity.md](07-data-integrity.md) apply twice.

## Source encoding corruption — `VERIFIED`

`VERIFIED` — several controller files contain mojibake in comments, e.g. box-drawing characters
rendered as `�"?�"?` in `StockController.php` and `ProductManagementController.php`. This is
evidence that source files passed through a lossy encoding conversion. It is cosmetic in comments
but means the files are **not valid UTF-8**, which can break tooling, diff review, and any future
`declare(strict_types=1)` or multibyte-aware tooling.

Files affected include `app/Http/Controllers/StockController.php` and
`app/Http/Controllers/ProductManagementController.php`.

## Order status summary

| Status | Meaning | Set by |
| --- | --- | --- |
| `pending` | Created, awaiting packing | `createProductSubscription()` |
| `processing` | Packing started | `PackingOrderController` |
| `shipped` | Handed to courier | `PackingDispatchController` / `upadetstatus()` |
| `out_for_delivery` | With delivery person | `PackingDeliveryController` |
| `delivered` | Complete | `PackingDeliveryController` |
| `cancelled` | Cancelled before dispatch | `cancelOrder()`, `upadetstatus()` |
| `return` | Customer return requested | `UNVERIFIED` |
| `return_resolved` | Return resolved | `resolveReturn()` |

`VERIFIED` — `ProductOrder::getDeliveryStatusAttribute()` maps `cancelled` and `return` both to
`6`, so the dashboard cannot distinguish a cancellation from a return in the numeric indicator.