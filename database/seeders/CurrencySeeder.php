<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Currency;

class CurrencySeeder extends Seeder
{
    public function run()
    {
        Currency::firstOrCreate(
            ['code' => 'USD'],
            ['name' => 'دولار أمريكي', 'exchange_rate' => 1.00, 'is_default' => true]
        );
        Currency::firstOrCreate(
            ['code' => 'SYPO'],
            ['name' => 'ليرة سورية (قديمة)', 'exchange_rate' => 15000.00, 'is_default' => false]
        );
        Currency::firstOrCreate(
            ['code' => 'SYPN'],
            ['name' => 'ليرة سورية (جديدة)', 'exchange_rate' => 15500.00, 'is_default' => false]
        );
    }
}
