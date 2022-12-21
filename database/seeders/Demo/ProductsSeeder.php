<?php

namespace Database\Seeders\Demo;

use App\Models\Product;
use App\Models\ProductAlias;
use Illuminate\Database\Seeder;

class ProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Product::factory()->count(50)->create();

        Product::query()->updateOrCreate(['sku' => '45', 'name' => 'Test Product - 45'], []);
        Product::query()->updateOrCreate(['sku' => '57', 'name' => 'Test Product - 57'], []);
        Product::query()->updateOrCreate(['sku' => '3001', 'name' => 'Test Product - 3001'], []);
        Product::query()->updateOrCreate(['sku' => '3002', 'name' => 'Test Product - 3002'], []);
        Product::query()->updateOrCreate(['sku' => '3003', 'name' => 'Test Product - 3003'], []);
        Product::query()->updateOrCreate(['sku' => '3004', 'name' => 'Test Product - 3004'], []);
        Product::query()->updateOrCreate(['sku' => '3005', 'name' => 'Test Product - 3005'], []);

//        $this->createSkuWithAliases([
//            '1',
//            '2',
//            '3',
//            '4',
//            '5',
//            '6',
//            '01',
//            '02',
//            '03',
//            '04',
//            '05',
//            '06',
//            '8413848043283',
//            '3276000690573'
//        ]);
//
//
//        ProductAlias::factory()->create([
//            'product_id' => Product::factory()->create()->getKey(),
//            'alias'      => '45',
//        ]);
    }

    private function createSkuWithAliases(array $skuList): void
    {
        foreach ($skuList as $sku) {
            if (!Product::query()->where('sku', '=', $sku)->exists()) {
                /** @var Product $product */
                $product = Product::factory()->create(['sku' => $sku]);

                ProductAlias::factory()->create([
                    'product_id' => $product->getKey(),
                    'alias'      => $product->sku.'-alias',
                ]);

                ProductAlias::factory()->create([
                    'product_id' => $product->getKey(),
                    'alias'      => $product->sku.'a',
                ]);
            }
        }
    }
}
