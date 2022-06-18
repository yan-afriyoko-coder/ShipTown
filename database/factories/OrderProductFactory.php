<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use Faker\Generator as Faker;
use Illuminate\Support\Arr;

$factory->define(OrderProduct::class, function (Faker $faker) {
    $product = Product::query()->inRandomOrder()->first() ?? factory(Product::class)->create();

    return [
        'order_id'         => function () {
            return factory(Order::class)->create()->getKey();
        },
        'product_id'       => $product->getKey(),
        'sku_ordered'      => $product->sku,
        'name_ordered'     => $product->name,
        'quantity_ordered' => Arr::random([1, 1, 1, 1, 2, 2, 3, 3]) * Arr::random([1, 1, 1, 1, 1, 1, 1, 1, 2, 3]),
        'price'            => $product->price,
    ];
});
