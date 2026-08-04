<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $countries = Country::all();
        return view("pages.countries", compact("countries"));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            "name" => "required|unique:countries",
            "currency" => "required"
        ]);

        Country::create([
            "name" => $request->name,
            "currency" => $request->currency,
        ]);

        $countries = Country::all();

        return response()->json([
            "message" => "Country Added Successfully",
            "countries" => $countries
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $country = Country::findOrFail($id);

        $request->validate([
            "name" => "required|unique:countries,name," . $id,
            "currency" => "required"
        ]);

        $country->update([
            "name" => $request->name,
            "currency" => $request->currency,
        ]);

        $countries = Country::all();

        return response()->json([
            "message" => "Country Updated Successfully",
            "countries" => $countries
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $country = Country::findOrFail($id);
        $country->delete();

        $countries = Country::all();

        return response()->json([
            "message" => "Country Deleted Successfully",
            "countries" => $countries
        ]);
    }

    public function validateCountryName(Request $request)
    {
        $request->validate([
            "name" => "required|unique:countries"
        ]);

        return response("success");
    }
}
