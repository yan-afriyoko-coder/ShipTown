<?php



namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\ProductAlias;

class ProductAliasFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $product = \App\Models\Product::query()->inRandomOrder()->first();

        if (empty($product)) {
            $product = \App\Models\Product::factory()->create();
        }

        return [
            'product_id' => $product->getKey(),
            'alias'      => $this->faker->ean13,
        ];
    }
}
