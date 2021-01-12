<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\ProductPrice;
use Faker\Generator as Faker;

$factory->define(ProductPrice::class, function (Faker $faker) {
    return [
        'product_id' => \App\Models\Product::query()->inRandomOrder()->first()->id,
        'location_id' => $faker->numberBetween(1, 100),
        'price' => $faker->randomFloat(2, 0.01,1000),
        'sale_price' => $faker->numberBetween(0, 100),
        'sale_price_start_date' => $faker->dateTimeBetween('-1 year', '+20 days'),
        'sale_price_end_date' => $faker->dateTimeBetween('+20 days', '+30 days'),
    ];
});
