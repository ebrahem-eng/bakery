<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    public function run()
    {
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
            'view accounts p&l', 'filter accounts p&l',
            'view accounts ledger', 'filter accounts ledger',
            'view accounts debts', 'pay accounts debts',

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
            'view settings general', 'edit settings general',
            'view settings currencies', 'edit settings currencies',
            'view settings categories', 'edit settings categories',

            // ── Public Contact Messages ────────────────────────────────
            'view contact messages', 'delete contact messages',

            // ── System Notifications ───────────────────────────────────
            'view notifications', 'manage notifications',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'admin']);
        }

        // ── Super Admin Role ───────────────────────────────────────────────
        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'admin']);
        
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Assign ALL permissions to Super Admin
        $superAdmin->syncPermissions(Permission::where('guard_name', 'admin')->get());
    }
}
