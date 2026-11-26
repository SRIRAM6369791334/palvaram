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

        // Fetch all categories for this country
        $categories = Category::whereRaw("FIND_IN_SET(?, country_id)", [$countryId])
            ->where(function($q) {
                $q->where('status', 0)->orWhereNull('status');
            })
            ->get();

        // Single grouped count query instead of per-category correlated subqueries
        $productCounts = Product::selectRaw('category_id, COUNT(*) as cnt')
            ->whereIn('category_id', $categories->pluck('id'))
            ->where(function ($sq) use ($countryId) {
                $sq->whereRaw("FIND_IN_SET(?, country_id)", [$countryId])
                   ->orWhere('country_id', $countryId)
                   ->orWhereNull('country_id')
                   ->orWhere('country_id', '');
            })
            ->groupBy('category_id')
            ->pluck('cnt', 'category_id');

        // Fetch all subcategories for this country
        $subcategories = SubCategory::whereRaw("FIND_IN_SET(?, country_id)", [$countryId])->get();

        // Attach product counts and subcategories directly to each category
        foreach ($categories as $category) {
            $category->products_count = (int) ($productCounts[$category->id] ?? 0);
            $category->subs = $subcategories->filter(function($sub) use ($category) {
                return (string)$sub->category_name === (string)$category->id;
            });
        }

        return view('pages.categories', compact('categories', 'subcategories'));
    }
}
