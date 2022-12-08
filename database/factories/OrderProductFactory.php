<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

class OrderProductFactory extends Factory
{
    protected $model = OrderProduct::class;

    public function definition(): array
    {
        $product = Product::query()->inRandomOrder()->first() ?? Product::factory()->create();

        return [
            'order_id'         => function () {
                return Order::factory()->create()->getKey();
            },
            'product_id'       => $product->getKey(),
            'sku_ordered'      => $product->sku,
            'name_ordered'     => $product->name,
            'quantity_ordered' => Arr::random([1, 1, 1, 1, 2, 2, 3, 3]) * Arr::random([1, 1, 1, 1, 1, 1, 1, 1, 2, 3]),
            'price'            => $product->price,
        ];
    }
}
