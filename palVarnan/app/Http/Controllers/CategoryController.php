<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Product;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of all categories with their subcategories and product counts.
     */
    public function index(Request $request)
    {
        if (!session('country_id')) {
            return redirect('/select-country');
        }

        $countryId = session('country_id');

        // Fetch all categories for this country with product counts
        $categories = Category::whereRaw("FIND_IN_SET(?, country_id)", [$countryId])
            ->where(function($q) {
                $q->where('status', 0)->orWhereNull('status');
            })
            ->withCount(['products' => function ($q) use ($countryId) {
                $q->where(function ($sq) use ($countryId) {
                    $sq->where('country_id', $countryId)->orWhereNull('country_id');
                });
            }])
            ->get();

        // Fetch all subcategories for this country
        $subcategories = SubCategory::whereRaw("FIND_IN_SET(?, country_id)", [$countryId])->get();

        // Attach subcategories directly to each category
        foreach ($categories as $category) {
            $category->subs = $subcategories->filter(function($sub) use ($category) {
                return (string)$sub->category_name === (string)$category->id;
            });
        }

        return view('pages.categories', compact('categories', 'subcategories'));
    }
}
