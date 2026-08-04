<?php

namespace App\Http\Controllers;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')->get();
        $permissions = Permission::all();

        return view('pages.roles', compact('roles', 'permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|min:3|unique:roles,name'
        ]);

        Role::create([
            'name' => $request->name,
            'guard_name' => 'web'
        ]);

        return response()->json([
            "message" => "Role Created",
            "roles" => Role::with('permissions')->get()
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|min:3|unique:roles,name,' . $id
        ]);

        $role = Role::findOrFail($id);

        $role->update([
            'name' => $request->name
        ]);

        return response()->json([
            "message" => "Role Updated",
            "roles" => Role::with('permissions')->get()
        ]);
    }

    public function destroy($id)
    {
        Role::findOrFail($id)->delete();

        return response()->json([
            "message" => "Deleted",
            "roles" => Role::with('permissions')->get()
        ]);
    }

    public function permissions(Role $role)
    {
        $permissions = Permission::all()->groupBy(function ($perm) {
            return explode('.', $perm->name)[0];
        });

        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return view('pages.permissions', compact('role', 'permissions', 'rolePermissions'));
    }

public function updatePermissions(Request $request, Role $role)
{
    $role->syncPermissions($request->permissions ?? []);
    return redirect()->route('roles.index')->with('success', 'Permissions updated');
}

}