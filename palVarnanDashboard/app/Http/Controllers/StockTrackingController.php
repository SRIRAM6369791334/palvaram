<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockTrackingController extends Controller
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

        $reservedByVariant = DB::table('product_order_items as poi')
            ->join('product_orders as po', 'po.id', '=', 'poi.order_id')
            ->where('po.status', 'processing')
            ->whereIn('po.country_id', $allowedCountryIds)
            ->select(
                'poi.product_variant_id',
                'poi.product_id',
                DB::raw('SUM(poi.quantity) as reserved_qty')
            )
            ->groupBy('poi.product_variant_id', 'poi.product_id')
            ->get()
            ->keyBy(fn($r) => $r->product_variant_id . '_' . $r->product_id);

        $purchasePriceByVariant = DB::table('purchase_order_items')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('variant_id')
            ->map(fn($rows) => $rows->first()->price);

        $stockRows = DB::table('productstocks as ps')
            ->join('products as p',              'p.id',         '=', 'ps.productid')       // ADD
            ->leftJoin('countries as co',        'co.id',        '=', 'ps.country_id')
            ->leftJoin('warehouse as w',         'w.id',         '=', 'ps.warehouse_id')
            ->leftJoin('warehouse_branch as wb', 'wb.id',        '=', 'ps.branch_id')
            ->leftJoin('product_varient as pv',  'pv.id',        '=', 'ps.pro_ver_id')
            ->leftJoin('categories as cat',      'cat.id',       '=', 'ps.category_id')
            ->leftJoin('sub_categories as sc',   'sc.id',        '=', 'ps.subcategory_id')
            ->select([
                'ps.id',
                'ps.productid',
                'ps.category_id',
                'ps.subcategory_id',
                'ps.country_id',
                'ps.warehouse_id',
                'ps.branch_id',
                'ps.pro_ver_id',
                'ps.productname',
                'ps.overallstock',
                'ps.availablestock',
                'ps.salestock',
                'ps.low_stocks',
                'ps.last_stockupdate_date',
                'co.name as country_name',
                'w.warehouse_name',
                'wb.branch_name',
                'pv.varient_name',
                'pv.sku',
                'pv.mrp_price',
                'pv.offer_price as variant_offer_price',
                DB::raw('0 as damaged_qty'),
            ])
            ->whereIn('ps.country_id', $allowedCountryIds)
            ->where('p.is_gift', 0)                                                          // ADD
            ->get();

        // ── Attach batch info ────────────────────────────────────────────────
        $stockIds = $stockRows->pluck('id')->toArray();

        $batches = DB::table('product_stock_batches')
            ->whereIn('product_stock_id', $stockIds)
            ->get()
            ->groupBy('product_stock_id');

        $stockRows = $stockRows->map(function ($row) use ($batches, $reservedByVariant, $purchasePriceByVariant) {

            $row->batches = $batches->get($row->id, collect())->toArray();

            $key = $row->pro_ver_id . '_' . $row->productid;
            $reservedRow = $reservedByVariant->get($key);
            $row->salestock = $reservedRow ? (int) $reservedRow->reserved_qty : 0;

            $row->purchase_price = $purchasePriceByVariant->get($row->pro_ver_id);   // ← NEW

            return $row;
        });

        // ── Summary stats ────────────────────────────────────────────────────
        $today = now()->startOfDay();

        $stats = [
            'available' => $stockRows->sum('availablestock'),
            'reserved'  => $stockRows->sum('salestock'),
            'damaged'   => $stockRows->sum('damaged_qty'),

            'expired'   => $stockRows->filter(function ($r) use ($today) {
                return collect($r->batches)->some(fn($b) =>
                    $b->expiry_date && \Carbon\Carbon::parse($b->expiry_date)->lt($today)
                );
            })->count(),

            'aging'     => $stockRows->filter(function ($r) use ($today) {
                $age = $r->last_stockupdate_date
                    ? $today->diffInDays(\Carbon\Carbon::parse($r->last_stockupdate_date))
                    : 0;

                return $age > 30 && $age <= 90;
            })->count(),

            'dead'      => $stockRows->filter(function ($r) use ($today) {
                $age = $r->last_stockupdate_date
                    ? $today->diffInDays(\Carbon\Carbon::parse($r->last_stockupdate_date))
                    : 0;

                return $age > 90 || ($r->overallstock ?? 0) == 0;
            })->count(),
        ];

        // ── Dropdown data (FILTERED BY COUNTRY) ───────────────────────────────
        $countries = DB::table('countries')
            ->whereIn('id', $allowedCountryIds)
            ->orderBy('name')
            ->get();

        $warehouses = DB::table('warehouse')
            ->whereIn('country_id', $allowedCountryIds)
            ->orderBy('warehouse_name')
            ->get();

        $branches = DB::table('warehouse_branch')
            ->whereIn('country_id', $allowedCountryIds)
            ->orderBy('branch_name')
            ->get();

        $categories = DB::table('categories')
            ->whereIn('country_id', $allowedCountryIds)
            ->orderBy('id')
            ->get();

        return view('pages.stock_tracking', compact(
            'stockRows',
            'stats',
            'countries',
            'warehouses',
            'branches',
            'categories'
        ));
    }

    public function giftIndex()
    {
        $allowedCountryIds = $this->getAllowedCountryIds();

        $reservedByVariant = DB::table('product_order_items as poi')
            ->join('product_orders as po', 'po.id', '=', 'poi.order_id')
            ->where('po.status', 'processing')
            ->whereIn('po.country_id', $allowedCountryIds)
            ->select(
                'poi.product_variant_id',
                'poi.product_id',
                DB::raw('SUM(poi.quantity) as reserved_qty')
            )
            ->groupBy('poi.product_variant_id', 'poi.product_id')
            ->get()
            ->keyBy(fn($r) => $r->product_variant_id . '_' . $r->product_id);

        $stockRows = DB::table('productstocks as ps')
            ->join('products as p',              'p.id',         '=', 'ps.productid')       // gift filter
            ->leftJoin('countries as co',        'co.id',        '=', 'ps.country_id')
            ->leftJoin('warehouse as w',         'w.id',         '=', 'ps.warehouse_id')
            ->leftJoin('warehouse_branch as wb', 'wb.id',        '=', 'ps.branch_id')
            ->leftJoin('product_varient as pv',  'pv.id',        '=', 'ps.pro_ver_id')
            ->leftJoin('categories as cat',      'cat.id',       '=', 'ps.category_id')
            ->leftJoin('sub_categories as sc',   'sc.id',        '=', 'ps.subcategory_id')
            ->select([
                'ps.id',
                'ps.productid',
                'ps.category_id',
                'ps.subcategory_id',
                'ps.country_id',
                'ps.warehouse_id',
                'ps.branch_id',
                'ps.pro_ver_id',
                'ps.productname',
                'ps.overallstock',
                'ps.availablestock',
                'ps.salestock',
                'ps.low_stocks',
                'ps.last_stockupdate_date',
                'co.name as country_name',
                'w.warehouse_name',
                'wb.branch_name',
                'pv.varient_name',
                'pv.sku',
                DB::raw('0 as damaged_qty'),
            ])
            ->whereIn('ps.country_id', $allowedCountryIds)
            ->where('p.is_gift', 1)                                                          // gift only
            ->get();

        $stockIds = $stockRows->pluck('id')->toArray();

        $batches = DB::table('product_stock_batches')
            ->whereIn('product_stock_id', $stockIds)
            ->get()
            ->groupBy('product_stock_id');

        $stockRows = $stockRows->map(function ($row) use ($batches, $reservedByVariant) {
            $row->batches = $batches->get($row->id, collect())->toArray();
            $key          = $row->pro_ver_id . '_' . $row->productid;
            $reservedRow  = $reservedByVariant->get($key);
            $row->salestock = $reservedRow ? (int) $reservedRow->reserved_qty : 0;
            return $row;
        });

        $today = now()->startOfDay();

        $stats = [
            'available' => $stockRows->sum('availablestock'),
            'reserved'  => $stockRows->sum('salestock'),
            'damaged'   => $stockRows->sum('damaged_qty'),

            'expired'   => $stockRows->filter(function ($r) use ($today) {
                return collect($r->batches)->some(fn($b) =>
                    $b->expiry_date && \Carbon\Carbon::parse($b->expiry_date)->lt($today)
                );
            })->count(),

            'aging'     => $stockRows->filter(function ($r) use ($today) {
                $age = $r->last_stockupdate_date
                    ? $today->diffInDays(\Carbon\Carbon::parse($r->last_stockupdate_date))
                    : 0;
                return $age > 30 && $age <= 90;
            })->count(),

            'dead'      => $stockRows->filter(function ($r) use ($today) {
                $age = $r->last_stockupdate_date
                    ? $today->diffInDays(\Carbon\Carbon::parse($r->last_stockupdate_date))
                    : 0;
                return $age > 90 || ($r->overallstock ?? 0) == 0;
            })->count(),
        ];

        $countries = DB::table('countries')
            ->whereIn('id', $allowedCountryIds)
            ->orderBy('name')
            ->get();

        $warehouses = DB::table('warehouse')
            ->whereIn('country_id', $allowedCountryIds)
            ->orderBy('warehouse_name')
            ->get();

        $branches = DB::table('warehouse_branch')
            ->whereIn('country_id', $allowedCountryIds)
            ->orderBy('branch_name')
            ->get();

        $categories = DB::table('categories')
            ->where('is_gift', 1)                                                            // gift only
            ->whereIn('country_id', $allowedCountryIds)
            ->orderBy('id')
            ->get();

        return view('pages.gifts.gift_stock_tracking', compact(
            'stockRows', 'stats', 'countries', 'warehouses', 'branches', 'categories'
        ));
    }


    public function setOfferPrice(Request $request, $batch)
    {
        $request->validate([
            'offer_price' => 'required|numeric|min:0',
        ]);

        $updated = DB::table('product_stock_batches')
            ->where('id', $batch)
            ->update([
                'offer_price' => $request->offer_price,
                'updated_at'  => now(),
            ]);

        if (!$updated) {
            return response()->json(['status' => 'error', 'message' => 'Batch not found.'], 404);
        }

        return response()->json(['status' => 'success', 'offer_price' => (float) $request->offer_price]);
    }


}