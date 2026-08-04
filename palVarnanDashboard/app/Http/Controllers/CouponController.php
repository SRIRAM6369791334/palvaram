<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CouponController extends Controller
{
    public $couponSuccessMessage = "Coupons Added Successfully";
    public function index()
    {
        $coupons = Coupon::all();
        return view('pages.coupons', compact("coupons"));
    }

    // add coupons
    public function addcoupon(Request $request)
    {
    $validated = $request->validate([
        'codename' => "required|unique:coupons,codename|min:4",
        'mini_amt'=>"required",
        'discounttype' => "required",
        'discount' => "required",
        'start_date' => "required",
        'end_date' => "required",

    ]);

    // Set default value for default_id
    $validated['default_id'] = 0;

    Coupon::create($validated);

    $coupons = Coupon::all();

    return response()->json([
        "message" => $this->couponSuccessMessage,
        "coupons" => $coupons
    ]); 

    
}

    // Update coupons
    public function update(Request $request, $id)
    {
        $coupons = Coupon::findOrFail($id);

        $validated = $request->validate([
            'codename' => "required|unique:coupons,codename|min:4",
            'mini_amt'=>"required",
            'discounttype' => "required",
            'discount' => "required",
            'start_date' => "required",
            'end_date' => "required",

        ]);

        $coupons->update([
            'codename' => $validated["codename"],
            'mini_amt'=>$validated["mini_amt"],
            'discounttype' => $validated["discounttype"],
            'discount' => $validated["discount"],
            'start_date' => $validated["start_date"],
            'end_date' => $validated["end_date"],


        ]);

        $coupons =  Coupon::all();
        return response()->json([
            "message" => $this->couponSuccessMessage,
            "coupons" => $coupons
        ]);
    }

    public function destroy($id)
{
    // Use a SQL DELETE query to remove the coupon with the given ID
    $deletedRows = Coupon::where('id', $id)->delete();

    if ($deletedRows) {
        $coupons = Coupon::all();
        return response()->json([
            "message" => "Coupon Code Deleted Successfully",
            "coupons" => $coupons
        ]);
    } else {
        return response()->json([
            "message" => "Coupon Not Found or Could Not Be Deleted",
        ], 404); // You can use a different HTTP status code if needed
    }
}


    public function toggleStatus($id)
    {
        $coupon = Coupon::findOrFail($id);
        $coupon->coupon_status = $coupon->coupon_status == 1 ? 0 : 1;
        $coupon->save();

        $coupons = Coupon::all();
        return response()->json([
            "message" => "Status Updated Successfully",
            "coupons" => $coupons
        ]);
    }

    
}