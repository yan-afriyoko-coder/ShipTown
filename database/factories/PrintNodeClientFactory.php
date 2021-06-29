<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Modules\PrintNode\src\Models\Client;
use Faker\Generator as Faker;

$factory->define(Client::class, function (Faker $faker) {
    return [
        'api_key' => $faker->text(20)
    ];
});
