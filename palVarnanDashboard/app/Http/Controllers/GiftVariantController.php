<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Product;
use App\Models\ProductVarient;
use App\Models\ProductStock;
use App\Models\Country;
use App\Models\GiftColor;
use App\Models\GiftSize;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class GiftVariantController extends Controller
{

    private function getAllowedCountryIds(): array
    {
        $user      = auth()->user();
        $countryIds = is_array($user->country_id)
            ? $user->country_id
            : json_decode($user->country_id, true) ?? [];
        $countryIds = array_map('intval', $countryIds);

        $sessionCountryId = session('country_id');
        if (!$sessionCountryId) return $countryIds;
        if (in_array((int) $sessionCountryId, $countryIds)) return [(int) $sessionCountryId];
        return $countryIds;
    }

    private function filterByCountry($collection, array $allowedCountryIds)
    {
        return $collection->filter(function ($item) use ($allowedCountryIds) {
            $ids = array_map('intval', explode(',', $item->country_id ?? ''));
            return count(array_intersect($ids, $allowedCountryIds)) > 0;
        })->values();
    }

    private function normalizeCountryIds(Request $request, array $allowedCountryIds): string
    {
        $raw = $request->input('country_id');

        if (is_array($raw)) {
            $ids = array_map('intval', $raw);
        } elseif (is_string($raw) && str_contains($raw, ',')) {
            $ids = array_map('intval', explode(',', $raw));
        } else {
            $ids = $raw ? [(int) $raw] : [];
        }

        if (empty($ids) && count($allowedCountryIds) === 1) {
            $ids = [$allowedCountryIds[0]];
        }

        $ids = array_values(array_filter($ids, fn($id) => in_array($id, $allowedCountryIds)));
        return implode(',', $ids);
    }


    private function getAllowedProducts(array $allowedCountryIds, ?array $filterCountryIds = null)
    {
        $useIds = $filterCountryIds ?? $allowedCountryIds;

        $allowedCategoryIds = Category::where('is_gift', 1)->get()
            ->filter(function ($cat) use ($useIds) {
                $ids = array_map('intval', explode(',', $cat->country_id ?? ''));
                return count(array_intersect($ids, $useIds)) > 0;
            })->pluck('id')->toArray();

        return Product::where('is_gift', 1)
            ->whereIn('category_id', $allowedCategoryIds)
            ->get();
    }


    private function viewData(array $allowedCountryIds): array
    {
        $allowedCategoryIds = Category::where('is_gift', 1)->get()
            ->filter(function ($cat) use ($allowedCountryIds) {
                $ids = array_map('intval', explode(',', $cat->country_id ?? ''));
                return count(array_intersect($ids, $allowedCountryIds)) > 0;
            })->pluck('id')->toArray();

        $variants = ProductVarient::whereHas('product', function ($q) use ($allowedCategoryIds) {
                $q->where('is_gift', 1)->whereIn('category_id', $allowedCategoryIds);
            })
            ->with('product')
            ->get();

        $countries     = Country::whereIn('id', $allowedCountryIds)->get();
        $categories    = Category::where('is_gift', 1)->whereIn('id', $allowedCategoryIds)->get();
        $subcategories = SubCategory::where('is_gift', 1)->whereIn('category_name', $allowedCategoryIds)->get();
        $products      = $this->getAllowedProducts($allowedCountryIds);

        $sizes  = GiftSize::get()->filter(function ($s) use ($allowedCountryIds) {
            $ids = array_map('intval', explode(',', $s->country_id ?? ''));
            return count(array_intersect($ids, $allowedCountryIds)) > 0;
        })->values();

        $colors = GiftColor::get()->filter(function ($c) use ($allowedCountryIds) {
            $ids = array_map('intval', explode(',', $c->country_id ?? ''));
            return count(array_intersect($ids, $allowedCountryIds)) > 0;
        })->values();

        $brands = Brand::get()->filter(function ($b) use ($allowedCountryIds) {
            $ids = array_map('intval', explode(',', $b->country_id ?? ''));
            return count(array_intersect($ids, $allowedCountryIds)) > 0;
        })->values();

        return compact(
            'variants', 'countries', 'categories', 'subcategories',
            'products', 'sizes', 'colors', 'brands', 'allowedCountryIds'
        );
    }


    public function index()
    {
        $allowedCountryIds = $this->getAllowedCountryIds();
        return view('pages.gifts.product_varient', $this->viewData($allowedCountryIds));
    }


    public function store(Request $request)
    {
        try {
            $allowedCountryIds = $this->getAllowedCountryIds();
            $countryIdString   = $this->normalizeCountryIds($request, $allowedCountryIds);

            if (empty($countryIdString)) {
                return response()->json(['message' => 'No valid country selected.'], 422);
            }

            $request->validate([
                'product_id'    => 'required|exists:products,id',
                'varient_image' => 'required|mimes:png,jpg,jpeg,webp',
                'mrp_price'     => 'required|numeric|min:0',
                'offer_price'   => 'required|numeric|min:0',
                'low_stock'     => 'required|integer|min:0',
            ]);

            // Authorize product ownership
            $product = Product::where('is_gift', 1)->findOrFail($request->product_id);
            $catIds  = array_map('intval', explode(',', $product->category->country_id ?? ''));
            if (count(array_intersect($catIds, $allowedCountryIds)) === 0) {
                return response()->json(['message' => 'Unauthorized product selected.'], 403);
            }

            $varientImagePath = $request->file('varient_image')->store('varient_images', 'public');

            $sku = $request->input('sku');
            if (empty($sku)) {
                $sku = strtoupper(substr($product->product_name, 0, 3)) . '-' . rand(1000, 9999);
            }

            $variant = ProductVarient::create([
                'categoryid'       => $product->category_id,
                'subcategoryid'    => $product->subcategory_id,
                'subcatename'      => $product->subcate_name,
                'product_id'       => $product->id,
                'country_id'       => $countryIdString,
                'sku'              => $sku,
                'barcode'          => $request->input('barcode'),
                'size_id'          => $request->input('size_id') ?: null,
                'color_id'         => $request->input('color_id') ?: null,
                'brand_id'         => $request->input('brand_id') ?: null,
                'varient'          => null,
                'varient_img'      => $varientImagePath,
                'varient_name'     => $request->input('varient_name'),
                'mrp_price'        => $request->input('mrp_price'),
                'offer_price'      => $request->input('offer_price'),
                'product_qty'      => 0,
                'low_stock'        => $request->input('low_stock'),
                'product_gst'      => $request->input('product_gst', 0),
                'hot_deals'        => $request->has('hot_deals') ? 1 : 0,
                'Popular_products' => $request->has('popular_prod') ? 1 : 0,
                'flash_sale'       => $request->has('flash_sale') ? 1 : 0,
                'flash_sale_date'  => $request->input('flash_sale_date') ?? null,
            ]);

            DB::table('productstocks')->insert([
                'productid'             => $product->id,
                'category_id'           => $product->category_id,
                'subcategory_id'        => $product->subcategory_id,
                'country_id'            => $countryIdString,
                'pro_ver_id'            => $variant->id,
                'productname'           => $product->product_name,
                'overallstock'          => 0,
                'availablestock'        => 0,
                'salestock'             => 0,
                'low_stocks'            => $request->input('low_stock'),
                'last_stockupdate_date' => date('Y-m-d'),
            ]);

            return response()->json([
                'message'  => 'Gift Variant Added Successfully',
                'variants' => $this->getVariants($allowedCountryIds),
            ]);

        } catch (\Illuminate\Validation\ValidationException $ve) {
            return response()->json(['message' => 'Validation failed.', 'errors' => $ve->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()], 500);
        }
    }


    public function update(Request $request, $id)
    {
        try {
            $allowedCountryIds  = $this->getAllowedCountryIds();
            $allowedCategoryIds = Category::where('is_gift', 1)->get()
                ->filter(function ($cat) use ($allowedCountryIds) {
                    $ids = array_map('intval', explode(',', $cat->country_id ?? ''));
                    return count(array_intersect($ids, $allowedCountryIds)) > 0;
                })->pluck('id')->toArray();

            $variant = ProductVarient::whereHas('product', function ($q) use ($allowedCategoryIds) {
                    $q->where('is_gift', 1)->whereIn('category_id', $allowedCategoryIds);
                })->findOrFail($id);

            $countryIdString = $this->normalizeCountryIds($request, $allowedCountryIds);
            if (empty($countryIdString)) {
                $countryIdString = $variant->country_id;
            }

            $request->validate([
                'product_id'  => 'required|exists:products,id',
                'mrp_price'   => 'required|numeric|min:0',
                'offer_price' => 'required|numeric|min:0',
                'low_stock'   => 'required|integer|min:0',
            ]);

            $product = Product::where('is_gift', 1)->findOrFail($request->product_id);

            $updateData = [
                'categoryid'       => $product->category_id,
                'subcategoryid'    => $product->subcategory_id,
                'subcatename'      => $product->subcate_name,
                'product_id'       => $product->id,
                'country_id'       => $countryIdString,
                'sku'              => $request->input('sku') ?: $variant->sku,
                'barcode'          => $request->input('barcode'),
                'size_id'          => $request->input('size_id') ?: null,
                'color_id'         => $request->input('color_id') ?: null,
                'brand_id'         => $request->input('brand_id') ?: null,
                'varient_name'     => $request->input('varient_name'),
                'mrp_price'        => $request->input('mrp_price'),
                'offer_price'      => $request->input('offer_price'),
                'low_stock'        => $request->input('low_stock'),
                'product_gst'      => $request->input('product_gst', 0),
                'hot_deals'        => $request->has('hot_deals') ? 1 : 0,
                'Popular_products' => $request->has('popular_prod') ? 1 : 0,
                'flash_sale'       => $request->has('flash_sale') ? 1 : 0,
                'flash_sale_date'  => $request->input('flash_sale_date') ?? null,
            ];

            if ($request->hasFile('varient_image')) {
                if ($variant->varient_img && Storage::disk('public')->exists($variant->varient_img)) {
                    Storage::disk('public')->delete($variant->varient_img);
                }
                $updateData['varient_img'] = $request->file('varient_image')->store('varient_images', 'public');
            }

            $variant->update($updateData);

            DB::table('productstocks')->updateOrInsert(
                ['pro_ver_id' => $variant->id],
                [
                    'productid'             => $product->id,
                    'category_id'           => $product->category_id,
                    'subcategory_id'        => $product->subcategory_id,
                    'productname'           => $product->product_name,
                    'low_stocks'            => $request->input('low_stock'),
                    'country_id'            => $countryIdString,
                    'last_stockupdate_date' => now()->toDateString(),
                ]
            );

            return response()->json([
                'message'  => 'Gift Variant Updated Successfully',
                'variants' => $this->getVariants($allowedCountryIds),
            ]);

        } catch (\Illuminate\Validation\ValidationException $ve) {
            return response()->json(['message' => 'Validation failed.', 'errors' => $ve->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()], 500);
        }
    }


    public function destroy($id)
    {
        $allowedCountryIds  = $this->getAllowedCountryIds();
        $allowedCategoryIds = Category::where('is_gift', 1)->get()
            ->filter(function ($cat) use ($allowedCountryIds) {
                $ids = array_map('intval', explode(',', $cat->country_id ?? ''));
                return count(array_intersect($ids, $allowedCountryIds)) > 0;
            })->pluck('id')->toArray();

        $variant = ProductVarient::whereHas('product', function ($q) use ($allowedCategoryIds) {
                $q->where('is_gift', 1)->whereIn('category_id', $allowedCategoryIds);
            })->findOrFail($id);

        if ($variant->varient_img && Storage::disk('public')->exists($variant->varient_img)) {
            Storage::disk('public')->delete($variant->varient_img);
        }

        DB::table('productstocks')->where('pro_ver_id', $variant->id)->delete();
        $variant->delete();

        return response()->json([
            'message'  => 'Gift Variant Deleted Successfully',
            'variants' => $this->getVariants($allowedCountryIds),
        ]);
    }


    public function getFilteredProducts(Request $request)
    {
        $allowedCountryIds = $this->getAllowedCountryIds();

        $countryIds = $request->input('country_ids', []);
        $countryIds = array_values(array_filter(
            array_map('intval', (array) $countryIds),
            fn($id) => in_array($id, $allowedCountryIds)
        ));

        if (empty($countryIds)) $countryIds = $allowedCountryIds;

        $categoryQuery = Category::where('is_gift', 1)->get()->filter(function ($cat) use ($countryIds) {
            $ids = array_map('intval', explode(',', $cat->country_id ?? ''));
            return count(array_intersect($ids, $countryIds)) > 0;
        });

        $categoryIds = $categoryQuery->pluck('id')->toArray();

        if ($request->filled('category_id')) {
            $catId = (int) $request->category_id;
            if (in_array($catId, $categoryIds)) $categoryIds = [$catId];
        }

        $subcategories = SubCategory::where('is_gift', 1)
            ->whereIn('category_name', $categoryIds)
            ->get();

        $subcategoryId = $request->input('subcategory_id');

        $brands = Brand::get()->filter(function ($b) use ($countryIds) {
            $ids = array_map('intval', explode(',', $b->country_id ?? ''));
            return count(array_intersect($ids, $countryIds)) > 0;
        })->values();

        $brandId = $request->input('brand_id');

        $productQuery = Product::where('is_gift', 1)->whereIn('category_id', $categoryIds);

        if ($subcategoryId) {
            $productQuery->where('subcategory_id', $subcategoryId);
        }

        if ($brandId) {
            $productQuery->where('brand_id', $brandId);
        }

        $products = $productQuery->get(['id', 'product_name', 'category_id', 'subcategory_id', 'country_id']);

        $categories = $categoryQuery->values()->map(fn($c) => [
            'id'            => $c->id,
            'category_name' => $c->category_name,
            'country_id'    => $c->country_id,
        ]);

        return response()->json([
            'products'      => $products,
            'categories'    => $categories,
            'subcategories' => $subcategories,
            'brands'        => $brands,
        ]);
    }


    private function getVariants(array $allowedCountryIds)
    {
        $allowedCategoryIds = Category::where('is_gift', 1)->get()
            ->filter(function ($cat) use ($allowedCountryIds) {
                $ids = array_map('intval', explode(',', $cat->country_id ?? ''));
                return count(array_intersect($ids, $allowedCountryIds)) > 0;
            })->pluck('id')->toArray();

        return ProductVarient::whereHas('product', function ($q) use ($allowedCategoryIds) {
                $q->where('is_gift', 1)->whereIn('category_id', $allowedCategoryIds);
            })
            ->with('product')
            ->get();
    }
}