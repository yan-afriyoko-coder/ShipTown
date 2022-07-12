<?php

use App\Jobs\RunHourlyJobs;
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
            ConfigurationSeeder::class,
            NavigationMenuSeeder::class,
            WarehousesSeeder::class,

            UsersSeeder::class,
            ProductsSeeder::class,
            PaidOrdersSeeder::class,
            DpdUkTestConnectionSeeder::class,
            WebhooksTestSeeder::class,
            InventorySeeder::class,
            RestockingReportSeeder::class,


//            AutomationsSeeder::class,
//            PrintNodeClientSeeder::class,
//            DpdIrelandSeeder::class,
//            ProductAliasSeeder::class,
//            ProductTagsSeeder::class,
//            ProductPriceSeeder::class,
//            SplitOrdersScenarioSeeder::class,
//            Orders_PackingWebDemoSeeder::class,
//            Orders_StorePickupDemoSeeder::class,
//            UnpaidOrdersSeeder::class,
//            ClosedOrdersSeeder::class,
//            PicksSeeder::class,
//            OrderShipmentsSeeder::class,
        ]);

        RunHourlyJobs::dispatchNow();
    }
}
