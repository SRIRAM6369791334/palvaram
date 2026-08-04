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

    private function getAllowedCountryIds()
    {
        $user = auth()->user();
        $countryIds = is_array($user->country_id) ? $user->country_id : json_decode($user->country_id, true) ?? [];
        $countryIds = array_map('intval', $countryIds);

        $sessionCountryId = session('country_id');

        if (!$sessionCountryId) {
            return $countryIds;
        }

        if (in_array((int)$sessionCountryId, $countryIds)) {
            return [(int)$sessionCountryId];
        }

        return $countryIds;
    }

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
    $allowedCountryIds = $this->getAllowedCountryIds();

    if ((!$request->has('country_id') || empty($request->country_id)) && count($allowedCountryIds) === 1) {
        $request->merge(['country_id' => [$allowedCountryIds[0]]]);
    }

    $request->validate([
        "brand_name"  => "required",
        "brand_image" => "nullable|image|mimes:png,jpg,jpeg,webp|max:2048",
        "country_id"  => "required|array",
    ]);

    foreach ($request->country_id as $cid) {
        if (!in_array((int)$cid, $allowedCountryIds)) {
            return response()->json(["message" => "Unauthorized country selected"], 403);
        }
    }

    $brandImagePath = null;
    if ($request->hasFile("brand_image")) {
        $brandImagePath = $request->file("brand_image")->store("brand_images", "public");
    }

    Brand::create([
        'country_id'  => implode(',', $request->country_id),
        "brand_name"  => $request->brand_name,
        "brand_slug"  => Str::slug($request->brand_name),
        "brand_image" => $brandImagePath,
        "status"      => true,
    ]);

    $brands = Brand::with('country')->get()->filter(function ($brand) use ($allowedCountryIds) {
        $ids = array_map('intval', explode(',', $brand->country_id));
        return count(array_intersect($ids, $allowedCountryIds)) > 0;
    })->values();

    return response()->json(["message" => "Brand Added Successfully", "brands" => $brands]);
}

public function update(Request $request, $id)
{
    $allowedCountryIds = $this->getAllowedCountryIds();

    $brand = Brand::get()->first(function ($b) use ($id, $allowedCountryIds) {
        $ids = array_map('intval', explode(',', $b->country_id));
        return $b->id == $id && count(array_intersect($ids, $allowedCountryIds)) > 0;
    });

    if (!$brand) abort(404);

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
        "brand_name"  => "required",
        "brand_image" => "nullable|image|mimes:png,jpg,jpeg,webp|max:2048",
    ]);

    $allowedSubmitted = array_filter((array)$submittedCountryId, function($cid) use ($allowedCountryIds) {
        return in_array((int)$cid, $allowedCountryIds);
    });

    $finalCountryIds = array_unique(array_merge(array_values($preservedCountryIds), array_values($allowedSubmitted)));

    $updateData = [
        "brand_name" => $request->brand_name,
        "brand_slug" => Str::slug($request->brand_name),
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
}

public function destroy($id)
{
    $allowedCountryIds = $this->getAllowedCountryIds();

    $brand = Brand::get()->first(function ($b) use ($id, $allowedCountryIds) {
        $ids = array_map('intval', explode(',', $b->country_id));
        return $b->id == $id && count(array_intersect($ids, $allowedCountryIds)) > 0;
    });

    if (!$brand) abort(404);

    if ($brand->brand_image && File::exists(public_path('images/') . $brand->brand_image)) {
        File::delete(public_path('images/') . $brand->brand_image);
    }

    $brand->delete();

    $brands = Brand::with('country')->get()->filter(function ($b) use ($allowedCountryIds) {
        $ids = array_map('intval', explode(',', $b->country_id));
        return count(array_intersect($ids, $allowedCountryIds)) > 0;
    })->values();

    return response()->json(["message" => "Brand Deleted Successfully", "brands" => $brands]);
}
}
