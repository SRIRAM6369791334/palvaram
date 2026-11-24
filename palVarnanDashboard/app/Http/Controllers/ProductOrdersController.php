<?php

namespace App\Http\Controllers;

use App\Models\ProductOrder;
use App\Models\ProductSlot;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductOrdersController extends Controller {
    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */

        public function index()
    {
        $allowedCountryIds = $this->getAllowedCountryIds();

        $productOrders = ProductOrder::query()
            ->with(
                'product',
                'orderAddress.area',
                'customer',
                'country'
            )
            ->whereIn('status', ['pending', 'Order Placed'])
            ->whereIn('country_id', $allowedCountryIds)
            ->latest()
            ->get();


        return view('pages.product_orders', compact('productOrders'));
    }


    public function allOrders()
    {
        $allowedCountryIds = $this->getAllowedCountryIds();

        $productOrders = ProductOrder::with(
                'orderAddress.area',
                'customer',
                'country'
            )
            ->withSum('items as total_quantity', 'quantity')
            ->whereIn('country_id', $allowedCountryIds)
            ->latest()
            ->get();

        $totalOrders = ProductOrder::whereIn('country_id', $allowedCountryIds)->count();

        $totalProductAmount = ProductOrder::whereIn('country_id', $allowedCountryIds)
            ->sum('subtotal');

        $totalAmount = ProductOrder::whereIn('country_id', $allowedCountryIds)
            ->sum('total_amount');

        $totalProducts = DB::table('product_order_items')
            ->join('product_orders', 'product_orders.id', '=', 'product_order_items.order_id')
            ->whereIn('product_orders.country_id', $allowedCountryIds)
            ->sum('product_order_items.quantity') ?? 0;

        return view('pages.all_orders', compact(
            'productOrders',
            'totalOrders',
            'totalProductAmount',
            'totalAmount',
            'totalProducts'
        ));
    }

    /**
     * Shiprocket order list. Syncs only Shiprocket-owned fields (status text,
     * delivered date, return window) onto the latest tracking row per order.
     * product_tracking.delivery_status is the dashboard's own pipeline code
     * (0 placed .. 4 delivered, 5 cancelled) and is never overwritten with
     * Shiprocket's status_code, which uses a different numbering.
     */
    public function orderStat() {
        $finale = [];

        try {
            $token = app(\App\Services\ShiprocketService::class)->getToken();
            $baseUrl = rtrim(config('services.shiprocket.base_url', 'https://apiv2.shiprocket.in/v1/external'), '/');
            $altresponse = \Illuminate\Support\Facades\Http::withToken($token)
                ->timeout(20)
                ->get($baseUrl . '/orders')
                ->object();
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Shiprocket order list failed: ' . $e->getMessage());
            $altresponse = null;
        }

        if ( isset( $altresponse->data ) && is_array( $altresponse->data ) ) {
            $finale = $altresponse->data;
            foreach ( $altresponse->data as $channelOrder ) {
                $channel_order_id = $channelOrder->channel_order_id ?? null;
                if ( ! $channel_order_id ) {
                    continue;
                }

                $update = [ 'status' => $channelOrder->status ?? null ];

                $channel_delivered_date = $channelOrder->delivered_date ?? null;
                if ( ! empty( $channel_delivered_date ) && strtotime( $channel_delivered_date ) ) {
                    $prod = DB::table( 'product_slots' )->where( 'order_id', $channel_order_id )->first();
                    $product = $prod ? DB::table( 'products' )->where( 'id', $prod->product_id )->first() : null;
                    $approvalDays = (int) ( $product->approval_days ?? 7 );
                    $update['delivered_date'] = date( 'Y-m-d', strtotime( $channel_delivered_date ) );
                    $update['return_approval_date'] = date( 'Y-m-d', strtotime( $channel_delivered_date . ' +' . $approvalDays . ' days' ) );
                }

                $latestId = DB::table( 'product_tracking' )->where( 'order_id', $channel_order_id )->max( 'id' );
                if ( $latestId ) {
                    DB::table( 'product_tracking' )->where( 'id', $latestId )->update( $update );
                }
            }
        }

        return view( 'pages.product_orders1', compact( 'finale' ) );
    }

        /**
        * Show the form for creating a new resource.
        *
        * @return \Illuminate\Http\Response
        */

        public function create() {
            //
        }

        /**
        * Store a newly created resource in storage.
        *
        * @param  \Illuminate\Http\Request  $request
        * @return \Illuminate\Http\Response
        */

        public function store( Request $request ) {
            //
        }

        /**
        * Display the specified resource.
        *
        * @param  int  $id
        * @return \Illuminate\Http\Response
        */

        public function show( $id ) {
            //
        }

        /**
        * Show the form for editing the specified resource.
        *
        * @param  int  $id
        * @return \Illuminate\Http\Response
        */

        public function edit( $id ) {
            //
        }

        /**
        * Update the specified resource in storage.
        *
        * @param  \Illuminate\Http\Request  $request
        * @param  int  $id
        * @return \Illuminate\Http\Response
        */

        public function update( Request $request, $id ) {
            //
        }

        /**
        * Remove the specified resource from storage.
        *
        * @param  int  $id
        * @return \Illuminate\Http\Response
        */

        public function destroy( $id ) {
            //
        }

        public function productOrderDeliveryAssign( Request $request ) {
            $orderId = $request->order_id;
            $deliveryPersonId =  $request->deliver_id;
            $productOrder =   ProductOrder::query()->where( 'order_id', $orderId )->first();
            $productSlot =  ProductSlot::query()->where( 'order_id', $orderId );

            if ( !$productOrder ) {
                return errorResponse( 'Order Id Not found' );
            }

            $productOrder->update( [
                'delivery_person_id' => $deliveryPersonId,
                'is_delivery_assigned' => 1
            ] );

            $productSlot->update( [
                'deliver_person_id' => $deliveryPersonId
            ] );

            $productOrders =  ProductOrder::query()->with( 'product', 'orderAddress.area', 'customer' )->where( 'payment_status', 'paid' )->where( 'status', 'pending' )->get();

            return response()->json( [
                'message' => 'Deivery Person assigned Successfully',
                'productOrders' => $productOrders
            ] );
        }

        public function updatestatus(Request $request)
        {
            $request->validate([
                'order_id' => 'required',
                'select_status' => 'required',
            ]);

            $order = ProductOrder::where(
                'order_id',
                $request->order_id
            )->first();

            if (!$order) {

                return response()->json([
                    'message' => 'Order not found'
                ], 404);
            }

            $updateData = [
                'updated_at' => now(),
            ];

           

            if ($request->select_status == 1) {

                $updateData['delivery_status'] = 1;
                $updateData['status'] = 'processing';

                $updateData['reason'] = null;
                $updateData['unavailable_count'] = null;
                $updateData['unavailable_amount'] = null;
            }

           

            if ($request->select_status == 'unavailable_stock') {

                // Move remaining stock to processing
                $updateData['delivery_status'] = 1;

                // IMPORTANT
                $updateData['status'] = 'processing';

                $updateData['reason']
                    = $request->reason;

                $updateData['unavailable_count']
                    = $request->unavailable_count;

                $updateData['unavailable_amount']
                    = $request->unavailable_amount;
            }


            if ($request->select_status == 'cancel') {

                $order->restoreStock();

                $updateData['status'] = 'cancelled';

                $updateData['reason']
                    = $request->reason;

                $updateData['delivery_status'] = 0;
            }

           

            if ($request->select_status == 'return') {

                $updateData['status'] = 'return';

                $updateData['reason']
                    = $request->reason;
            }

            $order->update($updateData);

            $productOrders = ProductOrder::with(
                'product',
                'orderAddress.area',
                'customer',
                'country'
            )
            ->withSum('items as total_quantity', 'quantity')
            ->latest()
            ->get();

            return response()->json([
                'status' => true,
                'message' => 'Status updated successfully',
                'productOrders' => $productOrders
            ]);
        }

                
        public function specialOrders()
        {
            $allowedCountryIds = $this->getAllowedCountryIds();

            $specialOrders = ProductOrder::with(
                    'orderAddress.area',
                    'customer',
                    'country'
                )
                ->withSum('items as total_quantity', 'quantity')
                ->whereIn('country_id', $allowedCountryIds)
                ->where(function ($query) {
                    $query->whereIn('status', ['cancelled', 'return'])
                        ->orWhereNotNull('unavailable_count')
                        ->orWhere('unavailable_count', '>', 0);
                })
                ->latest()
                ->get();

            $cancelledCount = $specialOrders->where('status', 'cancelled')->count();

            $returnCount = $specialOrders->where('status', 'return')->count();

            $unavailableCount = $specialOrders->whereNotNull('unavailable_count')
                ->where('unavailable_count', '>', 0)
                ->count();

            $totalUnavailableAmount = $specialOrders->sum('unavailable_amount');

            return view('pages.other_orders', compact(
                'specialOrders',
                'cancelledCount',
                'returnCount',
                'unavailableCount',
                'totalUnavailableAmount'
            ));
        }
    }
