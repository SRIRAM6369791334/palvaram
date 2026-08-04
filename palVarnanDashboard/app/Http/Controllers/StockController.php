<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Country;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\ProductVarient;
use App\Models\StockTransfer;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{
    public function index()
    {
        $query = ProductStock::select(
            'productstocks.*',
            'categories.category_name',
            'product_varient.varient',
            'product_varient.value',
            'product_varient.varient_name',
            'units.short_name as unit_short_name',
            'countries.name as country_name',
            'warehouse.warehouse_name',
            'warehouse_branch.branch_name'
        )
        ->join('categories',       'productstocks.category_id', '=', 'categories.id')
        ->join('product_varient',  'productstocks.pro_ver_id',  '=', 'product_varient.id')
        ->join('products',         'productstocks.productid',   '=', 'products.id')       // ADD
        ->leftJoin('units',            'product_varient.unit_id',    '=', 'units.id')
        ->leftJoin('countries',        'productstocks.country_id',   '=', 'countries.id')
        ->leftJoin('warehouse',        'productstocks.warehouse_id', '=', 'warehouse.id')
        ->leftJoin('warehouse_branch', 'productstocks.branch_id',    '=', 'warehouse_branch.id')
        ->where('products.is_gift', 0);                                                    // ADD


        $countryId = session('country_id');

        if (!empty($countryId)) {
            $query->where('productstocks.country_id', $countryId);
        }

        $stocks = $query->get();

        $warehouses = \App\Models\WareHouse::all();
        $warehouseBranches = \App\Models\WareHouseBranch::all();
        $countries = Country::all();
        $categories = Category::all();
        $subcategories = SubCategory::all();
        $products = Product::all();
        $variants = ProductVarient::all();


        $storeStocks = DB::table('stock_distributions as sd')
            ->join('productstocks as ps', function($join) {
                $join->on('ps.productid',    '=', 'sd.product_id')
                    ->on('ps.warehouse_id', '=', 'sd.warehouse_id');
            })
            ->join('products',              'sd.product_id',    '=', 'products.id')        // ADD
            ->join('categories',            'ps.category_id',   '=', 'categories.id')
            ->join('product_varient as pv', 'sd.variant_id',    '=', 'pv.id')
            ->leftJoin('units',             'pv.unit_id',        '=', 'units.id')
            ->leftJoin('countries',         'ps.country_id',     '=', 'countries.id')
            ->leftJoin('warehouse',         'sd.warehouse_id',   '=', 'warehouse.id')
            ->leftJoin('warehouse_branch',  'sd.branch_id',      '=', 'warehouse_branch.id')
            ->select(
                'sd.id',
                'sd.product_id',
                'sd.variant_id',
                'sd.warehouse_id',
                'sd.branch_id',
                'sd.quantity as availablestock',
                'sd.distributed_at',
                'ps.productname',
                'ps.salestock',
                'categories.category_name',
                'pv.varient_name',
                'pv.value',
                'units.short_name as unit_short_name',
                'countries.name as country_name',
                'warehouse.warehouse_name',
                'warehouse_branch.branch_name'
            )
            ->where('products.is_gift', 0)                                                  // ADD
            ->when(!empty($countryId), fn($q) => $q->where('ps.country_id', $countryId))
            ->where('sd.quantity', '>', 0)
            ->get();

        return view('pages.stocks', compact(
            'stocks', 'storeStocks',
            'warehouses', 'warehouseBranches',
            'categories', 'countries',
            'products', 'subcategories', 'variants'
        ));
    }

    public function store(Request $request)
    {
        $exists = ProductStock::where('productid', $request->product_id)
            ->where('warehouse_id', $request->warehouse_id)
            ->where('branch_id', $request->branch_id)
            ->first();

        if ($exists) {
            return response()->json([
                'status' => 'exists',
                'message' => 'Stock already exists',
                'stock_id' => $exists->id
            ]);
        }

        $product = Product::find($request->product_id);
        if (!$product) {
            return response()->json(['error' => 'Product not found.'], 404);
        }

        // Use the variant sent from the form directly
        $proVerId = $request->pro_ver_id;
        if (!$proVerId) {
            return response()->json(['error' => 'Please select a variant.'], 422);
        }

        $variant = ProductVarient::find($proVerId);
        if (!$variant) {
            return response()->json(['error' => 'Variant not found.'], 404);
        }

        ProductStock::create([
            'productid'             => $request->product_id,
            'pro_ver_id'            => $proVerId,
            'category_id'           => $request->category_id,
            'subcategory_id'        => $request->subcategory_id,
            'country_id'            => $request->country_id,
            'warehouse_id'          => $request->warehouse_id,
            'branch_id'             => $request->branch_id,
            'productname'           => $product->product_name,
            'overallstock'          => $request->stock_quantity,
            'availablestock'        => $request->stock_quantity,
            'salestock'             => 0,
            'last_stockupdate_date' => now(),
        ]);

        return response()->json(['status' => 'created']);
    }

    public function update(Request $request)
    {
        $stockqty = (int) $request->stock_quantity;
        $productStockId = $request->productid;

        $productStock = ProductStock::where('id', $productStockId)->first();

        if (!$productStock) {
            return response()->json(['error' => 'Product stock record not found.'], 404);
        }

        // Get the variant ID from stock table
        $productVariant = ProductVarient::where('id', $productStock->pro_ver_id)->first();

        if (!$productVariant) {
            return response()->json(['error' => 'Product variant not found.'], 404);
        }

        // Update stock records
        $productStock->overallstock += $stockqty;
        $productStock->availablestock += $stockqty;
        $productStock->save();

        // Update variant quantity
        $productVariant->product_qty = $productStock->availablestock;
        $productVariant->save();

        $stocks = ProductStock::select(
            'productstocks.*',
            'categories.category_name',
            'product_varient.varient',
            'product_varient.value',
            'product_varient.varient_name',
            'units.short_name as unit_short_name'
        )
            ->join('categories', 'productstocks.category_id', '=', 'categories.id')
            ->join('product_varient', 'productstocks.pro_ver_id', '=', 'product_varient.id')
            ->leftJoin('units', 'product_varient.unit_id', '=', 'units.id')
            ->get();
        return response()->json(["stocks" => $stocks]);
    }

   public function reduceStock(Request $request)
{
    $stockQty = (int) $request->stock_quantity;
    $productStockId = $request->productid;

    $productStock = ProductStock::where('id', $productStockId)->first();

    if (!$productStock) {
        return response()->json(['error' => 'Product stock not found.'], 404);
    }

    // Get the variant ID from stock table (assumes relation)
    $productVariant = ProductVarient::where('id', $productStock->pro_ver_id)->first();

    if (!$productVariant) {
        return response()->json(['error' => 'Product variant not found.'], 404);
    }

    // Reduce stocks (ensure not going below 0)
    $productStock->overallstock = max($productStock->overallstock - $stockQty, 0);
    $productStock->availablestock = max($productStock->availablestock - $stockQty, 0);
    $productStock->save();

    $productVariant->product_qty = max($productVariant->product_qty - $stockQty, 0);
    $productVariant->save();

    // Return updated list
    $stocks = ProductStock::select(
        'productstocks.*',
        'categories.category_name',
        'product_varient.varient',
        'product_varient.value',
        'product_varient.varient_name',
        'units.short_name as unit_short_name'
    )
        ->join('categories', 'productstocks.category_id', '=', 'categories.id')
        ->join('product_varient', 'productstocks.pro_ver_id', '=', 'product_varient.id')
        ->leftJoin('units', 'product_varient.unit_id', '=', 'units.id')
        ->get();

    return response()->json(["stocks" => $stocks]);
}




    // public function reduceStock(Request $request)
    // {
    //     $stockqty = $request->stock_quantity;
    //     $productid = $request->productid;



    //     $getstockdetails = ProductStock::where('id',$productid)->first();
    //     $productdetails = ProductVarient::where('id',$productid)->first();

    //     $updatestock = ProductStock::
    //     where('id',$productid)
    //     ->update([
    //         "overallstock"=>$getstockdetails->overallstock - $stockqty,
    //         "availablestock"=>$getstockdetails->availablestock - $stockqty,
    //     ]);

    //     $updatestockproduct = ProductVarient::
    //     where('id',$productid)
    //     ->update([
    //         "product_qty"=>$getstockdetails->availablestock - $stockqty,
    //     ]);

    //     $stocks = ProductStock::select('productstocks.*', 'categories.category_name')
    //     ->join('categories', 'productstocks.category_id', '=', 'categories.id')
    //     ->get();
    //    return response()->json(["stocks" =>  $stocks]);



    // }

    public function lowstock(){
        $lowstocks = ProductStock::select('productstocks.*', 'categories.category_name', 'product_varient.varient', 'product_varient.value', 'product_varient.low_stock')
        ->join('categories', 'productstocks.category_id', '=', 'categories.id')
        ->join('product_varient', 'productstocks.pro_ver_id', '=', 'product_varient.id')
        ->where('productstocks.availablestock', '<', DB::raw('product_varient.low_stock'))
        ->get();

    // Update availablestock to be equal to low_stock
    // foreach ($lowstocks as $lowstock) {
    //     $lowstock->update(['availablestock' => $lowstock->low_stock]);
    // }





    return view("pages.lowstock",compact("lowstocks"));

    }


    // public function update1(Request $request)
    // {
    //     $stockqty = $request->stock_quantity;
    //     $productid = $request->productid;




    //     $getstockdetails = ProductStock::where('id',$productid)->first();
    //     $productdetails = ProductVarient::where('id',$productid)->first();

    //     $updatestock = ProductStock::
    //     where('id',$productid)
    //     ->update([
    //         "overallstock"=>$getstockdetails->overallstock + $stockqty,
    //         "availablestock"=>$getstockdetails->availablestock + $stockqty,
    //     ]);

    //     $updatestockproduct = ProductVarient::
    //     where('id',$productid)
    //     ->update([
    //         "product_qty"=>$getstockdetails->availablestock + $stockqty,
    //     ]);

    //     $lowstocks = ProductStock::select('productstocks.*', 'categories.category_name')
    // ->join('categories', 'productstocks.category_id', '=', 'categories.id')
    // ->where('availablestock', '<', 6)
    // ->get();

    //    return response()->json(["lowstocks" =>  $lowstocks]);



    // }

    public function update1(Request $request)
{
    $stockQty = (int) $request->stock_quantity;
    $productStockId = $request->productid;

    $productStock = ProductStock::where('id', $productStockId)->first();

    if (!$productStock) {
        return response()->json(['error' => 'Product stock not found.'], 404);
    }

    $variantId = $productStock->pro_ver_id;

    $productVariant = ProductVarient::where('id', $variantId)->first();

    if (!$productVariant) {
        return response()->json(['error' => 'Product variant not found.'], 404);
    }

    // Update product stock values
    $productStock->overallstock += $stockQty;
    $productStock->availablestock += $stockQty;
    $productStock->save();

    // Update product variant quantity
    $productVariant->product_qty += $stockQty;
    $productVariant->save();

    // Return updated low stock list
    $lowstocks = ProductStock::select(
        'productstocks.*',
        'categories.category_name',
        'product_varient.varient',
        'product_varient.value',
        'product_varient.varient_name',
        'units.short_name as unit_short_name'
    )
        ->join('categories', 'productstocks.category_id', '=', 'categories.id')
        ->join('product_varient', 'productstocks.pro_ver_id', '=', 'product_varient.id')
        ->leftJoin('units', 'product_varient.unit_id', '=', 'units.id')
        ->where('availablestock', '<', 6)
        ->get();

    return response()->json(["lowstocks" => $lowstocks]);
}


    public function highselling(){
        $highsellings = ProductStock::select('productstocks.*', 'categories.category_name','product_varient.varient','product_varient.value',"products.product_image")
        ->join('categories', 'productstocks.category_id', '=', 'categories.id')
        ->join('product_varient','productstocks.pro_ver_id', '=', 'product_varient.id')
        ->join('products','productstocks.productid', '=', 'products.id')
        ->where('salestock', '<>', '') // Assuming you want to exclude empty salestock values
        ->orderBy('salestock', 'desc') // Order by salestock in descending order
        ->get();



        return view("pages.highselling",compact("highsellings"));
    }

public function transferIndex()
{
    $transfers = \DB::table('stock_transfers as t')
        ->join('productstocks as ps', 't.product_stock_id', '=', 'ps.id')
        ->join('products', 'ps.productid', '=', 'products.id')            // ADD
        ->join('warehouse as w1', 't.from_warehouse_id', '=', 'w1.id')
        ->leftJoin('warehouse_branch as b1', 't.from_branch_id', '=', 'b1.id')
        ->join('warehouse as w2', 't.to_warehouse_id', '=', 'w2.id')
        ->leftJoin('warehouse_branch as b2', 't.to_branch_id', '=', 'b2.id')
        ->select(
            't.*',
            'ps.productname',
            'w1.warehouse_name as from_warehouse',
            'b1.branch_name as from_branch',
            'w2.warehouse_name as to_warehouse',
            'b2.branch_name as to_branch'
        )
        ->where('products.is_gift', 0)                                     // ADD
        ->latest('t.created_at')
        ->get();

    $countries  = Country::all();
    $warehouses = \App\Models\WareHouse::all();
    $branches   = \App\Models\WareHouseBranch::all();

    $distributions = \App\Models\StockDistribution::select(
            'product_id', 'variant_id', 'warehouse_id', 'branch_id',
            \DB::raw('SUM(quantity) as total_distributed')
        )
        ->whereHas('product', fn($q) => $q->where('is_gift', 0))          // ADD
        ->groupBy('product_id', 'variant_id', 'warehouse_id', 'branch_id')
        ->get();

    $stocks = [];

    foreach ($distributions as $dist) {
        $available = max(0, $dist->total_distributed);
        if ($available <= 0) continue;

        $productStock = ProductStock::where('productid', $dist->product_id)
            ->where('warehouse_id', $dist->warehouse_id)
            ->first();

        $stocks[] = [
            'id'             => $dist->product_id . '_' . $dist->variant_id . '_' . $dist->warehouse_id . '_' . $dist->branch_id,
            'product_id'     => $dist->product_id,
            'variant_id'     => $dist->variant_id,
            'warehouse_id'   => $dist->warehouse_id,
            'branch_id'      => $dist->branch_id,
            'productname'    => $productStock->productname ?? 'Unknown',
            'availablestock' => $available,
        ];
    }

    $warehouseStocks = ProductStock::whereNull('branch_id')
        ->where('availablestock', '>', 0)
        ->whereHas('product', fn($q) => $q->where('is_gift', 0))          // ADD
        ->get()
        ->map(fn($s) => [
            'product_id'     => $s->productid,
            'variant_id'     => $s->pro_ver_id,
            'warehouse_id'   => $s->warehouse_id,
            'productname'    => $s->productname,
            'availablestock' => $s->availablestock,
        ]);

    return view('pages.transfer_stock', compact(
        'transfers', 'warehouses', 'branches', 'countries', 'stocks', 'warehouseStocks'
    ));
}


public function transferStock(Request $request)
{
    try {
        $request->validate([
            'from_warehouse_id' => 'required',
            'from_branch_id'    => 'required',
            'product_id'        => 'required',
            'variant_id'        => 'nullable',
            'to_branch_id'      => 'required',
            'transfer_quantity' => 'required|integer|min:1',
        ]);

        $transferQty  = (int) $request->transfer_quantity;
        $warehouseId  = $request->from_warehouse_id;
        $fromBranchId = $request->from_branch_id;
        $toBranchId   = $request->to_branch_id;
        $productId    = $request->product_id;
        $variantId    = $request->variant_id ?: null;

        if ($fromBranchId == $toBranchId) {
            return response()->json(['message' => 'Cannot transfer to the same store.'], 422);
        }

        $productStock = ProductStock::where('productid', $productId)
            ->where('warehouse_id', $warehouseId)
            ->whereNull('branch_id')
            ->where(function ($q) use ($variantId) {
                $variantId ? $q->where('pro_ver_id', $variantId) : $q->whereNull('pro_ver_id');
            })->first();

        if (!$productStock) {
            return response()->json(['message' => 'Product stock record not found.'], 422);
        }

        // ── Check available sender store stock ──
        $available = \App\Models\StockDistribution::where('product_id',  $productId)
            ->where('warehouse_id', $warehouseId)
            ->where('branch_id',    $fromBranchId)
            ->where(function ($q) use ($variantId) {
                $variantId ? $q->where('variant_id', $variantId) : $q->whereNull('variant_id');
            })->sum('quantity');

        if ($transferQty > $available) {
            return response()->json([
                'message' => "Insufficient stock. Only {$available} units available."
            ], 422);
        }

        // ── Just log, NO stock mutation yet ──
        $transfer = \App\Models\StockTransfer::create([
            'product_stock_id'  => $productStock->id,
            'from_warehouse_id' => $warehouseId,
            'from_branch_id'    => $fromBranchId,
            'to_warehouse_id'   => $warehouseId,
            'to_branch_id'      => $toBranchId,
            'quantity'          => $transferQty,
            'status'            => 'pending',
        ]);

        $transfer->transfer_code = 'TR-' . str_pad($transfer->id, 5, '0', STR_PAD_LEFT);
        $transfer->save();

        return response()->json(['success' => true]);

    } catch (\Exception $e) {
        return response()->json(['message' => $e->getMessage()], 500);
    }
}

public function warehouseTransfer(Request $request)
{
    try {
        $request->validate([
            'from_warehouse_id' => 'required|integer',
            'to_warehouse_id'   => 'required|integer|different:from_warehouse_id',
            'product_id'        => 'required|integer',
            'variant_id'        => 'nullable|integer',
            'transfer_quantity' => 'required|integer|min:1',
        ]);

        $qty           = (int) $request->transfer_quantity;
        $fromWarehouse = $request->from_warehouse_id;
        $toWarehouse   = $request->to_warehouse_id;
        $productId     = $request->product_id;
        $variantId     = $request->variant_id ?: null;

        $senderStock = ProductStock::where('productid', $productId)
            ->where('warehouse_id', $fromWarehouse)
            ->whereNull('branch_id')
            ->where(function ($q) use ($variantId) {
                $variantId ? $q->where('pro_ver_id', $variantId) : $q->whereNull('pro_ver_id');
            })->first();

        if (!$senderStock) {
            return response()->json(['message' => 'No warehouse stock record found.'], 422);
        }

        if ($qty > $senderStock->availablestock) {
            return response()->json([
                'message' => "Insufficient stock. Only {$senderStock->availablestock} units available."
            ], 422);
        }

        // ── Just log, NO stock mutation yet ──
        $transfer = \App\Models\StockTransfer::create([
            'product_stock_id'  => $senderStock->id,
            'from_warehouse_id' => $fromWarehouse,
            'from_branch_id'    => null,
            'to_warehouse_id'   => $toWarehouse,
            'to_branch_id'      => null,
            'quantity'          => $qty,
            'status'            => 'pending',
        ]);

        $transfer->transfer_code = 'TR-' . str_pad($transfer->id, 5, '0', STR_PAD_LEFT);
        $transfer->save();

        return response()->json(['success' => true]);

    } catch (\Exception $e) {
        return response()->json(['message' => $e->getMessage()], 500);
    }
}


public function acceptTransfer($id)
{
    try {
        $transfer = \App\Models\StockTransfer::findOrFail($id);

        if ($transfer->status !== 'pending') {
            return response()->json(['message' => 'Transfer already ' . $transfer->status . '.'], 422);
        }

        $qty       = (int) $transfer->quantity;
        $productStock = ProductStock::find($transfer->product_stock_id);
        if (!$productStock) {
            return response()->json(['message' => 'Product stock record not found.'], 500);
        }
        $productId = $productStock->productid;
        $variantId = $productStock->pro_ver_id ?? null;

        DB::beginTransaction();

        if ($transfer->from_branch_id === null) {
            // ── Warehouse → Warehouse ──

            $senderStock = ProductStock::where('productid',    $productId)
                ->where('warehouse_id', $transfer->from_warehouse_id)
                ->whereNull('branch_id')
                ->where(function ($q) use ($variantId) {
                    $variantId ? $q->where('pro_ver_id', $variantId) : $q->whereNull('pro_ver_id');
                })->first();

            if (!$senderStock || $senderStock->availablestock < $qty) {
                DB::rollBack();
                return response()->json(['message' => 'Insufficient warehouse stock.'], 422);
            }

            // Deduct sender
            $senderStock->availablestock       -= $qty;
            $senderStock->overallstock         -= $qty;
            $senderStock->last_stockupdate_date = now()->toDateString();
            $senderStock->save();

            // Add to receiver
            $receiverStock = ProductStock::where('productid',    $productId)
                ->where('warehouse_id', $transfer->to_warehouse_id)
                ->whereNull('branch_id')
                ->where(function ($q) use ($variantId) {
                    $variantId ? $q->where('pro_ver_id', $variantId) : $q->whereNull('pro_ver_id');
                })->first();

            if ($receiverStock) {
                $receiverStock->availablestock       += $qty;
                $receiverStock->overallstock         += $qty;
                $receiverStock->last_stockupdate_date = now()->toDateString();
                $receiverStock->save();
            } else {
                ProductStock::create([
                    'productid'             => $senderStock->productid,
                    'category_id'           => $senderStock->category_id,
                    'subcategory_id'        => $senderStock->subcategory_id,
                    'country_id'            => $senderStock->country_id,
                    'warehouse_id'          => $transfer->to_warehouse_id,
                    'branch_id'             => null,
                    'pro_ver_id'            => $senderStock->pro_ver_id,
                    'productname'           => $senderStock->productname,
                    'overallstock'          => $qty,
                    'availablestock'        => $qty,
                    'salestock'             => 0,
                    'low_stocks'            => $senderStock->low_stocks,
                    'last_stockupdate_date' => now()->toDateString(),
                ]);
            }

        } else {
            // ── Store → Store ──

            $warehouseId  = $transfer->from_warehouse_id;
            $fromBranchId = $transfer->from_branch_id;
            $toBranchId   = $transfer->to_branch_id;

            // Deduct sender store distribution record
            $senderDist = \App\Models\StockDistribution::where('product_id',  $productId)
                ->where('warehouse_id', $warehouseId)
                ->where('branch_id',    $fromBranchId)
                ->where(function ($q) use ($variantId) {
                    $variantId ? $q->where('variant_id', $variantId) : $q->whereNull('variant_id');
                })->orderBy('distributed_at', 'desc')->first();

            if (!$senderDist || $senderDist->quantity < $qty) {
                DB::rollBack();
                return response()->json(['message' => 'Insufficient store stock.'], 422);
            }

            $senderDist->quantity = max(0, $senderDist->quantity - $qty);
            $senderDist->save();

            // Add to receiver store distribution record
            $receiverDist = \App\Models\StockDistribution::where('product_id',  $productId)
                ->where('warehouse_id', $warehouseId)
                ->where('branch_id',    $toBranchId)
                ->where(function ($q) use ($variantId) {
                    $variantId ? $q->where('variant_id', $variantId) : $q->whereNull('variant_id');
                })->orderBy('distributed_at', 'desc')->first();

            if ($receiverDist) {
                $receiverDist->quantity += $qty;
                $receiverDist->save();
            } else {
                \App\Models\StockDistribution::create([
                    'product_id'     => $productId,
                    'variant_id'     => $variantId,
                    'warehouse_id'   => $warehouseId,
                    'branch_id'      => $toBranchId,
                    'quantity'       => $qty,
                    'distributed_by' => auth()->id() ?? null,
                    'distributed_at' => now(),
                ]);
            }
        }

        $transfer->status = 'accepted';
        $transfer->save();

        DB::commit();
        return response()->json(['success' => true, 'message' => 'Transfer accepted successfully.']);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['message' => $e->getMessage()], 500);
    }
}

public function cancelTransfer($id)
{
    try {
        $transfer = \App\Models\StockTransfer::findOrFail($id);

        if ($transfer->status !== 'pending') {
            return response()->json(['message' => 'Transfer already ' . $transfer->status . '.'], 422);
        }

        $transfer->status = 'cancelled';
        $transfer->save();

        return response()->json(['success' => true, 'message' => 'Transfer cancelled.']);

    } catch (\Exception $e) {
        return response()->json(['message' => $e->getMessage()], 500);
    }
}


public function giftIndex()
{
    $countryId = session('country_id');


    $query = ProductStock::select(
            'productstocks.*',
            'categories.category_name',
            'product_varient.varient',
            'product_varient.value',
            'product_varient.varient_name',
            'units.short_name as unit_short_name',
            'countries.name as country_name',
            'warehouse.warehouse_name',
            'warehouse_branch.branch_name'
        )
        ->join('categories',       'productstocks.category_id', '=', 'categories.id')
        ->join('product_varient',  'productstocks.pro_ver_id',  '=', 'product_varient.id')
        ->join('products',         'productstocks.productid',   '=', 'products.id')   // join products
        ->leftJoin('units',            'product_varient.unit_id',   '=', 'units.id')
        ->leftJoin('countries',        'productstocks.country_id',  '=', 'countries.id')
        ->leftJoin('warehouse',        'productstocks.warehouse_id','=', 'warehouse.id')
        ->leftJoin('warehouse_branch', 'productstocks.branch_id',   '=', 'warehouse_branch.id')
        ->where('products.is_gift', 1);  // gift only

    if (!empty($countryId)) {
        $query->where('productstocks.country_id', $countryId);
    }

    $stocks = $query->get();

    // ── Store stock for gift products only ──
    $storeStocks = DB::table('stock_distributions as sd')
        ->join('productstocks as ps', function ($join) {
            $join->on('ps.productid',    '=', 'sd.product_id')
                 ->on('ps.warehouse_id', '=', 'sd.warehouse_id');
        })
        ->join('products',         'sd.product_id',   '=', 'products.id')
        ->join('categories',       'ps.category_id',  '=', 'categories.id')
        ->join('product_varient as pv', 'sd.variant_id', '=', 'pv.id')
        ->leftJoin('units',            'pv.unit_id',        '=', 'units.id')
        ->leftJoin('countries',        'ps.country_id',     '=', 'countries.id')
        ->leftJoin('warehouse',        'sd.warehouse_id',   '=', 'warehouse.id')
        ->leftJoin('warehouse_branch', 'sd.branch_id',      '=', 'warehouse_branch.id')
        ->select(
            'sd.id', 'sd.product_id', 'sd.variant_id',
            'sd.warehouse_id', 'sd.branch_id',
            'sd.quantity as availablestock',
            'sd.distributed_at',
            'ps.productname', 'ps.salestock',
            'categories.category_name',
            'pv.varient_name', 'pv.value',
            'units.short_name as unit_short_name',
            'countries.name as country_name',
            'warehouse.warehouse_name',
            'warehouse_branch.branch_name'
        )
        ->where('products.is_gift', 1)   // gift only
        ->when(!empty($countryId), fn($q) => $q->where('ps.country_id', $countryId))
        ->where('sd.quantity', '>', 0)
        ->get();

    $warehouses        = \App\Models\WareHouse::all();
    $warehouseBranches = \App\Models\WareHouseBranch::all();
    $countries         = Country::all();
    $categories        = Category::where('is_gift', 1)->get();
    $subcategories     = SubCategory::all();
    $products          = Product::where('is_gift', 1)->get();
    $variants          = ProductVarient::whereHas('product', fn($q) => $q->where('is_gift', 1))->get();

    return view('pages.gifts.gift_stocks', compact(
        'stocks', 'storeStocks',
        'warehouses', 'warehouseBranches',
        'categories', 'countries',
        'products', 'subcategories', 'variants'
    ));
}


public function giftTransferIndex()
{
    $transfers = \DB::table('stock_transfers as t')
        ->join('productstocks as ps', 't.product_stock_id', '=', 'ps.id')
        ->join('products', 'ps.productid', '=', 'products.id')            // gift filter
        ->join('warehouse as w1', 't.from_warehouse_id', '=', 'w1.id')
        ->leftJoin('warehouse_branch as b1', 't.from_branch_id', '=', 'b1.id')
        ->join('warehouse as w2', 't.to_warehouse_id', '=', 'w2.id')
        ->leftJoin('warehouse_branch as b2', 't.to_branch_id', '=', 'b2.id')
        ->select(
            't.*',
            'ps.productname',
            'w1.warehouse_name as from_warehouse',
            'b1.branch_name as from_branch',
            'w2.warehouse_name as to_warehouse',
            'b2.branch_name as to_branch'
        )
        ->where('products.is_gift', 1)                                     // gift only
        ->latest('t.created_at')
        ->get();

    $countries  = Country::all();
    $warehouses = \App\Models\WareHouse::all();
    $branches   = \App\Models\WareHouseBranch::all();

    $distributions = \App\Models\StockDistribution::select(
            'product_id', 'variant_id', 'warehouse_id', 'branch_id',
            \DB::raw('SUM(quantity) as total_distributed')
        )
        ->whereHas('product', fn($q) => $q->where('is_gift', 1))          // gift only
        ->groupBy('product_id', 'variant_id', 'warehouse_id', 'branch_id')
        ->get();

    $stocks = [];

    foreach ($distributions as $dist) {
        $available = max(0, $dist->total_distributed);
        if ($available <= 0) continue;

        $productStock = ProductStock::where('productid', $dist->product_id)
            ->where('warehouse_id', $dist->warehouse_id)
            ->first();

        $stocks[] = [
            'id'             => $dist->product_id . '_' . $dist->variant_id . '_' . $dist->warehouse_id . '_' . $dist->branch_id,
            'product_id'     => $dist->product_id,
            'variant_id'     => $dist->variant_id,
            'warehouse_id'   => $dist->warehouse_id,
            'branch_id'      => $dist->branch_id,
            'productname'    => $productStock->productname ?? 'Unknown',
            'availablestock' => $available,
        ];
    }

    $warehouseStocks = ProductStock::whereNull('branch_id')
        ->where('availablestock', '>', 0)
        ->whereHas('product', fn($q) => $q->where('is_gift', 1))          // gift only
        ->get()
        ->map(fn($s) => [
            'product_id'     => $s->productid,
            'variant_id'     => $s->pro_ver_id,
            'warehouse_id'   => $s->warehouse_id,
            'productname'    => $s->productname,
            'availablestock' => $s->availablestock,
        ]);

    return view('pages.gifts.gift_transfer_stock', compact(
        'transfers', 'warehouses', 'branches', 'countries', 'stocks', 'warehouseStocks'
    ));
}

}

