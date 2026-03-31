<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    public function run()
    {
        Currency::firstOrCreate(
            ['code' => 'USD'],
            ['name' => 'دولار أمريكي', 'exchange_rate' => 1.00, 'is_default' => true]
        );
        Currency::firstOrCreate(
            ['name' => 'ليرة سورية (قديمة)', 'exchange_rate' => 15000.00, 'is_default' => false]
        );
        Currency::firstOrCreate(
            ['code' => 'SYP'],
            ['name' => 'ليرة سورية (جديدة)', 'exchange_rate' => 15500.00, 'is_default' => false]
        );
    }
}
