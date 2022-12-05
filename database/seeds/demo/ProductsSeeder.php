<?php

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
        Product::factory()->create(['sku' => '57', 'name' => 'ShipTown Test Product - 57']);

        $this->createSkuWithAliases([
            '01',
            '02',
            '03',
            '04',
            '05',
            '06',
            '8413848043283',
            '3276000690573'
        ]);

        Product::factory()->count(50)->create();

        ProductAlias::factory()->create([
            'product_id' => Product::factory()->create()->getKey(),
            'alias'      => '45',
        ]);
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
