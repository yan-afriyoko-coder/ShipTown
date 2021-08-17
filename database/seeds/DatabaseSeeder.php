<?php

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
            ProductTagsSeeder::class,
            ProductPriceSeeder::class,
            InventorySeeder::class,
            OrdersSeeder::class,
            UnpaidOrdersSeeder::class,
            ClosedOrdersSeeder::class,
            PicksSeeder::class,
            OrderShipmentsSeeder::class,
            NavigationMenuSeeder::class,

            //            Api2CartOrderImportSeeder::class,
            //            RmsapiConnectionSeeder::class,
            //            RmsapiProductImportSeeder::class,
            //            DpdSeeder::class,
            //            PicksSeeder::class
        ]);

        \App\Jobs\RunHourlyJobs::dispatchNow();
    }
}
