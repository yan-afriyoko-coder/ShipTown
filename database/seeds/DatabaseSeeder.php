<?php

use App\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
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

        $this->call([
            ProductsSeeder::class,
            ProductAliasSeeder::class,
            InventorySeeder::class,
            OrdersSeeder::class,

//            Api2CartOrderImportSeeder::class,
//            RmsapiConnectionSeeder::class,
//            RmsapiProductImportSeeder::class,
//
//            PicksSeeder::class
        ]);

        \App\Jobs\RunMaintenanceJob::dispatchNow();

    }
}
