<?php

/** @var Factory $factory */

use App\Models\Order;
use App\Models\OrderAddress;
use App\Models\OrderStatus;
use App\User;
use Carbon\Exceptions\InvalidFormatException;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(Order::class, function (Faker $faker) {
    $shippingAddress = factory(OrderAddress::class)->create();

    /** @var OrderStatus $orderStatus */
    $orderStatus = OrderStatus::query()->inRandomOrder()->first() ?? factory(OrderStatus::class)->create();

    do {
        try {
            $dateTime = $faker->dateTimeBetween('-7days', now());
            \Carbon\Carbon::parse($dateTime, new \DateTimeZone('UTC'));
        } catch (InvalidFormatException $exception) {
            $dateTime = null;
        }
    } while ($dateTime === null);


    $newOrder = [
        'order_number'         => (string) (10000000 + $faker->unique()->randomNumber(7)),
        'total'                => $faker->randomNumber(2),
        'shipping_address_id'  => $shippingAddress->getKey(),
        'shipping_method_code' => $faker->randomElement(['next_day', 'store_pickup', 'express']),
        'shipping_method_name' => $faker->randomElement(['method_name_1', 'method_name_2', 'method_name_3']),
        'order_placed_at'      => $dateTime,
        'status_code'          => $orderStatus->code,
    ];

    if (! $orderStatus->order_active) {
        $user = User::query()->inRandomOrder()->first('id') ?? factory(User::class)->create();

        $newOrder['order_closed_at'] = $faker->dateTimeBetween($newOrder['order_placed_at'], now());
        $newOrder['packer_user_id'] = $user->getKey();
        $newOrder['packed_at'] = $newOrder['order_closed_at'];
    }

    return $newOrder;
});
