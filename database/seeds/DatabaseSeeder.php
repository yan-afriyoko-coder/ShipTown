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
        $this->call([
            UsersSeeder::class,
            ProductsSeeder::class,
            ProductAliasSeeder::class,
            InventorySeeder::class,
            OrdersSeeder::class,
            ClosedOrdersSeeder::class,

//            Api2CartOrderImportSeeder::class,
//            RmsapiConnectionSeeder::class,
//            RmsapiProductImportSeeder::class,
//
//            PicksSeeder::class
        ]);

        \App\Jobs\RunMaintenanceJobs::dispatchNow();
    }
}
