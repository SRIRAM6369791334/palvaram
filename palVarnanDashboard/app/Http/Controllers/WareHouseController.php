<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\WareHouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WareHouseController extends Controller
{
    public function index()
    {
        $allowedCountryIds = $this->getAllowedCountryIds();

        $query = WareHouse::with('country');

        if (!empty($allowedCountryIds)) {
            $query->whereIn('country_id', $allowedCountryIds);
        }

        $sessionCountryId = session('country_id');
        if (!empty($sessionCountryId) && in_array((int)$sessionCountryId, $allowedCountryIds)) {
            $query->where('country_id', (int)$sessionCountryId);
        }

        $warehouse = $query->get();
        $countries = Country::whereIn('id', $allowedCountryIds)->get();

        return view('pages.warehouse', compact('warehouse', 'countries', 'allowedCountryIds'));
    }

    /**
     * Online postal code lookup for India and Mexico.
     */
    public function lookupPincode(Request $request)
    {
        $pincode = trim($request->get('pincode', ''));
        $country = strtolower(trim($request->get('country', 'india')));

        if (empty($pincode)) {
            return response()->json(['success' => false, 'message' => 'Pincode is required'], 422);
        }

        $isMexico = str_contains($country, 'mexic') || $country === '244' || $country === 'mx';

        try {
            if ($isMexico) {
                // Query Zippopotam for Mexico (5-digit postal code)
                $cleanPincode = str_pad(preg_replace('/[^0-9]/', '', $pincode), 5, '0', STR_PAD_LEFT);
                $response = Http::timeout(5)->get("https://api.zippopotam.us/mx/{$cleanPincode}");
                if ($response->successful()) {
                    $data = $response->json();
                    if (!empty($data['places'])) {
                        return response()->json([
                            'success' => true,
                            'city' => $data['places'][0]['place name'] ?? '',
                            'state' => $data['places'][0]['state'] ?? '',
                            'country' => 'Mexico',
                            'source' => 'zippopotam'
                        ]);
                    }
                }
            } else {
                // Clean 6-digit Indian pincode
                $cleanPincode = preg_replace('/[^0-9]/', '', $pincode);

                // 1. Try India Post API first (detailed district/state)
                try {
                    $response = Http::timeout(4)->get("https://api.postalpincode.in/pincode/{$cleanPincode}");
                    if ($response->successful()) {
                        $data = $response->json();
                        if (is_array($data) && isset($data[0]['Status']) && $data[0]['Status'] === 'Success' && !empty($data[0]['PostOffice'])) {
                            $po = $data[0]['PostOffice'][0];
                            return response()->json([
                                'success' => true,
                                'city' => $po['District'] ?: ($po['Block'] ?: $po['Name']),
                                'state' => $po['State'] ?? '',
                                'country' => 'India',
                                'source' => 'postalpincode'
                            ]);
                        }
                    }
                } catch (\Exception $e1) {
                    // Fall through to Zippopotam
                }

                // 2. Fallback to Zippopotam India
                $zipResponse = Http::timeout(4)->get("https://api.zippopotam.us/in/{$cleanPincode}");
                if ($zipResponse->successful()) {
                    $zipData = $zipResponse->json();
                    if (!empty($zipData['places'])) {
                        return response()->json([
                            'success' => true,
                            'city' => $zipData['places'][0]['place name'] ?? '',
                            'state' => $zipData['places'][0]['state'] ?? '',
                            'country' => 'India',
                            'source' => 'zippopotam'
                        ]);
                    }
                }
            }
        } catch (\Exception $e) {
            Log::warning("Pincode lookup error: " . $e->getMessage());
        }

        return response()->json(['success' => false, 'message' => 'Location not found online for this postal code'], 404);
    }

    // STORE
    public function store(Request $request)
    {
        $allowedCountryIds = $this->getAllowedCountryIds();

        if (!$request->has('country_id') || empty($request->country_id)) {
            $request->merge(['country_id' => $allowedCountryIds[0] ?? 243]);
        }

        $validated = $request->validate([
            'warehouse_name' => 'required|string|min:2|max:255',
            'phone' => 'required|string|min:7|max:50',
            'address' => 'required|string|min:3|max:255',
            'pincode' => 'required|string|min:4|max:20',
            'city' => 'required|string|min:2|max:100',
            'state' => 'required|string|min:2|max:100',
            'country_id' => 'required|exists:countries,id',
        ]);

        WareHouse::create($validated);

        $warehouseList = WareHouse::with('country')
            ->whereIn('country_id', $allowedCountryIds)
            ->get();

        return response()->json([
            'message' => 'Warehouse created successfully',
            'warehouse' => $warehouseList
        ]);
    }

    // UPDATE
    public function update(Request $request, $id)
    {
        $allowedCountryIds = $this->getAllowedCountryIds();

        $warehouse = WareHouse::findOrFail($id);

        if (!$request->has('country_id') || empty($request->country_id)) {
            $request->merge(['country_id' => $warehouse->country_id ?: ($allowedCountryIds[0] ?? 243)]);
        }

        $validated = $request->validate([
            'warehouse_name' => 'required|string|min:2|max:255',
            'phone' => 'required|string|min:7|max:50',
            'address' => 'required|string|min:3|max:255',
            'pincode' => 'required|string|min:4|max:20',
            'city' => 'required|string|min:2|max:100',
            'state' => 'required|string|min:2|max:100',
            'country_id' => 'required|exists:countries,id',
        ]);

        $warehouse->update($validated);

        $warehouseList = WareHouse::with('country')
            ->whereIn('country_id', $allowedCountryIds)
            ->get();

        return response()->json([
            'message' => 'Warehouse updated successfully',
            'warehouse' => $warehouseList
        ]);
    }

    // DELETE
    public function destroy($id)
    {
        $allowedCountryIds = $this->getAllowedCountryIds();
        $warehouse = WareHouse::findOrFail($id);
        $warehouse->delete();

        $warehouseList = WareHouse::with('country')
            ->whereIn('country_id', $allowedCountryIds)
            ->get();

        return response()->json([
            'message' => 'Warehouse deleted successfully',
            'warehouse' => $warehouseList
        ]);
    }
}
