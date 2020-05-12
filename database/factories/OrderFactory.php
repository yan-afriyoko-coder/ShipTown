<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Order;
use Faker\Generator as Faker;

$factory->define(Order::class, function (Faker $faker) {
    return [
        'order_number' => (string) (10000000 + $faker->unique()->randomNumber(7)),
        'status_code' => $faker->randomElement(['complete','processing','cancelled'])
    ];
});
