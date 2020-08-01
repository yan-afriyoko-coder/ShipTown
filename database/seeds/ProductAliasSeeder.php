<?php

use App\Models\Product;
use App\Models\ProductAlias;
use Illuminate\Database\Seeder;

class ProductAliasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(!ProductAlias::query()->where(['alias' => '45'])->exists()) {
            factory(ProductAlias::class)->create(['alias'    => '45']);
        }

        factory(ProductAlias::class, 10)->create();


        $product = Product::query()->where(['sku' => '0001'])->first();

        if(!$product) {
            $product = factory(Product::class)->create([
                'sku' => '0001'
            ]);
        }

        ProductAlias::query()->firstOrCreate([
            'alias' => $product->sku.'-alias'
        ], [
            'product_id' => $product->getKey()
        ]);

    }
}
