<?php

namespace App\Modules\Api2cart\src\Jobs;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class PushOutOfSyncPricingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private string $query = "
        SELECT
          products.sku,
          modules_api2cart_product_links.product_id,

          modules_api2cart_product_links.api2cart_price,
          products_prices.price,

          modules_api2cart_product_links.api2cart_sale_price,
          products_prices.sale_price,

          modules_api2cart_product_links.api2cart_sale_price_start_date,
          products_prices.sale_price_start_date,

          modules_api2cart_product_links.api2cart_sale_price_end_date,
          products_prices.sale_price_end_date,

          modules_api2cart_product_links.last_fetched_at,

          1

        FROM `modules_api2cart_product_links`

        LEFT JOIN products
          ON products.id = modules_api2cart_product_links.product_id

        LEFT JOIN modules_api2cart_connections
          ON modules_api2cart_connections.id = modules_api2cart_product_links.api2cart_connection_id

        LEFT JOIN products_prices
          ON products_prices.product_id = modules_api2cart_product_links.product_id
          AND products_prices.warehouse_id = modules_api2cart_connections.pricing_source_warehouse_id

        WHERE modules_api2cart_product_links.last_fetched_at IS NOT NULL
        #  AND modules_api2cart_product_links.product_id IN (SELECT id FROM products WHERE sku in ('420621'))
        AND (
            modules_api2cart_product_links.api2cart_price != products_prices.price
            OR (
                products_prices.sale_price_end_date > DATE_SUB(now(), INTERVAL 1 DAY)
                AND (
                    modules_api2cart_product_links.api2cart_sale_price != products_prices.sale_price
                    OR modules_api2cart_product_links.api2cart_sale_price_start_date != products_prices.sale_price_start_date
                    OR modules_api2cart_product_links.api2cart_sale_price_end_date != products_prices.sale_price_end_date
                )
            )
        )
    ";

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $result = DB::select($this->query);

        collect($result)->each(function ($record) {
            $product = Product::find(data_get($record, 'product_id'));
            activity()->withoutLogs(function () use ($product) {
                $product->attachTag('Not Synced');
            });
            $product->log('Out of sync eCommerce pricing detected, sync scheduled');
        });
    }
}
