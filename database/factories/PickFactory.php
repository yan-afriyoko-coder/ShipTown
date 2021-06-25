<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Pick;
use App\Models\Product;
use App\User;
use Faker\Generator as Faker;

$factory->define(Pick::class, function (Faker $faker) {
    $product = Product::query()->inRandomOrder()->first() ?? factory(Product::class);

    $user = User::query()->inRandomOrder()->first() ?? factory(User::class)->create();

    $skippingPick = (rand(1, 20) === 1);

    return [
        'product_id'               => $product->getKey(),
        'sku_ordered'              => $product->sku,
        'name_ordered'             => $product->name,
        'user_id'                  => $user->getKey(),
        'quantity_picked'          => $skippingPick ? 0 : $faker->numberBetween(1, 50),
        'quantity_skipped_picking' => $skippingPick ? $faker->numberBetween(1, 50) : 0,
    ];
});
