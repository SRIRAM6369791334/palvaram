<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductChildImage;
use App\Models\ProductVarient;
use App\Models\SubCategory;
use App\Models\Unit;
use App\Models\Country;
use App\Models\WareHouse;
use App\Models\WareHouseBranch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductVarientControllet extends Controller
{
    public $productvarientSuccessMessage = "Product varient Added Successfully";
    public function index()
    {
        $query = ProductVarient::select(
            'product_varient.*', 
            'products.product_name', 
            'products.category_id', 
            'categories.category_name',
            'products.brand_id',
            'units.short_name as unit_short_name'
        )
        ->join('products', 'product_varient.product_id', "=", 'products.id')
        ->join('categories', 'products.category_id', '=', 'categories.id')
        ->leftJoin('units', 'product_varient.unit_id', '=', 'units.id')
        ->where('products.is_gift', 0);

        $countryId = session('country_id');
        if (!empty($countryId)) {
         
            $query->whereRaw("FIND_IN_SET(?, product_varient.country_id)", [$countryId]);
        }

        $productvarient = $query->get();

        
        $allCountries = \App\Models\Country::all()->keyBy('id');
        $productvarient->each(function ($variant) use ($allCountries) {
            if (!empty($variant->country_id)) {
                $ids = array_map('trim', explode(',', $variant->country_id));
                $names = collect($ids)->map(function ($id) use ($allCountries) {
                    return isset($allCountries[$id]) ? $allCountries[$id]->name : null;
                })->filter()->implode(', ');
                $variant->country_name = $names;
            } else {
                $variant->country_name = '-';
            }
        });

        $products = Product::where('is_gift', 0)->get();
        $categories = Category::all();
        $subcategories = SubCategory::all();
        $units = Unit::all();
        $countries = Country::all();
        $brands = Brand::all(); 
        $warehouses = WareHouse::all();
        $warehouseBranches = WareHouseBranch::all();

        return view("pages.product_varient", compact(
            'productvarient', 
            'products', 
            'categories', 
            'subcategories', 
            'units', 
            'countries',
            'warehouses',       
            'warehouseBranches', 'brands' 
        ));
    }

private function getVariantList()
{
    $variants = ProductVarient::select(
            'product_varient.*',
            'products.product_name',
            'products.category_id',
            'categories.category_name',
            'products.brand_id',
            'units.short_name as unit_short_name'
        )
        ->join('products', 'product_varient.product_id', '=', 'products.id')
        ->join('categories', 'products.category_id', '=', 'categories.id')
        ->leftJoin('units', 'product_varient.unit_id', '=', 'units.id')
        ->where('products.is_gift', 0)
        ->get();

    
    $allCountries = \App\Models\Country::all()->keyBy('id');

    $variants->each(function ($variant) use ($allCountries) {
        if (!empty($variant->country_id)) {
            $ids = array_map('trim', explode(',', $variant->country_id));
            $names = collect($ids)->map(function ($id) use ($allCountries) {
                return isset($allCountries[$id]) ? $allCountries[$id]->name : null;
            })->filter()->implode(', ');
            $variant->country_name = $names;
        } else {
            $variant->country_name = '-';
        }
    });

    return $variants;
}

public function addproductvarient(Request $request)
{
    $validated = $request->validate([
        'country_id' => "required|array",
        'categoryid'      => "required",
        'subcategoryid'   => "nullable",
        'product_id'      => "required",
        'varient'         => "nullable",
        'unit_id'         => "nullable",
        'value'           => "required",
        'mrp_price'       => "required",
        'offer_price'     => "required",
        'low_stock'       => "required",
        'hot_deals'       => 'nullable',
        'Popular_products'=> 'nullable',
        'pre_order'       => 'nullable',
        'pre_note'        => 'nullable',
        'flash_sale'      => 'nullable',
        'flash_sale_date' => 'nullable',
        'product_gst'     => 'nullable',
        'sku'             => 'nullable',
        'barcode'         => 'nullable',
        'item_code'       => 'nullable',
    ]);

    $countryIdString = implode(',', $request->country_id);

    // Remove product_qty from validation — no stock entries for now
    $validated['hot_deals']        = $request->has('hot_deals') ? 1 : 0;
    $validated['Popular_products'] = $request->has('Popular_products') ? 1 : 0;
    $validated['pre_order']        = $request->has('pre_order') ? 1 : 0;
    $validated['flash_sale']       = $request->has('flash_sale') ? 1 : 0;
    if (!$request->has('pre_order')) {
        $validated['pre_note'] = null;
    }

    if ($request->hasFile("Varient_image")) {
        $variantImagePath = $request->file("Varient_image")->store("variant_images", "public");
    } else {
        $variantImagePath = null;
    }

    $product     = Product::where('is_gift', 0)->findOrFail($validated["product_id"]);
    $proname     = $product->product_name;
    $categoryId  = $product->category_id;

    // Use what the user typed in the form; only fall back to the parent
    // product's item_code if the field was left blank. Previously this
    // always pulled from $product->item_code and ignored the request,
    // so a typed item code never got saved.
    $itemCode = $request->filled('item_code') ? $request->item_code : $product->item_code;

    $subcatedisplay = SubCategory::where('id', $validated['subcategoryid'] ?? null)->first();
    $displayname    = $subcatedisplay ? $subcatedisplay->subcategory_name : '';

    $productver = ProductVarient::create([
        ...$validated,
        'country_id'  => $countryIdString, 
        "subcatename" => $displayname,
        "varient_img" => $variantImagePath,
        "product_qty" => 0,   // default 0 since no stock entry
        "sku"         => $request->sku ?? (strtoupper(substr($proname, 0, 3)) . '-' . rand(1000, 9999)),
        "barcode"     => $request->barcode,
        "unit_id"     => $request->unit_id,
        'item_code'   => $itemCode,
    ]);


    DB::table('productstocks')->insert([
        "country_id" => $countryIdString,
        "productid"             => $request->product_id,
        "category_id"           => $categoryId,
        "subcategory_id"        => $request->subcategoryid ?? null,
       
        "warehouse_id"          => null,
        "branch_id"             => null,
        "pro_ver_id"            => $productver->id,
        "productname"           => $proname,
        "overallstock"          => 0,
        "availablestock"        => 0,
        "salestock"             => 0,
        "low_stocks"            => $request->low_stock,
        "last_stockupdate_date" => now()->format('Y-m-d'),
    ]);

    if ($request->hasFile('product_image1')) {
        foreach ($request->file('product_image1') as $img) {
            if ($img->isValid()) {
                $path = $img->store('product_images1', 'public');
                DB::table('product_child_images')->insert([
                    'product_id'          => $request->product_id,
                    'variant_id'          => $productver->id,
                    'product_child_image' => $path,
                    'created_at'          => now(),
                    'updated_at'          => now(),
                ]);
            }
        }
    }

    $productvarient = $this->getVariantList();

    return response()->json([
        "message"        => $this->productvarientSuccessMessage,
        "productvarient" => $productvarient
    ]);
}

public function update(Request $request, $id)
{
    $productvarient = ProductVarient::findOrFail($id);

    $validated = $request->validate([
        'country_id' => "required|array",
        'categoryid'     => "required",
        'subcategoryid'  => "nullable",
        'product_id'     => "required",
        'varient'        => "nullable",
        'unit_id'        => "nullable",
        'value'          => "required",
        'mrp_price'      => "required",
        'offer_price'    => "required",
        'low_stock'      => "required",
        'pre_note'       => "nullable",
        'product_gst'    => 'nullable',
        'sku'            => 'nullable',
        'barcode'        => 'nullable',
        'flash_sale'     => 'nullable',
        'flash_sale_date'=> 'nullable',
        'item_code'      => 'nullable',
    ]);

    $countryIdString = implode(',', $request->country_id);

    $hotDealsValue  = $request->has('hot_deals') ? 1 : 0;
    $bestselValue   = $request->has('popular_prod') ? 1 : 0;
    $preorderValue  = $request->has('pre_order') ? 1 : 0;
    $flashSaleValue = $request->has('flash_sale') ? 1 : 0;

    $productvarient->update([
        'country_id' => $countryIdString,
        
        'categoryid'       => $validated["categoryid"],
        'subcategoryid'    => $validated["subcategoryid"] ?? null,
        'product_id'       => $validated["product_id"],
        'unit_id'          => $request->unit_id,
        'varient'          => $request->unit_id,
        'value'            => $validated["value"],
        'mrp_price'        => $validated["mrp_price"],
        'offer_price'      => $validated["offer_price"],
        'low_stock'        => $validated["low_stock"],
        'hot_deals'        => $hotDealsValue,
        'Popular_products' => $bestselValue,
        'pre_order'        => $preorderValue,
        'flash_sale'       => $flashSaleValue,
        'flash_sale_date'  => $validated['flash_sale_date'] ?? null,
        'pre_note'         => $validated["pre_note"],
        'product_gst'      => $validated["product_gst"],
        'sku'              => $request->sku,
        'barcode'          => $request->barcode,
        'item_code'        => $request->item_code,
        // product_qty untouched — managed via stock distribution
    ]);

    if ($request->hasFile("varient_image")) {
        $productvarient->update([
            'varient_img' => $request->file("varient_image")->store("variant_images", "public")
        ]);
    }

    $product    = Product::where('is_gift', 0)->findOrFail($validated['product_id']);
    $categoryId = $product->category_id;
    $proname    = $product->product_name;

    // Keep existing productstocks rows untouched — only update metadata
    DB::table('productstocks')
        ->where('pro_ver_id', $productvarient->id)
        ->update([
            'productname'    => $proname,
            'category_id'    => $categoryId,
            'subcategory_id' => $validated['subcategoryid'] ?? null,
            'country_id' => $countryIdString, 
            'low_stocks'     => $request->low_stock,
        ]);

    if (!empty($request->product_image1) && is_iterable($request->product_image1)) {
        foreach ($request->product_image1 as $img) {
            if ($img->isFile()) {
                ProductChildImage::create([
                    "variant_id"          => $productvarient->id,
                    "product_id"          => $product->id,
                    "product_child_image" => $img->store("product_images1", "public")
                ]);
            }
        }
    }

    DB::table('carts')
        ->where('product_id', $validated['product_id'])
        ->where('product_varient_id', $productvarient->id)
        ->update(['pre_order' => $preorderValue]);

    $productvarient = $this->getVariantList();

    return response()->json([
        "message"        => $this->productvarientSuccessMessage,
        "productvarient" => $productvarient
    ]);
}

     public function destroy($id)
{
    // Find the variant
    $variant = ProductVarient::find($id);

    if (!$variant) {
        return response()->json([
            "message" => "Product Variant not found",
        ], 404);
    }

    // Get the product ID associated with this variant
    $productId = $variant->product_id;

    // Delete the variant
    $deletedRows = ProductVarient::where('id', $id)->delete();

    // Delete related stock entry
    DB::table('productstocks')->where("pro_ver_id", $id)->delete();

    // Check if any more variants exist for this product
    $remainingVariants = ProductVarient::where('product_id', $productId)->count();

    if ($remainingVariants === 0) {
        // Delete the product if no more variants are left
        DB::table('products')->where('id', $productId)->delete();
    }

    // Return updated variant list
    // Return updated variant list
    $productvarient = ProductVarient::select(
        'product_varient.*', 
        'products.product_name', 
        'products.category_id', 
        'categories.category_name',
        'products.brand_id',
        'units.short_name as unit_short_name'
        )
        ->join('products', 'product_varient.product_id', "=", 'products.id')
        ->join('categories', 'products.category_id', '=', 'categories.id')
        ->leftJoin('countries', 'product_varient.country_id', '=', 'countries.id')
        ->leftJoin('units', 'product_varient.unit_id', '=', 'units.id')
        ->where('products.is_gift', 0)
        ->get();

    return response()->json([
        "message" => "Product Variant Deleted Successfully" . ($remainingVariants === 0 ? " and Product Removed" : ""),
        "productvarient" => $productvarient
    ]);
}


    public function Getsubcategory($id)
    {
        $product = Product::where('is_gift', 0)->with(['category', 'brand', 'country'])->where('subcategory_id', $id)->get();
        return response()->json($product);
    }
    public function Getproduct($id)
    {
        $product = Product::where('is_gift', 0)->with(['category', 'brand', 'country'])->where('category_id', $id)->get();
        return response()->json($product);
    }

    public function getproductverfilter(Request $request)
    {

        $catId =  $request->category_id;
        $productId = $request->product_id;



        $productvarient = ProductVarient::select(
            'product_varient.*', 
            'products.product_name', 
            'products.category_id', 
            'categories.category_name',
            'products.brand_id',
            'units.short_name as unit_short_name'
            )
            ->join('products', 'product_varient.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->leftJoin('countries', 'product_varient.country_id', '=', 'countries.id')
            ->leftJoin('units', 'product_varient.unit_id', '=', 'units.id')
            ->where('products.is_gift', 0)
            ->when($request->filled('category_id'), function ($query) use ($catId) {
                $query->where('products.category_id', $catId);
            })
            ->when($request->filled('product_id'), function ($query) use ($productId) {
                $query->where('products.id', $productId);
            })
            // Add more conditions as needed
            ->get();

        $data = [
            'productvarient' => $productvarient,
            'i' => 1,
        ];

        return $data;
    }


    public function getVariantStocks($id)
    {
        $stocks = DB::table('productstocks')->where('pro_ver_id', $id)->get();
        return response()->json($stocks);
    }
}