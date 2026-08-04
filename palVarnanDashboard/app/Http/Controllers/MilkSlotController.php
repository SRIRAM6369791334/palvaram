<?php

namespace App\Http\Controllers;

use App\Models\MilkRefund;
use App\Models\MilkSlot;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MilkSlotController extends Controller
{
    public function getMilkSlots($orderId)
    {


        $milkSlots = MilkSlot::query()->with("order.customer.area", "order.product")->where("order_id", $orderId)->get()->map(function ($item) {
            $item->delivery_date = Carbon::parse($item->delivery_date)->format('d-M-Y');
            return $item;
        });

        return view("pages.milk_slots", compact("milkSlots"));
    }


    public function cancelMilkSlot(Request $request)
    {
        $milkSlotId =   $request->milk_slot_id;
        $reasonForCancel = $request->reason_for_cancel;

        $milkSlot = MilkSlot::findOrFail($milkSlotId);

        $orderId = $milkSlot->order_id;

        MilkRefund::create([
            'order_id' => $orderId, 'slot_id' => $milkSlotId, 'cancelled_by' => "Admin", 'refund_status' => 0
        ]);
        $milkSlot->update([
            "is_cancelled" => 1,
            "delivery_status" => 2,
            "cancel_reason" => $reasonForCancel
        ]);

        $milkSlots = MilkSlot::query()->with("order.customer.area", "order.product")->where("order_id", $orderId)->get()->map(function ($item) {
            $item->delivery_date = Carbon::parse($item->delivery_date)->format('d-M-Y');
            return $item;
        });


        return response()->json([
            "message" => "Category Added Successfully",
            "milkSlots" => $milkSlots
        ]);
    }
}
