<?php

namespace App\Http\Controllers;

use App\Models\ProductOrder;
use Illuminate\Http\Request;

class packingCompleteController extends Controller
{

        public function index()
    {
        $allowedCountryIds = $this->getAllowedCountryIds();

        $productcompletes = ProductOrder::query()
            ->with(
                "product",
                "orderAddress.area",
                "customer",
                "country"
            )
            ->where("status", "delivered")
            ->whereIn('country_id', $allowedCountryIds)
            ->latest()
            ->get();

        return view("pages.product_delivered", compact("productcompletes"));
    }

}
