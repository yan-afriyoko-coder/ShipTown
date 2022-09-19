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
                DB::raw('product_price.*'),
            ])
            ->join('modules_api2cart_connections as api2cart_connection', function ($join) {
                $join->on('api2cart_connection.id', '=', 'modules_api2cart_product_links.api2cart_connection_id');
            })
            ->leftJoin('products_prices as product_price', function ($join) {
                $join->on('product_price.product_id', '=', 'modules_api2cart_product_links.product_id');
                $join->on('product_price.warehouse_id', '=', 'api2cart_connection.pricing_source_warehouse_id');
            })
            ->whereRaw('(api2cart_connection.pricing_source_warehouse_id IS NOT NULL)')
            ->whereRaw('(' .
                '   product_price.id IS NULL ' .
                '   OR modules_api2cart_product_links.api2cart_price IS NULL ' .
                '   OR product_price.price != modules_api2cart_product_links.api2cart_price' .
                ')');

        $query->limit(1000)->update(['modules_api2cart_product_links.is_in_sync' => false]);
    }
}
