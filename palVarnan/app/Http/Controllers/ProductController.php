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

        $categories = Category::whereRaw("FIND_IN_SET(?, country_id)", [$countryId])
            ->withCount(['products' => function ($q) use ($countryId) {
                $q->where(function ($sq) use ($countryId) {
                    $sq->where('country_id', $countryId)->orWhereNull('country_id');
                });
            }])
            ->get();

        $subcategories = SubCategory::whereRaw("FIND_IN_SET(?, country_id)", [$countryId])->get();

        $brands = Brand::whereRaw("FIND_IN_SET(?, country_id)", [$countryId])
            ->withCount(['products' => function ($q) use ($countryId) {
                $q->where(function ($sq) use ($countryId) {
                    $sq->where('country_id', $countryId)->orWhereNull('country_id');
                });
            }])
            ->get();

        $products = Product::with('variants.unit');

        $products->where(function ($q) use ($countryId) {
            $q->where('country_id', $countryId)
              ->orWhereNull('country_id');
        });

        // Canonical Slug Redirect when numeric IDs are requested (e.g. category=2 -> category=savouries)
        if ($request->has('category') && is_numeric($request->category)) {
            $cat = Category::find($request->category);
            if ($cat) {
                $params = $request->query();
                $params['category'] = \Illuminate\Support\Str::slug($cat->category_name);
                if ($request->has('subcategory') && is_numeric($request->subcategory)) {
                    $sub = SubCategory::find($request->subcategory);
                    if ($sub) {
                        $params['subcategory'] = \Illuminate\Support\Str::slug($sub->subcategory_name);
                    }
                }
                return redirect()->to(url('/products') . '?' . http_build_query($params));
            }
        } elseif ($request->has('subcategory') && is_numeric($request->subcategory)) {
            $sub = SubCategory::find($request->subcategory);
            if ($sub) {
                $params = $request->query();
                $params['subcategory'] = \Illuminate\Support\Str::slug($sub->subcategory_name);
                return redirect()->to(url('/products') . '?' . http_build_query($params));
            }
        }

        $selectedCategory = null;
        if ($request->category) {
            $catSlug = $request->category;
            $selectedCategory = $categories->first(function($c) use ($catSlug) {
                return (string)$c->id === (string)$catSlug || \Illuminate\Support\Str::slug($c->category_name) === (string)$catSlug;
            });
            if (!$selectedCategory) {
                $selectedCategory = Category::whereRaw("LOWER(REPLACE(category_name, ' ', '-')) = ?", [strtolower($catSlug)])
                    ->orWhereRaw("LOWER(category_name) = ?", [strtolower(str_replace('-', ' ', $catSlug))])
                    ->first();
            }
            if ($selectedCategory) {
                $products->where('category_id', $selectedCategory->id);
            }
        }

        $selectedSubcategory = null;
        if ($request->subcategory) {
            $subSlug = $request->subcategory;
            $selectedSubcategory = $subcategories->first(function($s) use ($subSlug) {
                return (string)$s->id === (string)$subSlug || \Illuminate\Support\Str::slug($s->subcategory_name) === (string)$subSlug;
            });
            if (!$selectedSubcategory) {
                $selectedSubcategory = SubCategory::whereRaw("LOWER(REPLACE(subcategory_name, ' ', '-')) = ?", [strtolower($subSlug)])
                    ->orWhereRaw("LOWER(subcategory_name) = ?", [strtolower(str_replace('-', ' ', $subSlug))])
                    ->first();
            }
            if ($selectedSubcategory) {
                $products->where('subcategory_id', $selectedSubcategory->id);
            }
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

        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'price_low_high':
                $products->orderByRaw('(SELECT MIN(offer_price) FROM product_varients WHERE product_varients.product_id = products.id) ASC');
                break;
            case 'price_high_low':
                $products->orderByRaw('(SELECT MAX(offer_price) FROM product_varients WHERE product_varients.product_id = products.id) DESC');
                break;
            case 'name_asc':
                $products->orderBy('product_name', 'asc');
                break;
            case 'name_desc':
                $products->orderBy('product_name', 'desc');
                break;
            default:
                $products->latest();
                break;
        }

        $perPage = (int) $request->get('per_page', 6);
        if (!in_array($perPage, [6, 12, 24, 48])) {
            $perPage = 6;
        }

        $products = $products->paginate($perPage);

        return view('pages.products', compact('categories', 'subcategories', 'brands', 'products', 'cartProductIds', 'cartItems', 'wishlistProductIds', 'sort', 'selectedCategory', 'selectedSubcategory'));
    }

    public function show($slug)
    {
        if (!session('country_id')) {
            return redirect('/select-country');
        }

        $countryId = session('country_id');

        $product = Product::with('variants.unit', 'category', 'brand', 'childImages')
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

        if ($relatedProducts->count() < 4) {
            $extraProducts = Product::with('variants.unit')
                ->where('id', '!=', $product->id)
                ->whereNotIn('id', $relatedProducts->pluck('id'))
                ->where(function ($q) use ($countryId) {
                    $q->where('country_id', $countryId)
                    ->orWhereNull('country_id');
                })
                ->latest()
                ->take(8 - $relatedProducts->count())
                ->get();
            $relatedProducts = $relatedProducts->concat($extraProducts);
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