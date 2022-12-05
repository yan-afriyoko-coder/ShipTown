<?php



namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use Illuminate\Support\Arr;

class OrderProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
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
