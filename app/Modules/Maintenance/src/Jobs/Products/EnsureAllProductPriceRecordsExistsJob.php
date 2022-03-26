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
    private string $query = /** @lang text */
        'INSERT INTO products_prices
        (
            product_id,
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
            price,
            sale_price,
            sale_price_start_date,
            sale_price_end_date,
            now(),
            now()

        FROM products
        LEFT JOIN warehouses ON 1 = 1
        WHERE products.id NOT IN (
                SELECT product_id FROM products_prices WHERE warehouse_code = warehouses.code
            )
        ';

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        DB::statement($this->query);
    }
}
