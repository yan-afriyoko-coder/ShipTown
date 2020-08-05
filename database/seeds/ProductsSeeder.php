<?php

use App\Models\Product;
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
        $this->createProductWithSKUs(['123456']);

        factory(Product::class, 50)->create();
    }

    private function createProductWithSKUs(array $skuList): void
    {
        foreach ($skuList as $sku) {
            if (!Product::query()->where('sku', '=', $sku)->exists()) {
                factory(Product::class)->create(['sku' => $sku]);
            }
        }
    }
}
