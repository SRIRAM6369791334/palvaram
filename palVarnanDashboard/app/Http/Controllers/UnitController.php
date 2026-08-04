<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
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

        $units     = $this->filterByCountry(Unit::all(), $allowedCountryIds);
        $countries = Country::whereIn('id', $allowedCountryIds)->get();

        return view('pages.units', compact('units', 'countries', 'allowedCountryIds'));
    }


    public function store(Request $request)
    {
        try {
            $allowedCountryIds = $this->getAllowedCountryIds();

            if (empty($request->country_id) && count($allowedCountryIds) === 1) {
                $request->merge(['country_id' => [$allowedCountryIds[0]]]);
            }

            $request->validate([
                'unit_name'  => 'required|string|max:100',
                'short_name' => 'required|string|max:20',
                'country_id' => 'required|array',
            ]);

            foreach ($request->country_id as $cid) {
                if (!in_array((int)$cid, $allowedCountryIds)) {
                    return response()->json(['message' => 'Unauthorized country selected'], 403);
                }
            }

            Unit::create([
                'country_id' => implode(',', $request->country_id),
                'unit_name'  => $request->unit_name,
                'short_name' => $request->short_name,
            ]);

            $units = $this->filterByCountry(Unit::all(), $allowedCountryIds);

            return response()->json(['message' => 'Unit Added Successfully', 'units' => $units]);

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

        $unit = $this->filterByCountry(Unit::all(), $allowedCountryIds)
            ->firstWhere('id', $id);

        if (!$unit) abort(404);

        // Preserve countries this user is NOT allowed to manage
        $existingIds  = array_map('intval', explode(',', $unit->country_id ?? ''));
        $preservedIds = array_filter($existingIds, fn($cid) => !in_array($cid, $allowedCountryIds));

        $submitted = is_array($request->country_id)
            ? $request->country_id
            : array_map('trim', explode(',', $request->country_id ?? ''));

        if (empty($submitted) && count($allowedCountryIds) === 1) {
            $submitted = [$allowedCountryIds[0]];
        }

        $request->validate([
            'unit_name'  => 'required|string|max:100',
            'short_name' => 'required|string|max:20',
        ]);

        $allowedSubmitted = array_filter($submitted, fn($cid) => in_array((int)$cid, $allowedCountryIds));
        $finalIds         = array_unique(array_merge(array_values($preservedIds), array_values($allowedSubmitted)));

        $unit->update([
            'country_id' => implode(',', $finalIds),
            'unit_name'  => $request->unit_name,
            'short_name' => $request->short_name,
        ]);

        $units = $this->filterByCountry(Unit::all(), $allowedCountryIds);

        return response()->json(['message' => 'Unit Updated Successfully', 'units' => $units]);
    }


    public function destroy($id)
    {
        $allowedCountryIds = $this->getAllowedCountryIds();

        $unit = $this->filterByCountry(Unit::all(), $allowedCountryIds)
            ->firstWhere('id', $id);

        if (!$unit) abort(404);

        $unit->delete();

        $units = $this->filterByCountry(Unit::all(), $allowedCountryIds);

        return response()->json(['message' => 'Unit Deleted Successfully', 'units' => $units]);
    }

}
