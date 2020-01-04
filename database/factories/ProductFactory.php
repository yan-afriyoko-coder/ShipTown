<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\Product;
use Faker\Generator as Faker;

$factory->define(Product::class, function (Faker $faker) {
    return [
        'sku' => $faker->word,
        'name' => $faker->name,
        'price' => $faker->randomFloat(2, 0),
        'sale_price' => $faker->randomFloat(2, 0),
        'sale_price_start_date' => $faker->dateTimeBetween('-1 year', '+5 months'),
        'sale_price_end_date' => $faker->dateTimeBetween('-1 month', '+1 year'),
        'quantity' => $faker->randomFloat(2,0),
        'quantity_reserved' => $faker->randomFloat(2, 0),
    ];
});
