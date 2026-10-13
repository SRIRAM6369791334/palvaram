# 20 — Scheduling and Background Work

## Directory inventory — `VERIFIED`

| Directory | Exists | Files |
| --- | --- | --- |
| `app/Jobs/` | **No** | — |
| `app/Events/` | **No** | — |
| `app/Listeners/` | **No** | — |
| `app/Notifications/` | **No** | — |
| `app/Policies/` | **No** | — |
| `app/Mail/` | Yes | 1 (`OrderStatusUpdated.php`) |
| `app/Console/Commands/` | Yes | 1 (`SyncShiprocketStatus.php`, 134 lines) |

`BROKEN` — the application has **no queue, no job, no event, no listener, and no notification
classes.** `composer.json` does not require `laravel/queue` work because the framework provides it,
but nothing uses it. `QUEUE_CONNECTION=sync` confirms the intent: every piece of deferred work runs
inline inside the HTTP request that triggered it.

## The schedule — `VERIFIED`

`app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule)
{
    $schedule->command('shiprocket:sync')->everyThirtyMinutes();
}
```

That is the entire schedule. One command, no constraints, no overlap protection.

### Missing scheduling guards — `BROKEN`

| Guard | Missing | Consequence |
| --- | --- | --- |
| `->withoutOverlapping()` | Yes | A slow run (many orders + inline SMTP) overlaps the next invocation, producing duplicate status emails and racing writes. |
| `->onOneServer()` | Yes | Irrelevant today (single host, file cache), but required the moment a second node exists. |
| `->runInBackground()` | Yes | `schedule:run` blocks on the command. |
| `->evenly()` / `->between()` | Yes | Runs 24×/day including peak and overnight hours. |
| `->name()` | Yes | Individual schedules cannot be selectively disabled. |
| `->emailOutputOnFailure()` | Yes | **Silent failure.** Nobody is told when shipment sync breaks. |

`UNVERIFIED` — whether a cron entry invoking `php artisan schedule:run` exists on the deployment
host. There is no deployment script, no cron documentation, and no `.github/` directory in this
project. **If cron is not configured, shipment status never syncs at all** and the entire
integration is non-functional. This is the single highest-priority operational unknown in the
project.

## `shiprocket:sync` — `VERIFIED`

`app/Console/Commands/SyncShiprocketStatus.php:43-120`.

### Selection query — `BROKEN`

```php
$orders = ProductOrder::with('customer', 'orderAddress')
    ->whereIn('status', ['processing', 'ready_for_pickup', 'shipped', 'out_for_delivery'])
    ->whereNotNull('shiprocket_order_id')
    ->get();
```

| Issue | Detail |
| --- | --- |
| **No `chunk()` / `chunkById()`** | The whole result set is materialised. With thousands of in-flight orders this is a full-table read plus model hydration every 30 minutes. |
| `orderAddress` eager-loaded but never used | Wasted join on every row. |
| `awb_code` filtered in the loop, not the query | `if (!$order->awb_code) continue;` at line 57 — rows without an AWB are fetched and discarded. Should be `whereNotNull('awb_code')`. |
| **Terminal states excluded** | `'delivered'` and `'return'` are absent from `whereIn`. Once an order reaches `delivered`, it is **never tracked again** — so a genuine return-after-delivery (RTO raised days later) is silently missed. Shiprocket codes 22/24/25 can therefore never apply to an already-delivered order. |
| No ordering | Status updates are applied in arbitrary DB order, which matters because status transitions are not monotonic. |

### Status mapping — `VERIFIED`

| Shiprocket `current_status_id` | Meaning | Local `status` |
| --- | --- | --- |
| 17 | Picked Up | `shipped` |
| 18 | Shipped | `shipped` |
| 19 | In Transit | `shipped` |
| 20 | Out for Delivery | `out_for_delivery` |
| 21 | Delivered | `delivered` |
| 22 | Return Initiated | `return` |
| 24 | RTO Initiated | `return` |
| 25 | RTO Delivered | `return` |

`BROKEN`

- **No default branch.** Any Shiprocket status code outside 17-25 (e.g. `26` Canceled, `29` Lost,
  `10` Manifested, `1` Pending) leaves `$newLocalStatus` null and the order is silently skipped.
  **Cancelled and lost shipments are never recorded.**
- **No transition validation.** `$order->update(['status' => $newLocalStatus])` runs for any
  mapping. There is no guard preventing a backward move (`shipped` → `processing` cannot occur via
  this map, but `delivered` → `return` is reachable in principle and nothing prevents future map
  edits from creating regressions).
- **Local status vocabulary conflicts with the rest of the app.** The dashboard elsewhere uses
  `Dispatched`/`Cancelled` (see [12-business-logic.md](12-business-logic.md)); this command writes
  `shipped`, `out_for_delivery`, `delivered`, `return`. The same `status` column holds at least
  three different vocabularies depending on the writer.

### Side effect: `product_slots` denormalisation — `BROKEN`

```php
\DB::table('product_slots')->where('order_id', $order->order_id_display)->update([
    'delivery_status' => $this->getStatusNum($newLocalStatus)
]);
```

| Issue | Detail |
| --- | --- |
| `\DB::` | Facade accessed with a leading backslash. Works only because Laravel registers a global `DB` class alias; inconsistent with the `DB::` used elsewhere. |
| Raw query builder | Bypasses the model entirely — no events, no casts, no observers. |
| **No transaction** | `$order->update()` at line 93 commits, then this `update()` runs separately. A failure here leaves `product_orders` and `product_slots` permanently inconsistent. |
| `order_id` → `order_id_display` | Relies on the legacy integer column. If it is null or stale, **zero rows are updated and no error is raised.** |
| `getStatusNum()` returns `0` for unknown | An unmapped status silently writes `delivery_status = 0` into every slot row for that order. |
| `return 0` at the end | See below. |

`getStatusNum()` map: `processing`=1, `shipped`=2, `out_for_delivery`=3, `delivered`=4,
`ready_for_pickup`=5, `return`=6, else `0`. `BROKEN` — this is a **second, independent status
encoding** alongside the string `status` column. Two representations of the same concept, mapped by
two separate hand-written switch/map blocks that can drift apart.

### Email notification — `INCOMPLETE`

```php
if ($order->customer && !empty($order->customer->email) && class_exists('App\Mail\OrderStatusUpdated')) {
    try {
        Mail::to($order->customer->email)->send(new OrderStatusUpdated($order, $newLocalStatus));
```

`VERIFIED` — `App\Mail\OrderStatusUpdated` **does exist** (`app/Mail/OrderStatusUpdated.php`,
subject `Order Status Update`, view `emails.order_status_updated`), so the `class_exists` guard is
always true and the dead-code check is misleading.

`BROKEN`

- `use Queueable` is applied to the mailable, but `QUEUE_CONNECTION=sync` means `send()` dispatches
  synchronously. Each changed order blocks on a full SMTP round-trip. With `withoutOverlapping()`
  missing, a slow mail server causes overlapping runs and **duplicate customer emails**.
- The customer's email address is printed to stdout (`"Email sent to {$order->customer->email}"`).
  With `LOG_LEVEL=debug` and no output redirection, customer PII lands in the log.
- The email contains no order identifier in the subject (`'Order Status Update'` for every
  customer), so it is indistinguishable from spam and will be filtered.
- No throttling: a single order changing status multiple times in one run sends multiple emails.
- The mailable's `$order` is a full model, serialised into the queued payload if the queue driver
  is ever changed — an unnecessary relation-eagerload cost.

### Error handling — `BROKEN`

```php
} catch (\Exception $e) {
    $this->error("Failed to sync Order #{$order->order_id_display}: " . $e->getMessage());
}
...
return 0;
```

| Issue | Detail |
| --- | --- |
| **Always returns 0** | The command reports success even if every order failed. Cron monitoring, `schedule:run` failure detection, and any external alerting see a healthy exit. |
| `$this->error()` writes to stdout only | With no output redirection configured, the message is lost. `Log::` is imported but never used in this command. |
| Catches `\Exception` only | A `TypeError` or `Error` from a malformed Shiprocket response (e.g. `$trackData['current_status_id']` on a string) escapes and aborts the entire run, leaving remaining orders unsynced. |
| No retry/backoff | A transient Shiprocket 429 or 500 permanently skips that order until the next 30-minute tick — which is acceptable — but nothing records why. |

`VERIFIED` — `use Illuminate\Support\Facades\Log;` is imported at line 9 and **never called**. The
import is dead.

## Good pattern present but unused

`VERIFIED` — `ShiprocketService` caches its auth token for 8 hours and
`SyncShiprocketStatus` is the **only** consumer of that service:

```php
$shiprocket = new ShiprocketService();
$tracking  = $shiprocket->trackAWB($order->awb_code);
```

`BROKEN` — it is constructed with `new` rather than injected via the container, so the service
cannot be mocked in a test. The `guard isset($tracking['tracking_data']['shipment_track'][0])` check
at line 61 is a reasonable defensive guard and the only place any response validation happens.

## What runs inline instead of in a queue — `BROKEN`

`VERIFIED` — `QUEUE_CONNECTION=sync`. These operations execute inside the HTTP request:

| Operation | Location | Blocking risk |
| --- | --- | --- |
| Shiprocket login + 2 API calls | `ProductController::upadetstatus()` | `CURLOPT_TIMEOUT => 0` — can hang indefinitely |
| Shiprocket login + create order | `ProductOrdersController` | Same |
| Shiprocket login + cancel/order-show/return | `ProductSlotController` (4 sites) | Same |
| Pickup generation | `ProductController:1024` | Same |
| Excel import of a ZIP extract | `ProductController::bulkUpload()` | Minutes for a large catalogue |
| Excel/PDF export | 6 report controllers | Minutes for a large export |
| Dompdf render | Invoice views | CPU-bound, blocks the worker |
| Order status email | `SyncShiprocketStatus` | SMTP round-trip per order |

`BROKEN` — with `CURLOPT_TIMEOUT => 0` on the Shiprocket cURL calls, a single hung upstream
connection consumes a PHP worker **forever**. Under Apache with `mod_php` and a fixed worker pool,
enough concurrent hung requests exhaust the pool and take the entire application offline.

## `routes/console.php` — `VERIFIED`

Contains only the scaffold `inspire` command. Nothing application-specific.

## Recommendations

1. **Confirm a cron entry exists for `php artisan schedule:run`.** Nothing else in this section
   matters if it does not.
2. Add `->withoutOverlapping()` and `->onOneServer()`; add `->emailOutputOnFailure()` or log to a
   dedicated channel.
3. Replace `->get()` with `->chunkById(200)`; move `whereNotNull('awb_code')` into the query; drop
   the unused `orderAddress` eager load.
4. Wrap the `product_orders` + `product_slots` update in a single transaction.
5. Make `getStatusNum()` and the status string share one source of truth (an enum or a status
   config file). Remove the silent `0` fallback.
6. Add a `default:` branch to the switch that handles cancellation, lost, and manifest statuses.
7. Return a non-zero exit code when any order fails; log failures via `Log::`, not `->error()`.
8. Catch `\Throwable` rather than `\Exception`.
9. Set `QUEUE_CONNECTION` to `redis`/`database` and push emails, exports, imports, and all
   Shiprocket calls onto queues.
10. Add `timeout(15)` and retries to every HTTP client call.
11. Inject `ShiprocketService` through the constructor so it can be faked in tests.
12. Remove the dead `use Log;` import or use it.