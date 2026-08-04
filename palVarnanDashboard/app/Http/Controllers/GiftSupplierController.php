<?php

namespace App\Http\Controllers;

use App\Models\SupplierDetail;
use App\Models\Country;
use Illuminate\Http\Request;

class GiftSupplierController extends Controller
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

    private function parseCountryIds($countryId): array
    {
        if (empty($countryId)) return [];
        return array_map('intval', explode(',', (string) $countryId));
    }

    private function getFilteredSuppliers(array $allowedCountryIds, bool $isGift = true)
    {
        return SupplierDetail::where('is_gift', $isGift ? 1 : 0)
            ->get()
            ->filter(function ($s) use ($allowedCountryIds) {
                $ids = $this->parseCountryIds($s->country_id);
                return count(array_intersect($ids, $allowedCountryIds)) > 0;
            })
            ->values();
    }

    public function index()
    {
        $allowedCountryIds = $this->getAllowedCountryIds();
        $suppliers = $this->getFilteredSuppliers($allowedCountryIds);
        $countries = Country::whereIn('id', $allowedCountryIds)->get();

        return view("pages.gifts.supplier_details", compact("suppliers", "countries", "allowedCountryIds"));
    }

    public function store(Request $request)
    {
        $allowedCountryIds = $this->getAllowedCountryIds();

        if ((!$request->has('country_id') || empty($request->country_id)) && count($allowedCountryIds) === 1) {
            $request->merge(['country_id' => $allowedCountryIds[0]]);
        }

        $request->validate([
            "name"       => "required",
            "shop"       => "required",
            "location"   => "required",
            "phone"      => "required",
            "country_id" => "required|integer",
        ]);

        if (!in_array((int)$request->country_id, $allowedCountryIds)) {
            return response()->json(["message" => "Unauthorized country selected"], 403);
        }

        SupplierDetail::create([
            "country_id" => $request->country_id,
            "name"       => $request->name,
            "shop"       => $request->shop,
            "location"   => $request->location,
            "phone"      => $request->phone,
            "is_gift"    => 1,
        ]);

        $suppliers = $this->getFilteredSuppliers($allowedCountryIds);

        return response()->json([
            "message"   => "Gift Supplier Added Successfully",
            "suppliers" => $suppliers
        ]);
    }

    public function update(Request $request, $id)
    {
        $allowedCountryIds = $this->getAllowedCountryIds();

        $supplier = SupplierDetail::where('is_gift', 1)->get()->first(function ($s) use ($id, $allowedCountryIds) {
            $ids = $this->parseCountryIds($s->country_id);
            return $s->id == $id && count(array_intersect($ids, $allowedCountryIds)) > 0;
        });

        if (!$supplier) abort(404);

        $request->validate([
            "name"     => "required",
            "shop"     => "required",
            "location" => "required",
            "phone"    => "required",
        ]);

        $submittedCountryId = (int) $request->country_id;

        if (empty($submittedCountryId) && count($allowedCountryIds) === 1) {
            $submittedCountryId = $allowedCountryIds[0];
        }

        $updateData = [
            "name"     => $request->name,
            "shop"     => $request->shop,
            "location" => $request->location,
            "phone"    => $request->phone,
        ];

        if ($submittedCountryId && in_array($submittedCountryId, $allowedCountryIds)) {
            $updateData["country_id"] = $submittedCountryId;
        }

        $supplier->update($updateData);

        $suppliers = $this->getFilteredSuppliers($allowedCountryIds);

        return response()->json([
            "message"   => "Gift Supplier Updated Successfully",
            "suppliers" => $suppliers
        ]);
    }

    public function destroy($id)
    {
        $allowedCountryIds = $this->getAllowedCountryIds();

        $supplier = SupplierDetail::where('is_gift', 1)->findOrFail($id);
        $supplier->delete();

        $suppliers = $this->getFilteredSuppliers($allowedCountryIds);

        return response()->json([
            "message"   => "Gift Supplier Deleted Successfully",
            "suppliers" => $suppliers
        ]);
    }
}