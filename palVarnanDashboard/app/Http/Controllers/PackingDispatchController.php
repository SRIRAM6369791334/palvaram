<?php

namespace App\Http\Controllers;

use App\Mail\OrderStatusUpdated;
use App\Models\ProductOrder;
use App\Models\ProductRefund;
use App\Models\ProductTracking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

// use App\Http\Controllers\SendSms; // Use autoloader if possible

class PackingDispatchController extends Controller
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

        $productDispaths = ProductOrder::query()
            ->with(
                "product",
                "orderAddress.area",
                "customer",
                "country"
            )
            ->where("status", "shipped")
            ->whereIn('country_id', $allowedCountryIds)
            ->latest()
            ->get();

        return view("pages.product_dispatch", compact("productDispaths"));
    }

    // update status
public function updatedispach(Request $request)
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
            "status" => $status,
            "delivery_person_name" => $request->delivery_person_name,
            "delivery_person_phone" => $request->delivery_person_phone,
        ]);

    DB::table('product_slots')
        ->where('order_id', $order_id)
        ->update([
            "delivery_status" => $status_num
        ]);

    $productDispaths_track = new ProductTracking();
    $productDispaths_track->order_id = $order_id;
    $productDispaths_track->delivery_status = $status_num;
    $productDispaths_track->user_id = $custometid;

    $productDispaths_track->save();

    $productDispaths =  ProductOrder::query()->with("product", "orderAddress.area", "customer")->where("payment_status", "paid")->where("status", "shipped")->get();

    //  $order = ProductOrder::with("customer")->where("order_id", $order_id)->first();
    $order = ProductOrder::with([
    'customer',
    'orderAddress' => function ($query) {
        $query->where('address_type_name', 'shipping'); // In new structure, it's just 'shipping' from migration
    }
])->where('order_id', $order_id)->first();

        if ($order && $order->customer && !empty($order->customer->email)) {
            try {
                Mail::to($order->customer->email)->send(new OrderStatusUpdated($order, $status_num));
            } catch (\Exception $e) {
                // Log the error but don't fail the request
                \Log::error("Email failed for order $order_id: " . $e->getMessage());
            }
        }

    return response()->json([
        "message" => "Status update successfully",
            "productDispaths" => $productDispaths
    ]);




}

public function updaterefund2(Request $request)
    {

        $order_id = $request->order_id;

        $custometid = $request->user_id;

        DB::table('product_orders')
            ->where('order_id', $order_id)
            ->update([
                "status" => "cancelled"
            ]);

           DB::table('product_slots')
            ->where('order_id', $order_id)
            ->update([
                "is_cancelled" => 1
            ]);

            $productSlot = DB::table('product_slots')
            ->where('order_id', $order_id)
            ->first();

            $productSlotId = $productSlot ? $productSlot->id : null;

            ProductRefund::create([
                'order_id' => $order_id, 'slot_id' => $productSlotId, 'cancelled_by' => "Admin", 'refund_status' => 0
            ]);


        $productDispaths =  ProductOrder::query()->with("product", "orderAddress.area", "customer")->where("payment_status", "paid")->where("status", "shipped")->get();

        return response()->json([
            "message" => "Status update successfully",
                "productDispaths" => $productDispaths
        ]);
    }
}