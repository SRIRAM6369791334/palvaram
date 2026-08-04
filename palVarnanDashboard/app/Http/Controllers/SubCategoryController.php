<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Country;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use PhpParser\Node\Stmt\Catch_;

class SubCategoryController extends Controller {

    // VIEW

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

    public function index() {
        $allowedCountryIds = $this->getAllowedCountryIds();
        
        $subcate = SubCategory::with('country')
            ->whereIn('country_id', $allowedCountryIds)
            ->orderBy('id', 'asc')
            ->get();

        $cate = Category::get()->filter(function ($cat) use ($allowedCountryIds) {
            $ids = array_map('intval', explode(',', $cat->country_id));
            return count(array_intersect($ids, $allowedCountryIds)) > 0;
        })->values();

        $countries = Country::whereIn('id', $allowedCountryIds)->get();
        $allowedCountryIds = $allowedCountryIds;
        
        return view('pages.subcategory', compact('subcate', 'cate', 'countries', 'allowedCountryIds'));
    }

    // ADD SUBCATEGORY

    public function store(Request $request)
    {
        $allowedCountryIds = $this->getAllowedCountryIds();

        if (!$request->country_id && count($allowedCountryIds) === 1) {
            $request->merge(['country_id' => $allowedCountryIds[0]]);
        }

        $request->validate([
            'subcategory_name' => 'required',
            'category_name'    => 'required',
            "country_id"       => "required|in:" . implode(',', $allowedCountryIds),
        ]);

        $categoryname  = $request->category_name;
        $subcatedisplay = Category::where('id', $categoryname)->first();

        $subcategories = $request->subcategory_name;

foreach ($subcategories as $subName) {
    SubCategory::create([
        'country_id'       => $request->country_id,
        'category_name'    => $categoryname,
        'subcategory_name' => $subName,
        'category_display' => $subcatedisplay->category_name,
    ]);
}

        $subcate = SubCategory::with('country')->whereIn('country_id', $allowedCountryIds)->orderBy('id', 'asc')->get();

        return response()->json(['message' => 'Sub Category Added Successfully', 'subcategories' => $subcate]);
    }

    public function update(Request $request, $id)
    {
        $allowedCountryIds = $this->getAllowedCountryIds();

        $subcategory = SubCategory::whereIn('country_id', $allowedCountryIds)->findOrFail($id);

        $request->validate([
            'edit_category_name'    => 'required',
            'edit_subcategory_name' => 'required',
        ]);

        $categoryname   = $request->edit_category_name;
        $subcatedisplay = Category::where('id', $categoryname)->first();

        $subcategory->update([
            'category_name'    => $categoryname,
            'subcategory_name' => $request->edit_subcategory_name,
            'category_display' => $subcatedisplay ? $subcatedisplay->category_name : $subcategory->category_display,
        ]);

        $subcategories = SubCategory::with('country')->whereIn('country_id', $allowedCountryIds)->orderBy('id', 'asc')->get();

        return response()->json(['message' => 'Sub Category Updated Successfully', 'subcategories' => $subcategories]);
    }

    public function destroy($id)
    {
        $allowedCountryIds = $this->getAllowedCountryIds();

        $subcategory = SubCategory::whereIn('country_id', $allowedCountryIds)->findOrFail($id);

        if ($subcategory->subcategory_image && File::exists(public_path('images/') . $subcategory->subcategory_image)) {
            File::delete(public_path('images/') . $subcategory->subcategory_image);
        }

        \App\Models\Product::where('subcategory_id', $subcategory->id)->delete();
        $subcategory->delete();

        $subcategories = SubCategory::with('country')->whereIn('country_id', $allowedCountryIds)->orderBy('id', 'asc')->get();

        return response()->json(['message' => 'Record Deleted Successfully', 'subcategories' => $subcategories]);
    }

}