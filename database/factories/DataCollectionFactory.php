<?php

/** @var Factory $factory */

use App\Models\DataCollection;
use App\Models\Warehouse;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(DataCollection::class, function (Faker $faker) {
    $warehouse = Warehouse::first() ?? factory(Warehouse::class)->create();
    return [
        'warehouse_id' => $warehouse->getKey(),
        'name' => $faker->word,
    ];
});
