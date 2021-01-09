<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = $this->getData();
        DB::table('products')->insert($data);
    }

    private function getData()
    {
        return [
            [
                'name' => 'Veg biriyani',
                'quantity' => 5,
                'price' => 70,
                'alert_quantity' => 2
            ],
            [
                'name' => 'Chicken biriyani',
                'quantity' => 16,
                'price' => 100,
                'alert_quantity' => 2
            ],
            [
                'name' => 'Meal',
                'quantity' => 5,
                'price' => 70,
                'alert_quantity' => null
            ],
            [
                'name' => 'Special meal',
                'quantity' => 15,
                'price' => 100,
                'alert_quantity' => 3
            ],
            [
                'name' => 'Tea',
                'quantity' => 100,
                'price' => 10,
                'alert_quantity' => 3
            ]
        ];
    }
}
