<?php

namespace App\Http\Controllers;

use App\Models\ProductStock;
use App\Models\ProductVarient;
use Illuminate\Http\Request;

class CompoStockController extends Controller
{
    public function index(){
        $combostocks = ProductStock::select('productstocks.*', 'categories.category_name','product_varient.varient','product_varient.value')
        ->join('categories', 'productstocks.category_id', '=', 'categories.id')
        ->join('product_varient','productstocks.pro_ver_id', '=', 'product_varient.id')
        ->where('categories.id','=', 1)

        ->get();


       return view('pages.combostock', compact('combostocks'));
    }


    public function update(Request $request)
    {
        $stockqty = $request->stock_quantity;
        $productid = $request->productid;



        $getstockdetails = ProductStock::where('id',$productid)->first();
        $productdetails = ProductVarient::where('id',$productid)->first();

        $updatestock = ProductStock::
        where('id',$productid)
        ->update([
            "overallstock"=>$getstockdetails->overallstock + $stockqty,
            "availablestock"=>$getstockdetails->availablestock + $stockqty,
        ]);

        $updatestockproduct = ProductVarient::
        where('id',$productid)
        ->update([
            "product_qty"=>$getstockdetails->availablestock + $stockqty,
        ]);

        $combostocks = ProductStock::select('productstocks.*', 'categories.category_name','product_varient.varient','product_varient.value')
        ->join('categories', 'productstocks.category_id', '=', 'categories.id')
        ->join('product_varient','productstocks.pro_ver_id', '=', 'product_varient.id')
        ->where('categories.id','=', 1)
        ->get();

        return response()->json(["combostocks" =>  $combostocks]);



    }

    public function reduceStock1(Request $request)
    {
        $stockqty = $request->stock_quantity;
        $productid = $request->productid;



        $getstockdetails = ProductStock::where('id',$productid)->first();
        $productdetails = ProductVarient::where('id',$productid)->first();

        $updatestock = ProductStock::
        where('id',$productid)
        ->update([
            "overallstock"=>$getstockdetails->overallstock - $stockqty,
            "availablestock"=>$getstockdetails->availablestock - $stockqty,
        ]);

        $updatestockproduct = ProductVarient::
        where('id',$productid)
        ->update([
            "product_qty"=>$getstockdetails->availablestock - $stockqty,
        ]);

        $combostocks = ProductStock::select('productstocks.*', 'categories.category_name','product_varient.varient','product_varient.value')
        ->join('categories', 'productstocks.category_id', '=', 'categories.id')
        ->join('product_varient','productstocks.pro_ver_id', '=', 'product_varient.id')
        ->where('categories.id','=', 1)
        ->get();
       return response()->json(["combostocks" =>  $combostocks]);



    }
}
