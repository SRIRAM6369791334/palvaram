<?php

namespace App\Http\Controllers;

use App\Models\MilkOrder;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\ProductOrder;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    public function  incomeReports()
    {

        $milkOrders = MilkOrder::where("payment_status", 1)
            ->with("product", "customer.area", "transactionLog")
            ->get()->map(function ($item) {
                $item->date_ordered_on = Carbon::parse($item->date_ordered_on)->format('d-M-Y');
                return $item;
            });

        return view("pages.income_reports", compact("milkOrders"));
    }


    public function getIncomeReports(Request $request)
    {
        $fromDate =  $request->from_date;
        $toDate =   $request->to_date;

        $productOrders = [];
        $productOrders = ProductOrder::whereIn("payment_status", [1, 2, 3])
                ->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])
                ->with("product", "customer.area", "transactionLog")->get()->map(function ($item) {
                    $item->date_ordered_on = Carbon::parse($item->date_ordered_on)->format('d-M-Y');
                    return $item;
                });

        // if ($productType == "all") {
        //     $milkOrders = MilkOrder::where("payment_status", 1)
        //         ->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])
        //         ->with("product", "customer.area", "transactionLog")
        //         ->get()->map(function ($item) {
        //             $item->date_ordered_on = Carbon::parse($item->date_ordered_on)->format('d-M-Y');
        //             return $item;
        //         });

        //     $productOrders = ProductOrder::where("payment_status", 1)
        //         ->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])
        //         ->with("product", "customer.area", "transactionLog")->get()->map(function ($item) {
        //             $item->date_ordered_on = Carbon::parse($item->date_ordered_on)->format('d-M-Y');
        //             return $item;
        //         });
        // } else if ($productType == env("MILK_PRODUCT")) {
        //     $milkOrders = MilkOrder::where("payment_status", 1)
        //         ->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])
        //         ->with("product", "customer.area", "transactionLog")
        //         ->get()->map(function ($item) {
        //             $item->date_ordered_on = Carbon::parse($item->date_ordered_on)->format('d-M-Y');
        //             return $item;
        //         });

        //     $productOrders = [];
        // } else {
        //     $milkOrders = [];

        //     $productOrders = ProductOrder::where("payment_status", 1)
        //         ->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])
        //         ->with("product", "customer.area", "transactionLog")->get()->map(function ($item) {
        //             $item->date_ordered_on = Carbon::parse($item->date_ordered_on)->format('d-M-Y');
        //             return $item;
        //         });
        // }


        return response()->json([
            "view" => view("ajaxPages.render_income_report", compact("productOrders"))->render()
        ]);
    }


    public function index()
    {
        $totalStock = ProductStock::sum('availablestock');

        return view('pages.reports_single', [

            'productCount'   => Product::count(),

            'productRevenue' => '₹' . number_format(ProductOrder::sum('total_amount') / 100000, 1) . 'L',

            'inStockPct' => ProductStock::count()
                ? round(
                    ProductStock::where('availablestock', '>', 0)->count()
                    / ProductStock::count() * 100
                ) . '%'
                : '—',

            'totalOrders' => ProductOrder::count(),

            'deliveredPct' => ProductOrder::count()
                ? round(
                    ProductOrder::where('status', 'delivered')->count()
                    / ProductOrder::count() * 100
                ) . '%'
                : '—',

            'pendingOrders' => ProductOrder::whereIn('status', [
                'processing', 'packing', 'dispatched'
            ])->count(),

            'months' => collect(range(5, 0))->map(fn($i) =>
                now()->subMonths($i)->format('M')
            )->values(),

            'productMonthly' => collect(range(5, 0))->map(fn($i) =>
                ProductOrder::whereMonth('created_at', now()->subMonths($i)->month)
                    ->sum('total_amount') / 1000
            )->values(),

            'orderMonthly' => collect(range(5, 0))->map(fn($i) =>
                ProductOrder::whereMonth('created_at', now()->subMonths($i)->month)
                    ->count()
            )->values(),

            'totalProducts' => ProductStock::distinct('pro_ver_id')->count('pro_ver_id'),

            'lowStockCount' => ProductStock::whereColumn(
                'availablestock', '<=', 'low_stocks'
            )->count(),

            'deadStockCount' => ProductStock::where('salestock', 0)
                ->where('overallstock', '>', 0)
                ->count(),

            'supplierCount' => PurchaseOrder::distinct('supplier_name')
                ->count('supplier_name'),

            'totalPurchase' => PurchaseOrderItem::sum('total'),

            'avgVariance' => PurchaseOrderItem::join('products', 'purchase_order_items.product_id', '=', 'products.id')
                ->selectRaw('AVG(products.product_mrp_price - purchase_order_items.price) as variance')
                ->value('variance'),
        ]);
    }

}
