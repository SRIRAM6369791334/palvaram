<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Product;
use App\Models\ProductVarient;
use App\Models\Unit;
use App\Models\WareHouse;
use App\Models\WareHouseBranch;
use App\Models\SupplierDetail;

class PurchaseOrderController extends Controller
{

    private function getAllowedCountryIds()
    {
        $user = auth()->user();

        $countryIds = is_array($user->country_id)
            ? $user->country_id
            : json_decode($user->country_id, true) ?? [];

        $countryIds = array_map('intval', $countryIds);

        $sessionCountryId = session('country_id');

        if (!$sessionCountryId) {
            return $countryIds;
        }

        if (in_array((int)$sessionCountryId, $countryIds)) {
            return [(int)$sessionCountryId];
        }

        return $countryIds;
    }

public function index()
{
    $allowedCountryIds = $this->getAllowedCountryIds();

    $orders = PurchaseOrder::with([
            'supplier',
            'items.product',
            'items.variant',
            'items.unit',
            'country',
            'warehouse',
        ])
        ->whereIn('country_id', $allowedCountryIds)
        ->latest()
        ->get();

    $supplierIds = PurchaseOrder::whereIn('country_id', $allowedCountryIds)
        ->pluck('supplier_id')
        ->unique();

    return view('pages.purchase_orders', [
        'orders'     => $orders,
        'products'   => Product::all(),
        'countries'  => Country::whereIn('id', $allowedCountryIds)->get(),
        'warehouses' => WareHouse::whereIn('country_id', $allowedCountryIds)->get(),
        'units'      => Unit::all(),
        'suppliers'  => SupplierDetail::whereIn('id', $supplierIds)->get(),
        'variants' => ProductVarient::with('product', 'unit')
                    ->whereHas('product', fn($q) => $q->where('is_gift', 0))
                    ->get(),
    ]);
}

    public function store(Request $request)
    {
        if ($request->input('is_gift', 0) == 1) {
            return response()->json(['message' => 'Invalid endpoint for gift purchase orders.'], 422);
        }

        try {
            $request->validate([
                "country_id"   => "required",
                "supplier_id"  => "required",
                "warehouse_id" => "required",
                "items"        => "required|array|min:1",
            ]);

            $last   = PurchaseOrder::latest()->first();
            $poCode = $last ? 'PO-' . str_pad($last->id + 1, 5, '0', STR_PAD_LEFT) : 'PO-00001';

           
            $supplier = SupplierDetail::find($request->supplier_id);

            if (!$supplier) {
                return response()->json(['message' => 'Selected supplier was not found.'], 422);
            }

            $order = PurchaseOrder::create([
                'po_code'       => $poCode,
                'supplier_id'   => $request->supplier_id,
                'supplier_name' => $supplier->name,
                'country_id'    => $request->country_id,
                'warehouse_id'  => $request->warehouse_id,
                'status'        => 'pending',
            ]);

            $totalAmount = 0;

            foreach ($request->items as $item) {

               
                if (empty($item['variant_id'])) {
                    continue;
                }

                $quantity = (float) ($item['quantity'] ?? 0);
                $price    = (float) ($item['price'] ?? 0);
                $total    = $quantity * $price;

                $variant = ProductVarient::find($item['variant_id']);

                PurchaseOrderItem::create([
                    'purchase_order_id' => $order->id,
                    'product_id'        => $item['product_id'] ?? ($variant->product_id ?? null),
                    'variant_id'        => $item['variant_id'],

                    'barcode'           => $variant?->barcode,
                    'item_code'         => $variant?->sku,
                    'value'             => $variant?->value,

                    'category_id'       => $item['category_id'] ?? null,
                    'subcategory_id'    => $item['subcategory_id'] ?? null,
                    'unit_id'           => $item['unit_id'] ?? null,
                    'quantity'          => $quantity,
                    'price'             => $price,
                    'total'             => $total,
                ]);

                $totalAmount += $total;
            }

            $order->update(['total_amount' => $totalAmount]);

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
           
            return response()->json([
                "message" => $e->getMessage(),
                "line"    => $e->getLine(),
                "file"    => $e->getFile(),
            ], 500);
        }
    }

    public function update(Request $request)
    {
        if ($request->input('is_gift', 0) == 1) {
            return response()->json(['message' => 'Invalid endpoint for gift purchase orders.'], 422);
        }
        
        try {
            $order = PurchaseOrder::findOrFail($request->id);

            $totalAmount = 0;

            foreach ($request->items as $item) {

                if (empty($item['variant_id']) || empty($item['id'])) {
                    continue;
                }

                $quantity = (float) ($item['quantity'] ?? 0);
                $price    = (float) ($item['price'] ?? 0);
                $total    = $quantity * $price;

                $variant = ProductVarient::find($item['variant_id']);

                PurchaseOrderItem::where('id', $item['id'])->update([
                    'variant_id'     => $item['variant_id'],
                    'barcode'        => $variant?->barcode,
                    'item_code'      => $variant?->sku,
                    'value'          => $variant?->value,

                    'unit_id'        => $item['unit_id'] ?? null,
                    'category_id'    => $item['category_id'] ?? null,
                    'subcategory_id' => $item['subcategory_id'] ?? null,
                    'quantity'       => $quantity,
                    'price'          => $price,
                    'total'          => $total,
                ]);

                $totalAmount += $total;
            }

            $updateData = [
                'supplier_id'  => $request->supplier_id,
                'country_id'   => $request->country_id,
                'warehouse_id' => $request->warehouse_id,
                'total_amount' => $totalAmount,
            ];

            if ($request->supplier_id) {
                $supplier = SupplierDetail::find($request->supplier_id);
                if ($supplier) {
                    $updateData['supplier_name'] = $supplier->name;
                }
            }

            $order->update($updateData);

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            // TEMPORARY: same debug surfacing as store().
            return response()->json([
                "message" => $e->getMessage(),
                "line"    => $e->getLine(),
                "file"    => $e->getFile(),
            ], 500);
        }
    }

    public function receiveBatches(Request $request)
    {
        $order = PurchaseOrder::with(['items.product', 'items.variant'])->findOrFail($request->po_id);

        foreach ($request->batches as $batch) {

            $stock = null; // ← reset every iteration

            $item = PurchaseOrderItem::with(['product', 'variant'])->find($batch['item_id']);

            if (!$item) continue;

            $product = $item->product;
            if (!$product) continue;

            $proVerId = $item->variant_id
                ? $item->variant_id
                : ($product->pro_ver_id ?? $item->product_id);

            // ── Stock lookup: must be specific to this exact product+variant+warehouse+country ──
            $stockQuery = \App\Models\ProductStock::where('productid',    $item->product_id)
                                                ->where('warehouse_id', $order->warehouse_id)
                                                ->where('country_id',   $order->country_id);

           
            if ($item->variant_id) {
                $stockQuery->where('pro_ver_id', $item->variant_id);
            } else {
                $stockQuery->where('pro_ver_id', $product->pro_ver_id ?? $item->product_id);
            }

            $stock = $stockQuery->first();

            if ($stock) {
                $stock->overallstock          += (int) $batch['quantity'];
                $stock->availablestock        += (int) $batch['quantity'];
                $stock->last_stockupdate_date  = now()->toDateString();
                $stock->save();

            } else {
                $stock = \App\Models\ProductStock::create([
                    'productid'             => $item->product_id,
                    'category_id'           => $item->category_id    ?? $product->category_id,
                    'subcategory_id'        => $item->subcategory_id ?? $product->subcategory_id,
                    'country_id'            => $order->country_id,
                    'warehouse_id'          => $order->warehouse_id,
                    'branch_id'             => $order->branch_id ?? null,
                    'pro_ver_id'            => $proVerId,
                    'productname'           => $product->product_name ?? '',
                    'overallstock'          => (int) $batch['quantity'],
                    'availablestock'        => (int) $batch['quantity'],
                    'salestock'             => 0,
                    'low_stocks'            => 0,
                    'last_stockupdate_date' => now()->toDateString(),
                ]);
            }

            \App\Models\ProductStockBatch::create([
                'product_stock_id'   => $stock->id,
                'batch_no'           => $batch['batch_no'],
                'manufacturing_date' => $batch['manufacturing_date'],
                'expiry_date'        => $batch['expiry_date'],
                'quantity'           => (int) $batch['quantity'],
                'remaining_qty'      => (int) $batch['quantity'],
            ]);
        }

        $order->status = 'received';
        $order->save();

        return response()->json(['success' => true]);
    }

        
    public function getVariants(Request $request)
    {
        $variants = \App\Models\ProductVarient::where('product_id', $request->product_id)->get();
        return response()->json($variants);
    }

    public function show($id)
    {
        $order = PurchaseOrder::with([
            'items.product',
            'items.variant',
            'items.unit',
            'items.category',
            'items.subCategory',
            'country',
            'warehouse',
            'branch',
        ])->findOrFail($id);

        return view('pages.purchase_order_detail', compact('order'));
    }

    public function updateStatus(Request $request)
    {
        $order = PurchaseOrder::find($request->id);

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        // ── Enforce transition rules ──
        $allowed = [
            'pending' => ['ordered'],
            'ordered' => ['received'],
            'received'=> [],            // terminal — no further changes
        ];

        if (!in_array($request->status, $allowed[$order->status] ?? [])) {
            return response()->json([
                'message' => "Cannot change status from '{$order->status}' to '{$request->status}'."
            ], 422);
        }

        $order->status = $request->status;
        $order->save();

        return response()->json(['success' => true]);
    }

}