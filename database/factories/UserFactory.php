<?php

namespace Database\Factories;

use App\Models\Warehouse;
use App\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
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

    public function admin(): UserFactory
    {
        return $this->afterCreating(function ($user) {
            $user->assignRole('admin');
        })->state([]);
    }
}
