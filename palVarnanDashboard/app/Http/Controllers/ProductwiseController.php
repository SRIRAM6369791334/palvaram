<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductSlot;
use App\Models\ProductVarient;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\ProductWiseReportExport;
use App\Models\Country;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ProductwiseController extends Controller
{
    public function productWiseReport()
    {
        $filter = 'this_month';
        $initialResults = $this->getFilteredReportDataFromDates(
            $filter,
            null,
            null,
            session('country_id')
        );
        $countries = Country::all(); 
        return view('pages.productwisereport', compact('filter', 'initialResults', 'countries'));
    }

    public function filterProductWiseReport(Request $request)
    {
        $results = $this->getFilteredReportData($request);
        return response()->json($results);
    }

    public function exportExcel(Request $request)
    {
        $results = $this->getFilteredReportData($request);
        return Excel::download(new ProductWiseReportExport($results), 'ProductWiseReport.xlsx');
    }

    public function exportPDF(Request $request)
    {
        $results = $this->getFilteredReportData($request);
        $pdf = Pdf::loadView('exports.productwise', ['results' => $results]);
        return $pdf->download('ProductWiseReport.pdf');
    }

    private function getFilteredReportData(Request $request)
    {
        $filter = $request->input('filter', 'this_month');
        $from = $request->input('from');
        $to = $request->input('to');
        $countryId = $request->input('country_id') ?? session('country_id');

        return $this->getFilteredReportDataFromDates($filter, $from, $to, $countryId);
    }

    private function getFilteredReportDataFromDates($filter, $from = null, $to = null, $countryId = null)
    {
        $now = Carbon::now();
        $unitOptions = [1 => 'l', 2 => 'ml', 3 => 'g', 4 => 'kg', 5 => "No's"];

        $query = ProductSlot::select(
            'product_slots.product_id',
            'product_slots.product_name',
            'product_slots.product_value',
            'product_slots.product_rate',
            'product_slots.product_varient_id',
            'product_orders.country_id',
            DB::raw('SUM(product_slots.quantity) as total_quantity'),
            DB::raw('SUM(product_slots.quantity * product_slots.product_rate) as total_value'),
            DB::raw('(SELECT name FROM countries WHERE countries.id = product_orders.country_id) as country_name')

        )
        ->join('product_orders', 'product_orders.order_id', '=', 'product_slots.order_id')
        ->groupBy('product_slots.product_id', 'product_slots.product_varient_id', 'product_slots.product_name', 'product_slots.product_value', 'product_slots.product_rate', 'product_orders.country_id');

        if ($countryId) {
            $query->where('product_orders.country_id', $countryId);
        }

        switch ($filter) {
            case 'this_week':
                $query->whereBetween('product_orders.created_at', [
                    $now->copy()->startOfWeek(),
                    $now->copy()->endOfWeek()->endOfDay()
                ]);
                break;

            case 'last_month':
                $query->whereBetween('product_orders.created_at', [
                    $now->copy()->subMonth()->startOfMonth(),
                    $now->copy()->subMonth()->endOfMonth()->endOfDay()
                ]);
                break;

            case 'custom':
                if ($from && $to) {
                    $query->whereBetween('product_orders.created_at', [
                        Carbon::parse($from)->startOfDay(),
                        Carbon::parse($to)->endOfDay()
                    ]);
                }
                break;

            case 'this_month':
            default:
                $query->whereBetween('product_orders.created_at', [
                    $now->copy()->startOfMonth(),
                    $now->copy()->endOfMonth()->endOfDay()
                ]);
                break;
        }

        // return $query->orderByDesc('total_quantity')->get()->map(function ($item) use ($unitOptions) {
        //     $product = Product::find($item->product_id);
        //     $variant = ProductVarient::find($item->product_varient_id);
        //     $unit = $unitOptions[$variant->varient ?? 5] ?? '';
        //     $item->product_name = $product->product_name ?? 'Unknown';
        //     $item->variant_label = $variant ? $variant->value . ' ' . $unit : 'N/A';
        //     return $item;
        // });
        return $query->orderByDesc('total_quantity')->get()->map(function ($item) {
            $variant = ProductVarient::with('unit')->find($item->product_varient_id);
            if ($variant) {
                $item->variant_label = trim($variant->value . ' ' . optional($variant->unit)->short_name);
            } else {
                $item->variant_label = $item->product_value;
            }
            return $item;
        });

    }
}
