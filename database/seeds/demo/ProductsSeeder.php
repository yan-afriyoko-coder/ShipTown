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
        factory(Product::class)->create(['sku' => '57', 'name' => 'ShipTown Test Product - 57']);

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

        factory(Product::class, 50)->create();

        factory(ProductAlias::class)->create([
            'product_id' => factory(Product::class)->create()->getKey(),
            'alias'      => '45',
        ]);
    }

    private function createSkuWithAliases(array $skuList): void
    {
        foreach ($skuList as $sku) {
            if (!Product::query()->where('sku', '=', $sku)->exists()) {
                /** @var Product $product */
                $product = factory(Product::class)->create(['sku' => $sku]);

                factory(ProductAlias::class)->create([
                    'product_id' => $product->getKey(),
                    'alias'      => $product->sku.'-alias',
                ]);

                factory(ProductAlias::class)->create([
                    'product_id' => $product->getKey(),
                    'alias'      => $product->sku.'a',
                ]);
            }
        }
    }
}
