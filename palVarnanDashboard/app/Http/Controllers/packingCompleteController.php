<?php

namespace App\Http\Controllers;

use App\Models\ProductOrder;
use Illuminate\Http\Request;

class packingCompleteController extends Controller
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
