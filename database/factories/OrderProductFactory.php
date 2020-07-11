<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use App\Models\OrderProduct;
use Faker\Generator as Faker;

$factory->define(OrderProduct::class, function (Faker $faker) {

    $product = \App\Models\Product::query()->inRandomOrder()->first();

    return [
        'product_id' => $product->id,
        'sku_ordered' => $product->sku,
        'name_ordered' => $product->name,
        'quantity' => rand(1,50),
        'price' => $product->price,
    ];

});
