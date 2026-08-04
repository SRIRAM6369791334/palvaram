<?php

namespace App\Http\Controllers;

use App\Mail\OrderStatusUpdated;
use App\Models\ProductOrder;
use App\Models\ProductTracking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

// use App\Http\Controllers\SendSms;

class PackingDeliveryController extends Controller
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

        $productDeliverys = ProductOrder::query()
            ->with(
                "product",
                "orderAddress.area",
                "customer",
                "country"
            )
            ->where("status", "out_for_delivery")
            ->whereIn('country_id', $allowedCountryIds)
            ->latest()
            ->get();

        return view("pages.product_delivery", compact("productDeliverys"));
    }

    public function updatedelive(Request $request)
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
                "delivery_status" => $status_num
            ]);

        $productDeliverys_track = new ProductTracking();
        $productDeliverys_track->order_id = $order_id;
        $productDeliverys_track->delivery_status = $status_num;
        $productDeliverys_track->user_id = $custometid;

        $productDeliverys_track->save();

        $productDeliverys =  ProductOrder::query()->with("product", "orderAddress.area", "customer")->where("payment_status", "paid")->where("status", "out_for_delivery")->get();

        return response()->json([
            "message" => "Status update successfully",
            "productDeliverys" => $productDeliverys
        ]);
    }


    public function collectdelive(Request $request)
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

        if ($status_num == 6) {
            DB::table('product_orders')
                ->where('order_id', $order_id)
                ->update([
                    "status" => "return"
                ]);

            DB::table('product_slots')
                ->where('order_id', $order_id)
                ->update([
                    "delivery_status" => $status_num
                ]);

            $productDeliverys_track = new ProductTracking();
            $productDeliverys_track->order_id = $order_id;
            $productDeliverys_track->delivery_status = $status_num;
            $productDeliverys_track->user_id = $custometid;

            $productDeliverys_track->save();

            $productDeliverys =  ProductOrder::query()->with("product", "orderAddress.area", "customer")->where("payment_status", "paid")->where("status", "out_for_delivery")->get();

            return response()->json([
                "message" => "Status update successfully",
                "productDeliverys" => $productDeliverys
            ]);
        } else {
            DB::table('product_orders')
                ->where('order_id', $order_id)
                ->update([
                    "status" => $status,
                ]);

            DB::table('product_slots')
                ->where('order_id', $order_id)
                ->update([
                    "delivery_status" => $status_num
                ]);

            $productDeliverys_track = new ProductTracking();
            $productDeliverys_track->order_id = $order_id;
            $productDeliverys_track->delivery_status = $status_num;
            $productDeliverys_track->user_id = $custometid;

            $productDeliverys_track->save();

            $productDeliverys =  ProductOrder::query()->with("product", "orderAddress.area", "customer")->where("payment_status", "paid")->where("status", "out_for_delivery")->get();

            $order = ProductOrder::with([
                'customer',
                'orderAddress' => function ($query) {
                    $query->where('address_type_name', 'shipping');
                }
            ])->where('order_id', $order_id)->first();

            if ($order && $order->customer && !empty($order->customer->email)) {
                try {
                    Mail::to($order->customer->email)->send(new OrderStatusUpdated($order, $status_num));
                } catch (\Exception $e) {
                    \Log::error("Email failed for order $order_id: " . $e->getMessage());
                }
            }

            return response()->json([
                "message" => "Status update successfully",
                "productDeliverys" => $productDeliverys
            ]);
        }
    }
}
