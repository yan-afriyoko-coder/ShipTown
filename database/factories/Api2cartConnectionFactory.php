<?php

/** @var Factory $factory */

use App\Modules\Api2cart\src\Models\Api2cartConnection;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(Api2cartConnection::class, function (Faker $faker) {
    return [
        'location_id' => random_int(1,100),
        'type' => $faker->randomElement(['Magento','Prestashop','Shopify']),
        'magento_store_id' => $faker->randomElement([0,1,2]),
        'inventory_location_id' => $faker->randomNumber(1),
        'pricing_location_id' => $faker->randomNumber(1),
        'bridge_api_key' => $faker->uuid
    ];
});
