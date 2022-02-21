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
        // create admin
        if (User::query()
            ->where('email', '=', 'demo+admin@products.management')
            ->doesntExist()) {
            $user = factory(User::class, 1)->create([
                'name' => 'Artur Hanusek',
                'email' => 'demo+admin@products.management'
            ]);
            $user->first()->assignRole('admin');
        }

        // create user
        if (User::query()
            ->where('email', '=', 'demo+user@products.management')
            ->doesntExist()) {
            $user = factory(User::class, 1)->create(['email' => 'demo+user@products.management']);
            $user->first()->assignRole('user');
        }
    }
}
