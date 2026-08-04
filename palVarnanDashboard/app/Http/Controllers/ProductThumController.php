<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductChildImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ProductThumController extends Controller
{
    public $productThumpuccessMessage = "Product Thum Images Added Successfully";
    public function index(){
        $productthump = ProductChildImage::select("product_child_images.*",'products.product_name')
        ->join('products','product_child_images.product_id', "=", 'products.id')->get();

        $products =  Product::all();

        return view("pages.productthum",compact('productthump','products'));
    }

    public function ThumImages(Request $request){

        $validated = $request->validate([
            'product_id' => "required",
            "product_child_image" => "required|mimes:png,jpg,webp,jpeg"

        ]);

        if ($request->hasFile("product_child_image")) {
            $productImage = $request->file("product_child_image");
            $path =  $productImage->store("product_images", "public");
        $productthump = ProductChildImage::create([...$validated, "product_child_image" => $path]);

        $productthump = ProductChildImage::select("product_child_images.*",'products.product_name')
        ->join('products','product_child_images.product_id', "=", 'products.id')->get();
        return response()->json([
            "message" => $this->productThumpuccessMessage,
            "productthump" => $productthump
        ]);

        }
    }

    // update

    public function update(Request $request, $id)
    {
        $productthump = ProductChildImage::findOrFail($id);


        $validated =   $request->validate([
            'product_id' => "required",


        ]);
        if ($request->hasFile("product_child_image")) {
             $productImage = $request->file("product_child_image");
            $path =  $productImage->store("product_images", "public");

            File::delete(public_path("images/" . $productthump->product_child_image));
            $productthump->update([
                ...$validated,
                "product_child_image" =>  $path,
            ]);
            $productthump = ProductChildImage::select("product_child_images.*",'products.product_name')
        ->join('products','product_child_images.product_id', "=", 'products.id')->get();
            return response()->json([
                "message" => $this->productThumpuccessMessage,
                "productthump" => $productthump
            ]);
        } else {

            $productthump->update([
                'product_id' => $validated["product_id"],
            ]);

            return response()->json([
                "message" => $this->productThumpuccessMessage,
                "productthump" => $productthump
            ]);
        }
    }

    public function destroy($id)
    {
        $productthump = ProductChildImage::findOrFail($id);

        if (File::exists(public_path("images/") . $productthump->product_child_image)) {
            File::delete(public_path("images/" . $productthump->product_child_image));
            $productthump->delete();

            $productthump =  ProductChildImage::all();
            return response()->json([
                "message" => "Product Thum Deleted Successfully",
                "productthump" => $productthump
            ]);
        }

        return redirect("products")->with("error", "Product Deleted Failed");
    }
}
