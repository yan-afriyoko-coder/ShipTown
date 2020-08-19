<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Order;
use App\Models\OrderAddress;
use Faker\Generator as Faker;

$factory->define(Order::class, function (Faker $faker) {

    $order_closed_at = null;

    $status_code = $faker->randomElement(['complete','processing','cancelled']);

    $order_placed_at = $faker->dateTimeBetween('-1 year');

    if($status_code !== 'processing') {
        $order_closed_at = $faker->dateTimeBetween($order_placed_at);
    }

    return [
        'order_number' => (string) (10000000 + $faker->unique()->randomNumber(7)),
        'shipping_address_id' => factory(OrderAddress::class)->create()->id,
        'order_placed_at' => $order_placed_at,
        'order_closed_at' => $order_closed_at,
        'status_code' => $status_code,
    ];

});
