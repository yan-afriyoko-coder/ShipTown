<?php

namespace Database\Factories;

use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @mixin InventoryMovement
 */
class InventoryMovementFactory extends Factory
{
    public function definition(): array
    {
        $user = User::query()->inRandomOrder()->first() ?? User::factory()->create();

        /** @var Inventory $inventory */
        $inventory = Inventory::query()->inRandomOrder()->first() ?? Inventory::factory()->create();

        $quantity_delta = rand(1, 100);

        return [
            'occurred_at'       => now(),
            'inventory_id'      => $inventory->getKey(),
            'type'              => $this->faker->word(),
            'quantity_delta'    => $quantity_delta,
            'quantity_before'   => $inventory->quantity,
            'quantity_after'    => $inventory->quantity + $quantity_delta,
            'description'       => $this->faker->word(),
            'product_id'        => $inventory->product_id,
            'warehouse_id'      => $inventory->warehouse_id,
            'user_id'           => $user->getKey(),
        ];
    }
}
