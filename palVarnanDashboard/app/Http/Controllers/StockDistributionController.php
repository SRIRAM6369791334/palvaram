<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\ProductVarient;
use App\Models\WareHouse;
use App\Models\WareHouseBranch;
use App\Models\Country;
use App\Models\StockDistribution;

class StockDistributionController extends Controller
{
    public function index()
    {
        $history = StockDistribution::with(['product', 'variant', 'warehouse', 'branch'])
                    ->whereHas('product', fn($q) => $q->where('is_gift', 0))  
                    ->latest()->take(30)->get();

        return view('pages.stock_distribution', [
            'countries' => Country::all(),
            'products'  => Product::where('is_gift', 0)->get(),               
            'history'   => $history,
        ]);
    }

    public function giftIndex()
    {
        $history = StockDistribution::with(['product', 'variant', 'warehouse', 'branch'])
                    ->whereHas('product', fn($q) => $q->where('is_gift', 1)) 
                    ->latest()->take(30)->get();

        return view('pages.gifts.gift_stock_distribution', [
            'countries' => Country::all(),
            'products'  => Product::where('is_gift', 1)->get(),               
            'history'   => $history,
        ]);
    }


    public function getVariants(Request $request)
    {
        $variants = ProductVarient::where('product_id', $request->product_id)->get();
        return response()->json($variants);
    }

    public function getWarehouses(Request $request)
    {
        $warehouses = WareHouse::where('country_id', $request->country_id)->get();
        return response()->json($warehouses);
    }

    public function getBranches(Request $request)
    {
        $branches = WareHouseBranch::where('warehouse_id', $request->warehouse_id)->get();
        return response()->json($branches);
    }

    public function getStocks(Request $request)
    {
        $query = ProductStock::where('productid',    $request->product_id)
                            ->where('warehouse_id', $request->warehouse_id)
                            ->whereNull('branch_id'); // warehouse-level only

        if ($request->variant_id) {
            $query->where('pro_ver_id', $request->variant_id);
        } else {
            $query->whereNull('pro_ver_id');
        }

        $stock = $query->first();

        if (!$stock) {
            return response()->json(['available' => 0, 'stock_id' => null]);
        }

        return response()->json([
            'available' => $stock->availablestock,
            'stock_id'  => $stock->id,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'warehouse_id' => 'required',
            'branch_id'    => 'required',
            'product_id'   => 'required',
            'quantity'     => 'required|integer|min:1',
        ]);

        $stockQuery = ProductStock::where('productid',    $request->product_id)
                                ->where('warehouse_id', $request->warehouse_id)
                                ->whereNull('branch_id');

        if ($request->variant_id) {
            $stockQuery->where('pro_ver_id', $request->variant_id);
        } else {
            $stockQuery->whereNull('pro_ver_id');
        }

        $warehouseStock = $stockQuery->first();

        if (!$warehouseStock) {
            return response()->json(['message' => 'No warehouse stock record found.'], 422);
        }

        if ($request->quantity > $warehouseStock->availablestock) {
            return response()->json([
                'message' => "Insufficient stock. Only {$warehouseStock->availablestock} units available."
            ], 422);
        }

        // ── Just log as pending, NO stock mutation yet ──
        StockDistribution::create([
            'product_id'     => $request->product_id,
            'variant_id'     => $request->variant_id ?? null,
            'warehouse_id'   => $request->warehouse_id,
            'branch_id'      => $request->branch_id,
            'quantity'       => (int) $request->quantity,
            'distributed_by' => auth()->id() ?? null,
            'distributed_at' => now(),
            'status'         => 'pending',
        ]);

        return response()->json(['success' => true]);
    }

    public function getStoreStock(Request $request)
    {
        $query = StockDistribution::where('product_id',  $request->product_id)
            ->where('warehouse_id', $request->warehouse_id)
            ->where('branch_id',    $request->branch_id)
            ->where('status',       'accepted');   // ← only accepted

        if ($request->variant_id) {
            $query->where('variant_id', $request->variant_id);
        } else {
            $query->whereNull('variant_id');
        }

        return response()->json(['available' => $query->sum('quantity')]);
    }

    public function acceptDistribution($id)
    {
        try {
            $dist = StockDistribution::findOrFail($id);

            if ($dist->status !== 'pending') {
                return response()->json(['message' => 'Distribution already ' . $dist->status . '.'], 422);
            }

            $stockQuery = ProductStock::where('productid', $dist->product_id)
                                    ->where('warehouse_id', $dist->warehouse_id)
                                    ->whereNull('branch_id');

            if ($dist->variant_id) {
                $stockQuery->where('pro_ver_id', $dist->variant_id);
            } else {
                $stockQuery->whereNull('pro_ver_id');
            }

            $warehouseStock = $stockQuery->first();

            if (!$warehouseStock) {
                return response()->json(['message' => 'Warehouse stock record not found.'], 422);
            }

            if ($dist->quantity > $warehouseStock->availablestock) {
                return response()->json([
                    'message' => "Insufficient stock. Only {$warehouseStock->availablestock} units available."
                ], 422);
            }

            $warehouseStock->availablestock       -= $dist->quantity;
            $warehouseStock->overallstock         -= $dist->quantity;
            $warehouseStock->last_stockupdate_date = now()->toDateString();
            $warehouseStock->save();

            $dist->status      = 'accepted';
            $dist->received_at = now();   // ← NEW
            $dist->save();

            return response()->json(['success' => true, 'message' => 'Marked as received successfully.']);

        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function cancelDistribution($id)
    {
        try {
            $dist = StockDistribution::findOrFail($id);

            if ($dist->status !== 'pending') {
                return response()->json(['message' => 'Distribution already ' . $dist->status . '.'], 422);
            }

            $dist->status = 'cancelled';
            $dist->save();

            return response()->json(['success' => true, 'message' => 'Distribution cancelled.']);

        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

}