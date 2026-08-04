<?php

namespace App\Http\Controllers;

use App\Models\Newcoupon;
use Illuminate\Http\Request;

class NewcouponController extends Controller
{

      public function index()
    {
        $coupons = Newcoupon::latest()->get();
        return view('pages.newcoupon', compact('coupons'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|unique:newcoupons,code',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $data['is_active'] = true;

        Newcoupon::create($data);

        return redirect()->back()->with('success', 'Coupon created successfully.');
    }

    public function toggle($id)
    {
        $coupon = Newcoupon::findOrFail($id);
        $coupon->is_active = !$coupon->is_active;
        $coupon->save();

        return redirect()->back()->with('success', 'Coupon status updated.');
    }

    public function edit($id)
{
    $coupon = Newcoupon::findOrFail($id);
    return response()->json($coupon);
}

public function update(Request $request, $id)
{
    $coupon = Newcoupon::findOrFail($id);

    $data = $request->validate([
        'code' => 'required|unique:newcoupons,code,' . $id,
        'type' => 'required|in:percentage,fixed',
        'value' => 'required|numeric|min:0',
        'min_order_amount' => 'nullable|numeric|min:0',
        'start_date' => 'nullable|date',
        'end_date' => 'nullable|date|after_or_equal:start_date',
    ]);

    $coupon->update($data);

    return redirect()->back()->with('success', 'Coupon updated successfully.');
}


public function destroy($id)
{
    $coupon = Newcoupon::find($id);

    if (!$coupon) {
        return redirect()->back()->with('error', 'Coupon not found.');
    }

    $coupon->delete();

    return redirect()->back()->with('success', 'Coupon deleted successfully.');
}




}
