<?php

namespace App\Modules\Api2cart\src\Jobs;

use App\Modules\Api2cart\src\Models\Api2cartProductLink;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class CheckForOutOfSyncPricesJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $query = Api2cartProductLink::query()
            ->select([
                'modules_api2cart_product_links.id',
                'modules_api2cart_product_links.is_in_sync',
                'modules_api2cart_product_links.product_id',
                'api2cart_connection.pricing_source_warehouse_id',
                'modules_api2cart_product_links.api2cart_price',
                'product_price.price as actual_price',


                'modules_api2cart_product_links.api2cart_sale_price',
                'product_price.sale_price as actual_sale_price',

                'modules_api2cart_product_links.api2cart_sale_price_start_date',
                'CASE WHEN product_price.sale_price_start_date < "2000-01-01" THEN "2000-01-01" ELSE product_price.sale_price_start_date END as actual_sale_price_start_date',

                'modules_api2cart_product_links.api2cart_sale_price_end_date',
                'product_price.sale_price_end_date as actual_sale_price_end_date',
                'CASE WHEN product_price.sale_price_end_date < "2000-01-01" THEN "2000-01-01" ELSE product_price.sale_price_end_date END as actual_sale_price_end_date',


                DB::raw('product_price.*'),
            ])
            ->join('modules_api2cart_connections as api2cart_connection', function ($join) {
                $join->on('api2cart_connection.id', '=', 'modules_api2cart_product_links.api2cart_connection_id');
            })
            ->leftJoin('products_prices as product_price', function ($join) {
                $join->on('product_price.product_id', '=', 'modules_api2cart_product_links.product_id');
                $join->on('product_price.warehouse_id', '=', 'api2cart_connection.pricing_source_warehouse_id');
            })
            ->whereRaw('(api2cart_connection.pricing_source_warehouse_id IS NOT NULL) ' .
                'AND api2cart_quantity > 0
                AND ( 1=2
                    OR product_price.id IS NULL
                    OR api2cart_price IS NULL
                    OR api2cart_sale_price IS NULL
                    OR api2cart_sale_price_start_date IS NULL
                    OR api2cart_sale_price_end_date IS NULL
                    OR product_price.price != api2cart_price
                    OR product_price.sale_price != api2cart_sale_price
                    OR CASE WHEN product_price.sale_price_start_date < "2000-01-01" THEN "2000-01-01" ELSE product_price.sale_price_start_date END != api2cart_sale_price_start_date
                    OR CASE WHEN product_price.sale_price_end_date < "2000-01-01" THEN "2000-01-01" ELSE product_price.sale_price_end_date END != api2cart_sale_price_end_date
                )
                ');

        $query->limit(1000)->update([
            'sync_first_failed_at' => DB::raw('IFNULL(sync_first_failed_at, NOW())'),
            'modules_api2cart_product_links.is_in_sync' => false
        ]);
    }
}
