<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\RmsapiConnection;
use Faker\Generator as Faker;

$factory->define(RmsapiConnection::class, function (Faker $faker) {
    return [
        'location_id' => $faker->randomNumber(2),
        'url'         => 'https://demo.rmsapi.products.management',
        'username'    => 'demo@products.management',
        'password'    => 'secret123',
    ];
});
