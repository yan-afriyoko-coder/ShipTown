<?php

namespace App\Modules\Api2cart\src\Listeners;

use App\Events\DailyEvent;
use App\Models\Warehouse;
use App\Modules\Api2cart\src\Jobs\PushOutOfSyncPricingJob;
use App\Modules\Api2cart\src\Jobs\ResyncCheckFailedTaggedJob;
use App\Modules\Api2cart\src\Jobs\ResyncLastDayJob;
use App\Modules\Api2cart\src\Jobs\ResyncSyncErrorsTaggedJob;
use Illuminate\Support\Facades\DB;

class DailyEventListener
{
    /**
     * Handle the event.
     *
     * @param DailyEvent $event
     *
     * @return void
     */
    public function handle(DailyEvent $event)
    {
        ResyncCheckFailedTaggedJob::dispatch();
        ResyncLastDayJob::dispatch();
        ResyncSyncErrorsTaggedJob::dispatch();
        PushOutOfSyncPricingJob::dispatch();

        $this->recreateQuantityComparisonView();
    }

    public function recreateQuantityComparisonView()
    {
        $warehouseIds = Warehouse::withAllTags(['magento_stock'])->get()->pluck('id')->collect();

        if ($warehouseIds->isEmpty()) {
            // so empty result will be returned
            $warehouseIds->push(0);
        }

        $query = 'CREATE OR REPLACE VIEW modules_api2cart_product_quantity_discrepancies AS
        SELECT
            modules_api2cart_product_links.id                     AS product_link_id,
            modules_api2cart_product_links.product_id             AS product_id,
            FLOOR(MAX(api2cart_quantity))                         AS quantity_api2cart,
            FLOOR(SUM(quantity_available))                        AS quantity_expected

        FROM modules_api2cart_product_links

        LEFT JOIN inventory
            ON inventory.product_id = modules_api2cart_product_links.product_id
            AND inventory.warehouse_id in (' . $warehouseIds->implode(',') . ')

        GROUP BY modules_api2cart_product_links.id, modules_api2cart_product_links.product_id
        ';

        DB::statement($query);
    }
}
