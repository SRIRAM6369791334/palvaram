<?php

namespace App\Http\Controllers;

use App\Models\SupplierDetail;
use App\Models\Country;
use Illuminate\Http\Request;

class SupplierDetailController extends Controller
{
    private function getAllowedCountryIds()
    {
        $user = auth()->user();
        $countryIds = is_array($user->country_id) ? $user->country_id : json_decode($user->country_id, true) ?? [];
        $countryIds = array_map('intval', $countryIds);

        $sessionCountryId = session('country_id');
        if (!$sessionCountryId) return $countryIds;
        if (in_array((int)$sessionCountryId, $countryIds)) return [(int)$sessionCountryId];
        return $countryIds;
    }

   
    private function parseCountryIds($countryId)
    {
        if (empty($countryId)) return [];
        return array_map('intval', explode(',', (string) $countryId));
    }

   
    public function index()
    {
        $allowedCountryIds = $this->getAllowedCountryIds();

        $suppliers = SupplierDetail::all()
            ->filter(function ($supplier) use ($allowedCountryIds) {
                $ids = $this->parseCountryIds($supplier->country_id);
                return count(array_intersect($ids, $allowedCountryIds)) > 0;
            })
            ->values();

        $countries = Country::whereIn('id', $allowedCountryIds)->get();

        return view("pages.supplier_details", compact("suppliers", "countries", "allowedCountryIds"));
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
        ]);

        $suppliers = SupplierDetail::all()
            ->filter(function ($supplier) use ($allowedCountryIds) {
                $ids = $this->parseCountryIds($supplier->country_id);
                return count(array_intersect($ids, $allowedCountryIds)) > 0;
            })
            ->values();

        return response()->json([
            "message"   => "Supplier Added Successfully",
            "suppliers" => $suppliers
        ]);
    }

    
    public function update(Request $request, $id)
    {
        $allowedCountryIds = $this->getAllowedCountryIds();

        $supplier = SupplierDetail::get()->first(function ($s) use ($id, $allowedCountryIds) {
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

        $suppliers = SupplierDetail::all()
            ->filter(function ($s) use ($allowedCountryIds) {
                $ids = $this->parseCountryIds($s->country_id);
                return count(array_intersect($ids, $allowedCountryIds)) > 0;
            })
            ->values();

        return response()->json([
            "message"   => "Supplier Updated Successfully",
            "suppliers" => $suppliers
        ]);
    }

    
    public function destroy($id)
    {
        $allowedCountryIds = $this->getAllowedCountryIds();

        $supplier = SupplierDetail::findOrFail($id);
        $supplier->delete();

        $suppliers = SupplierDetail::all()
            ->filter(function ($s) use ($allowedCountryIds) {
                $ids = $this->parseCountryIds($s->country_id);
                return count(array_intersect($ids, $allowedCountryIds)) > 0;
            })
            ->values();

        return response()->json([
            "message"   => "Supplier Deleted Successfully",
            "suppliers" => $suppliers
        ]);
    }

    public function validateSupplierName(Request $request)
    {
        $request->validate([
            "name" => "required"
        ]);

        return response("success");
    }

    
    public function getByCountry(Request $request)
    {
        $countryId = (int) $request->country_id;

        if (!$countryId) {
            return response()->json([]);
        }

        $allowedCountryIds = $this->getAllowedCountryIds();

        if (!in_array($countryId, $allowedCountryIds)) {
            return response()->json([]);
        }

        $isGift = $request->has('is_gift') ? (int) $request->is_gift : 0;

        $suppliers = SupplierDetail::where('is_gift', $isGift)
            ->get()
            ->filter(function ($supplier) use ($countryId) {
                $ids = $this->parseCountryIds($supplier->country_id);
                return in_array($countryId, $ids);
            })
            ->values();

        return response()->json($suppliers);
    }
}