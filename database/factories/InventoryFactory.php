<?php

namespace Database\Factories;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

class InventoryFactory extends Factory
{
    public function definition(): array
    {
        /** @var Product $product */
        $product = Product::factory()->create();

        /** @var Warehouse $warehouse */
        $warehouse = Warehouse::factory()->create();

        return [
            'product_id' => $product->getKey(),
            'warehouse_id' => $warehouse->getKey(),
            'warehouse_code' => $warehouse->code,
            'quantity' => rand(1, 100),
        ];
    }
}
