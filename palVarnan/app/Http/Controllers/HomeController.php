<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\HomePromotion;
use App\Models\Product;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        if (!session('country_id')) {
            return redirect('/select-country');
        }

        $countryId = session('country_id');
        $currencySymbol = ($countryId == 244) ? '$' : '₹';

        $banners = HomePromotion::where('country_id', $countryId)
            ->where('status', 1)
            ->orderBy('sort_order')
            ->get();

        $categories = Category::whereRaw("FIND_IN_SET(?, country_id)", [$countryId])->get();

        $featuredProducts = Product::with(['variants.unit', 'variants.size', 'variants.color', 'category'])
            ->where(function ($q) use ($countryId) {
                $q->whereRaw("FIND_IN_SET(?, country_id)", [$countryId])
                  ->orWhere('country_id', $countryId)
                  ->orWhereNull('country_id')
                  ->orWhere('country_id', '');
            })
            ->latest()
            ->take(12)
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

        return view('pages.home', compact('banners', 'categories', 'featuredProducts', 'cartProductIds', 'cartItems', 'currencySymbol'));
    }
}
