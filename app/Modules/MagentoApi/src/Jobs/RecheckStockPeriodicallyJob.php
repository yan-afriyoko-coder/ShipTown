<?php

namespace App\Modules\MagentoApi\src\Jobs;

use App\Abstracts\UniqueJob;
use Illuminate\Support\Facades\DB;

/**
 * Class SyncCheckFailedProductsJob.
 */
class RecheckStockPeriodicallyJob extends UniqueJob
{
    public function handle()
    {
        DB::update('
            UPDATE modules_magento2api_products
            SET stock_items_fetched_at = null
            WHERE
                stock_items_fetched_at IS NOT NULL
                AND product_id IN (
                    SELECT DISTINCT product_id

                    FROM `inventory`

                    WHERE last_movement_at BETWEEN DATE_SUB(now(), INTERVAL 2 DAY) AND DATE_SUB(now(), INTERVAL 1 DAY)
                    OR last_movement_at BETWEEN DATE_SUB(now(), INTERVAL 4 DAY) AND DATE_SUB(now(), INTERVAL 5 DAY)
                )
        ');
    }
}
