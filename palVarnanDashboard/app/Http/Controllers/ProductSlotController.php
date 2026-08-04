<?php

namespace App\Http\Controllers;

use App\Models\MilkSlot;
use App\Models\ProductOrder;
use App\Models\ProductRefund;
use App\Models\ProductSlot;
use App\Models\ProductVarient;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductSlotController extends Controller
{
    public function getProductSlots($orderId)
    {
        $productSlots = ProductSlot::query()->with("order.customer.user_addresses", "product", "productVarient")->where("order_id", $orderId)->get()->map(function ($item) {
            $item->delivery_date = Carbon::parse($item->delivery_date)->format('d-M-Y');
            return $item;
        });


        return view("pages.product_slots", compact("productSlots"));
    }

    public function getProductSlotss($orderId)
    {
        $productSlotss = ProductSlot::query()->with("order.customer.user_addresses", "product", "productVarient")->where("order_id", $orderId)->get()->map(function ($item) {
            $item->delivery_date = Carbon::parse($item->delivery_date)->format('d-M-Y');
            return $item;
        });





        return view("pages.orderslot", compact("productSlotss"));
    }


    public function cancelProductSlot(Request $request)
    {
        $productSlotId =   $request->product_slot_id;
        // $reasonForCancel = $request->reason_for_cancel;

        $productSlot = ProductSlot::findOrFail($productSlotId);

        $orderId = $productSlot->order_id;
        $prover_id = $productSlot->product_varient_id;
        $proid = $productSlot->product_id;
        $quet = $productSlot->quantity;




        ProductRefund::create([
            'order_id' => $orderId, 'slot_id' => $productSlotId, 'cancelled_by' => "Admin", 'refund_status' => 0
        ]);


        $productscount = ProductSlot::where('order_id', $orderId)->where('is_cancelled', 2)->count();

        if ($productscount > 0) {
            // If any product is canceled, update ProductSlot and check if all products are canceled
            $productSlot->update([
                "is_cancelled" => 1,
                "delivery_status" => 5,
                // "cancel_reason" => $reasonForCancel
            ]);

            $totalProductsCount = ProductSlot::where('order_id', $orderId)->count();
            $cancelledProductsCount = ProductSlot::where('order_id', $orderId)->where('is_cancelled', 1)->count();

            if ($cancelledProductsCount == $totalProductsCount) {
                // If all products are canceled, update ProductOrder
                ProductOrder::where("order_id", $orderId)->update([
                    "status" => 'cancelled',
                ]);
            }
        }


        $product =  DB::table('productstocks')->where("productid", $proid)->where('pro_ver_id', $prover_id)->first();



        DB::table('productstocks')->where("productid", $proid)->where('pro_ver_id', $prover_id)->update([
            "overallstock" => $product->overallstock + $quet,
            "availablestock" => $product->availablestock + $quet,
            "salestock" => $product->salestock - $quet,


        ]);

        $provaer = ProductVarient::where('product_id', $proid)->where('id', $prover_id)->first();
        ProductVarient::where('product_id', $proid)->where('id', $prover_id)->update([
            "product_qty" => $provaer->product_qty + $quet,
        ]);



        $productSlots = ProductSlot::query()->with("order.customer.user_addresses", "product", "productVarient")->where("order_id", $orderId)->get()->map(function ($item) {
            $item->delivery_date = Carbon::parse($item->delivery_date)->format('d-M-Y');
            return $item;
        });

        return response()->json([
            "message" => "Category Added Successfully",
            "productSlots" => $productSlots
        ]);
    }

    public function cancelrequests(){
        $requests = DB::table('product_orders')->where('status', 'cancelled')->get();
        return view("pages.cancelproduct", compact("requests"));
    }

    public function approverequest(Request $request){

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
            CURLOPT_POSTFIELDS =>'{"email": "thiruvenkatesh.sts@gmail.com","password": "Thiru@123"}',
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

        // GETTING ORDER_ID FROM AJAX
        $order_id = $request->order_id;
        $shiprocket_order_id = DB::table('product_tracking')->where('order_id',$order_id)->first();

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://apiv2.shiprocket.in/v1/external/orders/cancel',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>'{
        "ids": ['.$shiprocket_order_id->shiprocket_order_id.']
        }',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Authorization: Bearer'.$token
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        DB::table('product_orders')->where('order_id',$order_id)->update(['status'=> 'cancelled']);
        DB::table('product_tracking')->where('order_id',$order_id)->update(['tracking_url'=> 0]);

        $pendindRequests = DB::table('product_orders')->where('status', 'cancelled')->get();

        return response()->json([
            "message" => "Order cancelled Successfully",
            "pendingRequests" => $pendindRequests
        ]);
    }

    // VIEWING THE RETURN REQUESTS
    public function returnrequests(){
        $requests = DB::table('product_tracking')->where('return_requested',1)->get();
        return view("pages.return_product", compact("requests"));
    }
    
    // APPROVE RETURN REQUESTS 
    public function approveReturnRequest(Request $request){

        // SHIPROCKET LOGIN
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
            CURLOPT_POSTFIELDS =>'{"email": "thiruvenkatesh.sts@gmail.com","password": "Thiru@123"}',
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

        $order_id = $request->order_id;
        $order = DB::table('product_tracking')->where('order_id',$order_id)->get();
        $sip = $order->shiprocket_order_id;

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://apiv2.shiprocket.in/v1/external/orders/show/$sip",
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
        ));

        $response = curl_exec($curl);

        $data = json_decode($response);
        $channel_id = $data['data']['channel_id'];
        dd($channel_id);
        curl_close($curl);

        
        $product_order = DB::table('product_orders')->where('order_id',$order_id)->get();
        $product_order_address = DB::table('product_order_user_addresses')->where('order_id',$order_id)->get();
        $user =  DB::table('users')->where('user_id',$order->user_id)->get();

        $get_product = DB::table( 'product_slots' )
            ->join( 'product_orders', 'product_orders.order_id', '=', 'product_slots.order_id' )
            ->join( 'products', 'products.id', '=', 'product_slots.product_id' )
            ->join( 'product_varient', 'product_varient.id', '=', 'product_slots.product_varient_id' )
            ->select( 'product_slots.*', 'product_varient.*', 'product_orders.*', 'products.*', 'products.id as ogid' )
            ->where( 'product_slots.order_id', $order_id )->get();

             $html = '';

            // dd( $get_total );

            // store multiple products for shiprocket

            foreach ( $get_product as $product ) {
                $html .= '{
                    "name": "' . $product->product_name . '",
                    "qc_enable":true,
                    "qc_product_name": "' . $product->product_name . '",
                    "sku": "' . $product->ogid . '",
                    "units": ' . $product->quantity . ',
                    "selling_price": "' . $product->offer_price . '",
                    "discount": "",
                    "qc_brand":"Levi",
                    "qc_product_image":""
                },';
            }

            $html = rtrim( $html, ',' );

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://apiv2.shiprocket.in/v1/external/orders/create/return',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>'{
            "order_id": "'.$order->shiprocket_order_id.'",
            "order_date": "'.$product_order->date_ordered_on->format('Y-m-d').'",
            "channel_id": "'.$channel_id.'",
            "pickup_customer_name": "'.$user->name.'",
            "pickup_last_name": "",
            "company_name":"",
            "pickup_address": "'.$product_order_address->address_line_one.'",
            "pickup_address_2": "",
            "pickup_city": "'.$product_order_address->city.'",
            "pickup_state": "'.$product_order_address->state.'",
            "pickup_country": "India",
            "pickup_pincode": '.$product_order_address->pincode.',
            "pickup_email": "'.$user->email.'",
            "pickup_phone": "'.$user->phone_number.'",
            "pickup_isd_code": "91",
            "shipping_customer_name": "Jax", 
            "shipping_last_name": "Doe",
            "shipping_address": "Castle",
            "shipping_address_2": "Bridge",
            "shipping_city": "ghaziabad",
            "shipping_country": "India",
            "shipping_pincode": 201005,
            "shipping_state": "Uttarpardesh",
            "shipping_email": "kumar.abhishek@shiprocket.com",
            "shipping_isd_code": "91",
            "shipping_phone": 8888888888,

            
            "order_items": [
                '.$html.'
            ],
            "payment_method": "PREPAID",
            "total_discount": "0",
            "sub_total": '.$product_order->grand_total_amount.',
            "length": 11,
            "breadth": 11,
            "height": 11,
            "weight": 0.5
            }',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Bearer'.$token,
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        // GENERATING SEPARATE AWB CODE FOR RETURN ORDERS

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://apiv2.shiprocket.in/v1/external/courier/assign/awb',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>'{
            "shipment_id": "'.$order->shiprocket_shipment_id.'",
            "courier_id": "",
            "status": "",
            "is_return": "1"
            }',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

    }

    // REJECT RETURN REQUESTS
    public function rejectReturnRequests(Request $request){
        $order_id = $request->order_id;
        DB::table('product_tracking')->where('order_id',$order_id)->update(['return_requested'=> 3]);

        $requests = DB::table('product_tracking')->where('return_requested',1)->get();

        return response()->json([
            "message" => "Return Rejected Successfully",
            "requests" => $requests
        ]);
    }
}