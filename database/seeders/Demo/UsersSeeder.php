<?php

namespace Database\Seeders\Demo;

use App\Models\Warehouse;
use App\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /** @var User $admin */
        $admin = User::query()->firstOrCreate([
            'email' => 'admin@products.management',
        ], [
            'name' => 'Artur Hanusek',
            'warehouse_id' => Warehouse::firstOrCreate(['code' => 'DUB'], ['name' => 'Dublin'])->getKey(),
            'password' => bcrypt('secret123'),
        ]);
        $admin->assignRole(Role::findOrCreate('admin'));


        /** @var User $user */
        $user = User::query()->firstOrCreate([
            'email' => 'user@products.management',
        ], [
            'name' => 'Johny Melavo',
            'warehouse_id' => Warehouse::firstOrCreate(['code' => 'CRK'], ['name' => 'Cork'])->getKey(),
            'password' => bcrypt('secret123'),
        ]);
        $user->assignRole(Role::findOrCreate('user'));
    }
}
