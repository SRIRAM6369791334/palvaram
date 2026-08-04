<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\WareHouse;
use App\Models\WareHouseBranch;
use Illuminate\Http\Request;

class WareHouseBranchController extends Controller
{
    public function index()
    {
        $query = WareHouseBranch::with(['country', 'warehouse']);

        $countryId = session('country_id');

        if (!empty($countryId)) {
            $query->where('country_id', $countryId);
        }

        $branches = $query->get();

        $countries = Country::all();

        $warehousesQuery = WareHouse::with('country');
        if (!empty($countryId)) {
            $warehousesQuery->where('country_id', $countryId);
        }
        $warehouses = $warehousesQuery->get();

        return view('pages.warehouse_branch', compact('branches', 'countries', 'warehouses'));
    }

    // STORE
    public function store(Request $request)
    {
        $request->validate([
            'country_id' => 'required|exists:countries,id',
            'warehouse_id' => 'required|exists:warehouse,id',
            'phone' => 'required',
            'address' => 'required',
            'pincode' => 'required',
            'city' => 'required',
            'state' => 'required',
            'branch_name' => 'required',
        ]);

        WareHouseBranch::create([
            'country_id' => $request->country_id,
            'warehouse_id' => $request->warehouse_id,
            'phone' => $request->phone,
            'address' => $request->address,
            'pincode' => $request->pincode,
            'city' => $request->city,
            'state' => $request->state,
            'branch_name' => $request->branch_name,
        ]);

        $branches = WareHouseBranch::with(['country', 'warehouse'])->get();

        return response()->json([
            'message' => 'Branch created successfully',
            'branches' => $branches
        ]);
    }

    // UPDATE
    public function update(Request $request, $id)
    {
        $request->validate([
            'country_id' => 'required|exists:countries,id',
            'warehouse_id' => 'required|exists:warehouse,id',
            'phone' => 'required',
            'address' => 'required',
            'pincode' => 'required',
            'city' => 'required',
            'state' => 'required',
            'branch_name' => 'required',
        ]);

        $branch = WareHouseBranch::findOrFail($id);

        $branch->update([
            'country_id' => $request->country_id,
            'warehouse_id' => $request->warehouse_id,
            'phone' => $request->phone,
            'address' => $request->address,
            'pincode' => $request->pincode,
            'city' => $request->city,
            'state' => $request->state,
            'branch_name' => $request->branch_name,
        ]);

        $branches = WareHouseBranch::with(['country', 'warehouse'])->get();

        return response()->json([
            'message' => 'Branch updated successfully',
            'branches' => $branches
        ]);
    }

    // DELETE
    public function destroy($id)
    {
        $branch = WareHouseBranch::findOrFail($id);
        $branch->delete();

        $branches = WareHouseBranch::with(['country', 'warehouse'])->get();

        return response()->json([
            'message' => 'Branch deleted successfully',
            'branches' => $branches
        ]);
    }

    // AJAX: Get warehouses by country (IMPORTANT for dropdown)
    public function getWarehouses($country_id)
    {
        $warehouses = WareHouse::where('country_id', $country_id)->get();

        return response()->json($warehouses);
    }
}
