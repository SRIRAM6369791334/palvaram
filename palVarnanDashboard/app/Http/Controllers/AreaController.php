<?php

namespace App\Http\Controllers;

use App\Models\AllIndiaPincode;
use App\Models\Area;
use App\Models\City;
use App\Models\District;
use Illuminate\Http\Request;

class AreaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $areas =  Area::query()->withCount("areaAssigns")->get();
        return view("pages.areas", compact("areas"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */



    public function store(Request $request)
    {
        $request->validate([
            "area_name" => "required",
            "area_pincode"  => "required"
        ]);
        Area::create([
            "area_name" => $request->area_name,
            "area_pincode" => $request->area_pincode
        ]);

        $areas =  Area::query()->withCount("areaAssigns")->get();

        return response()->json([
            "message" => "Area Added Successfully",
            "areas" => $areas
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Area $area)
    {
        return "Edit Page";
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {

        $area = Area::findOrFail($id);
        $request->validate([
            "area_name" => "required",
            "area_pincode" => "required"
        ]);
        $area->update([
            "area_name" => $request->area_name,
            "area_pincode" => $request->area_pincode
        ]);
        $areas =  Area::query()->withCount("areaAssigns")->get();
        return response()->json([
            "message" => "Area updated Successfully",
            "areas" => $areas
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $area =  Area::findOrFail($id);

        $area->deleteOrFail();

        $areas =  Area::query()->withCount("areaAssigns")->get();

        return response()->json([
            "message" => "Area deleted Successfully",
            "areas" => $areas
        ]);
    }


    public function  checkAreaValidation(Request $request)
    {
        $areaName =     $request->area_name;

        if ($areaName) {
            return response(Area::where("area_name", $areaName)->count());
        }
    }


    public function getPincodeAreas(Request $request)
    {
        $pincode = $request->pincode;

        $areas = AllIndiaPincode::query()->select("id", "officename")->where("pincode", $pincode)->orderBy("officename", "asc")->get();

        return  view("ajaxPages.area_names", compact("areas"))->render();
    }
}
