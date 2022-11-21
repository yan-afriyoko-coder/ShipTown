<?php

/** @var Factory $factory */

use App\Models\DataCollection;
use App\Models\DataCollectionRecord;
use App\Models\Product;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(DataCollectionRecord::class, function (Faker $faker) {
    $product = Product::query()->inRandomOrder()->first() ?? factory(Product::class)->create();
    $data_collection = DataCollection::query()->inRandomOrder()->first() ?? factory(DataCollection::class)->create();

    return [
        'data_collection_id' => $data_collection->getKey(),
        'product_id' => $product->getKey(),
        'quantity_requested' => rand(1, 100),
    ];
});
