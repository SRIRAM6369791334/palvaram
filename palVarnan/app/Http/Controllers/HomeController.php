<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\HomePromotion;
use App\Models\Product;
use App\Models\ProductVarient;
use App\Models\Coupon;
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

        // 1. Hero Banners
        $banners = HomePromotion::where('country_id', $countryId)
            ->where('status', 1)
            ->orderBy('sort_order')
            ->get();

        // 2. All Categories (Filtered by Country)
        $categories = Category::whereRaw("FIND_IN_SET(?, country_id)", [$countryId])->get();

        // 3. Promotional Categories (Top 4 Categories with lowest price)
        $promoCategories = Category::whereRaw("FIND_IN_SET(?, country_id)", [$countryId])
            ->take(4)
            ->get();

        foreach ($promoCategories as $cat) {
            $minPrice = ProductVarient::whereHas('product', function ($q) use ($cat, $countryId) {
                $q->where('category_id', $cat->id)
                  ->where(function ($sq) use ($countryId) {
                      $sq->whereRaw("FIND_IN_SET(?, country_id)", [$countryId])
                        ->orWhere('country_id', $countryId)
                        ->orWhereNull('country_id')
                        ->orWhere('country_id', '');
                  });
            })->min('offer_price');

            $cat->min_price = $minPrice ?: 49.00;
        }

        // 4. Recommended for You (Main Catalog)
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

        // 5. Hot Deals Today (Checked hot_deals flag, with fallback to best discounts)
        $hotDeals = Product::with(['variants.unit', 'variants.size', 'variants.color', 'category'])
            ->where(function ($q) use ($countryId) {
                $q->whereRaw("FIND_IN_SET(?, country_id)", [$countryId])
                  ->orWhere('country_id', $countryId)
                  ->orWhereNull('country_id')
                  ->orWhere('country_id', '');
            })
            ->whereHas('variants', function ($q) {
                $q->where('hot_deals', 1);
            })
            ->latest()
            ->take(8)
            ->get();

        if ($hotDeals->isEmpty()) {
            $hotDeals = Product::with(['variants.unit', 'variants.size', 'variants.color', 'category'])
                ->where(function ($q) use ($countryId) {
                    $q->whereRaw("FIND_IN_SET(?, country_id)", [$countryId])
                      ->orWhere('country_id', $countryId)
                      ->orWhereNull('country_id')
                      ->orWhere('country_id', '');
                })
                ->whereHas('variants', function ($q) {
                    $q->whereColumn('mrp_price', '>', 'offer_price');
                })
                ->latest()
                ->take(8)
                ->get();
        }

        // 6. Active Coupon Banner
        $activeCoupon = Coupon::where('coupon_status', 1)
            ->where(function ($q) {
                $today = now()->toDateString();
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>=', $today);
            })
            ->latest()
            ->first();

        // 7. Short Product Lists
        // 7a. Featured Products
        $shortFeatured = Product::with(['variants', 'category'])
            ->where(function ($q) use ($countryId) {
                $q->whereRaw("FIND_IN_SET(?, country_id)", [$countryId])
                  ->orWhere('country_id', $countryId)
                  ->orWhereNull('country_id')
                  ->orWhere('country_id', '');
            })
            ->latest()
            ->take(8)
            ->get();

        // 7b. Top Selling (Popular_products = 1 flag with fallback)
        $topSelling = Product::with(['variants', 'category'])
            ->where(function ($q) use ($countryId) {
                $q->whereRaw("FIND_IN_SET(?, country_id)", [$countryId])
                  ->orWhere('country_id', $countryId)
                  ->orWhereNull('country_id')
                  ->orWhere('country_id', '');
            })
            ->whereHas('variants', function ($q) {
                $q->where('Popular_products', 1);
            })
            ->take(8)
            ->get();

        if ($topSelling->isEmpty()) {
            $topSelling = $shortFeatured->reverse()->take(8);
        }

        // 7c. Trending Products
        $trending = Product::with(['variants', 'category'])
            ->where(function ($q) use ($countryId) {
                $q->whereRaw("FIND_IN_SET(?, country_id)", [$countryId])
                  ->orWhere('country_id', $countryId)
                  ->orWhereNull('country_id')
                  ->orWhere('country_id', '');
            })
            ->inRandomOrder()
            ->take(8)
            ->get();

        // 7d. Deal of the Week (flash_sale = 1 with fallback)
        $dealOfWeek = Product::with(['variants.unit', 'variants.size', 'variants.color', 'category'])
            ->where(function ($q) use ($countryId) {
                $q->whereRaw("FIND_IN_SET(?, country_id)", [$countryId])
                  ->orWhere('country_id', $countryId)
                  ->orWhereNull('country_id')
                  ->orWhere('country_id', '');
            })
            ->whereHas('variants', function ($q) {
                $q->where('flash_sale', 1);
            })
            ->first();

        if (!$dealOfWeek) {
            $dealOfWeek = $hotDeals->first() ?? $shortFeatured->first();
        }

        // Cart items & IDs
        $cartProductIds = [];
        $cartItems = collect();
        if (Auth::check()) {
            $cartItems      = Cart::where('user_id', Auth::id())->get();
            $cartProductIds = $cartItems->pluck('product_varient_id')->toArray();
        } else {
            $cartItems      = Cart::where('ip_addresscart', request()->cookie('guest_token') ?? request()->ip())
                ->whereNull('user_id')
                ->get();
            $cartProductIds = $cartItems->pluck('product_varient_id')->toArray();
        }

        return view('pages.home', compact(
            'banners',
            'categories',
            'promoCategories',
            'featuredProducts',
            'hotDeals',
            'activeCoupon',
            'shortFeatured',
            'topSelling',
            'trending',
            'dealOfWeek',
            'cartProductIds',
            'cartItems',
            'currencySymbol'
        ));
    }
}
