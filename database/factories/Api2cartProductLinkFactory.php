<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Product;
use App\Modules\Api2cart\src\Models\Api2cartConnection;
use App\Modules\Api2cart\src\Models\Api2cartProductLink;
use Faker\Generator as Faker;

$factory->define(Api2cartProductLink::class, function (Faker $faker) {
    $connection = Api2cartConnection::query()->inRandomOrder()->first() ?? factory(Api2cartConnection::class)->create();
    $product = Product::query()->inRandomOrder()->first() ?? factory(Product::class)->create();

    return [
        'product_id'                     => $product->getKey(),
        'api2cart_connection_id'         => $connection->getKey(),
        'api2cart_product_type'          => null,
        'api2cart_product_id'            => null,
        'api2cart_quantity'              => null,
        'api2cart_price'                 => null,
        'api2cart_sale_price'            => null,
        'api2cart_sale_price_start_date' => null,
        'api2cart_sale_price_end_date'   => null,
        'last_fetched_at'                => null,
        'last_fetched_data'              => null,
    ];
});
