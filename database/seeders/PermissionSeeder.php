<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            // Work Days
            'view work days', 'create work days', 'edit work days', 'delete work days',
            // Suppliers
            'view suppliers', 'create suppliers', 'edit suppliers', 'delete suppliers',
            // Supply (Purchases)
            'view supplies', 'create supplies', 'edit supplies', 'delete supplies',
            // Workers
            'view workers', 'create workers', 'edit workers', 'delete workers',
            // Worker Attendance / Changes
            'view attendance', 'manage attendance',
            'view presence', 'manage presence',
            'view wages', 'manage wages',
            // Distributors
            'view distributors', 'create distributors', 'edit distributors', 'delete distributors',
            // Sales / Distributions
            'view distributions', 'create distributions', 'edit distributions', 'delete distributions',
            // Expenses / Drawings
            'view expenses', 'create expenses', 'edit expenses', 'delete expenses',
            // Reports / Financials
            'view financial reports',
            // Admins & Roles
            'view admins', 'create admins', 'edit admins', 'delete admins',
            'view roles', 'create roles', 'edit roles', 'delete roles',
            // Settings & Categories
            'manage settings', 'manage categories',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'admin']);
        }
    }
}
