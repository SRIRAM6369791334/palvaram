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
        $validated = $request->validate([
            'country_id' => 'required|exists:countries,id',
            'warehouse_id' => 'required|exists:warehouse,id',
            'branch_name' => 'required|string|min:2|max:255',
            'phone' => 'required|string|min:7|max:50',
            'address' => 'required|string|min:3|max:255',
            'pincode' => 'required|string|min:4|max:20',
            'city' => 'required|string|min:2|max:100',
            'state' => 'required|string|min:2|max:100',
        ]);

        WareHouseBranch::create($validated);

        $branches = WareHouseBranch::with(['country', 'warehouse'])->get();

        return response()->json([
            'message' => 'Branch created successfully',
            'branches' => $branches
        ]);
    }

    // UPDATE
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'country_id' => 'required|exists:countries,id',
            'warehouse_id' => 'required|exists:warehouse,id',
            'branch_name' => 'required|string|min:2|max:255',
            'phone' => 'required|string|min:7|max:50',
            'address' => 'required|string|min:3|max:255',
            'pincode' => 'required|string|min:4|max:20',
            'city' => 'required|string|min:2|max:100',
            'state' => 'required|string|min:2|max:100',
        ]);

        $branch = WareHouseBranch::findOrFail($id);

        $branch->update($validated);

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
