<?php

namespace App\Providers;

use App\Models\Country;
use App\Models\MilkOrder;
use App\Models\MilkRefund;
use App\Models\ProductOrder;
use App\Models\ProductRefund;
use App\Models\ProductSlot;
use App\Models\ProductStock;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

        if ($this->app->environment('local')) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (config('app.env') === 'production' || request()->header('x-forwarded-proto') === 'https') {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        View::composer("layouts.sidebar", function ($view) {
            $milkOrderPendingCount  = MilkOrder::query()->where("is_delivery_assigned", "0")->count();
            $productOrderPendingCount  = ProductOrder::query()->where("payment_status", "paid")->where("status", "processing")->count();
            $milkRefundPendingCount  = MilkRefund::query()->where("refund_status", 0)->count();
            $productRefundPendingCount  = ProductRefund::query()->where("refund_status", 0)->count();
            $productOrderDispatchCount  = ProductOrder::query()->where("payment_status", "paid")->where("status", "shipped")->count();
            $productOrderDeliveryCount  = ProductOrder::query()->where("payment_status", "paid")->where("status", "out_for_delivery")->count();
            $productOrdercompleteCount  = ProductOrder::query()->where("payment_status", "paid")->where("status", "delivered")->count();
            $productOrderBIllingCount  = ProductOrder::query()->where("payment_status", "paid")->where("status", "pending")->count();
            $cancelreq = ProductOrder::query()->where('status', "cancelled")->count();
            $returnreqcount = DB::table('product_tracking')->where('return_requested',1)->count();
            $productreturn = ProductOrder::query()->where("payment_status", "paid")->where("status", "return")->count(); // Assuming return status might be used later




            $view->with("milkOrderPendingCount", $milkOrderPendingCount);
            $view->with("productOrderPendingCount", $productOrderPendingCount);
            $view->with("productOrderBIllingCount", $productOrderBIllingCount);
            $view->with("milkRefundPendingCount", $milkRefundPendingCount);
            $view->with("productRefundPendingCount", $productRefundPendingCount);
            $view->with("productOrderDispatchCount", $productOrderDispatchCount);
            $view->with("productOrderDeliveryCount", $productOrderDeliveryCount);
            $view->with("productOrdercompleteCount",$productOrdercompleteCount);
            $view->with("cancelreq",$cancelreq);
            $view->with("returnreqcount",$returnreqcount);
            $view->with("productreturn",$productreturn);

        });

        View::composer("layouts.topbar", function ($view) {
            // $stockscount = ProductStock::select('productstocks.*', 'categories.category_name')
            // ->join('categories', 'productstocks.category_id', '=', 'categories.id')
            // ->where('availablestock', '<', 6)
            // ->count();

            $stockscount  = ProductStock::select('productstocks.*', 'categories.category_name', 'product_varient.varient', 'product_varient.value', 'product_varient.low_stock')
            ->join('categories', 'productstocks.category_id', '=', 'categories.id')
            ->join('product_varient', 'productstocks.pro_ver_id', '=', 'product_varient.id')
            ->where('productstocks.availablestock', '<', DB::raw('product_varient.low_stock'))
            ->count();

            $countries = Country::all();
            $selectedCountry = session('country_name');

            $expiringItemsQuery = DB::table('product_stock_batches as b')
                ->join('productstocks as ps', 'ps.id', '=', 'b.product_stock_id')
                ->whereNotNull('b.expiry_date')
                ->whereDate('b.expiry_date', '>=', now()->toDateString())
                ->whereNull('b.offer_price')
                ->where(function ($q) {
                    $q->where(function ($q2) {
                        // Has manufacturing date → use 25%-of-shelf-life rule
                        $q2->whereNotNull('b.manufacturing_date')
                        ->whereRaw('DATEDIFF(b.manufacturing_date, b.expiry_date) != 0')
                        ->whereRaw('DATEDIFF(b.expiry_date, CURDATE()) <= 0.25 * DATEDIFF(b.expiry_date, b.manufacturing_date)');
                    })->orWhere(function ($q2) {
                        // No manufacturing date on record → fallback flat 30-day window
                        $q2->whereNull('b.manufacturing_date')
                        ->whereDate('b.expiry_date', '<=', now()->addDays(30)->toDateString());
                    });
                });

            $sessionCountryId = session('country_id');
            if (!empty($sessionCountryId)) {
                $expiringItemsQuery->where('ps.country_id', $sessionCountryId);
            }

            $expiringItems = $expiringItemsQuery
                ->select('b.id as batch_id', 'b.expiry_date', 'ps.productname')
                ->orderBy('b.expiry_date')
                ->get();


            $overdueDistributionsQuery = DB::table('stock_distributions as sd')
                ->join('products as p', 'p.id', '=', 'sd.product_id')
                ->leftJoin('warehouse as w', 'w.id', '=', 'sd.warehouse_id')
                ->leftJoin('warehouse_branch as wb', 'wb.id', '=', 'sd.branch_id')
                ->where('sd.status', 'pending')
                ->where('sd.distributed_at', '<=', now()->subDay())
                ->select(
                    'sd.id',
                    'sd.quantity',
                    'sd.distributed_at',
                    'p.product_name',
                    'w.warehouse_name',
                    'wb.branch_name'
                );

            if (!empty($sessionCountryId)) {
                $overdueDistributionsQuery->where('w.country_id', $sessionCountryId);
            }

            $overdueDistributions = $overdueDistributionsQuery
                ->orderBy('sd.distributed_at')
                ->get();


            $view->with("stockscount", $stockscount);
            $view->with("countries", $countries);
            $view->with("selectedCountry", $selectedCountry);
            $view->with("expiringItems", $expiringItems);
            $view->with("expiringCount", $expiringItems->count());
            $view->with("overdueDistributions", $overdueDistributions);          // ← NEW
            $view->with("overdueDistributionsCount", $overdueDistributions->count()); // ← NEW

        });
    }
}