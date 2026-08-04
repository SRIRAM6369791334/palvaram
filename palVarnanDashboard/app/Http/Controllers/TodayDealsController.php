<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVarient;
use App\Models\TodayDeals;
use Illuminate\Http\Request;

class TodayDealsController extends Controller {
    //

    public function index() {
        $deals = TodayDeals::all();
        $prods = ProductVarient::all();
        return view( 'pages.todaydeals', compact( 'deals', 'prods' ) );
    }

    // STORE FUNCTION

    public function store( Request $request ) {
        $request->validate( [
            'add_productname'=> 'required',
            'add_offervalue' => 'required',
        ] );

        $productname = $request->add_productname;
        $offervalue = $request->add_offervalue;
        $proddisplay = ProductVarient::where( 'id', $productname )->first();
        $proid = $proddisplay->product_id;
        $displayname = $proddisplay->varient_name;

        TodayDeals::create( [
            'product_id'=>$proid,
            'variant_id'=>$productname,
            'product_name' => $displayname,
            'offer_value' =>  $offervalue,
        ] );

        $deals = TodayDeals::all();

        return response()->json( [
            'message' => 'Deal Added Successfully',
            'deals' => $deals
        ] );

    }

    // UPDATE FUNCTION

    public function update( Request $request, $id ) {
        $todayDeals = TodayDeals::findOrFail( $id );

        $request->validate( [
            'edit_productname'=> 'required',
            'edit_offervalue' => 'required',
        ] );

        $varid = $request->edit_productname;
        $editoffer = $request->edit_offervalue;
        $findProd = ProductVarient::where( 'id', $varid )->first();
        $prodid = $findProd->product_id;
        $prodname = $findProd->varient_name;

        $todayDeals->update( [
            'product_id' => $prodid,
            'variant_id'=>$varid,
            'product_name' =>  $prodname,
            'offer_value'=> $editoffer
        ] );

        $deals = TodayDeals::all();

        return response()->json( [
            'message' => 'Sub Category Added Successfully',
            'deals' => $deals
        ] );

    }

    public function destroy( $id ) {
        $tdydeals = TodayDeals::findOrFail( $id );
        $tdydeals->delete();

        $deals = TodayDeals::all();

        return response()->json( [
            'message' => 'Record Deleted Successfully',
            'deals' => $deals,
        ] );
    }
}