<?php

use App\User;
use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // create demo user
        if (User::query()
            ->where('email', '=', 'demo@products.management')
            ->doesntExist()) {
            $user = factory(User::class, 1)
                ->create(['email' => 'demo@products.management']);
            $user->first()->assignRole('admin');
        }

        // create demo user
        if (User::query()
            ->where('email', '=', 'demo2@products.management')
            ->doesntExist()) {
            $user = factory(User::class, 1)
                ->create(['email' => 'demo2@products.management']);
            $user->first()->assignRole('user');
        }

        $users = factory(User::class, 15)->create();
        $users->each(function ($user) {
            $user->assignRole('user');
        });
    }
}
