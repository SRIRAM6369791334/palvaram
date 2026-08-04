<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\AreaAssign;
use App\Models\DeliveryPerson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class AreaAssignController extends Controller
{
    public function assignDeliveryPerson(Request $request)
    {
        $areaId = $request->area_id;
        $deliverPersons = $request->delivery_persons;

        foreach ($deliverPersons as $deliverPerson) {
            AreaAssign::create([
                "area_id" => $areaId,
                "delivery_people_id" => $deliverPerson
            ]);
        }

        $areas =  Area::query()->withCount("areaAssigns")->get();
        return response()->json([
            "stauts" => "success",
            "data" => $areas
        ]);
    }


    public function deleteDeliveryPerson(Request $request)
    {
        $areaId = $request->area_id;
        $deliverPersons = $request->delivery_persons;

        AreaAssign::where("area_id", $areaId)->delete();

        if ($deliverPersons) {
            foreach ($deliverPersons as $deliverPerson) {
                AreaAssign::create([
                    "area_id" => $areaId,
                    "delivery_people_id" => $deliverPerson
                ]);
            }
        }

        $areas =  Area::query()->withCount("areaAssigns")->get();
        return response()->json([
            "stauts" => "success",
            "data" => $areas
        ]);
    }


    // Assign delivery Partner ajax
    public function fetchAreaDeliveryPartners($areaId)
    {
        $deliveryPersons =  DeliveryPerson::whereNotIn('id', function ($query) use ($areaId) {
            $query->select('delivery_people_id')
                ->from('area_assigns')
                ->where('area_id', '=', $areaId);
        })->get();

        $responseHtml = view("ajaxPages.delivery_option", compact("deliveryPersons"))->render();
        return response()->json([
            "status" => "success",
            "data" => $responseHtml
        ]);
    }


    public function deleteAreaDeliveryPartners($areaid)
    {
        $deliveryPersons = AreaAssign::with('deliveryPerson')->where("area_id", $areaid)->get();
        $responseHtml = view("ajaxPages.assignedDeliveryPersons", compact("deliveryPersons"))->render();

        return response()->json([
            "status" => "success",
            "data" => $responseHtml
        ]);
    }
}
