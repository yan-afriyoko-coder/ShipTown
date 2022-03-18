<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\OrderAddress;
use Faker\Generator as Faker;

$factory->define(\App\Models\Warehouse::class, function (Faker $faker) {
    $address_id = factory(OrderAddress::class)->create();

    $randomCode = $faker->randomLetter . $faker->randomLetter . $faker->randomLetter . $faker->randomLetter;
    return [
        'name'  => $faker->city,
        'code'  => \Illuminate\Support\Str::upper($randomCode),
        'address_id' => $address_id,
    ];
});
