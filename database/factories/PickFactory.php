<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Pick;
use Faker\Generator as Faker;

$factory->define(Pick::class, function (Faker $faker) {
    return [
        'sku_ordered' => $faker->name,
        'name_ordered' => $faker->name,
        'quantity_required' => $faker->numberBetween(1,50),
    ];
});
