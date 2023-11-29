<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

class InventoryFactory extends Factory
{
    private Warehouse $warehouse;

    public function definition(): array
    {
        return [
            'quantity' => rand(1, 100),
            'shelve_location' => $this->faker->randomLetter() . $this->faker->randomNumber(2),
            'product_id' => function () {
                return Product::factory()->create();
            },
            'warehouse_id' => function() {
                return Warehouse::factory()->create();
            },
            'warehouse_code' => function() {
                return Warehouse::factory()->create();
            },
        ];
    }

    private function getOrCreateWarehouse(): Warehouse
    {
        if ($this->warehouse) {
            return $this->warehouse;
        }

        $this->warehouse = Warehouse::factory()->create();

        return $this->warehouse;
    }
}
