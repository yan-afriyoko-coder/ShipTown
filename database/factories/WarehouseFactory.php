<?php



namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\OrderAddress;

class WarehouseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $address_id = OrderAddress::factory()->create();

        $randomCode = $this->faker->randomLetter . $this->faker->randomLetter . $this->faker->randomLetter . $this->faker->randomLetter;
        return [
            'name'  => $this->faker->city,
            'code'  => \Illuminate\Support\Str::upper($randomCode),
            'address_id' => $address_id,
        ];
    }
}
