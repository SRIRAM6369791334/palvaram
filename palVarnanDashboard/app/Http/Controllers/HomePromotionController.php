<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\HomePromotion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class HomePromotionController extends Controller
{
        
private function getAllowedCountryIds()
{
    $user = auth()->user();
    $countryIds = is_array($user->country_id) ? $user->country_id : json_decode($user->country_id, true) ?? [];
    $countryIds = array_map('intval', $countryIds);

    $sessionCountryId = session('country_id');

    // If empty string or null (All Countries selected), return all allowed
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

        $promotions = HomePromotion::with('country')
            ->whereIn('country_id', $allowedCountryIds)
            ->orderBy('sort_order', 'asc')
            ->get();

        // ✅ Only show countries the user is assigned to
        $countries = Country::whereIn('id', $allowedCountryIds)->get();

        return view("pages.home_promotions", compact("promotions", "countries"));
    }

    public function store(Request $request)
    {
        $allowedCountryIds = $this->getAllowedCountryIds();

        $request->validate([
            "bg_image"       => "required|mimes:png,jpg,webp,jpeg|dimensions:width=597,height=354",
            "badge_text"     => "required|string|max:255",
            "main_title"     => "required|string|max:255",
            "highlight_text" => "required|string|max:255",
            "link_url"       => "required|string|max:255",
            "country_id"     => "required|in:" . implode(',', $allowedCountryIds),
        ], [
            "bg_image.dimensions" => "Image resolution must be exactly 597 × 354 px.",
            "country_id.in"       => "You are not allowed to create promotions for this country.",
        ]);

        if ($request->hasFile("bg_image")) {
            $path = $request->file("bg_image")->store("home_promotions", "public");

            HomePromotion::create([
                "country_id"     => $request->country_id,
                "badge_text"     => $request->badge_text,
                "main_title"     => $request->main_title,
                "highlight_text" => $request->highlight_text,
                "link_url"       => $request->link_url,
                "bg_image"       => $path,
            ]);

            $promotions = HomePromotion::with('country')
                ->whereIn('country_id', $allowedCountryIds)
                ->orderBy('sort_order', 'asc')
                ->get();

            return response()->json(["message" => "Promotion Added Successfully", "promotions" => $promotions]);
        }

        return response()->json(["error" => "No image found"], 400);
    }

    public function update(Request $request, $id)
    {
        
        $allowedCountryIds = $this->getAllowedCountryIds();

        $promo = HomePromotion::whereIn('country_id', $allowedCountryIds)->findOrFail($id);

        $request->validate([
            "bg_image"       => $request->hasFile("bg_image") ? "mimes:png,jpg,webp,jpeg|dimensions:width=597,height=354" : "nullable",
            "badge_text"     => "required|string|max:255",
            "main_title"     => "required|string|max:255",
            "highlight_text" => "required|string|max:255",
            "link_url"       => "required|string|max:255",
        ], [
            "bg_image.dimensions" => "Image resolution must be exactly 597 × 354 px."
        ]);

        $data = [
            "badge_text"     => $request->badge_text,
            "main_title"     => $request->main_title,
            "highlight_text" => $request->highlight_text,
            "link_url"       => $request->link_url,
        ];

        // ✅ Only update country_id if user is allowed
        if ($request->country_id && in_array((int)$request->country_id, $allowedCountryIds)) {
            $data["country_id"] = $request->country_id;
        }

        if ($request->hasFile("bg_image")) {
            $oldImagePath = public_path("images/") . $promo->bg_image;
            if (File::exists($oldImagePath)) File::delete($oldImagePath);
            $data["bg_image"] = $request->file("bg_image")->store("home_promotions", "public");
        }

        $promo->update($data);

        $promotions = HomePromotion::with('country')
            ->whereIn('country_id', $allowedCountryIds)
            ->orderBy('sort_order', 'asc')
            ->get();

        return response()->json(["message" => "Promotion Updated Successfully", "promotions" => $promotions]);
    }

    public function destroy($id)
    {
        $allowedCountryIds = $this->getAllowedCountryIds();

        // ✅ Admin can only delete their own country's promotions
        $promo = HomePromotion::whereIn('country_id', $allowedCountryIds)->findOrFail($id);

        $oldImagePath = public_path("images/") . $promo->bg_image;
        if (File::exists($oldImagePath)) File::delete($oldImagePath);

        $promo->delete();

        $promotions = HomePromotion::with('country')
            ->whereIn('country_id', $allowedCountryIds)
            ->orderBy('sort_order', 'asc')
            ->get();

        return response()->json(["message" => "Promotion Deleted Successfully", "promotions" => $promotions]);
    }

    public function updateSortOrder(Request $request)
    {
        $order = $request->input('order');
        foreach ($order as $position => $id) {
            HomePromotion::where('id', $id)->update(['sort_order' => $position]);
        }
        return response()->json(['success' => true]);
    }
}