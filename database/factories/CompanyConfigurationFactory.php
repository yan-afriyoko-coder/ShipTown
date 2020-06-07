<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\CompanyConfiguration;
use Faker\Generator as Faker;

$factory->define(CompanyConfiguration::class, function (Faker $faker) {
    return [
        'bridge_api_key' =>'ed58a22dfecb405a50ea3ea56979360d'
    ];
});
