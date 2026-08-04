<?php

namespace App\Http\Controllers;

use App\Models\AreaAssign;
use App\Models\MilkOrder;
use App\Models\MilkRefund;
use App\Models\MilkSlot;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MilkOrdersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $milkOrders =  MilkOrder::query()->with("product", "orderAddress.area", "customer")->where("payment_status", 1)->get();


        return view("pages.milk_orders", compact("milkOrders"));
    }


    public function getMilkSlots($orderId)
    {
        $milkSlots = MilkSlot::query()->with("order.customer.area", "order.product")->where("order_id", $orderId)->get();

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
            "cancel_reason" => $reasonForCancel
        ]);

        $milkSlots = MilkSlot::query()->with("order.customer.area", "order.product")->where("order_id", $orderId)->get();


        return response()->json([
            "message" => "Category Added Successfully",
            "milkSlots" => $milkSlots
        ]);
    }



    public function  getAreaAssignedDelvieryPerson($areaId)
    {
        $delvieryPersons = AreaAssign::with("deliveryPerson")->where("area_id", $areaId)->get();

        return view("ajaxPages.milk_order_delivery_person", compact("delvieryPersons"));
    }

    public function milkOrderDeliveryAssign(Request $request)
    {
        $orderId = $request->order_id;
        $deliveryPersonId =  $request->deliver_id;
        $milkOrder =   MilkOrder::query()->where("order_id", $orderId)->first();
        $milkSlot =  MilkSlot::query()->where("order_id", $orderId);

        if (!$milkOrder) {
            return errorResponse("Order Id Not found");
        }

        $milkOrder->update([
            "delivery_person_id" => $deliveryPersonId,
            "is_delivery_assigned" => 1
        ]);

        $milkSlot->update([
            "deliver_person_id" => $deliveryPersonId
        ]);


        $milkOrders =  MilkOrder::query()->with("product", "orderAddress.area", "customer")->where("payment_status", 1)->get();

        return response()->json([
            "message" => "Category Added Successfully",
            "milkOrders" => $milkOrders
        ]);
    }
}
