<?php

namespace App\Http\Controllers;

use App\Models\AllIndiaPincode;
use App\Models\Area;
use App\Models\Category;
use App\Models\City;
use App\Models\GenderType;
use App\Models\Product;
use App\Models\ProductVarient;
use App\Models\State;
use App\Models\User;
use App\Models\UserAddress;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $countryId = session('country_id');

        $users = User::select(
                'users.*',
                'countries.name as country_name',
                'user_addresses.address_line_one',
                'user_addresses.address_line_two',
                'user_addresses.landmark',
                'user_addresses.pincode',
                'user_addresses.city',
                'user_addresses.area_name',
                'user_addresses.state',
                'user_addresses.phone_code',
                'user_addresses.address_type_id'
            )
            ->leftJoin('user_addresses', 'users.user_default_address_id', '=', 'user_addresses.id')
            ->leftJoin('countries', 'users.country_id', '=', 'countries.id'); // ✅ ADD THIS

        if (!empty($countryId)) {
            $users->where('users.country_id', $countryId);
        }

        $users = $users->get();

        $products = Product::all();
        $categories = Category::all();
        $states = State::all();
        $citiess = City::all();

        return view("pages.customer", compact("users", "products", "categories", "states", "citiess"));
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



    public function store(Request $request)
    {
        $request->validate([
            "user_name" => "required",
            "email" => "required|email",
            "phone_number" => "required|digits:10",
            "password" => "required",
            "is_guest_user" => "required",

            'address_line_one' => "required",
            'address_line_two' => "required",
            'landmark' => "nullable",
            'address_type_id' => "required",
            'pincode' => "required",
            'city' => 'required',
            'area_name' => 'required',
            'state' => 'required',
        ]);

        // Generate a unique user ID
        $currentYear = date("y");
        $lastUserId = User::max('id');
        $newUserId = sprintf('HG-%s-%05d', $currentYear, $lastUserId + 1);

        // Create the user
        $user = User::create([
            "name" => $request->user_name,
            "email" => $request->email,
            "phone_number" => $request->phone_number,
            "first_name" => $request->user_name,
            "password" => Hash::make($request->password),
            "is_guest_user" => $request->is_guest_user,
            "enc_password" => Crypt::encryptString($request->password),
            "user_id" => $newUserId,
            "address_type_id" => $request->address_type_id,
        ]);

        // Create the user address
        $userAddress = UserAddress::create([
            'user_id' => $user->user_id,
            'address_username' => $user->name,// Assuming you have a foreign key relationship between User and UserAddress
            'address_line_one' => $request->address_line_one,
            'address_line_two' => $request->address_line_two,
            'landmark' => $request->landmark,
            'address_type_id' => $request->address_type_id,
            'pincode' => $request->pincode,
            'area_name' => $request->area_name,
            'city' => $request->city,
            'state' => $request->state,
        ]);


        // Update the user's default address
        $user->update([
            "user_default_address_id" => $userAddress->id,
        ]);

        // Retrieve the user with addresses for response
        $users = User::with("user_addresses", "defaultAddress.area")->find($user->user_id);

        $users = User::select('users.*', 'user_addresses.address_line_one','user_addresses.address_line_two','user_addresses.landmark','user_addresses.pincode','user_addresses.city','user_addresses.area_name','user_addresses.state','user_addresses.address_type_id')
            ->Join('user_addresses', 'users.user_default_address_id', '=', 'user_addresses.id')
            ->get();
        return response()->json([
            "message" => "User Added Successfully",
            "users" => $users,
        ]);
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
    public function edit($customerId)
    {
        $user = User::with("user_addresses.area")
            ->where("user_id", $customerId)
            ->orWhere("id", $customerId)
            ->first();

        if (!$user) {
            abort(404, 'User not found');
        }

        $genderTypes =   GenderType::all();
        $areas = Area::all();
        return view("pages.customer_edit", compact("user", "genderTypes", "areas"));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
{
    $user = User::findOrFail($id);
    $userAddress = UserAddress::findOrFail($id);

    $request->validate([
        "user_name" => "required",
        "email" => "required|email",
        "phone_number" => "required|digits:10",
        "password" => $request->password ? "required" : "",
        "is_guest_user" => "required",

        'address_line_one' => "required",
        'address_line_two' => "required",
        'landmark' => "nullable",
        'address_type_id' => "required",
        'pincode' => "required",
        'city' => 'required',
        'area_name' => 'required',
        'state' => 'required',
    ]);

    $userUpdateData = [
        "name" => $request->user_name,
        "email" => $request->email,
        "phone_number" => $request->phone_number,
        "is_guest_user" => $request->is_guest_user,
    ];

    if ($request->password) {
        $userUpdateData["password"] = Hash::make($request->password);
        $userUpdateData["enc_password"] = Crypt::encryptString($request->password);
    }

    $user->update($userUpdateData);

    $userAddressUpdateData = [
        "address_line_one" => $request->address_line_one,
        "address_line_two" => $request->address_line_two,
        "landmark" => $request->landmark,
        "address_type_id" => $request->address_type_id,
        "pincode" => $request->pincode,
        "city" => $request->city,
        "area_name" => $request->area_name,
        "state" => $request->state,
    ];

    $userAddress->update($userAddressUpdateData);

    $users = User::select('users.*', 'user_addresses.address_line_one','user_addresses.address_line_two','user_addresses.landmark','user_addresses.pincode','user_addresses.city','user_addresses.area_name','user_addresses.state','user_addresses.address_type_id')
            ->Join('user_addresses', 'users.user_default_address_id', '=', 'user_addresses.id')
            ->get();

    return response()->json([
        "message" => "User and UserAddress updated successfully",
        "users" => $users
    ]);
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user =  User::findOrFail($id);
        $UserAddress = UserAddress::findOrFail($id);

        $user->deleteOrFail();
        $UserAddress->deleteOrFail();

        $users =  User::with("user_addresses", "defaultAddress.area")->get();

        return response()->json([
            "message" => "User deleted Successfully",
            "users" => $users
        ]);
    }


    public function getProductsOptions(Request $request)
    {
        $categoryId =   $request->category_id;

        $products =  Product::query()->where("category_id", $categoryId)->get();

        return view("ajaxPages.product_options", compact("products"));
    }

    public function getProductsverentOptions(Request $request)
    {
        $productId =   $request->product_id;

        $productsver =  ProductVarient::query()->where("product_id", $productId)->get();

        // dd($productsver);

        return view("ajaxPages.productvar_options", compact("productsver"));
    }

    public function getProductsverentqty(Request $request)
    {
        $productVarId =   $request->id;

        $productsverqty =  ProductVarient::query()->where("id", $productVarId)->first();

        // dd($productsver);
        return response()->json($productsverqty);

        return view("ajaxPages.productvar_qty", compact("productsverqty"));
    }

    public function getcustomersummery(Request $request)
    {
        $fromDate = Carbon::parse($request->input('frdate'))->subDay()->format('Y-m-d');
        $toDate = Carbon::parse($request->input('todate'))->addDay()->format('Y-m-d');


        $users = User::select('users.*', 'user_addresses.address_line_one')
            ->join('user_addresses', 'users.user_id', '=', 'user_addresses.user_id')
            ->whereBetween('users.created_at', [$fromDate, $toDate]) // Specify the table for created_at
            ->get();



        $users->transform(function ($user) {
            $user->created_at = Carbon::parse($user->created_at)->format('Y-m-d');
            return $user;
        });

        $data = [
            'users' => $users,
            'i' => 1,
        ];

        return $data;
    }


    public function updatePass(Request $request, $userId)
    {

        // dd($userId);
        $users = User::find($userId);


        $validated = $request->validate([
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $hashedPassword =  Hash::make($validated['password']);


        $users->update([
            'password' => $hashedPassword,
        ]);



        return $users;
    }

    public function addaddressvalue(Request $request)
    {
        $validated = $request->validate([
            'user_id' => "required",
            'value_id' => "required",
            'address_line_one' => "required",
            'address_line_two' => "required",
            'landmark' => "nullable",
            'address_type_id' => "required",
            'pincode' => "required",
            'city' => 'required',


        ]);

        $userid = $request->user_id;

        $users =  UserAddress::create([...$validated,]);
        $userAddressId = $users->id;


        $user_details = User::where('user_id', $userid)->first();


        $users = User::where('user_id', $userid)
            ->update([
                "user_default_address_id" => $userAddressId,
            ]);

        $users = User::select('users.*', 'user_addresses.address_line_one')
            ->Join('user_addresses', 'users.user_id', '=', 'user_addresses.user_id')
            ->get();

        return response()->json([
            "message" => "User Address Successfully",
            "users" => $users
        ]);
    }

    public function Getcity($custid)
    {


        $users = City::where('state_id', $custid)->get();
        return response()->json($users);
    }

    public function getAddressDetails(Request $request)
    {
        $pincode = $request->get('pincode');

        // Make a request to the external API using the pincode
        $databaseData = AllIndiaPincode::where('pincode', $pincode)->get();

        $cities = [];

        if ($databaseData->isNotEmpty()) {
            foreach ($databaseData as $pincodeRow) {
                $city = $pincodeRow->officename;
                $district = $pincodeRow->Districtname;
                $state = $pincodeRow->statename;

                // Add city and state details to the array
                $cities[] = "City: $city, District: $district, State: $state";
            }

            // Return the response after processing all items in the collection
            return response()->json($cities);
        }

        // If $databaseData is empty, you may want to handle this case accordingly
        // For example, return an empty array or a specific message
        return response()->json([]);
    }

    public function getAddressDetails1(Request $request)
    {
        $pincode = $request->get('pincode');

        // Make a request to the external API using the pincode
        $databaseData = AllIndiaPincode::where('pincode', $pincode)->get();

        $cities = [];

        if ($databaseData->isNotEmpty()) {
            foreach ($databaseData as $pincodeRow) {
                $city = $pincodeRow->officename;
                $district = $pincodeRow->Districtname;
                $state = $pincodeRow->statename;

                // Add city and state details to the array
                $cities[] = "City: $city, District: $district, State: $state";
            }

            // Return the response after processing all items in the collection
            return response()->json($cities);
        }

        // If $databaseData is empty, you may want to handle this case accordingly
        // For example, return an empty array or a specific message
        return response()->json([]);
    }



    // Return the details (you can format this as needed)
    // return $cities;

}
