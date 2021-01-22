<?php

/** @var Factory $factory */

use App\Models\Configuration;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(Configuration::class, function (Faker $faker) {
    return [
        'key' => $faker->word,
        'value' => $faker->word
    ];
});
