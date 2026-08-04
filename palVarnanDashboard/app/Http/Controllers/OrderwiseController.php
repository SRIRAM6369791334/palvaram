<?php

namespace App\Http\Controllers;

// OrderwiseController.php

use App\Models\ProductSlot;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\OrderWiseReportExport;
use App\Models\Country;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderwiseController extends Controller
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
        return view('pages.orderwisereport', compact('filter', 'initialResults', 'countries'));
    }

    public function filterorderWiseReport(Request $request)
    {
        $results = $this->getFilteredOrderData($request);
        return response()->json($results);
    }

    public function exportExcel(Request $request)
    {
        $results = $this->getFilteredOrderData($request);
        return Excel::download(new OrderWiseReportExport($results['orders']), 'OrderWiseReport.xlsx');
    }

    public function exportPDF(Request $request)
    {
        $results = $this->getFilteredOrderData($request);
        $pdf = Pdf::loadView('exports.orderwise', ['results' => $results['orders']]);
        return $pdf->download('OrderWiseReport.pdf');
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

    );


    if ($countryId) {
        $query->where('product_orders.country_id', $countryId);
    }

    // Apply date filters
    // if ($filter === 'this_month') {
    //     $query->whereBetween('date_ordered_on', [
    //         $now->copy()->startOfMonth()->toDateString(),
    //         $now->copy()->endOfMonth()->toDateString()
    //     ]);
    // } elseif ($filter === 'last_month') {
    //     $query->whereBetween('date_ordered_on', [
    //         $now->copy()->subMonth()->startOfMonth()->toDateString(),
    //         $now->copy()->subMonth()->endOfMonth()->toDateString()
    //     ]);
    // } elseif ($filter === 'this_week') {
    //     $query->whereBetween('date_ordered_on', [
    //         $now->copy()->startOfWeek()->toDateString(),
    //         $now->copy()->endOfWeek()->toDateString()
    //     ]);
    // } elseif ($filter === 'custom' && $from && $to) {
    //     $query->whereBetween('date_ordered_on', [
    //         Carbon::parse($from)->startOfDay()->toDateTimeString(),
    //         Carbon::parse($to)->endOfDay()->toDateTimeString()
    //     ]);
    // }
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
        'total_value' => $orders->sum('total_amount')
    ];

    return [
        'orders' => $orders,
        'summary' => $summary
    ];
}

}

