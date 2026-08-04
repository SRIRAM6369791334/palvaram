<?php

namespace App\Http\Controllers;

use App\Models\FreeShippingSetting;
use Illuminate\Http\Request;

class ShippingController extends Controller
{
public function freeship()
    {
        $freeShipping = FreeShippingSetting::where('country', 'IN')->first();

        return view('pages.freeshipping', compact('freeShipping'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'minimum_order_amount' => 'required|numeric|min:0',
            'country' => 'required|string',
        ]);

        $setting = FreeShippingSetting::updateOrCreate(
            ['country' => $data['country']],
            [
                'is_enabled' => $request->has('is_enabled'),
                'minimum_order_amount' => $data['minimum_order_amount'],
            ]
        );

        return redirect()->back()->with('success', 'Free shipping settings updated.');
    }
}
