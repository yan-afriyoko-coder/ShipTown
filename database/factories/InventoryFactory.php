<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\Inventory;
use Faker\Generator as Faker;

$factory->define(Inventory::class, function (Faker $faker) {
    return [
        'sku' => $faker->word,
        'location_id' => $faker->numberBetween(1,100),
        'quantity' => $faker->randomNumber(),
        'quantity_reserved' => $faker->randomNumber(),
    ];
});
