<?php

namespace App\Modules\Maintenance\src\Jobs\Products;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class EnsureAllInventoryRecordsExistsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var string
     */
    private string $query = /** @lang text */
        'INSERT INTO inventory (
          product_id,
          warehouse_id,
          location_id,
          warehouse_code,
          created_at,
          updated_at
        )
        SELECT
          products.id as product_id,
          warehouses.id as warehouse_id,
          warehouses.code as location_id,
          warehouses.code as warehouse_code,
          now(),
          now()

        FROM products
        LEFT JOIN warehouses ON 1 = 1
        WHERE products.id NOT IN (SELECT product_id FROM inventory WHERE warehouse_code = warehouses.code)
        LIMIT 100000
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
