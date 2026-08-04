<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\WareHouse;
use Illuminate\Http\Request;

class WareHouseController extends Controller
{
    public function index()
    {
        $query = WareHouse::with('country');

        $countryId = session('country_id');

        if (!empty($countryId)) {
            $query->where('country_id', $countryId);
        }

        $warehouse = $query->get();

        $countries = Country::all();

        return view('pages.warehouse', compact('warehouse', 'countries'));
    }

    // STORE
    public function store(Request $request)
    {
        $request->validate([
            'warehouse_name' => 'required',
            'phone' => 'required',
            'address' => 'required',
            'pincode' => 'required',
            'city' => 'required',
            'state' => 'required',
            'country_id' => 'required|exists:countries,id',
        ]);

        $warehouse = WareHouse::create([
            'warehouse_name' => $request->warehouse_name,
            'phone' => $request->phone,
            'address' => $request->address,
            'pincode' => $request->pincode,
            'city' => $request->city,
            'state' => $request->state,
            'country_id' => $request->country_id,
        ]);

        $warehouse = WareHouse::with('country')->get();

        return response()->json([
            'message' => 'Warehouse created successfully',
            'warehouse' => $warehouse
        ]);
    }

    // UPDATE
    public function update(Request $request, $id)
    {
        $request->validate([
            'warehouse_name' => 'required',
            'phone' => 'required',
            'address' => 'required',
            'pincode' => 'required',
            'city' => 'required',
            'state' => 'required',
            'country_id' => 'required|exists:countries,id',
        ]);

        $warehouse = WareHouse::findOrFail($id);

        $warehouse->update([
            'warehouse_name' => $request->warehouse_name,
            'phone' => $request->phone,
            'address' => $request->address,
            'pincode' => $request->pincode,
            'city' => $request->city,
            'state' => $request->state,
            'country_id' => $request->country_id,
        ]);

        $warehouse = WareHouse::with('country')->get();

        return response()->json([
            'message' => 'Warehouse updated successfully',
            'warehouse' => $warehouse
        ]);
    }

    // DELETE
    public function destroy($id)
    {
        $warehouse = WareHouse::findOrFail($id);
        $warehouse->delete();

        $warehouse = WareHouse::with('country')->get();

        return response()->json([
            'message' => 'Warehouse deleted successfully',
            'warehouse' => $warehouse
        ]);
    }
}
