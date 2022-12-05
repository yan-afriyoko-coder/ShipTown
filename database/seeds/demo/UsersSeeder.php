<?php

use App\Models\Warehouse;
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
            ->where('email', '=', 'admin@products.management')
            ->doesntExist()) {
            $user = User::factory()->count(1)->create([
                'name' => 'Artur Hanusek',
                'email' => 'admin@products.management',
                'warehouse_id' => Warehouse::firstOrCreate(['code' => 'DUB'], ['name' => 'Dublin'])->getKey(),
            ]);
            $user->first()->assignRole('admin');
        }

        // create user
        if (User::query()
            ->where('email', '=', 'user@products.management')
            ->doesntExist()) {
            $user = User::factory()->count(1)->create(['email' => 'user@products.management']);
            $user->first()->assignRole('user');
        }
    }
}
