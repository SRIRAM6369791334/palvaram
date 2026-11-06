<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\HomePromotion;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        if (!session('country_id')) {
            return redirect('/select-country');
        }

        $countryId = session('country_id');

        $banners = HomePromotion::where('country_id', $countryId)
            ->where('status', 1)
            ->orderBy('sort_order')
            ->get();

        $categories = Category::whereRaw("FIND_IN_SET(?, country_id)", [$countryId])->get();

        $featuredProducts = Product::with(['variants.unit', 'category'])
            ->where(function ($q) use ($countryId) {
                $q->whereRaw("FIND_IN_SET(?, country_id)", [$countryId])
                  ->orWhere('country_id', $countryId)
                  ->orWhereNull('country_id')
                  ->orWhere('country_id', '');
            })
            ->latest()
            ->take(12)
            ->get();

        return view('pages.home', compact('banners', 'categories', 'featuredProducts'));
    }
}
