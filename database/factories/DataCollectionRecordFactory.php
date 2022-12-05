<?php



namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\DataCollection;
use App\Models\DataCollectionRecord;
use App\Models\Product;

class DataCollectionRecordFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $product = Product::query()->inRandomOrder()->first() ?? Product::factory()->create();
        $data_collection = DataCollection::query()->inRandomOrder()->first() ?? DataCollection::factory()->create();

        return [
            'data_collection_id' => $data_collection->getKey(),
            'product_id' => $product->getKey(),
            'quantity_requested' => rand(1, 100),
        ];
    }
}
