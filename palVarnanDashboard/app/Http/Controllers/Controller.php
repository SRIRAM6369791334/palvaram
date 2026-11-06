<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Models\Country;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Safely get allowed country IDs for the authenticated user,
     * supporting all representations of country_id:
     * array, integer, numeric string, JSON string, comma-separated string, or empty/null.
     */
    protected function getAllowedCountryIds(): array
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

    /**
     * Helper to filter collections by allowed country IDs.
     */
    protected function filterByCountry($collection, array $allowedCountryIds)
    {
        return $collection->filter(function ($item) use ($allowedCountryIds) {
            $ids = array_map('intval', explode(',', $item->country_id ?? ''));
            return count(array_intersect($ids, $allowedCountryIds)) > 0;
        })->values();
    }
}
