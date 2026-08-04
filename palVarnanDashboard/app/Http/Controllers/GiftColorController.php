<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\GiftColor;
use Illuminate\Http\Request;

class GiftColorController extends Controller
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


    public function index()
    {
        $allowedCountryIds = $this->getAllowedCountryIds();

        $gifts     = $this->filterByCountry(GiftColor::all(), $allowedCountryIds);
        $countries = Country::whereIn('id', $allowedCountryIds)->get();

        return view('pages.gifts.gift_color', compact('gifts', 'countries', 'allowedCountryIds'));
    }


    public function store(Request $request)
    {
        try {
            $allowedCountryIds = $this->getAllowedCountryIds();

            if (empty($request->country_id) && count($allowedCountryIds) === 1) {
                $request->merge(['country_id' => [$allowedCountryIds[0]]]);
            }

            $request->validate([
               
                'color' => 'required|string|max:25',
                'country_id' => 'required|array',
            ]);

            foreach ($request->country_id as $cid) {
                if (!in_array((int)$cid, $allowedCountryIds)) {
                    return response()->json(['message' => 'Unauthorized country selected'], 403);
                }
            }

            GiftColor::create([
                'country_id' => implode(',', $request->country_id),
                
                'color' => $request->color,
            ]);

            $gifts = $this->filterByCountry(GiftColor::all(), $allowedCountryIds);

            return response()->json(['message' => 'Gift Color Added Successfully', 'gifts' => $gifts]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'line'    => $e->getLine(),
                'file'    => $e->getFile(),
            ], 500);
        }
    }


    public function update(Request $request, $id)
    {
        $allowedCountryIds = $this->getAllowedCountryIds();

        $gifts = $this->filterByCountry(GiftColor::all(), $allowedCountryIds)
            ->firstWhere('id', $id);

        if (!$gifts) abort(404);

        // Preserve countries this user is NOT allowed to manage
        $existingIds  = array_map('intval', explode(',', $gifts->country_id ?? ''));
        $preservedIds = array_filter($existingIds, fn($cid) => !in_array($cid, $allowedCountryIds));

        $submitted = is_array($request->country_id)
            ? $request->country_id
            : array_map('trim', explode(',', $request->country_id ?? ''));

        if (empty($submitted) && count($allowedCountryIds) === 1) {
            $submitted = [$allowedCountryIds[0]];
        }

        $request->validate([
           
            'color' => 'required|string|max:25',
        ]);

        $allowedSubmitted = array_filter($submitted, fn($cid) => in_array((int)$cid, $allowedCountryIds));
        $finalIds         = array_unique(array_merge(array_values($preservedIds), array_values($allowedSubmitted)));

        $gifts->update([
            'country_id' => implode(',', $finalIds),
           
            'color' => $request->color,
        ]);

        $gifts = $this->filterByCountry(GiftColor::all(), $allowedCountryIds);

        return response()->json(['message' => 'Gift Color Updated Successfully', 'gifts' => $gifts]);
    }


    public function destroy($id)
    {
        $allowedCountryIds = $this->getAllowedCountryIds();

        $gifts = $this->filterByCountry(GiftColor::all(), $allowedCountryIds)
            ->firstWhere('id', $id);

        if (!$gifts) abort(404);

        $gifts->delete();

        $gifts = $this->filterByCountry(GiftColor::all(), $allowedCountryIds);

        return response()->json(['message' => 'Gift Color Deleted Successfully', 'gifts' => $gifts]);
    }
}
