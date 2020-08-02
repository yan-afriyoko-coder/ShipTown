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
        factory(ProductAlias::class, 10)->create();

        $this->generateDemoAliases(['45', '3276000690573']);

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

    private function generateDemoAliases(array $aliasList): void
    {
        foreach ($aliasList as $alias) {
            if (!ProductAlias::query()->where(['alias' => $alias])->exists()) {
                factory(ProductAlias::class)->create(['alias' => $alias]);
            }
        }
    }
}
