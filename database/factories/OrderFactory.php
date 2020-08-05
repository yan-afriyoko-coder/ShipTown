<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Order;
use Faker\Generator as Faker;

$factory->define(Order::class, function (Faker $faker) {

    $status_code = $faker->randomElement(['complete','processing','cancelled']);

    $order_placed_at = $faker->dateTimeBetween('-1 year');

    $order_closed_at = null;

    if($status_code !== 'processing') {
        $order_closed_at = $faker->dateTimeBetween($order_placed_at);
    }

    return [
        'order_number' => (string) (10000000 + $faker->unique()->randomNumber(7)),
        'order_placed_at' => $order_placed_at,
        'order_closed_at' => $order_closed_at,
        'product_line_count' => $faker->numberBetween(1,4),
        'total_quantity_ordered' => $faker->randomNumber(2),
        'status_code' => $status_code,
    ];

});
