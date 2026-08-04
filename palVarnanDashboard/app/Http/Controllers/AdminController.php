<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\State;
use Illuminate\Http\Request;

class AdminController extends Controller
{

    public function shippingAmount()
{
    $states = State::all();
    $international_charge = Country::where('id', '!=', 98)->value('ship_charge');

    return view('pages.shippingamt', compact('states', 'international_charge'));
}

public function updateShippingIndia(Request $request)
{
     foreach ($request->shipping as $stateId => $charges) {
        State::where('id', $stateId)->update([
            'name' => $charges['name'] ?? null,
            'shipping_charge' => $charges['standard'],
            'express_shipping_charge' => $charges['express'] ?? null,
        ]);
    }
    return back()->with('success', 'India shipping charges updated successfully.');
}

public function updateShippingInternational(Request $request)
{
    $amount = $request->input('international_charge');
    Country::where('id', '!=', 98)->update(['ship_charge' => $amount]);

    return back()->with('success', 'International shipping charges updated successfully.');
}
public function addNewState(Request $request)
{
// dd($request);

    State::create([
        'name' => $request->state_name,
        'country_id' => $request->country_id,
        'shipping_charge' => $request->priority_charge,
        'express_shipping_charge' => $request->express_charge,
    ]);

    return back()->with('success', 'New state added successfully.');
}

public function deleteState($id)
{
    $state = State::find($id);

    if (!$state) {
        return redirect()->back()->with('error', 'State not found.');
    }

    $state->delete();

    return redirect()->back()->with('success', 'State deleted successfully.');
}

}
