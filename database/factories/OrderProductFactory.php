<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Product;
use App\Models\Order;
use App\Models\OrderProduct;
use Faker\Generator as Faker;

$factory->define(OrderProduct::class, function (Faker $faker) {
    $product = factory(Product::class)->create();

    return [
        'order_id' => factory(Order::class)->create()->id,
        'product_id' => $product->getKey(),
        'sku_ordered' => $product->sku,
        'name_ordered' => $product->name,
        'quantity_ordered' => rand(1, 10),
        'price' => $product->price,
    ];
});
