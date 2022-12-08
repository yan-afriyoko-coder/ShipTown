<?php

namespace Database\Factories;

use App\Models\DataCollection;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

class DataCollectionFactory extends Factory
{
    protected $model = DataCollection::class;

    public function definition(): array
    {
        $warehouse = Warehouse::first() ?? Warehouse::factory()->create();
        return [
            'warehouse_id' => $warehouse->getKey(),
            'name' => $this->faker->word,
        ];
    }
}
