<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\HomePromotion;
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

        return view('pages.home', compact('banners', 'categories'));
    }
}
