<?php

/** @var Factory $factory */

use App\Models\OrderStatus;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(OrderStatus::class, function (Faker $faker) {
    $status = $faker->randomElement([
        [
            'code' => 'open',
            'name' => 'open',
        ],
        [
            'code' => 'closed',
            'name' => 'closed',
        ],
        [
            'code' => 'pending',
            'name' => 'pending',
        ],
    ]);

    return [
        'code'           => $status['code'],
        'name'           => $status['name'],
        'reserves_stock' => $faker->boolean,
    ];
});
