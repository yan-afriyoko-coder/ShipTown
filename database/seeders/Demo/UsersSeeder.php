<?php

namespace Database\Seeders\Demo;

use App\Models\Warehouse;
use App\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        /** @var User $admin */
        $warehouseDublin = Warehouse::query()->firstOrCreate(['code' => 'DUB'], ['name' => 'Dublin']);

        $admin = User::query()->firstOrCreate([
            'email' => 'demo-admin@ship.town',
        ], [
            'name' => 'Artur Hanusek',
            'warehouse_id' => $warehouseDublin->getKey(),
            'warehouse_code' => $warehouseDublin->code,
            'password' => bcrypt('secret1144'),
            'ask_for_shipping_number' => false,
        ]);
        $admin->assignRole(Role::findOrCreate('admin'));

        /** @var User $user */
        $warehouseGalway = Warehouse::query()->firstOrCreate(['code' => 'GAL'], ['name' => 'Galway']);

        $user = User::query()->firstOrCreate([
            'email' => 'demo-user@ship.town',
        ], [
            'name' => 'Joni Melabo',
            'warehouse_id' => $warehouseGalway->getKey(),
            'warehouse_code' => $warehouseGalway->code,
            'password' => bcrypt('secret1144'),
        ]);
        $user->assignRole(Role::findOrCreate('user'));
    }
}
