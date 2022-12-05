<?php



namespace Database\Factories\Modules\DpdUk\src\Models;

use App\Models\OrderAddress;
use App\Modules\DpdUk\src\Models\Connection;
use Illuminate\Database\Eloquent\Factories\Factory;

class ConnectionFactory extends Factory
{
    protected $model = Connection::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $address = OrderAddress::query()->first() ?? OrderAddress::factory()->create();

        return [
            'username' => env('TEST_DPDUK_USERNAME', $this->faker->randomNumber(6)),
            'password' => env('TEST_DPDUK_PASSWORD', 'password'),
            'account_number' => env('TEST_DPDUK_ACCNUMBER', '123456'),
            'collection_address_id' => $address->getKey(),
        ];
    }
}
