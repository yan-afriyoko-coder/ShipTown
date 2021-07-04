<?php

/** @var Factory $factory */

use App\Modules\Api2cart\src\Models\Api2cartConnection;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(Api2cartConnection::class, function (Faker $faker) {
    return [
        'location_id'           => random_int(1, 100),
        'type'                  => $faker->randomElement(['Magento', 'Prestashop', 'Shopify']),
        'magento_store_id'      => 0,
        'inventory_location_id' => $faker->randomNumber(1),
        'pricing_location_id'   => $faker->randomNumber(1),
        'bridge_api_key'        => config('api2cart.api2cart_test_store_key') ?? $faker->uuid,
    ];
});
