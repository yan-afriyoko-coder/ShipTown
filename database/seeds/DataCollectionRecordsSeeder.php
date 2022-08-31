<?php

use App\Models\DataCollectionRecord;
use App\Models\Product;
use Illuminate\Database\Seeder;

class DataCollectionRecordsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Product::query()
            ->inRandomOrder()
            ->limit(20)
            ->get()
            ->each(function (Product $product) {
                DataCollectionRecord::query()->firstOrCreate([
                   'product_id' => $product->id,
                ], [
                    'quantity_requested' => 12,
                ]);
            });

        $product = Product::skuOrAlias('45')->first();

        DataCollectionRecord::query()->firstOrCreate(['product_id' => $product->id], ['quantity_requested' => 12]);
    }
}
