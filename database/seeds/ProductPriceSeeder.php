<?php

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
                factory(\App\Models\ProductPrice::class)
                    ->create([
                        'product_id'  => $product->id,
                        'location_id' => 100,
                    ]);
            });

        \App\Models\Product::query()
            ->get()
            ->each(function (\App\Models\Product $product) {
                factory(\App\Models\ProductPrice::class)
                    ->create([
                        'product_id'  => $product->id,
                        'location_id' => 99,
                    ]);
            });
    }
}
