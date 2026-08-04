<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GiftPurchaseReportController extends Controller
{
    public function index()
    {
        return view('pages.gifts.gift_purchase_reports', [
            'supplierData' => $this->getSupplierWise(),
            'varianceData' => $this->getCostVariance(),
            'suppliers'    => DB::table('gift_purchase_orders')
                                ->select('supplier_name')
                                ->distinct()
                                ->get(),
        ]);
    }

    private function baseQuery($filters = [])
    {
        $q = DB::table('gift_purchase_order_items as poi')
            ->join('gift_purchase_orders as po', 'poi.gift_purchase_order_id', '=', 'po.id')
            ->join('products as p', 'poi.product_id', '=', 'p.id')
            ->select(
                'poi.*',
                'po.supplier_name',
                'po.created_at as order_date',
                'p.product_name',
                'p.product_mrp_price'
            );

        if (!empty($filters['supplier'])) {
            $q->where('po.supplier_name', $filters['supplier']);
        }

        return $q;
    }

    private function getSupplierWise($filters = [])
    {
        return $this->baseQuery($filters)
            ->select(
                'po.supplier_name',
                DB::raw('COUNT(DISTINCT po.id) as total_orders'),
                DB::raw('SUM(poi.quantity) as total_qty'),
                DB::raw('SUM(poi.total) as total_amount')
            )
            ->groupBy('po.supplier_name')
            ->get();
    }

    private function getCostVariance($filters = [])
    {
        return $this->baseQuery($filters)
            ->select(
                'p.product_name',
                DB::raw('AVG(poi.price) as purchase_price'),
                'p.product_mrp_price as selling_price',
                DB::raw('(p.product_mrp_price - AVG(poi.price)) as variance')
            )
            ->groupBy('p.id', 'p.product_name', 'p.product_mrp_price')
            ->get();
    }

    public function filterData(Request $request)
    {
        $filters = $request->only(['supplier']);
        $type    = $request->report_type ?? 'supplier';

        $data = $type === 'variance'
            ? $this->getCostVariance($filters)
            : $this->getSupplierWise($filters);

        return response()->json(['data' => $data]);
    }
}