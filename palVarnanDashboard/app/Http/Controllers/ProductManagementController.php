<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProductManagementController extends Controller
{

    public function index()
    {
        $today = Carbon::today();

        // Orders with return status
        $returnOrders = DB::table('product_orders as po')
            ->where('po.status', 'return')
            ->leftJoin('countries as co', 'co.id', '=', 'po.country_id')
            ->select('po.*', 'co.name as country_name')
            ->orderByDesc('po.created_at')
            ->get()
            ->map(function ($order) {
                $order->items = DB::table('product_order_items')
                    ->where('order_id', $order->order_id)
                    ->get();
                return $order;
            });

        // All orders that are cancelled OR can be cancelled (pending/processing)
        $cancelledOrders = DB::table('product_orders')
            ->whereIn('status', ['cancelled', 'pending', 'processing'])
            ->orderByDesc('created_at')
            ->get();

        // All stock rows (for damage logging)
        $damagedStock = DB::table('productstocks as ps')
            ->leftJoin('countries as co',       'co.id', '=', 'ps.country_id')
            ->leftJoin('warehouse as w',         'w.id',  '=', 'ps.warehouse_id')
            ->leftJoin('warehouse_branch as wb', 'wb.id', '=', 'ps.branch_id')
            ->leftJoin('product_varient as pv',  'pv.id', '=', 'ps.pro_ver_id')
            ->select(
                'ps.*',
                'co.name as country_name',
                'w.warehouse_name',
                'wb.branch_name',
                'pv.varient_name'
            )
            ->get();

        // Expired batches
        $expiredBatches = DB::table('product_stock_batches as psb')
            ->join('productstocks as ps', 'ps.id', '=', 'psb.product_stock_id')
            ->where('psb.expiry_date', '<', $today)
            ->where('psb.remaining_qty', '>', 0)
            ->select('psb.*', 'ps.productname', 'ps.country_id', 'ps.warehouse_id', 'ps.branch_id')
            ->orderBy('psb.expiry_date')
            ->get();

        // Products list for replacement dropdowns
        $products = DB::table('productstocks as ps')
            ->leftJoin('product_varient as pv', 'pv.id', '=', 'ps.pro_ver_id')
            ->select('ps.id', 'ps.productname as product_name', 'pv.varient_name', 'pv.sku', 'ps.availablestock')
            ->where('ps.availablestock', '>', 0)
            ->get();

        // Stats
        $stats = [
            'pending_returns'  => $returnOrders->count(),
            'cancelled_orders' => $cancelledOrders->where('status', 'cancelled')->count(),
            'damaged_items'    => 0, // extend if you have a damage_logs table
            'expired_batches'  => $expiredBatches->count(),
        ];

        return view('pages.product_management', compact(
            'returnOrders', 'cancelledOrders', 'damagedStock',
            'expiredBatches', 'products', 'stats'
        ));
    }

    // ── Return Resolution ─────────────────────────────────────────────────────
    public function resolveReturn(Request $request)
    {
        $request->validate([
            'order_id'   => 'required|string',
            'resolution' => 'required|string|in:same,different,composite,wallet',
        ]);

        DB::beginTransaction();
        try {
            $order = DB::table('product_orders')->where('order_id', $request->order_id)->first();
            if (!$order) return response()->json(['success' => false, 'message' => 'Order not found']);

            // Mark order resolved
            DB::table('product_orders')
                ->where('order_id', $request->order_id)
                ->update(['status' => 'return_resolved', 'updated_at' => now()]);

            // Deduct stock for replacement
            if (in_array($request->resolution, ['same', 'different', 'composite'])) {
                $productId = $request->resolution === 'different'
                    ? $request->diff_product_id
                    : DB::table('product_order_items')->where('order_id', $request->order_id)->value('product_id');

                $qty = (int)($request->same_qty ?? $request->diff_qty ?? 1);

                if ($productId && $qty > 0) {
                    DB::table('productstocks')
                        ->where('productid', $productId)
                        ->decrement('availablestock', $qty);
                    DB::table('productstocks')
                        ->where('productid', $productId)
                        ->update(['last_stockupdate_date' => now()->toDateString(), 'updated_at' => now()]);
                }
            }

            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // ── Cancel Order ──────────────────────────────────────────────────────────
    public function cancelOrder(Request $request)
    {
        $request->validate([
            'order_id'      => 'required|string',
            'reason'        => 'required|string',
            'refund_method' => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            $order = DB::table('product_orders')->where('order_id', $request->order_id)->first();
            if (!$order) return response()->json(['success' => false, 'message' => 'Order not found']);

            if (!in_array($order->status, ['pending', 'processing'])) {
                return response()->json(['success' => false, 'message' => 'Order cannot be cancelled in its current status.']);
            }

            // Cancel the order
            DB::table('product_orders')
                ->where('order_id', $request->order_id)
                ->update([
                    'status'     => 'cancelled',
                    'reason'     => $request->reason,
                    'updated_at' => now(),
                ]);

            // Re-stock cancelled order items
            $items = DB::table('product_order_items')->where('order_id', $request->order_id)->get();
            foreach ($items as $item) {
                DB::table('productstocks')
                    ->where('productid', $item->product_id)
                    ->increment('availablestock', $item->quantity);
                DB::table('productstocks')
                    ->where('productid', $item->product_id)
                    ->update(['last_stockupdate_date' => now()->toDateString(), 'updated_at' => now()]);
            }

            // Log refund (extend with actual Razorpay API call or wallet logic as needed)
            DB::table('cancel_requests')->insert([
                'order_id'   => $request->order_id,
                'reason'     => $request->reason,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // ── Log Damaged Stock ─────────────────────────────────────────────────────
    public function logDamaged(Request $request)
    {
        $request->validate([
            'stock_id'    => 'required|integer',
            'damaged_qty' => 'required|integer|min:1',
            'reason'      => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            $stock = DB::table('productstocks')->find($request->stock_id);
            if (!$stock) return response()->json(['success' => false, 'message' => 'Stock record not found']);

            if ($request->damaged_qty > $stock->availablestock) {
                return response()->json(['success' => false, 'message' => 'Damaged qty exceeds available stock']);
            }

            // Deduct from available stock
            DB::table('productstocks')
                ->where('id', $request->stock_id)
                ->decrement('availablestock', $request->damaged_qty);
            DB::table('productstocks')
                ->where('id', $request->stock_id)
                ->decrement('overallstock', $request->damaged_qty);
            DB::table('productstocks')
                ->where('id', $request->stock_id)
                ->update(['last_stockupdate_date' => now()->toDateString(), 'updated_at' => now()]);

            // If replacement: deduct replacement stock too
            if (in_array($request->resolution, ['same', 'different', 'composite'])) {
                $replaceStockId = $request->resolution === 'same'
                    ? $request->stock_id
                    : null; // extend: resolve product_id from diff product
                if ($replaceStockId) {
                    DB::table('productstocks')
                        ->where('id', $replaceStockId)
                        ->decrement('availablestock', $request->damaged_qty);
                }
            }

            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // ── Write Off Expired Batch ───────────────────────────────────────────────
    public function writeOffExpired(Request $request)
    {
        $request->validate([
            'batch_id'    => 'required|integer',
            'expired_qty' => 'required|integer|min:1',
            'disposal'    => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            $batch = DB::table('product_stock_batches')->find($request->batch_id);
            if (!$batch) return response()->json(['success' => false, 'message' => 'Batch not found']);

            if ($request->expired_qty > $batch->remaining_qty) {
                return response()->json(['success' => false, 'message' => 'Write-off qty exceeds remaining qty in batch']);
            }

            // Deduct from batch
            DB::table('product_stock_batches')
                ->where('id', $request->batch_id)
                ->decrement('remaining_qty', $request->expired_qty);

            // Deduct from productstocks
            DB::table('productstocks')
                ->where('id', $batch->product_stock_id)
                ->decrement('availablestock', $request->expired_qty);
            DB::table('productstocks')
                ->where('id', $batch->product_stock_id)
                ->decrement('overallstock', $request->expired_qty);
            DB::table('productstocks')
                ->where('id', $batch->product_stock_id)
                ->update(['last_stockupdate_date' => now()->toDateString(), 'updated_at' => now()]);

            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}