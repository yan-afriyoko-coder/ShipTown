<?php



namespace Database\Factories\Modules\Api2cart\src\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Warehouse;
use App\Modules\Api2cart\src\Models\Api2cartConnection;

class Api2cartConnectionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $warehouse = Warehouse::query()->inRandomOrder()->first() ?? Warehouse::factory()->create();

        return [
            'type'                           => $this->faker->randomElement(['Magento', 'Prestashop', 'Shopify']),
            'inventory_source_warehouse_tag' => 'magento_stock',
            'pricing_source_warehouse_id'    => $warehouse->getKey(),
            'magento_store_id'               => 0,
            'pricing_location_id'            => $warehouse->code,
            'bridge_api_key'                 => config('api2cart.api2cart_test_store_key') ?? $this->faker->uuid,
        ];
    }
}
