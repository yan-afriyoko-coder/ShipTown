<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ProductPriceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Product::query()
            ->get()
            ->each(function (\App\Models\Product $product) {
                \App\Models\ProductPrice::factory()
                    ->create([
                        'product_id'  => $product->id,
                        'location_id' => 100,
                    ]);
            });

        \App\Models\Product::query()
            ->get()
            ->each(function (\App\Models\Product $product) {
                \App\Models\ProductPrice::factory()
                    ->create([
                        'product_id'  => $product->id,
                        'location_id' => 99,
                    ]);
            });
    }
}
