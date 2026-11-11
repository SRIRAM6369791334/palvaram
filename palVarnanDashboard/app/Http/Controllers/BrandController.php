<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index()
{
    $allowedCountryIds = $this->getAllowedCountryIds();

    $brands = Brand::with('country')->get()->filter(function ($brand) use ($allowedCountryIds) {
        $ids = array_map('intval', explode(',', $brand->country_id));
        return count(array_intersect($ids, $allowedCountryIds)) > 0;
    })->values();

    $countries = Country::whereIn('id', $allowedCountryIds)->get();
    $allowedCountryIds = $allowedCountryIds; // for blade

    return view("pages.brands", compact("brands", "countries", "allowedCountryIds"));
}

    public function store(Request $request)
    {
        try {
            $allowedCountryIds = $this->getAllowedCountryIds();

            if ((!$request->has('country_id') || empty($request->country_id)) && count($allowedCountryIds) === 1) {
                $request->merge(['country_id' => [$allowedCountryIds[0]]]);
            }

            $request->validate([
                "brand_name"  => "required|string|max:255",
                "brand_image" => "nullable|image|mimes:png,jpg,jpeg,webp|max:2048",
                "country_id"  => "required|array|min:1",
            ], [
                "brand_name.required" => "Brand Name is required.",
                "country_id.required" => "Please select at least one country.",
                "country_id.min"      => "Please select at least one country.",
                "brand_image.image"   => "Brand Image must be a valid image file.",
                "brand_image.max"     => "Brand Image must not exceed 2MB.",
            ]);

            $submittedCountryIds = array_filter(array_map('intval', (array)$request->country_id), fn($id) => $id > 0);
            if (empty($submittedCountryIds)) {
                return response()->json(["message" => "Please select at least one valid country."], 422);
            }

            foreach ($submittedCountryIds as $cid) {
                if (!in_array((int)$cid, $allowedCountryIds)) {
                    return response()->json(["message" => "Unauthorized country selected."], 403);
                }
            }

            // Generate unique brand_slug to avoid database duplicate key violations
            $baseSlug = Str::slug($request->brand_name);
            if (empty($baseSlug)) {
                $baseSlug = 'brand';
            }
            $brandSlug = $baseSlug;
            $counter = 1;
            while (Brand::where('brand_slug', $brandSlug)->exists()) {
                $brandSlug = $baseSlug . '-' . time() . ($counter > 1 ? '-' . $counter : '');
                $counter++;
            }

            $brandImagePath = null;
            if ($request->hasFile("brand_image")) {
                $brandImagePath = $request->file("brand_image")->store("brand_images", "public");
            }

            Brand::create([
                'country_id'  => implode(',', $submittedCountryIds),
                "brand_name"  => trim($request->brand_name),
                "brand_slug"  => $brandSlug,
                "brand_image" => $brandImagePath,
                "status"      => true,
            ]);

            $brands = Brand::with('country')->get()->filter(function ($brand) use ($allowedCountryIds) {
                $ids = array_map('intval', explode(',', $brand->country_id));
                return count(array_intersect($ids, $allowedCountryIds)) > 0;
            })->values();

            return response()->json(["message" => "Brand Added Successfully", "brands" => $brands]);
        } catch (\Illuminate\Validation\ValidationException $ve) {
            return response()->json([
                "message" => "Validation failed",
                "errors" => $ve->errors()
            ], 422);
        } catch (\Throwable $e) {
            return response()->json([
                "message" => "Failed to add brand: " . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $allowedCountryIds = $this->getAllowedCountryIds();

            $brand = Brand::get()->first(function ($b) use ($id, $allowedCountryIds) {
                $ids = array_map('intval', explode(',', $b->country_id));
                return $b->id == $id && count(array_intersect($ids, $allowedCountryIds)) > 0;
            });

            if (!$brand) {
                return response()->json(["message" => "Brand not found or unauthorized."], 404);
            }

            // Preserve countries user is NOT allowed to manage
            $existingCountryIds = array_map('intval', explode(',', $brand->country_id));
            $preservedCountryIds = array_filter($existingCountryIds, function($cid) use ($allowedCountryIds) {
                return !in_array($cid, $allowedCountryIds);
            });

            // Handle country_id from request
            $submittedCountryId = $request->country_id;
            if (is_string($submittedCountryId) && !empty($submittedCountryId)) {
                $submittedCountryId = array_map('trim', explode(',', $submittedCountryId));
            }

            if (empty($submittedCountryId) && count($allowedCountryIds) === 1) {
                $submittedCountryId = [$allowedCountryIds[0]];
            }

            $request->validate([
                "brand_name"  => "required|string|max:255",
                "brand_image" => "nullable|image|mimes:png,jpg,jpeg,webp|max:2048",
            ], [
                "brand_name.required" => "Brand Name is required.",
                "brand_image.image"   => "Brand Image must be a valid image file.",
                "brand_image.max"     => "Brand Image must not exceed 2MB.",
            ]);

            $allowedSubmitted = array_filter(array_map('intval', (array)$submittedCountryId), function($cid) use ($allowedCountryIds) {
                return in_array($cid, $allowedCountryIds);
            });

            $finalCountryIds = array_unique(array_merge(array_values($preservedCountryIds), array_values($allowedSubmitted)));

            // Generate unique slug excluding current brand
            $baseSlug = Str::slug($request->brand_name);
            if (empty($baseSlug)) {
                $baseSlug = 'brand';
            }
            $brandSlug = $baseSlug;
            $counter = 1;
            while (Brand::where('brand_slug', $brandSlug)->where('id', '!=', $id)->exists()) {
                $brandSlug = $baseSlug . '-' . time() . ($counter > 1 ? '-' . $counter : '');
                $counter++;
            }

            $updateData = [
                "brand_name" => trim($request->brand_name),
                "brand_slug" => $brandSlug,
            ];

            if (!empty($finalCountryIds)) {
                $updateData["country_id"] = implode(',', $finalCountryIds);
            }

            if ($request->hasFile("brand_image")) {
                if ($brand->brand_image && File::exists(public_path('images/') . $brand->brand_image)) {
                    File::delete(public_path('images/') . $brand->brand_image);
                }
                $updateData["brand_image"] = $request->file("brand_image")->store("brand_images", "public");
            }

            $brand->update($updateData);

            $brands = Brand::with('country')->get()->filter(function ($b) use ($allowedCountryIds) {
                $ids = array_map('intval', explode(',', $b->country_id));
                return count(array_intersect($ids, $allowedCountryIds)) > 0;
            })->values();

            return response()->json(["message" => "Brand Updated Successfully", "brands" => $brands]);
        } catch (\Illuminate\Validation\ValidationException $ve) {
            return response()->json([
                "message" => "Validation failed",
                "errors" => $ve->errors()
            ], 422);
        } catch (\Throwable $e) {
            return response()->json([
                "message" => "Failed to update brand: " . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $allowedCountryIds = $this->getAllowedCountryIds();

            $brand = Brand::get()->first(function ($b) use ($id, $allowedCountryIds) {
                $ids = array_map('intval', explode(',', $b->country_id));
                return $b->id == $id && count(array_intersect($ids, $allowedCountryIds)) > 0;
            });

            if (!$brand) {
                return response()->json(["message" => "Brand not found or unauthorized."], 404);
            }

            // Check if any product is using this brand
            if ($brand->products()->count() > 0) {
                return response()->json([
                    "message" => "Cannot delete brand because it is currently assigned to one or more products."
                ], 422);
            }

            if ($brand->brand_image && File::exists(public_path('images/') . $brand->brand_image)) {
                File::delete(public_path('images/') . $brand->brand_image);
            }

            $brand->delete();

            $brands = Brand::with('country')->get()->filter(function ($b) use ($allowedCountryIds) {
                $ids = array_map('intval', explode(',', $b->country_id));
                return count(array_intersect($ids, $allowedCountryIds)) > 0;
            })->values();

            return response()->json(["message" => "Brand Deleted Successfully", "brands" => $brands]);
        } catch (\Throwable $e) {
            return response()->json([
                "message" => "Failed to delete brand: " . $e->getMessage()
            ], 500);
        }
    }
}
