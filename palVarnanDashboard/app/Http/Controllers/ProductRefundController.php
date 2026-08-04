<?php

namespace App\Http\Controllers;

use App\Models\ProductRefund;
use Illuminate\Http\Request;

class ProductRefundController extends Controller
{
    public function index()
    {

        $productRefunds =  ProductRefund::all();
        return view("pages.product_refunds", compact("productRefunds"));
    }

    public function getRefundDatas(Request $request)
    {
        $refundId = $request->refund_id;

        $currentProductSlot   =  ProductRefund::with("product_order.customer", "product_slot.product","product_slot.productVarient")->find($refundId);


        return response($currentProductSlot);
    }


    public function refundProductSlot(Request $request)
    {
        $refundId = $request->refund_id;

        ProductRefund::find($refundId)->update([
            "refund_status" => 1,
            "updated_at" => now()
        ]);
        $productRefunds = ProductRefund::all();
        return response()->json([
            "message" => "Product refund Successfull",
            "productRefunds" => $productRefunds
        ]);
    }
}
