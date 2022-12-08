<?php

namespace Database\Factories;

use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\Warehouse;
use App\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class InventoryMovementFactory extends Factory
{
    protected $model = InventoryMovement::class;

    public function definition(): array
    {
        $user = User::query()->inRandomOrder()->first() ?? User::factory()->create();

        /** @var Inventory $inventory */
        $inventory = Inventory::first();

        if ($inventory === null) {
            Warehouse::first() ?? Warehouse::factory()->create();
            Product::first() ?? Product::factory()->create();
            $inventory = Inventory::create([
                'warehouse_id' => Warehouse::first()->getKey(),
                'product_id' => Product::first()->getKey(),
                'quantity' => 0,
            ]);
        }

        $quantity_delta = rand(1, 100);
        $quantity_before = $inventory->quantity;
        $quantity_after = $quantity_before + $quantity_delta;

        $inventory->update(['quantity' => $quantity_after]);

        return [
            'inventory_id' => $inventory->getKey(),
            'product_id' => $inventory->product_id,
            'warehouse_id' => $inventory->warehouse_id,
            'quantity_delta' => $quantity_delta,
            'quantity_before' => $quantity_before,
            'quantity_after' => $quantity_after,
            'description' => $this->faker->word,
            'user_id' => $user->getKey(),
        ];
    }
}
