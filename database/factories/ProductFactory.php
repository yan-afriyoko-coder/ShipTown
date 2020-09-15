<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\Product;
use Faker\Generator as Faker;

$factory->define(Product::class, function (Faker $faker) {
    return [
        'sku' => (string) $faker->unique()->randomNumber(6),
        'name' => implode(' ', $faker->words()),
        'price' => $faker->randomFloat(2, 0, 1000),
        'sale_price' => $faker->randomFloat(2, 0, 1000),
        'sale_price_start_date' => $faker->dateTimeBetween('-1 year', '+5 months'),
        'sale_price_end_date' => $faker->dateTimeBetween('-1 month', '+1 year'),
    ];
});
