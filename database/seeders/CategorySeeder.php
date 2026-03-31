<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            [
                'name' => 'طحين',
                'unit' => 'kg',
                'input_mode' => 'bags_weight',
                'track_in_daily_close' => true,
            ],
            [
                'name' => 'مازوت',
                'unit' => 'liters',
                'input_mode' => 'simple_quantity',
                'track_in_daily_close' => true,
            ],
            [
                'name' => 'خميرة',
                'unit' => 'molds',
                'input_mode' => 'cartons_molds',
                'track_in_daily_close' => true,
            ],
            [
                'name' => 'ملح',
                'unit' => 'kg',
                'input_mode' => 'simple_quantity',
                'track_in_daily_close' => true,
            ],
            [
                'name' => 'أكياس',
                'unit' => 'kg',
                'input_mode' => 'simple_quantity',
                'track_in_daily_close' => false,
            ],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(
                ['name' => $cat['name']],
                $cat
            );
        }
    }
}
