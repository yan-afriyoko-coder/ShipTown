<?php

/** @var Factory $factory */

use App\Models\Picklist;
use App\Models\Product;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;
use Illuminate\Support\Str;

$factory->define(Picklist::class, function (Faker $faker) {
    return [
        'product_id' => Product::query()->inRandomOrder()->first()->id,
        'location_id' => 'WWW',
        'shelve_location' => Str::upper($faker->randomLetter)
            .$faker->numberBetween(0,9),
        'quantity_to_pick' => $faker->numberBetween(0,30)
    ];
});
