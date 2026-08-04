<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Exports\OrderWiseReportExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class GiftOrderWiseController extends Controller
{
    public function orderwisereport()
    {
        $filter = 'this_month';
        $initialResults = $this->getFilteredOrderDataFromDates(
            $filter,
            null,
            null,
            session('country_id')
        );
        $countries = Country::all();
        return view('pages.gift_orderwisereport', compact('filter', 'initialResults', 'countries'));
    }

    public function filterorderWiseReport(Request $request)
    {
        $results = $this->getFilteredOrderData($request);
        return response()->json($results);
    }

    public function exportExcel(Request $request)
    {
        $results = $this->getFilteredOrderData($request);
        return Excel::download(new OrderWiseReportExport($results['orders']), 'GiftOrderWiseReport.xlsx');
    }

    public function exportPDF(Request $request)
    {
        $results = $this->getFilteredOrderData($request);
        $pdf = Pdf::loadView('exports.orderwise', ['results' => $results['orders']]);
        return $pdf->download('GiftOrderWiseReport.pdf');
    }

    private function getFilteredOrderData(Request $request)
    {
        $filter = $request->input('filter', 'this_month');
        $from = $request->input('from');
        $to = $request->input('to');
        $countryId = $request->input('country_id') ?? session('country_id');

        return $this->getFilteredOrderDataFromDates($filter, $from, $to, $countryId);
    }

    private function getFilteredOrderDataFromDates($filter, $from = null, $to = null, $countryId = null)
    {
        $now = Carbon::now();

        $query = DB::table('product_orders')
            ->leftJoin('product_order_user_addresses', function ($join) {
                $join->on('product_order_user_addresses.order_id', '=', 'product_orders.order_id')
                     ->where('product_order_user_addresses.address_type_name', '=', 'billing');
            })
            ->select(
                'product_orders.order_id',
                'product_orders.created_at',
                'product_orders.total_amount',
                'product_orders.subtotal',
                'product_orders.gst_amount',
                'product_orders.payment_status',
                'product_order_user_addresses.firstname',
                'product_order_user_addresses.address_phone_number',
                DB::raw('(SELECT COUNT(*) FROM product_slots WHERE product_slots.order_id = product_orders.order_id) as total_items'),
                DB::raw('(SELECT SUM(quantity) FROM product_slots WHERE product_slots.order_id = product_orders.order_id) as total_quantity'),
                DB::raw('(SELECT name FROM countries WHERE countries.id = product_orders.country_id) as country_name')
            )
            // ── Only orders that contain at least one gift product ──
            ->whereExists(function ($sub) {
                $sub->select(DB::raw(1))
                    ->from('product_slots')
                    ->join('products', 'products.id', '=', 'product_slots.product_id')
                    ->whereColumn('product_slots.order_id', 'product_orders.order_id')
                    ->where('products.is_gift', 1);
            });

        if ($countryId) {
            $query->where('product_orders.country_id', $countryId);
        }

        if ($filter === 'this_month') {
            $query->whereBetween('product_orders.created_at', [
                $now->copy()->startOfMonth()->startOfDay()->toDateTimeString(),
                $now->copy()->endOfMonth()->endOfDay()->toDateTimeString()
            ]);
        } elseif ($filter === 'last_month') {
            $query->whereBetween('product_orders.created_at', [
                $now->copy()->subMonth()->startOfMonth()->startOfDay()->toDateTimeString(),
                $now->copy()->subMonth()->endOfMonth()->endOfDay()->toDateTimeString()
            ]);
        } elseif ($filter === 'this_week') {
            $query->whereBetween('product_orders.created_at', [
                $now->copy()->startOfWeek()->startOfDay()->toDateTimeString(),
                $now->copy()->endOfWeek()->endOfDay()->toDateTimeString()
            ]);
        } elseif ($filter === 'custom' && $from && $to) {
            $query->whereBetween('product_orders.created_at', [
                Carbon::parse($from)->startOfDay()->toDateTimeString(),
                Carbon::parse($to)->endOfDay()->toDateTimeString()
            ]);
        }

        $orders = $query->orderByDesc('product_orders.created_at')->get();

        $summary = [
            'total_orders' => $orders->count(),
            'total_value'  => $orders->sum('total_amount')
        ];

        return [
            'orders'  => $orders,
            'summary' => $summary
        ];
    }
}
