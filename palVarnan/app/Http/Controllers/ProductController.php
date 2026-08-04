<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Product;
use App\Models\SubCategory;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        if (!session('country_id')) {
            return redirect('/select-country');
        }

        $countryId = session('country_id');

        $categories = Category::whereRaw("FIND_IN_SET(?, country_id)", [$countryId])->get();

        $subcategories = SubCategory::whereRaw("FIND_IN_SET(?, country_id)", [$countryId])->get();

        $brands = Brand::whereRaw("FIND_IN_SET(?, country_id)", [$countryId])->get();

        $products = Product::with('variants.unit');

        $products->where(function ($q) use ($countryId) {
            $q->where('country_id', $countryId)
              ->orWhereNull('country_id');
        });

        if ($request->category) {
            $products->where('category_id', $request->category);
        }

        if ($request->subcategory) {
            $products->where('subcategory_id', $request->subcategory);
        }

        if ($request->brand) {
            $products->where('brand_id', $request->brand);
        }

        $cartProductIds = [];
        $cartItems = collect();

        if (Auth::check()) {
            $cartItems      = Cart::where('user_id', Auth::id())->get();
            $cartProductIds = $cartItems->pluck('product_varient_id')->toArray();
        } else {
            $cartItems      = Cart::where('ip_addresscart', request()->ip())
                                ->whereNull('user_id')
                                ->get();
            $cartProductIds = $cartItems->pluck('product_varient_id')->toArray();
        }

        $wishlistProductIds = [];
if (Auth::check()) {
    $wishlistProductIds = Wishlist::where('user_id', Auth::id())
                            ->pluck('product_id')
                            ->toArray();
}

        $products = $products->latest()->paginate(12);

        return view('pages.products', compact('categories', 'subcategories', 'brands', 'products', 'cartProductIds', 'cartItems', 'wishlistProductIds'));
    }

    public function show($slug)
    {
        if (!session('country_id')) {
            return redirect('/select-country');
        }

        $countryId = session('country_id');

        $product = Product::with('variants.unit', 'category', 'brand')
            ->where('slug', $slug)
            ->firstOrFail();

        $relatedProducts = Product::with('variants.unit')
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where(function ($q) use ($countryId) {
                $q->where('country_id', $countryId)
                ->orWhereNull('country_id');
            })
            ->latest()
            ->take(8)
            ->get();

        $cartProductIds = [];
        $cartItems = collect();

        if (Auth::check()) {
            $cartItems      = Cart::where('user_id', Auth::id())->get();
            $cartProductIds = $cartItems->pluck('product_varient_id')->toArray();
        } else {
            $cartItems      = Cart::where('ip_addresscart', request()->ip())
                                ->whereNull('user_id')
                                ->get();
            $cartProductIds = $cartItems->pluck('product_varient_id')->toArray();
        }
        $wishlistVariantIds = collect();
        $wishlistProductIds = [];
if (Auth::check()) {
    $wishlistProductIds = Wishlist::where('user_id', Auth::id())
                            ->pluck('product_id')
                            ->toArray();
}

        // if (Auth::check()) {
        //     $cartItems = Cart::where('user_id', Auth::id())->get();
        //     $cartProductIds = $cartItems->pluck('product_varient_id')->toArray();
        //     $wishlistVariantIds = Wishlist::where('user_id', Auth::id())
        //         ->where('product_id', $product->id)
        //         ->pluck('product_varient_id');
        // }

        return view('pages.product-detail', compact(
            'product', 'relatedProducts', 'cartProductIds', 'cartItems', 'wishlistVariantIds', 'wishlistProductIds'
        ));
    }
    
}