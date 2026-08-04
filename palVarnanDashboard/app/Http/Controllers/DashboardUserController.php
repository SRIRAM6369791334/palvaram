<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\DashboardUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;
use Spatie\Permission\Models\Role;

class DashboardUserController extends Controller
{

    private function getAllowedCountryIds()
    {
        $user = auth()->user();

        $countryIds = is_array($user->country_id)
            ? $user->country_id
            : json_decode($user->country_id, true) ?? [];

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

    public function index()
    {
        $allowedCountryIds = $this->getAllowedCountryIds();

        $users = DashboardUser::with(['roles'])
            ->get()
            ->filter(function ($user) use ($allowedCountryIds) {

                $ids = is_array($user->country_id)
                    ? $user->country_id
                    : json_decode($user->country_id, true);

                $ids = array_map('intval', $ids ?? []);

                return count(array_intersect($ids, $allowedCountryIds)) > 0;
            })
            ->values();

        $roles = Role::all();

        $countries = Country::whereIn('id', $allowedCountryIds)->get();

        return view(
            'pages.dashboard_user',
            compact('users', 'roles', 'countries', 'allowedCountryIds')
        );
    }

    public function userss(Request $request)
    {
        $validated = $request->validate([
            'name' => "required",
            'email' => "required",
            'phone_number' => "required",
            'role' => "required",
            'status' => "required",
            'password' => "required",
            'country_id' => 'required|array',
            'country_id.*' => 'exists:countries,id',
        ]);

        // Generate Employee ID
        $lastInsertId = DashboardUser::max('id');
        $maxValue = $lastInsertId ? $lastInsertId + 1 : 1;
        $invID = str_pad($maxValue, 5, '0', STR_PAD_LEFT);
        $stylecode = "HG-EMP-" . $invID;

        // Create user (ONLY ONCE ✅)
        $user = DashboardUser::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'status' => $request->status,
            'password' => Hash::make($request->password),
            'empl_num' => $stylecode,
            'country_id' => $request->country_id 
        ]);

        // ✅ Assign role
        $user->assignRole($request->role);

        // ✅ Return all users with roles
        $users = DashboardUser::with(['roles'])->get();

        return response()->json([
            "message" => "User Added Successfully",
            "users" => $users
        ]);
    }


    public function update(Request $request, $id)
    {
        $user = DashboardUser::findOrFail($id);

        $validated = $request->validate([
            'name' => "required",
            'email' => "required",
            'phone_number' => "required",
            'role' => "required",
            'status' => "required",
            'country_id' => 'nullable|array',
            'country_id.*' => 'exists:countries,id',
        ]);

        $countryIds = $request->country_id;

        if (!is_array($countryIds)) {
            $countryIds = [];
        }

        $user->update([
            'name' => $validated["name"],
            'email' => $validated["email"],
            'phone_number' => $validated["phone_number"],
            'status' => $validated["status"],
            'country_id' => $countryIds
        ]);

        // ✅ Update role
        $user->syncRoles([$request->role]);

        // ✅ Return all users with roles
        $users = DashboardUser::with(['roles'])->get();

        return response()->json([
            "message" => "User Updated Successfully",
            "users" => $users
        ]);
    }


public function destroy($id)
{
    DashboardUser::where('id', $id)->delete();

    $users = DashboardUser::with(['roles'])->get();

    return response()->json([
        "message" => "User Deleted Successfully",
        "users" => $users
    ]);
}

public function update1(Request $request, $id)
{
    $user = DashboardUser::findOrFail($id);

    $validated = $request->validate([
        'password' => 'required',
    ]);

    $user->update([
        'password' => Hash::make($validated['password']),
    ]);

    $users = DashboardUser::with(['roles'])->get();

    return response()->json([
        'message' => 'User Password Successfully Updated',
        'users' => $users,
    ]);
}



}
