<?php



namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Picklist;
use App\Models\Product;

class PicklistFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $product = Product::factory()->create();

        return [
            'product_id'         => $product->getKey(),
            'sku_ordered'        => $product->sku,
            'name_ordered'       => $product->name,
            'quantity_requested' => $this->faker->numberBetween(1, 30),
        ];
    }
}
