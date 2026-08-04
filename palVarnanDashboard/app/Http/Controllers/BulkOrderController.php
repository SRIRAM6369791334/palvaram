<?php

namespace App\Http\Controllers;

use App\Models\BulkOrder;
use Illuminate\Http\Request;

class BulkOrderController extends Controller
{
    public function index(){

        $bulkorders = BulkOrder::get();



        return view('pages.bulkorder', compact('bulkorders'));






    }
}
