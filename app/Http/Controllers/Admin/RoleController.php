<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::where('guard_name', 'admin')->with('permissions')->get();

        return view('Admin.Roles.index', compact('roles'));
    }

    private function getGroupedPermissions()
    {
        $permissions = Permission::where('guard_name', 'admin')->get();
        $grouped = [];

        foreach ($permissions as $permission) {
            $parts = explode(' ', $permission->name);
            $groupName = count($parts) > 1 ? end($parts) : 'general';

            if (! isset($grouped[$groupName])) {
                $grouped[$groupName] = [];
            }
            $grouped[$groupName][] = $permission;
        }

        return collect($grouped);
    }

    public function create()
    {
        $permissionGroups = $this->getGroupedPermissions();

        return view('Admin.Roles.create', compact('permissionGroups'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
            'permissions' => 'array',
        ]);

        $role = Role::create(['name' => $request->name, 'guard_name' => 'admin']);
        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return redirect()->route('admin.roles.index')->with('success', __('Role created successfully.'));
    }

    public function edit(Role $role)
    {
        $permissionGroups = $this->getGroupedPermissions();
        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return view('Admin.Roles.edit', compact('role', 'permissionGroups', 'rolePermissions'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|unique:roles,name,'.$role->id,
            'permissions' => 'array',
        ]);

        $role->update(['name' => $request->name]);
        $role->syncPermissions($request->permissions ?? []);

        return redirect()->route('admin.roles.index')->with('success', __('Role updated successfully.'));
    }

    public function destroy(Role $role)
    {
        $role->delete();

        return redirect()->route('admin.roles.index')->with('success', __('Role deleted successfully.'));
    }
}
