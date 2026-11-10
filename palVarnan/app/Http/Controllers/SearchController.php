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

        $results = [];
        $baseImageUrl = rtrim(env('MAIN_URL'), '/') . '/images/';

        $products = Product::where('product_name', 'LIKE', "%{$q}%")
            ->limit(5)
            ->get();

        foreach ($products as $item) {
            $results[] = [
                'name'  => $item->product_name,
                'type'  => 'Product',
                'image' => $baseImageUrl . $item->product_image,
                'url'   => url('/product/' . $item->slug),
            ];
        }

        $variants = ProductVarient::with('product')
            ->where('varient', 'LIKE', "%{$q}%")
            ->limit(5)
            ->get();

        foreach ($variants as $item) {
            $results[] = [
                'name'  => ($item->product->product_name ?? '') . ' - ' . $item->varient,
                'type'  => 'Variant',
                'image' => !empty($item->varient_img)
                    ? $baseImageUrl . $item->varient_img
                    : (!empty($item->product->product_image)
                        ? $baseImageUrl . $item->product->product_image
                        : ''),
                'url'   => url('/product/' . ($item->product->slug ?? '#')),
            ];
        }

        $brands = Brand::where('brand_name', 'LIKE', "%{$q}%")
            ->limit(5)
            ->get();

        foreach ($brands as $item) {
            $results[] = [
                'name'  => $item->brand_name,
                'type'  => 'Brand',
                'image' => $baseImageUrl . $item->brand_image,
                'url'   => url('/products?brand=' . $item->id),
            ];
        }

        $categories = Category::where('category_name', 'LIKE', "%{$q}%")
            ->limit(5)
            ->get();

        foreach ($categories as $item) {
            $catSlug = !empty($item->slug) ? $item->slug : \Illuminate\Support\Str::slug($item->category_name);
            $results[] = [
                'name'  => $item->category_name,
                'type'  => 'Category',
                'image' => $baseImageUrl . $item->category_image,
                'url'   => url('/products?category=' . $catSlug),
            ];
        }

        return response()->json($results);
    }
}
