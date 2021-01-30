<?php

/** @var Factory $factory */

use App\Models\Order;
use App\Models\OrderAddress;
use App\Models\OrderStatus;
use App\User;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(Order::class, function (Faker $faker) {
    $shippingAddress = factory(OrderAddress::class)->create();

    $newOrder = [
        'order_number' => (string)(10000000 + $faker->unique()->randomNumber(7)),
        'shipping_address_id' => $shippingAddress->getKey(),
        'order_placed_at' => $faker->dateTimeBetween('-1 months', now()),
        'status_code' => $faker->randomElement([
            'complete','processing','cancelled','on_hold','paid','picking','packing'
        ])
    ];

    if (OrderStatus::isComplete($newOrder['status_code'])) {
        $user = User::query()->inRandomOrder()->first('id') ?? factory(User::class)->create();

        $newOrder['order_closed_at'] = $faker->dateTimeBetween($newOrder['order_placed_at'], now());
        $newOrder['packer_user_id'] = $user->getKey();
        $newOrder['packed_at'] = $newOrder['order_closed_at'];
    }

    return $newOrder;
});
