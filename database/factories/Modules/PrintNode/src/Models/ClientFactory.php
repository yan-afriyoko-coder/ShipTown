<?php



namespace Database\Factories\Modules\PrintNode\src\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Modules\PrintNode\src\Models\Client;

class ClientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'api_key' => $this->faker->text(20)
        ];
    }
}
