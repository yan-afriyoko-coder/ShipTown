<?php

/** @var Factory $factory */

use App\Modules\Api2cart\src\Models\Api2cartConnection;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(Api2cartConnection::class, function (Faker $faker) {
    return [
        'location_id' => random_int(1,100),
        'type' => $faker->randomElement(['Magento','Prestashop','Shopify']),
        'bridge_api_key' => $faker->uuid
    ];
});
