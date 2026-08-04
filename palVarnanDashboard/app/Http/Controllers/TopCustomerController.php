<?php

namespace App\Http\Controllers;

use App\Models\ProductOrder;
use Illuminate\Http\Request;

class TopCustomerController extends Controller
{
    public function index(){

        $topcustomers = ProductOrder::selectRaw('users.name, product_orders.user_id, SUM(product_orders.total_amount) as total_amount')
        ->join('users', 'product_orders.user_id', '=', 'users.user_id')
        ->groupBy('product_orders.user_id', 'users.name')
        ->orderByDesc('total_amount')
        ->get();



        return view('pages.topcustomer', compact('topcustomers'));






    }
}
