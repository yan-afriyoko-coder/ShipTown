<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductsManySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $i = 50000;
        while ($i > 0) {
            try {
                Product::query()->create(['sku' => rand(1000000, 1000000000), 'name' => 'test']);
                $i--;
            } catch (\Exception $e) {
                report($e);
            }
        }
    }
}
