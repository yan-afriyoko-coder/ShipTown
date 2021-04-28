<?php

/** @var Factory $factory */

use App\Models\OrderStatus;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(OrderStatus::class, function (Faker $faker) {
    return [
        'code' => 'open',
        'name' => 'open',
        'reserves_stock' => $faker->boolean,
    ];
});
