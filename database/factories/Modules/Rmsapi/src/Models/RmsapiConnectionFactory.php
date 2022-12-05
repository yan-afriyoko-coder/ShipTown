<?php

namespace Database\Factories\Modules\Rmsapi\src\Models;

use App\Models\Warehouse;
use App\Modules\Rmsapi\src\Models\RmsapiConnection;
use Illuminate\Database\Eloquent\Factories\Factory;

class RmsapiConnectionFactory extends Factory
{
    protected $model = RmsapiConnection::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $warehouse = Warehouse::query()->inRandomOrder()->first() ?? Warehouse::factory()->create();

        return [
            'location_id'    => $warehouse->code,
            'url'            => $this->faker->url,
            'username'       => $this->faker->companyEmail,
            'password'       => $this->faker->password,
        ];
    }
}
