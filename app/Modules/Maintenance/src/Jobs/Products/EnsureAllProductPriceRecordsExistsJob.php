<?php

namespace App\Modules\Maintenance\src\Jobs\Products;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class EnsureAllProductPriceRecordsExistsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var string
     */
    private string $checkQuery = /** @lang text */
    '
        SELECT count(*) as count
        FROM products
        LEFT JOIN warehouses ON 1 = 1
        WHERE NOT EXISTS (
            SELECT product_id FROM products_prices
            WHERE products_prices.warehouse_id = warehouses.id AND products_prices.product_id = products.id
        )
    ';

    /**
     * @var string
     */
    private string $insertQuery = /** @lang text */
        'INSERT INTO products_prices
        (
            product_id,
            warehouse_id,
            location_id,
            warehouse_code,
            price,
            sale_price,
            sale_price_start_date,
            sale_price_end_date,
            created_at,
            updated_at
        )
        SELECT
            products.id,
            warehouses.id,
            warehouses.code,
            warehouses.code,
            price,
            sale_price,
            sale_price_start_date,
            sale_price_end_date,
            now(),
            now()

        FROM products
        LEFT JOIN warehouses ON 1 = 1
        WHERE NOT EXISTS (
            SELECT product_id FROM products_prices
            WHERE products_prices.warehouse_id = warehouses.id AND products_prices.product_id = products.id
        )
        ';

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        do {
            DB::statement($this->insertQuery);
        } while (data_get(DB::select($this->checkQuery), '0.count') > 0);
    }
}
