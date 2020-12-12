<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;

$factory->define(\App\Models\Invite::class, function (Faker $faker) {
    return [
        'email' => $faker->unique()->safeEmail
    ];
});
