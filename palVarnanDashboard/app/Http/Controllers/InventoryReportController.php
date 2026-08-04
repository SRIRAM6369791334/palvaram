<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class InventoryReportController extends Controller
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

        $stockOnHand = $this->getStockOnHand($allowedCountryIds);
        $lowStock    = $this->getLowStock($allowedCountryIds);
        $deadStock   = $this->getDeadStock($allowedCountryIds);

        return view('pages.inventory_reports', [
            'stockOnHand'   => $stockOnHand,
            'lowStock'      => $lowStock,
            'deadStock'     => $deadStock,

            'totalProducts' => DB::table('productstocks')
                ->whereIn('country_id', $allowedCountryIds)
                ->whereExists(function ($q) {
                    $q->select(DB::raw(1))
                    ->from('products')
                    ->whereColumn('products.id', 'productstocks.productid')
                    ->where('products.is_gift', 0);
                })
                ->distinct('pro_ver_id')
                ->count('pro_ver_id'),

            'totalStock' => DB::table('productstocks')
                ->whereIn('country_id', $allowedCountryIds)
                ->whereExists(function ($q) {
                    $q->select(DB::raw(1))
                    ->from('products')
                    ->whereColumn('products.id', 'productstocks.productid')
                    ->where('products.is_gift', 0);
                })
                ->sum('overallstock'),

            'lowStockCount'  => count($lowStock),
            'deadStockCount' => count($deadStock),

            'countries' => DB::table('countries')
                ->whereIn('id', $allowedCountryIds)
                ->select('id', 'name')
                ->get(),

            'warehouses' => DB::table('warehouse')
                ->whereIn('country_id', $allowedCountryIds)
                ->select('id', 'warehouse_name')
                ->get(),

            'branches' => DB::table('warehouse_branch')
                ->whereIn('country_id', $allowedCountryIds)
                ->select('id', 'branch_name')
                ->get(),

            'categories' => DB::table('categories')
                ->where('is_gift', 0)
                ->whereIn('id', function ($q) use ($allowedCountryIds) {
                    $q->select('category_id')
                    ->from('productstocks')
                    ->whereIn('country_id', $allowedCountryIds);
                })
                ->select('id', 'category_name')
                ->get(),
        ]);
    }

   
    public function filter(Request $request)
    {
        $filters = $request->only(['country_id', 'warehouse_id', 'branch_id', 'category_id']);

        return response()->json([
            'stock_on_hand' => $this->getStockOnHand($filters),
            'low_stock'     => $this->getLowStock($filters),
            'dead_stock'    => $this->getDeadStock($filters),
        ]);
    }

    
    public function exportExcel(Request $request)
    {
        $type    = $request->get('report_type', 'stock_on_hand');
        $filters = $request->only(['country_id', 'warehouse_id', 'branch_id', 'category_id']);
        $data    = $this->getReportData($type, $filters);
        $headers = $this->getHeaders($type);

        // Using simple array export — swap with your Excel export class if needed
        return Excel::download(new class($data, $headers) implements \Maatwebsite\Excel\Concerns\FromArray,
                                                                         \Maatwebsite\Excel\Concerns\WithHeadings {
            public function __construct(public $rows, public $heads) {}
            public function array(): array { return $this->rows; }
            public function headings(): array { return $this->heads; }
        }, 'inventory_' . $type . '_' . now()->format('Ymd') . '.xlsx');
    }

  
    public function exportPdf(Request $request)
    {
        $type    = $request->get('report_type', 'stock_on_hand');
        $filters = $request->only(['country_id', 'warehouse_id', 'branch_id', 'category_id']);
        $data    = $this->getReportData($type, $filters);
        $headers = $this->getHeaders($type);

        $pdf = Pdf::loadView('reports.inventory_pdf', compact('data', 'headers', 'type'))
                  ->setPaper('a4', 'landscape');

        return $pdf->download('inventory_' . $type . '_' . now()->format('Ymd') . '.pdf');
    }

   
private function baseQuery(array $filters = [])
{
    $q = DB::table('productstocks as ps')
        ->join('product_varient as pv', 'ps.pro_ver_id', '=', 'pv.id')
        ->join('products as p', 'ps.productid', '=', 'p.id')
        ->leftJoin('categories as cat', 'ps.category_id', '=', 'cat.id')
        ->leftJoin('countries as c', 'ps.country_id', '=', 'c.id')
        ->leftJoin('warehouse as wh', 'ps.warehouse_id', '=', 'wh.id')
        ->leftJoin('warehouse_branch as br', 'ps.branch_id', '=', 'br.id')
        ->where('p.is_gift', 0) 
        ->select(
            'ps.id',
            'ps.productid',
            'ps.pro_ver_id',
            'ps.overallstock',
            'ps.availablestock',
            'ps.salestock',
            'ps.low_stocks',
            'ps.last_stockupdate_date',
            'ps.country_id',
            'ps.warehouse_id',
            'ps.branch_id',
            'ps.category_id',
            'p.product_name as productname',
            'pv.varient_name',
            'pv.sku',
            'pv.mrp_price',
            'pv.offer_price',
            'cat.category_name as cate_name',
            'c.name as country_name',
            'wh.warehouse_name as warehouse_name',
            'br.branch_name as branch_name'
        )
        // 🔥 FORCE COUNTRY SECURITY FILTER HERE
        ->whereIn('ps.country_id', $this->getAllowedCountryIds());

    if (!empty($filters['warehouse_id'])) $q->where('ps.warehouse_id', $filters['warehouse_id']);
    if (!empty($filters['branch_id']))    $q->where('ps.branch_id', $filters['branch_id']);
    if (!empty($filters['category_id']))  $q->where('ps.category_id', $filters['category_id']);

    return $q;
}

    private function getStockOnHand(array $filters = [])
    {
        return $this->baseQuery($filters)->orderByDesc('ps.overallstock')->get();
    }

    private function getLowStock(array $filters = [])
    {
        return $this->baseQuery($filters)
            ->whereNotNull('ps.low_stocks')
            ->whereRaw('ps.availablestock <= ps.low_stocks')
            ->orderBy('ps.availablestock')
            ->get();
    }

    private function getDeadStock(array $filters = [])
    {
        $cutoff = Carbon::now()->subDays(30)->toDateString();

        return $this->baseQuery($filters)
            ->where('ps.salestock', 0)
            ->where('ps.overallstock', '>', 0)
            ->where('ps.last_stockupdate_date', '<=', $cutoff)
            ->selectRaw('DATEDIFF(NOW(), ps.last_stockupdate_date) as days_stagnant')
            ->orderByDesc('days_stagnant')
            ->get();
    }

    private function getReportData(string $type, array $filters): array
    {
        $rows = match ($type) {
            'low_stock'  => $this->getLowStock($filters),
            'dead_stock' => $this->getDeadStock($filters),
            default      => $this->getStockOnHand($filters),
        };

        return $rows->map(function ($r, $i) use ($type) {
            $base = [
                $i + 1,
                $r->productname ?? '-',
                $r->varient_name ?? '-',
                $r->sku ?? '-',
                $r->cate_name ?? '-',
                $r->country_name ?? '-',
                $r->branch_name ?? '-',
                $r->overallstock,
                $r->availablestock,
                $r->salestock,
                '₹' . number_format($r->mrp_price ?? 0, 2),
                $r->last_stockupdate_date ?? '-',
            ];

            if ($type === 'low_stock')  $base[] = $r->low_stocks ?? '-';
            if ($type === 'dead_stock') $base[] = ($r->days_stagnant ?? '-') . ' days';

            return $base;
        })->toArray();
    }

    private function getHeaders(string $type): array
    {
        $base = ['S.No','Product Name','Variant','SKU','Category','Country','Branch',
                 'Overall Stock','Available Stock','Sold Stock','MRP','Last Updated'];

        if ($type === 'low_stock')  $base[] = 'Threshold';
        if ($type === 'dead_stock') $base[] = 'Days Stagnant';

        return $base;
    }

    public function getWarehouses(Request $request)
    {
        $warehouses = DB::table('warehouse')
            ->select('id', 'warehouse_name')
            ->when($request->country_id, fn($q) => $q->where('country_id', $request->country_id))
            ->get();

        return response()->json($warehouses);
    }

    public function getBranches(Request $request)
    {
        $branches = DB::table('warehouse_branch')
            ->select('id', 'branch_name')
            ->when($request->warehouse_id, fn($q) => $q->where('warehouse_id', $request->warehouse_id))
            ->get();

        return response()->json($branches);
    }

    public function getCategories(Request $request)
    {
        $categories = DB::table('categories')
            ->select('id', 'category_name')
            ->when($request->country_id, fn($q) => $q->where('country_id', $request->country_id))
            ->get();

        return response()->json($categories);
    }

    public function filterData(Request $request)
    {
        $filters = $request->only([
            'country_id',
            'warehouse_id',
            'branch_id',
            'category_id'
        ]);

        $type = $request->report_type ?? 'stock_on_hand';

        if ($type === 'low_stock') {
            $data = $this->getLowStock($filters);
        } elseif ($type === 'dead_stock') {
            $data = $this->getDeadStock($filters);
        } else {
            $data = $this->getStockOnHand($filters);
        }

        return response()->json([
            'data' => $data,
            'stock_count' => $this->getStockOnHand($filters)->count(),
            'low_count'   => $this->getLowStock($filters)->count(),
            'dead_count'  => $this->getDeadStock($filters)->count(),
        ]);
    }

}