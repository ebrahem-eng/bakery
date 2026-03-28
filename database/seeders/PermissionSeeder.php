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
            'view_work_days', 'add_work_day', 'edit_work_day', 'delete_work_day',
            // Suppliers
            'view_suppliers', 'add_supplier', 'edit_supplier', 'delete_supplier',
            // Supply (Purchases)
            'view_supplies', 'add_supply', 'edit_supply', 'delete_supply',
            // Workers
            'view_workers', 'add_worker', 'edit_worker', 'delete_worker',
            // Worker Attendance / Changes
            'view_attendance', 'manage_attendance',
            'view_presence', 'manage_presence',
            'view_wages', 'manage_wages',
            // Distributors
            'view_distributors', 'add_distributor', 'edit_distributor', 'delete_distributor',
            // Sales / Distributions
            'view_sales', 'add_sale', 'edit_sale', 'delete_sale',
            // Expenses / Drawings
            'view_expenses', 'add_expense', 'edit_expense', 'delete_expense',
            // Reports / Financials
            'view_financial_reports',
            // Admins & Roles
            'view_admins', 'add_admin', 'edit_admin', 'delete_admin',
            'view_roles', 'add_role', 'edit_role', 'delete_role',
            // Settings & Categories
            'manage_settings', 'manage_categories',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'admin']);
        }
    }
}
