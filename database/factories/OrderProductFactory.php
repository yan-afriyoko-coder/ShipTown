<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use App\Models\OrderProduct;
use Faker\Generator as Faker;

$factory->define(OrderProduct::class, function (Faker $faker) {

    $product = \App\Models\Product::query()->inRandomOrder()->first();

    return [
        'product_id' => $product->id,
        'model' => $product->sku,
        'name' => $product->name,

        'quantity' => rand(1,50),
        'price' => $product->price,
    ];

});
