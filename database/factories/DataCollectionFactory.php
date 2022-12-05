<?php



namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\DataCollection;
use App\Models\Warehouse;

class DataCollectionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $warehouse = Warehouse::first() ?? Warehouse::factory()->create();
        return [
            'warehouse_id' => $warehouse->getKey(),
            'name' => $this->faker->word,
        ];
    }
}
