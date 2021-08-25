<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;

$factory->define(\App\Models\Warehouse::class, function (Faker $faker) {
    $name = $faker->city;
    return [
        'name'  => $name,
        'code'  => str_replace(" ", "", $name)
    ];
});
