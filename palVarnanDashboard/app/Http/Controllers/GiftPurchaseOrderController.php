<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;
use App\Models\GiftPurchaseOrder;
use App\Models\GiftPurchaseOrderItem;
use App\Models\Product;
use App\Models\ProductVarient;
use App\Models\Unit;
use App\Models\WareHouse;
use App\Models\SupplierDetail;
use App\Models\Category;

class GiftPurchaseOrderController extends Controller
{
    private function getAllowedCountryIds()
    {
        $user = auth()->user();
        $countryIds = is_array($user->country_id)
            ? $user->country_id
            : json_decode($user->country_id, true) ?? [];
        $countryIds = array_map('intval', $countryIds);
        $sessionCountryId = session('country_id');
        if (!$sessionCountryId) return $countryIds;
        if (in_array((int)$sessionCountryId, $countryIds)) return [(int)$sessionCountryId];
        return $countryIds;
    }

    private function getGiftVariants(array $allowedCountryIds)
    {
        $allowedCategoryIds = Category::where('is_gift', 1)->get()
            ->filter(function ($cat) use ($allowedCountryIds) {
                $ids = array_map('intval', explode(',', $cat->country_id ?? ''));
                return count(array_intersect($ids, $allowedCountryIds)) > 0;
            })->pluck('id')->toArray();

        return ProductVarient::whereHas('product', function ($q) use ($allowedCategoryIds) {
                $q->where('is_gift', 1)->whereIn('category_id', $allowedCategoryIds);
            })
            ->with(['product', 'unit', 'color', 'size'])
            ->get();
    }

    public function index()
    {
        $allowedCountryIds = $this->getAllowedCountryIds();

        $orders = GiftPurchaseOrder::with([
                'supplier',
                'items.product',
                'items.variant.color',
                'items.variant.size',
                'items.variant',
                'items.unit',
                'country',
                'warehouse',
            ])
            ->whereIn('country_id', $allowedCountryIds)
            ->latest()
            ->get();

        $variants = $this->getGiftVariants($allowedCountryIds);

        return view('pages.gifts.gift_purchase_orders', [
            'orders'     => $orders,
            'countries'  => Country::whereIn('id', $allowedCountryIds)->get(),
            'warehouses' => WareHouse::whereIn('country_id', $allowedCountryIds)->get(),
            'units'      => Unit::all(),
            'variants'   => $variants,
        ]);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                "country_id"   => "required",
                "supplier_id"  => "required",
                "warehouse_id" => "required",
                "items"        => "required|array|min:1",
            ]);

            $last   = GiftPurchaseOrder::latest()->first();
            $poCode = $last ? 'GPO-' . str_pad($last->id + 1, 5, '0', STR_PAD_LEFT) : 'GPO-00001';

            $supplier = SupplierDetail::find($request->supplier_id);
            if (!$supplier) {
                return response()->json(['message' => 'Selected supplier was not found.'], 422);
            }

            $order = GiftPurchaseOrder::create([
                'po_code'       => $poCode,
                'supplier_id'   => $request->supplier_id,
                'supplier_name' => $supplier->name,
                'country_id'    => $request->country_id,
                'warehouse_id'  => $request->warehouse_id,
                'status'        => 'pending',
            ]);

            $totalAmount = 0;

            foreach ($request->items as $item) {
                if (empty($item['variant_id'])) continue;

                $quantity = (float) ($item['quantity'] ?? 0);
                $price    = (float) ($item['price'] ?? 0);
                $total    = $quantity * $price;

                $variant = ProductVarient::find($item['variant_id']);

                GiftPurchaseOrderItem::create([
                    'gift_purchase_order_id' => $order->id,
                    'product_id'             => $item['product_id'] ?? ($variant->product_id ?? null),
                    'variant_id'             => $item['variant_id'],
                    'barcode'                => $variant?->barcode,
                    'item_code'              => $variant?->sku,
                    'value'                  => $variant?->value,
                    'color_id'               => $variant?->color_id,
                    'size_id'                => $variant?->size_id,
                    'category_id'            => $item['category_id'] ?? null,
                    'subcategory_id'         => $item['subcategory_id'] ?? null,
                    'unit_id'                => $item['unit_id'] ?? null,
                    'quantity'               => $quantity,
                    'price'                  => $price,
                    'total'                  => $total,
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
        try {
            $order = GiftPurchaseOrder::findOrFail($request->id);

            $totalAmount = 0;

            foreach ($request->items as $item) {
                if (empty($item['variant_id']) || empty($item['id'])) continue;

                $quantity = (float) ($item['quantity'] ?? 0);
                $price    = (float) ($item['price'] ?? 0);
                $total    = $quantity * $price;

                $variant = ProductVarient::find($item['variant_id']);

                GiftPurchaseOrderItem::where('id', $item['id'])->update([
                    'variant_id'     => $item['variant_id'],
                    'barcode'        => $variant?->barcode,
                    'item_code'      => $variant?->sku,
                    'value'          => $variant?->value,
                    'color_id'       => $variant?->color_id,
                    'size_id'        => $variant?->size_id,
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
                if ($supplier) $updateData['supplier_name'] = $supplier->name;
            }

            $order->update($updateData);

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            return response()->json([
                "message" => $e->getMessage(),
                "line"    => $e->getLine(),
                "file"    => $e->getFile(),
            ], 500);
        }
    }

    public function receiveBatches(Request $request)
    {
        $order = GiftPurchaseOrder::with(['items.product', 'items.variant'])->findOrFail($request->po_id);

        foreach ($request->batches as $batch) {
            $item = GiftPurchaseOrderItem::with(['product', 'variant'])->find($batch['item_id']);
            if (!$item) continue;

            $product = $item->product;
            if (!$product) continue;

            $proVerId = $item->variant_id
                ? $item->variant_id
                : ($product->pro_ver_id ?? $item->product_id);

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
                $stock->overallstock         += (int) $batch['quantity'];
                $stock->availablestock       += (int) $batch['quantity'];
                $stock->last_stockupdate_date = now()->toDateString();
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
                'manufacturing_date' => $batch['manufacturing_date'] ?? null,
                'expiry_date'        => $batch['expiry_date'] ?? null,
                'quantity'           => (int) $batch['quantity'],
                'remaining_qty'      => (int) $batch['quantity'],
            ]);
        }

        $order->status = 'received';
        $order->save();

        return response()->json(['success' => true]);
    }

    public function updateStatus(Request $request)
    {
        $order = GiftPurchaseOrder::find($request->id);
        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $allowed = [
            'pending'  => ['ordered'],
            'ordered'  => ['received'],
            'received' => [],
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

    public function show($id)
    {
        $order = GiftPurchaseOrder::with([
            'items.product',
            'items.variant.color',
            'items.variant.size',
            'items.category',
            'items.subCategory',
            'items.unit',
            'country',
            'warehouse',
        ])->findOrFail($id);

        return view('pages.gifts.gift_purchase_order_detail', compact('order'));
    }
}