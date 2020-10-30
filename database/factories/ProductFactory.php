<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\Product;
use Faker\Generator as Faker;

$factory->define(Product::class, function (Faker $faker) {

    $productNameBricks = [
        'adjective' => ['Small', 'Ergonomic', 'Rustic', 'Intelligent', 'Gorgeous', 'Incredible', 'Fantastic', 'Practical', 'Sleek', 'Awesome', 'Enormous', 'Mediocre', 'Synergistic', 'Heavy Duty', 'Lightweight', 'Aerodynamic', 'Durable'],
        'material' => ['Steel', 'Wooden', 'Concrete', 'Plastic', 'Cotton', 'Granite', 'Rubber', 'Leather', 'Silk', 'Wool', 'Linen', 'Marble', 'Iron', 'Bronze', 'Copper', 'Aluminum', 'Paper'],
        'product' => ['Chair', 'Car', 'Computer', 'Gloves', 'Pants', 'Shirt', 'Table', 'Shoes', 'Hat', 'Plate', 'Knife', 'Bottle', 'Coat', 'Lamp', 'Keyboard', 'Bag', 'Bench', 'Clock', 'Watch', 'Wallet'],
    ];

    $randomProductName = $faker->randomElement($productNameBricks['adjective'])
        . ' ' . $faker->randomElement($productNameBricks['material'])
        . ' ' . $faker->randomElement($productNameBricks['product']);

    return [
        'sku' => (string) $faker->unique()->randomNumber(6),
        'name' => $randomProductName,
        'price' => $faker->randomFloat(2, 0, 1000),
        'sale_price' => $faker->randomFloat(2, 0, 1000),
        'sale_price_start_date' => $faker->dateTimeBetween('-1 year', '+5 months'),
        'sale_price_end_date' => $faker->dateTimeBetween('-1 month', '+1 year'),
    ];
});
