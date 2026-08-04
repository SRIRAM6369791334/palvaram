<?php

namespace App\Http\Controllers;

use App\Models\MilkRefund;
use App\Models\MilkSlot;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MilkRefundController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $milkRefunds =  MilkRefund::all();
        return view("pages.milk_refunds", compact("milkRefunds"));
    }



    public function getRefundDatas(Request $request)
    {
        $refundId = $request->refund_id;

        $currentMilkSlot   =  MilkRefund::with("milk_order.customer", "milk_order.product")->find($refundId);


        return response($currentMilkSlot);
    }


    public function refundMilkSlot(Request $request)
    {
        $refundId = $request->refund_id;

        MilkRefund::find($refundId)->update([
            "refund_status" => 1,
            "updated_at" => now()
        ]);
        $milkRefunds = MilkRefund::all();
        return response()->json([
            "message" => "MIlk refund Successfull",
            "milkRefunds" => $milkRefunds
        ]);
    }
}
