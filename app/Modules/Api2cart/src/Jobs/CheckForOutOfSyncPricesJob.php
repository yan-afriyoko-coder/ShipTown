<?php

namespace App\Modules\Api2cart\src\Jobs;

use App\Modules\Api2cart\src\Models\Api2cartProductLink;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

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
                'modules_api2cart_product_links.product_id',
                'modules_api2cart_product_links.api2cart_price',
                'product_price.price',
            ])
            ->join('modules_api2cart_connections as api2cart_connection', function ($join) {
                $join->on('api2cart_connection.id', '=', 'modules_api2cart_product_links.api2cart_connection_id');
            })
            ->leftJoin('products_prices as product_price', function ($join) {
                $join->on('product_price.product_id', '=', 'modules_api2cart_product_links.product_id');
                $join->on('product_price.warehouse_id', '=', 'api2cart_connection.pricing_source_warehouse_id');
            })
            ->whereNotNull('modules_api2cart_product_links.product_id')
            ->where('product_price.price', '!=', 'modules_api2cart_product_links.api2cart_price');


        $query->get()->each(function (Api2cartProductLink $productLink) {
            $productLink->product->attachTag('Not Synced');
            $productLink->product->log('eCommerce Api2cart: Out Of Sync Pricing, attached NOT SYNCED tag');
        });
    }
}
