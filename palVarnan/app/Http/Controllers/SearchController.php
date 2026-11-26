<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVarient;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $q = trim($request->q ?? $request->search ?? '');

        // If accessed directly from browser address bar or traditional form submit without 'q' or json, redirect to products catalog
        if (!$request->ajax() && !$request->wantsJson() && !$request->has('q')) {
            return redirect()->to(url('/products') . '?search=' . urlencode($q));
        }

        if (!$q) {
            return response()->json([]);
        }

        $safeQ = addcslashes($q, '%_\\');
        $countryId = session('country_id');

        $results = [];
        $baseImageUrl = rtrim(env('MAIN_URL'), '/') . '/images/';
        $formatImage = function ($path) use ($baseImageUrl) {
            if (empty($path)) {
                return '/assets/images/logo/logo.png';
            }
            if (\Illuminate\Support\Str::startsWith($path, ['http://', 'https://', '/'])) {
                return $path;
            }
            return $baseImageUrl . ltrim($path, '/');
        };

        $productQuery = Product::where('product_name', 'LIKE', "%{$safeQ}%");
        if ($countryId) {
            $productQuery->where(function ($sq) use ($countryId) {
                $sq->whereRaw("FIND_IN_SET(?, country_id)", [$countryId])
                  ->orWhere('country_id', $countryId)
                  ->orWhereNull('country_id')
                  ->orWhere('country_id', '');
            });
        }
        $products = $productQuery->limit(5)->get();

        foreach ($products as $item) {
            $results[] = [
                'name'  => $item->product_name,
                'type'  => 'Product',
                'image' => $formatImage($item->product_image),
                'url'   => url('/product/' . $item->slug),
            ];
        }

        $variantQuery = ProductVarient::with('product')
            ->whereHas('product', function ($pq) use ($countryId) {
                if ($countryId) {
                    $pq->where(function ($sq) use ($countryId) {
                        $sq->whereRaw("FIND_IN_SET(?, country_id)", [$countryId])
                          ->orWhere('country_id', $countryId)
                          ->orWhereNull('country_id')
                          ->orWhere('country_id', '');
                    });
                }
            })
            ->where('varient', 'LIKE', "%{$safeQ}%");
        $variants = $variantQuery->limit(5)->get();

        foreach ($variants as $item) {
            if (!$item->product) continue;
            $results[] = [
                'name'  => ($item->product->product_name ?? '') . ' - ' . $item->varient,
                'type'  => 'Variant',
                'image' => $formatImage($item->varient_img ?: $item->product->product_image),
                'url'   => url('/product/' . $item->product->slug),
            ];
        }

        $brandQuery = Brand::where('brand_name', 'LIKE', "%{$safeQ}%");
        if ($countryId) {
            $brandQuery->whereRaw("FIND_IN_SET(?, country_id)", [$countryId]);
        }
        $brands = $brandQuery->limit(5)->get();

        foreach ($brands as $item) {
            $results[] = [
                'name'  => $item->brand_name,
                'type'  => 'Brand',
                'image' => $formatImage($item->brand_image),
                'url'   => url('/products?brand=' . $item->id),
            ];
        }

        $catQuery = Category::where('category_name', 'LIKE', "%{$safeQ}%");
        if ($countryId) {
            $catQuery->whereRaw("FIND_IN_SET(?, country_id)", [$countryId]);
        }
        $categories = $catQuery->limit(5)->get();

        foreach ($categories as $item) {
            $catSlug = !empty($item->slug) ? $item->slug : \Illuminate\Support\Str::slug($item->category_name);
            $results[] = [
                'name'  => $item->category_name,
                'type'  => 'Category',
                'image' => $formatImage($item->category_image),
                'url'   => url('/products?category=' . $catSlug),
            ];
        }

        return response()->json($results);
    }
}
