<?php

/** @var Factory $factory */

use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\Warehouse;
use App\User;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(InventoryMovement::class, function (Faker $faker) {
    $user = User::first() ?? factory(User::class)->create();

    /** @var Inventory $inventory */
    $inventory = Inventory::first();

    if ($inventory === null) {
        $warehouse = Warehouse::first() ?? factory(Warehouse::class)->create();
        $product = Product::first() ?? factory(Product::class)->create();
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
        'description' => $faker->word,
        'user_id' => $user->getKey(),
    ];
});
