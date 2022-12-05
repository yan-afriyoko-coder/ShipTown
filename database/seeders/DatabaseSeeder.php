<?php

namespace Database\Seeders;

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
            Demo\ConfigurationSeeder::class,
            Demo\WarehousesSeeder::class,

            FulfilmentCenterSeeder::class,

            Demo\UsersSeeder::class,
            Demo\ProductsSeeder::class,
            ProductsTagsSeeder::class,

            Demo\PaidOrdersSeeder::class,
            DpdUk\DpdUkTestOrdersSeeder::class,
            WebhooksTestSeeder::class,
            InventorySeeder::class,

            RestockingReportSeeder::class,

//            DataCollectionsSeeder::class,
            DataCollectionsTransferInFromWarehouseSeeder::class,


            RmsapiConnectionSeeder::class,
            StocktakeSuggestionsSeeder::class,
//            AutomationsSeeder::class,
//            PrintNodeClientSeeder::class,
//            DpdIrelandSeeder::class,
//            ProductAliasSeeder::class,
            ProductTagsSeeder::class,
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
