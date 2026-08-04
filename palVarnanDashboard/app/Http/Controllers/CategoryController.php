<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Country;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;


class CategoryController extends Controller
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
        if (!$sessionCountryId) return $countryIds;
        if (in_array((int)$sessionCountryId, $countryIds)) return [(int)$sessionCountryId];
        return $countryIds;
    }

public function index()
{
    $allowedCountryIds = $this->getAllowedCountryIds();

    $categories = Category::with('country')
        ->get()
        ->filter(function ($cat) use ($allowedCountryIds) {
            $ids = array_map('intval', explode(',', $cat->country_id));
            return count(array_intersect($ids, $allowedCountryIds)) > 0;
        })
        ->values();

    $countries = Country::whereIn('id', $allowedCountryIds)->get();

    return view("pages.categories", compact("categories", "countries", "allowedCountryIds")); // ADD allowedCountryIds
}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(Request $request)
    // {
    //     $request->validate([
    //         "category_name" => "required",
    //         // "category_image" => "required|mimes:png,jpg,webp,jpeg"
    //     ]);

    //     if ($request->hasFile("category_image")) {
    //         $categoryImage = $request->file("category_image");
    //         $path =  $categoryImage->store("category_images", "public");
    //         Category::create([
    //             "category_name" => $request->category_name,
    //             "category_image" =>  $path,
    //         ]);

    //         $categories =  Category::all();

    //         return response()->json([
    //             "message" => "Category Added Successfully",
    //             "categories" => $categories
    //         ]);
    //     }else{
    //         Category::create([
    //             "category_name" => $request->category_name,
    //         ]);
    //         $categories =  Category::all();

    //         return response()->json([
    //             "message" => "Category Added Successfully",
    //             "categories" => $categories
    //         ]);
    //     }



    //     return redirect("categories")->with("error", "No Image found");
    // }


public function store(Request $request)
{
    try {
        $allowedCountryIds = $this->getAllowedCountryIds();

        if ((!$request->has('country_id') || empty($request->country_id)) && count($allowedCountryIds) === 1) {
            $request->merge(['country_id' => [$allowedCountryIds[0]]]);
        }

        $request->validate([
            "category_name"  => "required",
            "category_image" => "required|mimes:png,jpg,jpeg",
            "country_id"     => "required|array",
        ]);

        foreach ($request->country_id as $cid) {
            if (!in_array((int)$cid, $allowedCountryIds)) {
                return response()->json(["message" => "Unauthorized country selected"], 403);
            }
        }

        $categoryImagePath = null;
        if ($request->hasFile("category_image")) {
            $categoryImagePath = $request->file("category_image")->store("category_images", "public");
        }

        Category::create([
            "country_id"      => implode(',', $request->country_id),
            "category_name"   => $request->category_name,
            "category_image"  => $categoryImagePath,
            "category_banner" => null,
            "category_icon"   => null,
        ]);

        $categories = Category::with('country')->get()->filter(function ($cat) use ($allowedCountryIds) {
            $ids = array_map('intval', explode(',', $cat->country_id));
            return count(array_intersect($ids, $allowedCountryIds)) > 0;
        })->values();

        return response()->json(["message" => "Category Added Successfully", "categories" => $categories]);

    } catch (\Exception $e) {
        return response()->json([
            "message" => $e->getMessage(),
            "line"    => $e->getLine(),
            "file"    => $e->getFile(),
        ], 500);
    }
}

        /**
         * Display the specified resource.
         */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    // public function update(Request $request, $id)

    // {

    //     $category = Category::findOrFail($id);

    //     $request->validate([
    //         "category_name" => "required",
    //         // "category_image" => $request->hasFile("category_image") ? "required|mimes:png,jpg,webp,jpeg" : ""
    //     ]);

    //     if ($request->hasFile("category_image")) {
    //         $categoryImage = $request->file("category_image");
    //         $path =  $categoryImage->store("category_images", "public");
    //         File::delete(public_path("images/") . $category->category_image);
    //         $category->update([
    //             "category_name" => $request->category_name,
    //             "category_image" =>  $path,
    //         ]);

    //         $categories =  Category::all();

    //         return response()->json([
    //             "message" => "Category Added Successfully",
    //             "categories" => $categories
    //         ]);
    //     } else {
    //         $category->update([
    //             "category_name" => $request->category_name,
    //         ]);

    //         $categories =  Category::all();

    //         return response()->json([
    //             "message" => "Category Added Successfully",
    //             "categories" => $categories
    //         ]);
    //     }
    // }


        

public function update(Request $request, $id)
{
    $allowedCountryIds = $this->getAllowedCountryIds();

    $category = Category::get()->first(function ($cat) use ($id, $allowedCountryIds) {
        $ids = array_map('intval', explode(',', $cat->country_id));
        return $cat->id == $id && count(array_intersect($ids, $allowedCountryIds)) > 0;
    });

    if (!$category) abort(404);

    // Get existing country IDs stored in DB
    $existingCountryIds = array_map('intval', explode(',', $category->country_id));

    // Countries in existing record that this user is NOT allowed to manage — preserve these
    $preservedCountryIds = array_filter($existingCountryIds, function($cid) use ($allowedCountryIds) {
        return !in_array($cid, $allowedCountryIds);
    });

    // Handle country_id from request (array from select2 or string from hidden field)
    $submittedCountryId = $request->country_id;
    if (is_string($submittedCountryId) && !empty($submittedCountryId)) {
        $submittedCountryId = array_map('trim', explode(',', $submittedCountryId));
    }

    // Auto-assign if empty and user has only one country
    if (empty($submittedCountryId) && count($allowedCountryIds) === 1) {
        $submittedCountryId = [$allowedCountryIds[0]];
    }

    $request->validate([
        "category_name"  => "required",
        "category_image" => $request->hasFile("category_image") ? "mimes:png,jpg,jpeg" : "nullable",
    ]);

    $updateData = ["category_name" => $request->category_name];

    // Filter submitted to only allowed countries
    $allowedSubmitted = array_filter((array)$submittedCountryId, function($cid) use ($allowedCountryIds) {
        return in_array((int)$cid, $allowedCountryIds);
    });

    // Merge preserved (not allowed to touch) + newly submitted (allowed)
    $finalCountryIds = array_unique(array_merge(array_values($preservedCountryIds), array_values($allowedSubmitted)));

    if (!empty($finalCountryIds)) {
        $updateData["country_id"] = implode(',', $finalCountryIds);
    }

    if ($request->hasFile("category_image")) {
        if ($category->category_image && Storage::disk('public')->exists($category->category_image)) {
            Storage::disk('public')->delete($category->category_image);
        }
        $updateData["category_image"] = $request->file("category_image")->store("category_images", "public");
    }

    $category->update($updateData);

    Product::where('category_id', $category->id)->update(['cate_name' => $request->category_name]);

    $categories = Category::with('country')->get()->filter(function ($cat) use ($allowedCountryIds) {
        $ids = array_map('intval', explode(',', $cat->country_id));
        return count(array_intersect($ids, $allowedCountryIds)) > 0;
    })->values();

    return response()->json(["message" => "Category Updated Successfully", "categories" => $categories]);
}



    /**
     * Remove the specified resource from storage.
     */
public function destroy($id)
{
    $allowedCountryIds = $this->getAllowedCountryIds();

    $category = Category::findOrFail($id);

    if ($category->category_image && Storage::disk('public')->exists($category->category_image)) {
        Storage::disk('public')->delete($category->category_image);
    }

    DB::table('products')->where('category_id', $category->id)->delete();
    DB::table('sub_categories')->where('category_name', $category->id)->delete();
    $category->delete();

    $categories = Category::with('country')->get()->filter(function ($cat) use ($allowedCountryIds) {
        $ids = array_map('intval', explode(',', $cat->country_id));
        return count(array_intersect($ids, $allowedCountryIds)) > 0;
    })->values();

    return response()->json(["message" => "Category Deleted Successfully", "categories" => $categories]);
}

    public function  validateCategoryName(Request $request)
    {


        $request->validate([
            "category_name" => "required|unique:categories"
        ]);

        return response("success");
    }
}
