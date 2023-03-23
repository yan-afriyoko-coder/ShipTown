<?php

namespace Database\Seeders;

use App\Jobs\RunHourlyJobs;
use App\Modules\QueueMonitor\src\QueueMonitorServiceProvider;
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
        QueueMonitorServiceProvider::enableModule();

        $this->call([
            Demo\ConfigurationSeeder::class,
            Demo\OrderStatusesSeeder::class,
            Demo\WarehousesSeeder::class,
            Demo\UsersSeeder::class,

            Demo\ProductsSeeder::class,
            Demo\PaidOrdersSeeder::class,
            Demo\TestOrdersSeeder::class,
            Demo\ProductsTagsSeeder::class,
            ProductPriceSeeder::class,

            InventorySeeder::class,
            StocktakeSuggestionsSeeder::class,

//            RestockingReportSeeder::class,
//            DataCollectionsSeeder::class,
//            DataCollectionsTransferInFromWarehouseSeeder::class,
//            RmsapiConnectionSeeder::class,
//            StocktakeSuggestionsSeeder::class,
//            AutomationsSeeder::class,

            PrintNodeClientSeeder::class,

//            DpdIrelandSeeder::class,
//            ProductAliasSeeder::class,
//            ProductTagsSeeder::class,
//            SplitOrdersScenarioSeeder::class,
//            Orders_PackingWebDemoSeeder::class,
//            Orders_StorePickupDemoSeeder::class,
//            UnpaidOrdersSeeder::class,
//            ClosedOrdersSeeder::class,
//            PicksSeeder::class,
//            OrderShipmentsSeeder::class,

//        Modules Seeders
//            WebhooksTestSeeder::class,
//            DpdUk\DpdUkTestOrdersSeeder::class,
        ]);

//        RunHourlyJobs::dispatchSync();
    }
}
