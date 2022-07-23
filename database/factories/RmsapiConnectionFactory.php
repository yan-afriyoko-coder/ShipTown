<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Warehouse;
use App\Modules\Rmsapi\src\Models\RmsapiConnection;
use Faker\Generator as Faker;

$factory->define(RmsapiConnection::class, function (Faker $faker) {
    $warehouse = Warehouse::query()->inRandomOrder()->first() ?? factory(Warehouse::class)->create();

    return [
        'location_id'    => $warehouse->code,
        'url'            => $faker->url,
        'username'       => $faker->companyEmail,
        'password'       => $faker->password,
    ];
});
