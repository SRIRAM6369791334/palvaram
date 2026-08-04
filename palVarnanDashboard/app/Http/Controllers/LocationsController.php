<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\WareHouse;
use App\Models\WareHouseBranch;
use Illuminate\Http\JsonResponse;

class LocationsController extends Controller
{
        
    public function index()
    {
        $countries  = Country::all();
        $warehouses = WareHouse::with('country')->get();
        $branches   = WareHouseBranch::with('warehouse')->get();

        return view('pages.locations', compact('countries', 'warehouses', 'branches'));
    }

    public function countries(): JsonResponse
    {
        $countries = Country::orderBy('name')->get(['id', 'name', 'iso_code', 'currency']);
        return response()->json($countries);
    }

    public function warehouses(int $countryId): JsonResponse
    {
        $warehouses = WareHouse::where('country_id', $countryId)
            ->orderBy('warehouse_name')
            ->get(['id', 'warehouse_name', 'country_id']);

        return response()->json($warehouses);
    }

    public function branches(int $warehouseId): JsonResponse
    {
        $branches = WareHouseBranch::where('warehouse_id', $warehouseId)
            ->orderBy('branch_name')
            ->get(['id', 'branch_name', 'warehouse_id', 'country_id']);

        return response()->json($branches);
    }
}
