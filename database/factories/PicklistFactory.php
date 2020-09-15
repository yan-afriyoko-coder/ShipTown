<?php

/** @var Factory $factory */

use App\Models\Picklist;
use App\Models\Product;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;
use Illuminate\Support\Str;

$factory->define(Picklist::class, function (Faker $faker) {

    $product = factory(Product::class)->create();

    return [
        'product_id' => $product->getKey(),
        'sku_ordered' => $product->sku,
        'name_ordered' => $product->name,
        'quantity_requested' => $faker->numberBetween(1, 30)
    ];
});
