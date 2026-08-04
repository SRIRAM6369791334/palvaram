<?php

namespace App\Http\Controllers;

use App\Mail\OrderStatusUpdated;
use App\Models\ProductOrder;
use App\Models\ProductRefund;
use App\Models\ProductTracking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

// use App\Http\Controllers\SendSms;

class PackingOrderController extends Controller
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

        $productPackings = ProductOrder::query()
            ->with(
                "product",
                "orderAddress.area",
                "customer",
                "country"
            )
            ->where("status", "processing")
            ->whereIn('country_id', $allowedCountryIds)
            ->latest()
            ->get();

        return view("pages.product_packing", compact("productPackings"));
    }

// update status
public function updatepacking(Request $request)
{

    $order_id = $request->order_id;
    $status_num = $request->select_status;
    $custometid = $request->user_id;
    $numbercus = $request->phone_number;

    // Map numeric status back to string
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
            "delivery_status" => $status_num // slots might still use numbers
        ]);

    $productPackings_track = new ProductTracking();
    $productPackings_track->order_id = $order_id;
    $productPackings_track->delivery_status = $status_num;
    $productPackings_track->user_id = $custometid;

    $productPackings_track->save();

    $productPackings =  ProductOrder::query()->with("product", "orderAddress.area", "customer")->where("payment_status", "paid")->where("status", "processing")->get();

    //  $order = ProductOrder::with("customer")->where("order_id", $order_id)->first();
    $order = ProductOrder::with([
    'customer',
    'orderAddress' => function ($query) {
        $query->where('address_type_name', 'billing');
    }
])->where('order_id', $order_id)->first();

        if ($order && $order->customer && !empty($order->customer->email)) {
            try {
                Mail::to($order->customer->email)->send(new OrderStatusUpdated($order, $status));
            } catch (\Exception $e) {
                \Log::error("Email failed for order $order_id: " . $e->getMessage());
            }
        }
    return response()->json([
        "message" => "Status update successfully",
        "productPackings" => $productPackings
    ]);




}

public function updaterefund1(Request $request)
    {

        $order_id = $request->order_id;

        $custometid = $request->user_id;

        DB::table('product_orders')
            ->where('order_id', $order_id)
            ->update([
                "is_cancelled" => 1
            ]);

           DB::table('product_slots')
            ->where('order_id', $order_id)
            ->update([
                "is_cancelled" => 1
            ]);

          $order =  DB::table('product_slots')
            ->where('order_id', $order_id)
            ->get();



            foreach ($order as $ord) {

                $getstock =  DB::table('productstocks')->where("productid",$ord->product_id)->where('pro_ver_id',$ord->product_varient_id)->first();

                DB::table('productstocks')->where("productid",$ord->product_id)->where('pro_ver_id',$ord->product_varient_id)->update([
                    "overallstock" => $getstock->overallstock+$ord->quantity,
                    "availablestock" => $getstock->availablestock+$ord->quantity,
                    "salestock" => $getstock->salestock-$ord->quantity,


                ]);
            }

            $productSlot = DB::table('product_slots')
            ->where('order_id', $order_id)
            ->first();

            $productSlotId = $productSlot ? $productSlot->id : null;

            ProductRefund::create([
                'order_id' => $order_id, 'slot_id' => $productSlotId, 'cancelled_by' => "Admin", 'refund_status' => 0
            ]);


        $productPackings =  ProductOrder::query()->with("product", "orderAddress.area", "customer")->where("payment_status", 'paid')->where("status", 'processing')->where("is_cancelled", "!=", 1)->get();

        return response()->json([
            "message" => "Status update successfully",
                "productPackings" => $productPackings
        ]);
    }

}
