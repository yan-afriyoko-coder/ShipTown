<?php

namespace Database\Seeders\Demo;

use App\Models\Product;
use App\Models\ProductAlias;
use App\Models\ProductPrice;
use App\Services\PricingService;
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

        Product::factory()->create(['sku' => '41', 'name' => 'Tennis Balls 6pk']);
        Product::factory()->create(['sku' => '42', 'name' => 'White Tennis Shirt L']);
        Product::factory()->create(['sku' => '43', 'name' => 'Equipment Trolley Black']);
        Product::factory()->create(['sku' => '44', 'name' => 'Tennis Racket EVO PRO']);
        Product::factory()->create(['sku' => '45', 'name' => 'Test Product - 45']);
        Product::factory()->create(['sku' => '57', 'name' => 'Test Product - 57']);

        PricingService::updateProductPrice('41', 29);

        Product::factory()->create(['sku' => '3001', 'name' => 'Test Product - 3001']);
        Product::factory()->create(['sku' => '3002', 'name' => 'Test Product - 3002']);
        Product::factory()->create(['sku' => '3003', 'name' => 'Test Product - 3003']);
        Product::factory()->create(['sku' => '3004', 'name' => 'Test Product - 3004']);
        Product::factory()->create(['sku' => '3005', 'name' => 'Test Product - 3005']);
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
