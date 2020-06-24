<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\Inventory;
use Faker\Generator as Faker;

$factory->define(Inventory::class, function (Faker $faker) {
    return [
        'location_id' => $faker->numberBetween(1,100),
        'shelve_location' => \Illuminate\Support\Str::upper($faker->randomLetter)
            .$faker->numberBetween(1,20),
        'product_id' => \App\Models\Product::query()->inRandomOrder()->first()->id,
        'quantity' => $faker->numberBetween(0,10000),
        'quantity_reserved' => $faker->numberBetween(0, 10000),
    ];
});
