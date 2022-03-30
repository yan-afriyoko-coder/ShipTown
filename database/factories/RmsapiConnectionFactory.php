<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Modules\Rmsapi\src\Models\RmsapiConnection;
use Faker\Generator as Faker;

$factory->define(RmsapiConnection::class, function (Faker $faker) {
    $warehouse = \App\Models\Warehouse::query()->inRandomOrder()->first();

    return [
        'location_id'    => $warehouse->code,
        'url'            => 'https://demo.rmsapi.products.management',
        'username'       => 'demo@products.management',
        'password'       => 'secret123',
    ];
});
