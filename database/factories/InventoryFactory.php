<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\Inventory;
use Faker\Generator as Faker;

$factory->define(Inventory::class, function (Faker $faker) {
    return [
        'location_id' => $faker->numberBetween(1, 100),
        'product_id' => \App\Models\Product::query()->inRandomOrder()->first()->id,
        'shelve_location' => \Illuminate\Support\Str::upper($faker->randomLetter)
            .$faker->numberBetween(1, 20),
        'quantity' => $faker->numberBetween(0, 100),
        'quantity_reserved' => 0,
    ];
});
