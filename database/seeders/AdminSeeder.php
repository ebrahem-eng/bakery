<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        Admin::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Default Admin',
                'email' => 'admin@example.com',
                'password' => Hash::make('Password@123'),
                'gender' => 'male',
                'status' => 'active',
                'phone' => '0000000000',
            ]
        );
    }
}
