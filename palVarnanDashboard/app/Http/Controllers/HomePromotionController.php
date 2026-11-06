<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\HomePromotion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class HomePromotionController extends Controller
{
    /**
     * Safely parse user's allowed country IDs regardless of how country_id is stored in DB
     * (array, int, JSON string, comma-separated string, or null).
     */
    private function getAllowedCountryIds()
    {
        $user = auth()->user();
        if (!$user) {
            return [];
        }

        $raw = $user->country_id;
        if (is_array($raw)) {
            $countryIds = $raw;
        } elseif (is_numeric($raw)) {
            $countryIds = [(int)$raw];
        } elseif (is_string($raw)) {
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                $countryIds = $decoded;
            } elseif (is_numeric($decoded)) {
                $countryIds = [(int)$decoded];
            } else {
                $countryIds = array_filter(explode(',', $raw));
            }
        } else {
            $countryIds = [];
        }

        $countryIds = array_values(array_unique(array_map('intval', (array)$countryIds)));

        // Super Admin or unrestricted user gets all available countries
        $isSuperAdmin = method_exists($user, 'hasRole') && $user->hasRole('Super Admin');
        if ($isSuperAdmin || empty($countryIds)) {
            $allCountryIds = Country::pluck('id')->toArray();
            $countryIds = !empty($countryIds) ? $countryIds : $allCountryIds;
        }

        $sessionCountryId = session('country_id');
        if ($sessionCountryId && in_array((int)$sessionCountryId, $countryIds)) {
            return [(int)$sessionCountryId];
        }

        return $countryIds;
    }

    public function index()
    {
        $allowedCountryIds = $this->getAllowedCountryIds();
        $user = auth()->user();
        $isSuperAdmin = $user && method_exists($user, 'hasRole') && $user->hasRole('Super Admin');

        $promotionsQuery = HomePromotion::with('country');
        $countriesQuery = Country::query();

        if (!$isSuperAdmin && !empty($allowedCountryIds)) {
            $promotionsQuery->whereIn('country_id', $allowedCountryIds);
            $countriesQuery->whereIn('id', $allowedCountryIds);
        }

        $promotions = $promotionsQuery->orderBy('sort_order', 'asc')->get();
        $countries = $countriesQuery->get();

        return view("pages.home_promotions", compact("promotions", "countries"));
    }

    public function store(Request $request)
    {
        try {
            $allowedCountryIds = $this->getAllowedCountryIds();
            $user = auth()->user();
            $isSuperAdmin = $user && method_exists($user, 'hasRole') && $user->hasRole('Super Admin');

            $countryRule = "required";
            if (!$isSuperAdmin && !empty($allowedCountryIds)) {
                $countryRule .= "|in:" . implode(',', $allowedCountryIds);
            }

            $request->validate([
                "bg_image"       => "required|mimes:png,jpg,webp,jpeg|dimensions:width=597,height=354",
                "badge_text"     => "required|string|max:255",
                "main_title"     => "required|string|max:255",
                "highlight_text" => "required|string|max:255",
                "link_url"       => "required|string|max:255",
                "country_id"     => $countryRule,
            ], [
                "bg_image.dimensions" => "Image resolution must be exactly 597 × 354 px.",
                "country_id.in"       => "You are not allowed to create promotions for this country.",
            ]);

            $uploadDir = public_path("images/home_promotions");
            if (!File::isDirectory($uploadDir)) {
                File::makeDirectory($uploadDir, 0775, true, true);
            }

            try {
                $path = $request->file("bg_image")->store("home_promotions", "public");
            } catch (\Throwable $storeEx) {
                $file = $request->file("bg_image");
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move($uploadDir, $filename);
                $path = "home_promotions/" . $filename;
            }

            HomePromotion::create([
                "country_id"     => $request->country_id,
                "badge_text"     => $request->badge_text,
                "main_title"     => $request->main_title,
                "highlight_text" => $request->highlight_text,
                "link_url"       => $request->link_url,
                "bg_image"       => $path,
            ]);

            $promotionsQuery = HomePromotion::with('country');
            if (!$isSuperAdmin && !empty($allowedCountryIds)) {
                $promotionsQuery->whereIn('country_id', $allowedCountryIds);
            }
            $promotions = $promotionsQuery->orderBy('sort_order', 'asc')->get();

            return response()->json([
                "message" => "Promotion Added Successfully",
                "promotions" => $promotions
            ]);
        } catch (\Illuminate\Validation\ValidationException $ve) {
            throw $ve;
        } catch (\Throwable $e) {
            Log::error("Home promotion store error: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                "message" => "Failed to add promotion: " . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $allowedCountryIds = $this->getAllowedCountryIds();
            $user = auth()->user();
            $isSuperAdmin = $user && method_exists($user, 'hasRole') && $user->hasRole('Super Admin');

            // Find promotion
            $promo = HomePromotion::findOrFail($id);

            // Authorization check
            if (!$isSuperAdmin && !empty($allowedCountryIds) && !in_array((int)$promo->country_id, $allowedCountryIds)) {
                return response()->json(["message" => "You are not authorized to update this promotion."], 403);
            }

            $rules = [
                "badge_text"     => "required|string|max:255",
                "main_title"     => "required|string|max:255",
                "highlight_text" => "required|string|max:255",
                "link_url"       => "required|string|max:255",
            ];

            if ($request->hasFile("bg_image")) {
                $rules["bg_image"] = "required|mimes:png,jpg,webp,jpeg|dimensions:width=597,height=354";
            }

            $request->validate($rules, [
                "bg_image.dimensions" => "Image resolution must be exactly 597 × 354 px."
            ]);

            $data = [
                "badge_text"     => $request->badge_text,
                "main_title"     => $request->main_title,
                "highlight_text" => $request->highlight_text,
                "link_url"       => $request->link_url,
            ];

            // Only update country_id if valid
            if ($request->filled('country_id')) {
                if ($isSuperAdmin || in_array((int)$request->country_id, $allowedCountryIds)) {
                    $data["country_id"] = $request->country_id;
                }
            }

            if ($request->hasFile("bg_image")) {
                $uploadDir = public_path("images/home_promotions");
                if (!File::isDirectory($uploadDir)) {
                    File::makeDirectory($uploadDir, 0775, true, true);
                }

                if (!empty($promo->bg_image)) {
                    $oldImagePath = public_path("images/") . $promo->bg_image;
                    if (File::exists($oldImagePath) && !File::isDirectory($oldImagePath)) {
                        @File::delete($oldImagePath);
                    }
                }

                try {
                    $data["bg_image"] = $request->file("bg_image")->store("home_promotions", "public");
                } catch (\Throwable $storeEx) {
                    $file = $request->file("bg_image");
                    $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $file->move($uploadDir, $filename);
                    $data["bg_image"] = "home_promotions/" . $filename;
                }
            }

            $promo->update($data);

            $promotionsQuery = HomePromotion::with('country');
            if (!$isSuperAdmin && !empty($allowedCountryIds)) {
                $promotionsQuery->whereIn('country_id', $allowedCountryIds);
            }
            $promotions = $promotionsQuery->orderBy('sort_order', 'asc')->get();

            return response()->json([
                "message" => "Promotion Updated Successfully",
                "promotions" => $promotions
            ]);
        } catch (\Illuminate\Validation\ValidationException $ve) {
            throw $ve;
        } catch (\Throwable $e) {
            Log::error("Home promotion update error: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                "message" => "Failed to update promotion: " . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $allowedCountryIds = $this->getAllowedCountryIds();
            $user = auth()->user();
            $isSuperAdmin = $user && method_exists($user, 'hasRole') && $user->hasRole('Super Admin');

            $promo = HomePromotion::findOrFail($id);

            if (!$isSuperAdmin && !empty($allowedCountryIds) && !in_array((int)$promo->country_id, $allowedCountryIds)) {
                return response()->json(["message" => "You are not authorized to delete this promotion."], 403);
            }

            if (!empty($promo->bg_image)) {
                $oldImagePath = public_path("images/") . $promo->bg_image;
                if (File::exists($oldImagePath) && !File::isDirectory($oldImagePath)) {
                    @File::delete($oldImagePath);
                }
            }

            $promo->delete();

            $promotionsQuery = HomePromotion::with('country');
            if (!$isSuperAdmin && !empty($allowedCountryIds)) {
                $promotionsQuery->whereIn('country_id', $allowedCountryIds);
            }
            $promotions = $promotionsQuery->orderBy('sort_order', 'asc')->get();

            return response()->json([
                "message" => "Promotion Deleted Successfully",
                "promotions" => $promotions
            ]);
        } catch (\Throwable $e) {
            Log::error("Home promotion delete error: " . $e->getMessage());
            return response()->json([
                "message" => "Failed to delete promotion: " . $e->getMessage()
            ], 500);
        }
    }

    public function updateSortOrder(Request $request)
    {
        $order = $request->input('order', []);
        if (is_array($order)) {
            foreach ($order as $position => $id) {
                HomePromotion::where('id', $id)->update(['sort_order' => $position]);
            }
        }
        return response()->json(['success' => true]);
    }
}
