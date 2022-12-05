<?php



namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\Warehouse;
use App\User;

class InventoryMovementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $user = User::first() ?? User::factory()->create();

        /** @var Inventory $inventory */
        $inventory = Inventory::first();

        if ($inventory === null) {
            $warehouse = Warehouse::first() ?? Warehouse::factory()->create();
            $product = Product::first() ?? Product::factory()->create();
            $inventory = Inventory::first();
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
