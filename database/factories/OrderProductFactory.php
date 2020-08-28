<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use App\Models\OrderProduct;
use Faker\Generator as Faker;

$factory->define(OrderProduct::class, function (Faker $faker) {

    $product = \App\Models\Product::query()
        ->inRandomOrder()
        ->firstOrCreate(
            [],
            factory(\App\Models\Product::class)->make()->getAttributes()
        );

    return [
        'product_id' => $product->getKey(),
        'sku_ordered' => $product->sku,
        'name_ordered' => $product->name,
        'quantity_ordered' => rand(1, 10),
        'price' => $product->price,
    ];
});
