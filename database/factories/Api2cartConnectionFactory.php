<?php

/** @var Factory $factory */

use App\Models\Warehouse;
use App\Modules\Api2cart\src\Models\Api2cartConnection;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(Api2cartConnection::class, function (Faker $faker) {
    $warehouse = Warehouse::query()->inRandomOrder()->first();

    return [
        'type'                    => $faker->randomElement(['Magento', 'Prestashop', 'Shopify']),
        'magento_store_id'        => 0,
        'pricing_location_id'     => $warehouse->code,
        'bridge_api_key'          => config('api2cart.api2cart_test_store_key') ?? $faker->uuid,
    ];
});
