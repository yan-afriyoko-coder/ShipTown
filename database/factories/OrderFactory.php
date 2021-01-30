<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Order;
use App\Models\OrderAddress;
use App\Models\OrderStatus;
use App\User;
use Faker\Generator as Faker;

$factory->define(Order::class, function (Faker $faker) {
    $order = [
        'order_number' => (string)(10000000 + $faker->unique()->randomNumber(7)),
        'shipping_address_id' => factory(OrderAddress::class)->create()->id,
        'order_placed_at' => $faker->dateTimeBetween('-1 months', now()),
        'status_code' => $faker->randomElement([
            'complete','processing','cancelled','on_hold','paid','picking','packing'
        ])
    ];

    if (OrderStatus::isComplete($order['status_code'])) {
        $order['order_closed_at'] = $faker->dateTimeBetween($order['order_placed_at'], now());
        $order['packer_user_id'] = User::query()->inRandomOrder()->first('id')->getKey();
        $order['packed_at'] = $order['order_closed_at'];
    }

    return $order;
});
