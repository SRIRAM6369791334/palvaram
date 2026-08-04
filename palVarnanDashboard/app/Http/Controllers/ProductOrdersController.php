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

        $productOrders = ProductOrder::query()
            ->with(
                'product',
                'orderAddress.area',
                'customer',
                'country'
            )
            ->where('status', 'pending')
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

    public function orderStat() {
        $curl = curl_init();
        curl_setopt_array( $curl, array(
            CURLOPT_URL => 'https://apiv2.shiprocket.in/v1/external/auth/login',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>'{"email": "blokert5320@gmail.com","password": "Gokulnath@123"}',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ) );
        $SR_login_Response = curl_exec( $curl );
        curl_close( $curl );
        $SR_login_Response_out = json_decode( $SR_login_Response );
        $token = $SR_login_Response_out-> {
            'token'}
            ;

            $curl = curl_init();

            curl_setopt_array( $curl, array(
                CURLOPT_URL => 'https://apiv2.shiprocket.in/v1/external/orders',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    'Authorization: Bearer'.$token,
                ),
            ) );

            $response = curl_exec( $curl );
            curl_close( $curl );

            $altresponse = json_decode( $response );
            // $channelOrderId = $altresponse[ 'data' ][ 0 ][ 'channel_order_id' ];
            // $channelOrders = $altresponse->data[ 0 ];
            if ( isset( $altresponse->data ) && is_array( $altresponse->data ) ) {
                foreach ( $altresponse->data as $index => $channelOrder ) {
                    $channel_order_id = $channelOrder->channel_order_id;
                    $channel_customer_name =  $channelOrder->customer_name;
                    $channel_customer_email = $channelOrder->customer_email;
                    $channel_customer_phone = $channelOrder->customer_phone;
                    $channel_total = $channelOrder->total;
                    $channel_status = $channelOrder->status;
                    $channel_status_code = $channelOrder->status_code;
                    $channel_delivered_date = $channelOrder->delivered_date;
                    // Add 7 days to the delivered date using strtotime
                    $prod = DB::table( 'product_slots' )->where( 'order_id', $channel_order_id )->first();
                    $prodid = DB::table( 'products' )->where( 'id', $prod->product_id )->first();
                    // $return_approval_timestamp = strtotime( $channel_delivered_date . + $prodid->approval_days );
                    $return_approval_timestamp = strtotime( $channel_delivered_date . '+' . $prodid->approval_days . ' days' );
                    // Convert the timestamp back to a date string
                    $return_approval_date = date( 'Y-m-d', $return_approval_timestamp );

                    DB::table( 'product_tracking' )->where( 'order_id', $channel_order_id )->update( [ 'delivery_status'=>$channel_status_code, 'status'=>$channel_status, 'delivered_date'=>$channel_delivered_date, 'return_approval_date'=>$return_approval_date ] );
                }
            } else {
                echo 'No orders found.';
            }
            $finale = $altresponse->data;
            // dd( $finale );

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
