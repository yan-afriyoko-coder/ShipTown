<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\OrderAddress;
use App\Modules\DpdUk\src\Models\Connection;
use Faker\Generator as Faker;

$factory->define(Connection::class, function (Faker $faker) {
    $address = OrderAddress::query()->first() ?? factory(OrderAddress::class)->create();

    return [
        'username' => env('TEST_DPDUK_USERNAME', $faker->randomNumber(6)),
        'password' => env('TEST_DPDUK_PASSWORD', 'password'),
        'account_number' => env('TEST_DPDUK_ACCNUMBER', '123456'),
        'collection_address_id' => $address->getKey(),
    ];
});
