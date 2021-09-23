<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Modules\Automations\src\Models\Automation;
use Faker\Generator as Faker;

$factory->define(Automation::class, function (Faker $faker) {
    return [
        'name' => $faker->sentence(3),
    ];
});
