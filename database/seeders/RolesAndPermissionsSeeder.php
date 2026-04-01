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

        // ── List of All Permissions ─────────────────────────────────────────
        $permissions = [
            // ── System Access ──────────────────────────────────────────
            'access admin panel', 'view dashboard', 'view analytics', 'view quick access',

            // ── Work Days ──────────────────────────────────────────────
            'view work days', 'create work days', 'edit work days', 'delete work days', 'filter work days',

            // ── Warehouse & Materials ──────────────────────────────────
            'view warehouse', 'create warehouse', 'edit warehouse', 'delete warehouse', 'filter warehouse',

            // ── Suppliers ──────────────────────────────────────────────
            'view suppliers', 'create suppliers', 'edit suppliers', 'delete suppliers', 'filter suppliers',

            // ── Supplies (Purchases) ───────────────────────────────────
            'view supplies', 'create supplies', 'edit supplies', 'delete supplies', 'filter supplies', 'pay supplies',

            // ── Distributors ───────────────────────────────────────────
            'view distributors', 'create distributors', 'edit distributors', 'delete distributors', 'filter distributors',

            // ── Sales / Distributions ──────────────────────────────────
            'view distributions', 'create distributions', 'edit distributions', 'delete distributions', 'filter distributions', 'return distributions',

            // ── Accounts & Ledger ───────────────────────────────────────
            'view accounts', 'view ledger', 'view debts', 'filter accounts', 'filter ledger', 'pay debts',

            // ── Expenses ───────────────────────────────────────────────
            'view expenses', 'create expenses', 'edit expenses', 'delete expenses', 'filter expenses',

            // ── Personnel (HR) ─────────────────────────────────────────
            'view workers', 'create workers', 'edit workers', 'delete workers', 'filter workers',

            // ── Attendance & Shifts ────────────────────────────────────
            'view attendance', 'create attendance', 'edit attendance', 'delete attendance', 'view presence', 'create presence', 'filter attendance',

            // ── Wages & Payments ───────────────────────────────────────
            'view wages', 'create wages', 'edit wages', 'delete wages', 'filter wages',

            // ── Administration ─────────────────────────────────────────
            'view admins', 'create admins', 'edit admins', 'delete admins',
            'view roles', 'create roles', 'edit roles', 'delete roles', 'manage roles',

            // ── Logs & Settings ────────────────────────────────────────
            'view logs', 'clear logs', 'filter logs',
            'manage settings',
        ];

        // Create the guard-specific permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'admin']);
        }

        // ── Super Admin Role ───────────────────────────────────────────────
        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'admin']);
        // Assign ALL permissions to Super Admin
        $superAdmin->syncPermissions(Permission::where('guard_name', 'admin')->get());

        // ── Administrator Role (Sample) ────────────────────────────────────
        $adminRole = Role::firstOrCreate(['name' => 'Administrator', 'guard_name' => 'admin']);
        $adminRole->syncPermissions([
            'access admin panel',
            'view dashboard',
            'view quick access',
            'view work days', 'create work days', 'filter work days',
            'view warehouse', 'filter warehouse',
            'view distributions', 'create distributions', 'filter distributions',
            'view expenses', 'create expenses', 'filter expenses',
            'view workers', 'view attendance', 'filter attendance', 'view presence',
        ]);
    }
}
