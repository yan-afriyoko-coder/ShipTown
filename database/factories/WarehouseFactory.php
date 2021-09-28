<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;

$factory->define(\App\Models\Warehouse::class, function (Faker $faker) {
    return [
        'name'  => $faker->city,
        'code'  => rand(1, 1000),
    ];
});
