<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Map of standard modules and their basic permissions
        $standardModules = [
            'work days',
            'supplies',
            'suppliers',
            'warehouse',
            'expenses',
            'distributions',
            'distributors',
            'workers',
            'attendance',
            'admins',
            'roles',
        ];

        $permissions = [];

        // Generate standard CRUD permissions for modules
        foreach ($standardModules as $module) {
            $permissions[] = "view {$module}";
            $permissions[] = "create {$module}";
            $permissions[] = "edit {$module}";
            $permissions[] = "delete {$module}";
        }

        // Add special/singleton permissions
        $specialPermissions = [
            'view dashboard',
            'view accounts',
            'manage settings',
            'view activity log',
        ];

        $permissions = array_merge($permissions, $specialPermissions);

        // Create the guard-specific permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'admin']);
        }

        // Create Super Admin role and assign ALL permissions
        $role = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'admin']);
        $role->givePermissionTo(Permission::where('guard_name', 'admin')->get());

        // Create standard Admin role (example of a limited role)
        // Adjust these as necessary based on real-world requirements
        $adminRole = Role::firstOrCreate(['name' => 'Administrator', 'guard_name' => 'admin']);
        $adminRole->givePermissionTo([
            'view dashboard',
            'view work days', 'create work days', 'edit work days',
            'view expenses', 'create expenses',
            'view workers', 'view attendance',
            'view distributions', 'create distributions',
        ]);
    }
}
