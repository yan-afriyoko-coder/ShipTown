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
    private string $checkQuery = /** @lang text */
        '
        SELECT count(*) as count
        FROM products
        LEFT JOIN warehouses ON 1 = 1
        WHERE NOT EXISTS (
            SELECT product_id FROM inventory
            WHERE inventory.warehouse_id = warehouses.id AND inventory.product_id = products.id
        )
        ';

    /**
     * @var string
     */
    private string $insertQuery = /** @lang text */
        'INSERT INTO inventory (
          product_sku,
          product_id,
          warehouse_id,
          warehouse_code,
          created_at,
          updated_at
        )
        SELECT
          products.sku as product_sku,
          products.id as product_id,
          warehouses.id as warehouse_id,
          warehouses.code as warehouse_code,
          now(),
          now()

        FROM products
        LEFT JOIN warehouses ON 1 = 1
        WHERE NOT EXISTS (
            SELECT product_id FROM inventory
            WHERE inventory.warehouse_id = warehouses.id AND inventory.product_id = products.id
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
