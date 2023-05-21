<?php

namespace Database\Factories;

use App\Models\DataCollection;
use App\Models\DataCollectionRecord;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class DataCollectionRecordFactory extends Factory
{
    public function definition(): array
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
