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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class GiftController extends Controller
{

    private function getAllowedCountryIds(): array
    {
        $user = auth()->user();
        $countryIds = is_array($user->country_id)
            ? $user->country_id
            : json_decode($user->country_id, true) ?? [];
        $countryIds = array_map('intval', $countryIds);

        $sessionCountryId = session('country_id');
        if (!$sessionCountryId) return $countryIds;
        if (in_array((int)$sessionCountryId, $countryIds)) return [(int)$sessionCountryId];
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

        // Could be array (multi-select) or string
        if (is_array($raw)) {
            $ids = array_map('intval', $raw);
        } elseif (is_string($raw) && str_contains($raw, ',')) {
            $ids = array_map('intval', explode(',', $raw));
        } else {
            $ids = $raw ? [(int)$raw] : [];
        }

        // If empty and only one country allowed, auto-assign
        if (empty($ids) && count($allowedCountryIds) === 1) {
            $ids = [$allowedCountryIds[0]];
        }

        // Filter to only allowed
        $ids = array_values(array_filter($ids, fn($id) => in_array($id, $allowedCountryIds)));

        return implode(',', $ids);
    }


    public function categoryIndex()
    {
        $allowedCountryIds = $this->getAllowedCountryIds();

        $categories = $this->filterByCountry(
            Category::where('is_gift', 1)->with('country')->get(),
            $allowedCountryIds
        );

        $countries = Country::whereIn('id', $allowedCountryIds)->get();

        return view("pages.gifts.categories", compact("categories", "countries", "allowedCountryIds"));
    }

    public function categoryStore(Request $request)
    {
        try {
            $allowedCountryIds = $this->getAllowedCountryIds();

            if ((!$request->has('country_id') || empty($request->country_id)) && count($allowedCountryIds) === 1) {
                $request->merge(['country_id' => [$allowedCountryIds[0]]]);
            }

            $request->validate([
                "category_name"  => "required",
                "category_image" => "required|mimes:png,jpg,jpeg,webp",
                "country_id"     => "required|array",
            ]);

            foreach ($request->country_id as $cid) {
                if (!in_array((int)$cid, $allowedCountryIds)) {
                    return response()->json(["message" => "Unauthorized country selected"], 403);
                }
            }

            $categoryImagePath = null;
            if ($request->hasFile("category_image")) {
                $categoryImagePath = $request->file("category_image")->store("category_images", "public");
            }

            Category::create([
                "country_id"      => implode(',', $request->country_id),
                "category_name"   => $request->category_name,
                "category_image"  => $categoryImagePath,
                "is_gift"         => 1,
                "category_banner" => null,
                "category_icon"   => null,
            ]);

            $categories = $this->filterByCountry(
                Category::where('is_gift', 1)->with('country')->get(),
                $allowedCountryIds
            );

            return response()->json(["message" => "Gift Category Added Successfully", "categories" => $categories]);

        } catch (\Exception $e) {
            return response()->json([
                "message" => $e->getMessage(),
                "line"    => $e->getLine(),
                "file"    => $e->getFile(),
            ], 500);
        }
    }

    public function categoryUpdate(Request $request, $id)
    {
        $allowedCountryIds = $this->getAllowedCountryIds();

        $category = Category::where('is_gift', 1)->get()->first(function ($cat) use ($id, $allowedCountryIds) {
            $ids = array_map('intval', explode(',', $cat->country_id ?? ''));
            return $cat->id == $id && count(array_intersect($ids, $allowedCountryIds)) > 0;
        });

        if (!$category) abort(404);

        $existingCountryIds  = array_map('intval', explode(',', $category->country_id ?? ''));
        $preservedCountryIds = array_filter($existingCountryIds, fn($cid) => !in_array($cid, $allowedCountryIds));

        $submittedCountryId = $request->country_id;
        if (is_string($submittedCountryId) && !empty($submittedCountryId)) {
            $submittedCountryId = array_map('trim', explode(',', $submittedCountryId));
        }

        if (empty($submittedCountryId) && count($allowedCountryIds) === 1) {
            $submittedCountryId = [$allowedCountryIds[0]];
        }

        $request->validate([
            "category_name"  => "required",
            "category_image" => $request->hasFile("category_image") ? "mimes:png,jpg,jpeg,webp" : "nullable",
        ]);

        $updateData = ["category_name" => $request->category_name];

        $allowedSubmitted = array_filter((array)$submittedCountryId, fn($cid) => in_array((int)$cid, $allowedCountryIds));
        $finalCountryIds  = array_unique(array_merge(array_values($preservedCountryIds), array_values($allowedSubmitted)));

        if (!empty($finalCountryIds)) {
            $updateData["country_id"] = implode(',', $finalCountryIds);
        }

        if ($request->hasFile("category_image")) {
            if ($category->category_image && Storage::disk('public')->exists($category->category_image)) {
                Storage::disk('public')->delete($category->category_image);
            }
            $updateData["category_image"] = $request->file("category_image")->store("category_images", "public");
        }

        $category->update($updateData);

        $categories = $this->filterByCountry(
            Category::where('is_gift', 1)->with('country')->get(),
            $allowedCountryIds
        );

        return response()->json(["message" => "Gift Category Updated Successfully", "categories" => $categories]);
    }

    public function categoryDestroy($id)
    {
        $allowedCountryIds = $this->getAllowedCountryIds();

        $category = Category::where('is_gift', 1)->get()->first(function ($cat) use ($id, $allowedCountryIds) {
            $ids = array_map('intval', explode(',', $cat->country_id ?? ''));
            return $cat->id == $id && count(array_intersect($ids, $allowedCountryIds)) > 0;
        });

        if (!$category) abort(404);

        if ($category->category_image && Storage::disk('public')->exists($category->category_image)) {
            Storage::disk('public')->delete($category->category_image);
        }

        $category->delete();

        $categories = $this->filterByCountry(
            Category::where('is_gift', 1)->with('country')->get(),
            $allowedCountryIds
        );

        return response()->json(["message" => "Gift Category Deleted Successfully", "categories" => $categories]);
    }


    public function subcategoryIndex()
    {
        $allowedCountryIds = $this->getAllowedCountryIds();

        $allowedCategoryIds = Category::where('is_gift', 1)->get()
            ->filter(function ($cat) use ($allowedCountryIds) {
                $ids = array_map('intval', explode(',', $cat->country_id ?? ''));
                return count(array_intersect($ids, $allowedCountryIds)) > 0;
            })->pluck('id')->toArray();

        $subcategories = SubCategory::where('is_gift', 1)
            ->whereIn('category_name', $allowedCategoryIds)
            ->get();

        $categories = Category::where('is_gift', 1)
            ->whereIn('id', $allowedCategoryIds)
            ->get();

        $countries = Country::whereIn('id', $allowedCountryIds)->get();

        return view("pages.gifts.subcategories", compact("subcategories", "categories", "countries", "allowedCountryIds"));
    }

    public function subcategoryStore(Request $request)
    {
        try {
            $allowedCountryIds = $this->getAllowedCountryIds();

            $request->validate([
                "subcategory_name"   => "required|array",
                "subcategory_name.*" => "required|string|max:50",
                "category_name"      => "required",
            ]);

            $parentCategory = Category::where('is_gift', 1)->findOrFail($request->category_name);
            $parentCatCountryIds = array_map('intval', explode(',', $parentCategory->country_id ?? ''));

            if (count(array_intersect($parentCatCountryIds, $allowedCountryIds)) === 0) {
                return response()->json(["message" => "Unauthorized category selected"], 403);
            }

            $path = null;
            if ($request->hasFile("subcategory_image")) {
                $path = $request->file("subcategory_image")->store("subcategory_images", "public");
            }

            foreach ($request->subcategory_name as $subName) {
                SubCategory::create([
                    "subcategory_name"  => $subName,
                    "category_name"     => $request->category_name,
                    "subcategory_image" => $path,
                    "is_gift"           => 1,
                    "category_display"  => $parentCategory->category_name,
                    "country_id"        => $request->country_id,
                ]);
            }

            $allowedCategoryIds = Category::where('is_gift', 1)->get()
                ->filter(function ($cat) use ($allowedCountryIds) {
                    $ids = array_map('intval', explode(',', $cat->country_id ?? ''));
                    return count(array_intersect($ids, $allowedCountryIds)) > 0;
                })->pluck('id')->toArray();

            $subcategories = SubCategory::where('is_gift', 1)
                ->whereIn('category_name', $allowedCategoryIds)
                ->get();

            return response()->json([
                "message"       => "Gift SubCategory Added Successfully",
                "subcategories" => $subcategories,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                "message" => $e->getMessage(),
                "line"    => $e->getLine(),
                "file"    => $e->getFile(),
            ], 500);
        }
    }

    public function subcategoryUpdate(Request $request, $id)
    {
        $allowedCountryIds = $this->getAllowedCountryIds();

        $allowedCategoryIds = Category::where('is_gift', 1)->get()
            ->filter(function ($cat) use ($allowedCountryIds) {
                $ids = array_map('intval', explode(',', $cat->country_id ?? ''));
                return count(array_intersect($ids, $allowedCountryIds)) > 0;
            })->pluck('id')->toArray();

        $subcategory = SubCategory::where('is_gift', 1)
            ->whereIn('category_name', $allowedCategoryIds)
            ->findOrFail($id);

        $request->validate([
            "subcategory_name"  => "required",
            "category_name"     => "required",
            "subcategory_image" => $request->hasFile("subcategory_image") ? "mimes:png,jpg,jpeg,webp" : "nullable",
        ]);

        $parentCategory = Category::where('is_gift', 1)->findOrFail($request->category_name);
        $parentCatCountryIds = array_map('intval', explode(',', $parentCategory->country_id ?? ''));

        if (count(array_intersect($parentCatCountryIds, $allowedCountryIds)) === 0) {
            return response()->json(["message" => "Unauthorized category selected"], 403);
        }

        $updateData = [
            "subcategory_name" => $request->subcategory_name,
            "category_name"    => $request->category_name,
            "category_display" => $parentCategory->category_name,
        ];

        if ($request->hasFile("subcategory_image")) {
            if ($subcategory->subcategory_image && Storage::disk('public')->exists($subcategory->subcategory_image)) {
                Storage::disk('public')->delete($subcategory->subcategory_image);
            }
            $updateData["subcategory_image"] = $request->file("subcategory_image")->store("subcategory_images", "public");
        }

        $subcategory->update($updateData);

        $subcategories = SubCategory::where('is_gift', 1)
            ->whereIn('category_name', $allowedCategoryIds)
            ->get();

        return response()->json(["message" => "Gift SubCategory Updated Successfully", "subcategories" => $subcategories]);
    }

    public function subcategoryDestroy($id)
    {
        $allowedCountryIds = $this->getAllowedCountryIds();

        $allowedCategoryIds = Category::where('is_gift', 1)->get()
            ->filter(function ($cat) use ($allowedCountryIds) {
                $ids = array_map('intval', explode(',', $cat->country_id ?? ''));
                return count(array_intersect($ids, $allowedCountryIds)) > 0;
            })->pluck('id')->toArray();

        $subcategory = SubCategory::where('is_gift', 1)
            ->whereIn('category_name', $allowedCategoryIds)
            ->findOrFail($id);

        if ($subcategory->subcategory_image && Storage::disk('public')->exists($subcategory->subcategory_image)) {
            Storage::disk('public')->delete($subcategory->subcategory_image);
        }

        $subcategory->delete();

        $subcategories = SubCategory::where('is_gift', 1)
            ->whereIn('category_name', $allowedCategoryIds)
            ->get();

        return response()->json(["message" => "Gift SubCategory Deleted Successfully", "subcategories" => $subcategories]);
    }


    public function productIndex()
    {
        $allowedCountryIds = $this->getAllowedCountryIds();

        $allowedCategoryIds = Category::where('is_gift', 1)->get()
            ->filter(function ($cat) use ($allowedCountryIds) {
                $ids = array_map('intval', explode(',', $cat->country_id ?? ''));
                return count(array_intersect($ids, $allowedCountryIds)) > 0;
            })->pluck('id')->toArray();

        $products = Product::where('is_gift', 1)
            ->whereIn('category_id', $allowedCategoryIds)
            ->with(['variants'])
            ->get();

        $categories    = Category::where('is_gift', 1)->whereIn('id', $allowedCategoryIds)->get();
        $subcategories = SubCategory::where('is_gift', 1)->whereIn('category_name', $allowedCategoryIds)->get();
        $countries     = Country::whereIn('id', $allowedCountryIds)->get();

        $sizes = GiftSize::get()->filter(function ($s) use ($allowedCountryIds) {
            $ids = array_map('intval', explode(',', $s->country_id ?? ''));
            return count(array_intersect($ids, $allowedCountryIds)) > 0;
        })->values();

        $colors = GiftColor::get()->filter(function ($c) use ($allowedCountryIds) {
            $ids = array_map('intval', explode(',', $c->country_id ?? ''));
            return count(array_intersect($ids, $allowedCountryIds)) > 0;
        })->values();

        return view("pages.gifts.products", compact(
            "products", "categories", "subcategories",
            "countries", "allowedCountryIds", "sizes", "colors"
        ));
    }

    public function productStore(Request $request)
    {
        try {
            $allowedCountryIds = $this->getAllowedCountryIds();

            // ── 1. Resolve country_id → comma-separated string ──────────────
            $countryIdString = $this->normalizeCountryIds($request, $allowedCountryIds);

            if (empty($countryIdString)) {
                return response()->json(["message" => "No valid country selected."], 422);
            }

            // ── 2. Validate product-level fields ─────────────────────────────
            $request->validate([
                "product_name"  => "required|string",
                "category_id"   => "required",
                "product_image" => "required|mimes:png,jpg,jpeg,webp",
            ]);

            // ── 3. Authorize category ────────────────────────────────────────
            $category = Category::where('is_gift', 1)->findOrFail($request->category_id);
            $catCountryIds = array_map('intval', explode(',', $category->country_id ?? ''));
            if (count(array_intersect($catCountryIds, $allowedCountryIds)) === 0) {
                return response()->json(["message" => "Unauthorized category selected."], 403);
            }

            // ── 4. Store product image ────────────────────────────────────────
            $productImagePath = $request->file("product_image")->store("product_images", "public");

            $subcategory = SubCategory::find($request->subcategory_id);

        
            $mrpPrices = $request->input('mrp_price');
            $isMulti   = is_array($mrpPrices);

           
            $firstMrp   = $isMulti ? reset($mrpPrices)                          : $mrpPrices;
            $firstOffer = $isMulti ? reset($request->input('offer_price', []))   : $request->input('offer_price');
            $firstLow   = $isMulti ? reset($request->input('low_stock', []))     : $request->input('low_stock');

            $product = Product::create([
                "product_name"          => $request->product_name,
                "category_id"           => $request->category_id,
                "subcategory_id"        => $request->subcategory_id,
                "product_description"   => $request->product_description,
                "product_image"         => $productImagePath,
                "product_mrp_price"     => $firstMrp,
                "product_regular_price" => $firstOffer,
                "product_quantity"      => 0,
                "low_stock"             => $firstLow,
                "is_gift"               => 1,
                "cate_name"             => $category->category_name,
                "subcate_name"          => $subcategory->subcategory_name ?? null,
                "slug"                  => \Illuminate\Support\Str::slug($request->product_name) . '-' . time(),
                "product_specification" => $request->product_name,
                "approval_days"         => 0,
                "country_id"            => $countryIdString,
            ]);

           
            if ($isMulti) {
                $variantKeys = array_keys($mrpPrices); // e.g. [0, 1, 2, ...]
            } else {
                $variantKeys = ['__single__'];
            }

            foreach ($variantKeys as $key) {

                // ── Variant image ──
                if ($isMulti) {
                    $variantImageFile = $request->file("varient_image.$key");
                } else {
                    $variantImageFile = $request->file("varient_image");
                }

                $varientImagePath = null;
                if ($variantImageFile && $variantImageFile->isFile()) {
                    $varientImagePath = $variantImageFile->store("varient_images", "public");
                }

                // ── Per-variant scalar helpers ──
                $get = function (string $field, $default = null) use ($request, $isMulti, $key) {
                    if ($isMulti) {
                        return $request->input("{$field}.{$key}", $default);
                    }
                    return $request->input($field, $default);
                };

                $mrp        = $get('mrp_price',    0);
                $offer      = $get('offer_price',  0);
                $lowStock   = $get('low_stock',    5);
                $sku        = $get('sku',          null);
                $barcode    = $get('barcode',      null);
                $varName    = $get('varient_name', null);
                $sizeId     = $get('size_id',      null) ?: null;
                $colorId    = $get('color_id',     null) ?: null;
                $gst        = $get('product_gst',  0);
                $hotDeals   = $get('hot_deals',    null) ? 1 : 0;
                $popularProd= $get('popular_prod', null) ? 1 : 0;
                $flashSale  = $get('flash_sale',   null) ? 1 : 0;
                $flashDate  = $get('flash_sale_date', null);

                // Auto-generate SKU if empty
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
                    'barcode'          => $barcode,
                    'size_id'          => $sizeId,
                    'color_id'         => $colorId,
                    'varient'          => null,
                    'varient_img'      => $varientImagePath,
                    'varient_name'     => $varName,
                    'mrp_price'        => $mrp,
                    'offer_price'      => $offer,
                    'product_qty'      => 0,
                    'low_stock'        => $lowStock,
                    'product_gst'      => $gst,
                    'hot_deals'        => $hotDeals,
                    'Popular_products' => $popularProd,
                    'flash_sale'       => $flashSale,
                    'flash_sale_date'  => $flashDate ?? null,
                ]);

                DB::table('productstocks')->insert([
                    "productid"             => $product->id,
                    "category_id"           => $product->category_id,
                    "subcategory_id"        => $product->subcategory_id,
                    "country_id"            => $countryIdString,
                    "pro_ver_id"            => $variant->id,
                    "productname"           => $product->product_name,
                    "overallstock"          => 0,
                    "availablestock"        => 0,
                    "salestock"             => 0,
                    "low_stocks"            => $lowStock,
                    "last_stockupdate_date" => date("Y-m-d"),
                ]);
            }

            $products = $this->getGiftProducts($allowedCountryIds);
            return response()->json(["message" => "Gift Product Added Successfully", "products" => $products]);

        } catch (\Illuminate\Validation\ValidationException $ve) {
            return response()->json([
                "message" => "Validation failed.",
                "errors"  => $ve->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                "message" => $e->getMessage(),
                "line"    => $e->getLine(),
                "file"    => $e->getFile(),
            ], 500);
        }
    }

    public function productUpdate(Request $request, $id)
    {
        try {
            $allowedCountryIds  = $this->getAllowedCountryIds();
            $allowedCategoryIds = Category::where('is_gift', 1)->get()
                ->filter(function ($cat) use ($allowedCountryIds) {
                    $ids = array_map('intval', explode(',', $cat->country_id ?? ''));
                    return count(array_intersect($ids, $allowedCountryIds)) > 0;
                })->pluck('id')->toArray();

            $product = Product::where('is_gift', 1)
                ->whereIn('category_id', $allowedCategoryIds)
                ->findOrFail($id);

            // ── country_id ──────────────────────────────────────────────────
            $countryIdString = $this->normalizeCountryIds($request, $allowedCountryIds);
            if (empty($countryIdString)) {
                // Preserve existing country if nothing submitted
                $countryIdString = $product->country_id;
            }

            $request->validate([
                "product_name" => "required|string",
                "category_id"  => "required",
               
            ]);

            $category    = Category::where('is_gift', 1)->findOrFail($request->category_id);
            $subcategory = SubCategory::find($request->subcategory_id);

            // ── mrp_price may be scalar (edit modal) or array (future) ──────
            $mrpRaw = $request->input('mrp_price');
            $isMulti = is_array($mrpRaw);
            $firstMrp   = $isMulti ? reset($mrpRaw)                                  : $mrpRaw;
            $firstOffer = $isMulti ? reset($request->input('offer_price', []))        : $request->input('offer_price');
            $firstLow   = $isMulti ? reset($request->input('low_stock', []))          : $request->input('low_stock');

            $updateData = [
                "product_name"          => $request->product_name,
                "category_id"           => $request->category_id,
                "subcategory_id"        => $request->subcategory_id,
                "product_description"   => $request->product_description,
               
                "cate_name"             => $category->category_name,
                "subcate_name"          => $subcategory->subcategory_name ?? null,
                "slug"                  => \Illuminate\Support\Str::slug($request->product_name) . '-' . time(),
                "country_id"            => $countryIdString,
            ];

            if ($request->hasFile("product_image")) {
                if ($product->product_image && Storage::disk('public')->exists($product->product_image)) {
                    Storage::disk('public')->delete($product->product_image);
                }
                $updateData["product_image"] = $request->file("product_image")->store("product_images", "public");
            }

            $product->update($updateData);

            

            $products = $this->getGiftProducts($allowedCountryIds);
            return response()->json(["message" => "Gift Product Updated Successfully", "products" => $products]);

        } catch (\Illuminate\Validation\ValidationException $ve) {
            return response()->json([
                "message" => "Validation failed.",
                "errors"  => $ve->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                "message" => $e->getMessage(),
                "line"    => $e->getLine(),
                "file"    => $e->getFile(),
            ], 500);
        }
    }

    public function productDestroy($id)
    {
        $allowedCountryIds = $this->getAllowedCountryIds();

        $allowedCategoryIds = Category::where('is_gift', 1)->get()
            ->filter(function ($cat) use ($allowedCountryIds) {
                $ids = array_map('intval', explode(',', $cat->country_id ?? ''));
                return count(array_intersect($ids, $allowedCountryIds)) > 0;
            })->pluck('id')->toArray();

        $product = Product::where('is_gift', 1)
            ->whereIn('category_id', $allowedCategoryIds)
            ->findOrFail($id);

        if ($product->product_image && Storage::disk('public')->exists($product->product_image)) {
            Storage::disk('public')->delete($product->product_image);
        }

        // Delete variants and stocks
        $variantIds = ProductVarient::where('product_id', $id)->pluck('id');
        DB::table('productstocks')->whereIn('pro_ver_id', $variantIds)->delete();
        ProductVarient::where('product_id', $id)->delete();

        $product->delete();

        $products = $this->getGiftProducts($allowedCountryIds);
        return response()->json(["message" => "Gift Product Deleted Successfully", "products" => $products]);
    }

    private function getGiftProducts(array $allowedCountryIds)
    {
        $allowedCategoryIds = Category::where('is_gift', 1)->get()
            ->filter(function ($cat) use ($allowedCountryIds) {
                $ids = array_map('intval', explode(',', $cat->country_id ?? ''));
                return count(array_intersect($ids, $allowedCountryIds)) > 0;
            })->pluck('id')->toArray();

        return Product::where('is_gift', 1)
            ->whereIn('category_id', $allowedCategoryIds)
            ->with(['variants'])
            ->get();
    }
}