<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Core Config
        $this->call(CurrencySeeder::class);
        $this->call(CategorySeeder::class);
        
        // 2. Roles & Admin
        $this->call(RolesAndPermissionsSeeder::class);
        $this->call(AdminSeeder::class);

        // 3. Realistic Application Data for Testing
        // $this->call(SystemTestingSeeder::class);
    }
}
