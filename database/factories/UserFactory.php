<?php



namespace Database\Factories;

use App\Models\Warehouse;
use App\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/


// Add a dummy state so we can assign the role on a callback

class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $warehouse = Warehouse::query()->inRandomOrder()->first() ?? Warehouse::factory()->create();

        $email = $this->faker->unique()->safeEmail;

        while (User::query()->where(['email' => $email])->exists()) {
            $email = $this->faker->unique()->safeEmail;
        }

        return [
            'name'              => $this->faker->firstName .' '. $this->faker->lastName,
            'warehouse_id'      => $warehouse->getKey(),
            'email'             => $this->faker->unique()->safeEmail,
            'email_verified_at' => now(),
            'password'          => bcrypt('secret123'), // password
            'remember_token'    => Str::random(10),
        ];
    }

    public function admin()
    {
        return $this->afterCreating(function ($user) {
            $user->assignRole('admin');
        })->state([]);
    }
}
