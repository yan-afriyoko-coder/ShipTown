<?php

namespace App\Modules\Api2cart\src\Jobs;

use App\Modules\Api2cart\src\Models\Api2cartProductLink;
use App\Modules\Api2cart\src\Models\Api2cartVariant;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class CheckForOutOfSyncProductsJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use IsMonitored;

    /**
     * Execute the job.
     *
     * @throws Exception
     *
     * @return void
     */
    public function handle()
    {
        // out of sync quantity
        DB::statement('
            UPDATE modules_api2cart_product_links as product_link

            INNER JOIN modules_api2cart_product_quantity_comparison_view as comparison_view
              ON product_link.id = comparison_view.product_link_id
              AND comparison_view.product_link_is_in_sync = 1
              AND comparison_view.quantity_api2cart != comparison_view.quantity_expected

            SET product_link.is_in_sync = 0
        ');

        // out of sync price
        DB::statement('
            UPDATE modules_api2cart_product_links as product_link

            INNER JOIN modules_api2cart_product_pricing_comparison_view as comparison_view
              ON product_link.id = comparison_view.product_link_id
              AND comparison_view.product_link_is_in_sync = 1
              AND comparison_view.api2cart_price != comparison_view.actual_price

            SET product_link.is_in_sync = 0
        ');

        // out of sync sale prices
        DB::statement('
            UPDATE modules_api2cart_product_links as product_link

            INNER JOIN modules_api2cart_product_pricing_comparison_view as comparison_view
              ON product_link.id = comparison_view.product_link_id
                AND comparison_view.product_link_is_in_sync = 1
                AND (
                    # we will select ony records with promotions that are scheduled or currently active on api2cart
                    comparison_view.actual_sale_price_end_date > now()
                    OR comparison_view.api2cart_sale_price_end_date > now()
                )
                AND (
                    comparison_view.api2cart_sale_price != comparison_view.actual_sale_price
                    OR comparison_view.api2cart_sale_price_start_date != comparison_view.actual_sale_price_start_date
                    OR comparison_view.api2cart_sale_price_end_date != comparison_view.actual_sale_price_end_date
                )

            SET product_link.is_in_sync = 0
        ');
    }
}
