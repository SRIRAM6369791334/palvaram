<?php

namespace App\Http\Controllers;

use App\Models\DeliveryPerson;
use App\Http\Requests\StoreDeliveryPersonRequest;
use App\Http\Requests\UpdateDeliveryPersonRequest;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class DeliveryPersonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $deliveryPersons =  DeliveryPerson::all();
        return view("pages.delivery_person", compact("deliveryPersons"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Artisan::call('storage:link');

        // You can also get the output of the command by calling `Artisan::output()`
        $output = Artisan::output();

        // Do something with the output or return a response
        return response()->json(['output' => $output]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            "deliveryPerson_name" => "required",
            "email" => "required|email",
            "phone_number" => "required|digits:10",
            "password" => "required"
        ]);

        $lastdeliveryPersonId = DeliveryPerson::max('id');
        $newdeliveryPersonId = sprintf('MV-DEL-%05d', $lastdeliveryPersonId + 1);

        DeliveryPerson::create([
            "name" => $request->deliveryPerson_name,
            "email" => $request->email,
            "phone_number" => $request->phone_number,
            "password" => Hash::make($request->password),
            "enc_password" => Crypt::encryptString($request->password),
            "delivery_person_id" => $newdeliveryPersonId
        ]);

        $deliveryPersons = DeliveryPerson::all();

        return response()->json([
            "message" => "Delivery Person Added Successfully",
            "deliveryPersons" => $deliveryPersons
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(DeliveryPerson $deliveryPerson)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DeliveryPerson $deliveryPerson)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $deliveryPersonId)
    {
        $deliveryPerson  = DeliveryPerson::query()->findOrFail($deliveryPersonId);
        $request->validate([
            "deliveryPerson_name" => "required",
            "email" => "required|email",
            "phone_number" => "required|digits:10",
            "password" => $request->password ? "required" : ""
        ]);

        if (!$request->password) {
            $deliveryPerson->update([
                "name" => $request->deliveryPerson_name,
                "email" => $request->email,
                "phone_number" => $request->phone_number,
            ]);
        } else {

            $deliveryPerson->update([
                "name" => $request->deliveryPerson_name,
                "email" => $request->email,
                "phone_number" => $request->phone_number,
                "password" => Hash::make($request->password),
                "enc_password" => Crypt::encryptString($request->password),
            ]);
        }


        $deliveryPersons = DeliveryPerson::all();

        return response()->json([
            "message" => "Delivery Person Updated Successfully",
            "deliveryPersons" => $deliveryPersons
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DeliveryPerson $deliveryPerson)
    {

        $deliveryPerson->delete();
        $deliveryPersons = DeliveryPerson::all();

        return response()->json([
            "message" => "Delivery Person Deleted Successfully",
            "deliveryPersons" => $deliveryPersons
        ]);
    }
}
