<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Admin::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'first_name' => 'Default',
                'last_name' => 'Admin',
                'password' => Hash::make('Password@123'),
                'gender' => 'male',
                'status' => 'active',
            ]
        );

        // Assign Super Admin role to the default administrator
        if (!$admin->hasRole('Super Admin')) {
            $admin->assignRole('Super Admin');
        }
    }
}
