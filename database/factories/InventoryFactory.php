<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\Inventory;
use App\Models\Product;
use Faker\Generator as Faker;

$factory->define(Inventory::class, function (Faker $faker) {
    $product = Product::query()->inRandomOrder()->first() ?? factory(Product::class)->create();
    $shelveLocation = \Illuminate\Support\Str::upper($faker->randomLetter) . $faker->numberBetween(1, 20);

    return [
        'location_id'       => $faker->numberBetween(1, 100),
        'product_id'        => $product->getKey(),
        'shelve_location'   => $shelveLocation,
        'quantity'          => $faker->numberBetween(0, 100),
        'quantity_reserved' => 0,
    ];
});
