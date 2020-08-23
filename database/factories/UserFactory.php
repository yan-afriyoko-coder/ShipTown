<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\User;
use Illuminate\Support\Str;
use Faker\Generator as Faker;

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

$factory->define(User::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'email_verified_at' => now(),
        'password' => bcrypt('secret123'), // password
        'remember_token' => Str::random(10),
    ];
});

// Add a dummy state so we can assign the role on a callback
$factory->state(User::class, 'admin', []);

$factory->afterCreatingState(App\User::class, 'admin', function ($user, $faker) {
    $user->assignRole('admin');
});