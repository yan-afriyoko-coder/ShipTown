<?php

/** @var Factory $factory */

use App\Models\Warehouse;
use App\Modules\Api2cart\src\Models\Api2cartConnection;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(Api2cartConnection::class, function (Faker $faker) {
    $warehouse = Warehouse::query()->inRandomOrder()->first();

    return [
        'location_id'             => random_int(1, 100),
        'type'                    => $faker->randomElement(['Magento', 'Prestashop', 'Shopify']),
        'magento_store_id'        => 0,
        'inventory_warehouse_ids' => Warehouse::query()->pluck('id'),
        'inventory_location_id'   => $faker->randomNumber(1),
        'pricing_location_id'     => $warehouse->code,
        'bridge_api_key'          => config('api2cart.api2cart_test_store_key') ?? $faker->uuid,
    ];
});
