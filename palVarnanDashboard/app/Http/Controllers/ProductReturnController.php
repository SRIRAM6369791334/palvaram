<?php

namespace App\Http\Controllers;

use App\Models\ProductOrder;
use App\Models\ProductTracking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductReturnController extends Controller
{
    public function index(){
        $productReturns =  ProductOrder::query()->with("product", "orderAddress.area", "customer")->where("payment_status", "paid") ->where("status", "return")->get();

        return view("pages.return_product", compact("productReturns"));
    }
    public function update(Request $request){
        $order_id = $request->order_id;
        $status_num = $request->select_status;
        $custometid = $request->user_id;
        $numcusdata = $request->phone_number;

        $status_map = [
            '1' => 'processing',
            '2' => 'shipped',
            '3' => 'out_for_delivery',
            '4' => 'delivered',
            '6' => 'return',
        ];
        $status = $status_map[$status_num] ?? $status_num;

        DB::table('product_orders')
            ->where('order_id', $order_id)
            ->update([
                "status" => $status
            ]);

        DB::table('product_slots')
            ->where('order_id', $order_id)
            ->update([
                "delivery_status" => $status_num
            ]);

        $productReturns_track = new ProductTracking();
        $productReturns_track->order_id = $order_id;
        $productReturns_track->delivery_status = $status_num;
        $productReturns_track->user_id = $custometid;

        $productReturns_track->save();

        $productReturns =  ProductOrder::query()->with("product", "orderAddress.area", "customer")->where("payment_status", "paid")->where("status", "return")->get();

        return response()->json([
            "message" => "Status update successfully",
                "productReturns" => $productReturns
        ]);
    }


    public function updateed(Request $request){
        $order_id = $request->order_id;
        $status_num = $request->select_status;
        $custometid = $request->user_id;
        $numcusdata = $request->phone_number;

        $status_map = [
            '1' => 'processing',
            '2' => 'shipped',
            '3' => 'out_for_delivery',
            '4' => 'delivered',
            '6' => 'return',
        ];
        $status = $status_map[$status_num] ?? $status_num;

        if($status_num == 3){
            DB::table('product_orders')
            ->where('order_id', $order_id)
            ->update([
                "status" => "out_for_delivery"
            ]);

        DB::table('product_slots')
            ->where('order_id', $order_id)
            ->update([
                "delivery_status" => $status_num
            ]);

        $productReturns_track = new ProductTracking();
        $productReturns_track->order_id = $order_id;
        $productReturns_track->delivery_status = $status_num;
        $productReturns_track->user_id = $custometid;

        $productReturns_track->save();
        $productReturns =  ProductOrder::query()->with("product", "orderAddress.area", "customer")->where("payment_status", "paid")->where("status", "return")->get();

        return response()->json([
            "message" => "Status update successfully",
                "productReturns" => $productReturns
        ]);
        }else{
            DB::table('product_orders')
            ->where('order_id', $order_id)
            ->update([
                "status" => $status,
                "payment_status" => "failed" // Was 3 before
            ]);

        DB::table('product_slots')
            ->where('order_id', $order_id)
            ->update([
                "delivery_status" => $status_num
            ]);

        $productReturns_track = new ProductTracking();
        $productReturns_track->order_id = $order_id;
        $productReturns_track->delivery_status = $status_num;
        $productReturns_track->user_id = $custometid;

        $productReturns_track->save();

        $productReturns =  ProductOrder::query()->with("product", "orderAddress.area", "customer")->where("payment_status", "paid")->where("status", "return")->get();

        return response()->json([
            "message" => "Status update successfully",
                "productReturns" => $productReturns
        ]);

        }





    }
}
