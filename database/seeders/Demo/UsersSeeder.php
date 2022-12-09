<?php

namespace Database\Seeders\Demo;

use App\Http\Controllers\Api\Admin\UserRoleController;
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
        // create admin
        if (User::query()
            ->where('email', '=', 'admin@products.management')
            ->doesntExist()) {
            /** @var User $user */
            $user = User::factory()->create([
                'name' => 'Artur Hanusek',
                'email' => 'admin@products.management',
                'warehouse_id' => Warehouse::firstOrCreate(['code' => 'DUB'], ['name' => 'Dublin'])->getKey(),
            ]);
            $user->assignRole(Role::findOrCreate('admin'));
        }

        // create user
        if (User::query()
            ->where('email', '=', 'user@products.management')
            ->doesntExist()) {
            $user = User::factory()->count(1)->create(['email' => 'user@products.management']);
            $user->first()->assignRole(Role::findOrCreate('user'));
        }
    }
}
