<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use Spatie\Tags\Tag;

$factory->define(Tag::class, function (Faker $faker) {
    return [
        'name' => $faker->word,
    ];
});
