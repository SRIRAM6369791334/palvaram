<?php

namespace App\Http\Controllers;

use App\Models\ProductOrder;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OrderSummeryController extends Controller
{
    public function index()
    {

        $ordersummerys =  ProductOrder::query()->with("product", "orderAddress.area", "customer", "country")->where("payment_status", "paid")->get();

        return view("pages.order_summery", compact("ordersummerys"));
    }



    public function getoversummery(Request $request)
    {

        $status_num = $request->delivery_status;
        $status_map = [
            '0' => 'pending',
            '1' => 'processing',
            '2' => 'shipped',
            '3' => 'out_for_delivery',
            '4' => 'delivered',
            '6' => 'return',
        ];
        $status = $status_map[$status_num] ?? $status_num;

        $fromDate = Carbon::parse($request->input('frdate'))->startOfDay();
        $toDate = Carbon::parse($request->input('todate'))->endOfDay();


        $ordersummerys = ProductOrder::query()->with("product", "orderAddress.area", "customer")
            ->where("payment_status", "paid")
            ->where('status', $status)
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->get();



        $data = [
            'ordersummerys' => $ordersummerys,
            'i' => 1,
        ];


        return $data;
    }
}
