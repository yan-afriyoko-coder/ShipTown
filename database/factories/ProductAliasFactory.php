<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\ProductAlias;
use Faker\Generator as Faker;

$factory->define(ProductAlias::class, function (Faker $faker) {
    $product = \App\Models\Product::query()->inRandomOrder()->first();

    if(empty($product)) {
        $product = factory(\App\Models\Product::class)->create();
    }

    return [
        'product_id' => $product->getKey(),
        'alias' => $faker->ean13,
    ];
});
