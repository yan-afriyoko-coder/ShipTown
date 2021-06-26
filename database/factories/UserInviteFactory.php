<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;

$factory->define(\App\Models\UserInvite::class, function (Faker $faker) {
    return [
        'email' => $faker->unique()->safeEmail,
    ];
});
