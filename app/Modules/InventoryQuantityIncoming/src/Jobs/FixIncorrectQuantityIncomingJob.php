<?php

namespace App\Modules\InventoryQuantityIncoming\src\Jobs;

use App\Models\Inventory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

/**
 *
 */
class FixIncorrectQuantityIncomingJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public ?int $product_id;
    public ?int $warehouse_id;

    public function __construct(int $product_id = null, int $warehouse_id = null)
    {
        $this->product_id = $product_id;
        $this->warehouse_id = $warehouse_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $inventoryRecords = DB::select('
            SELECT inventory.id as id,
                    MAX(inventory.product_id) as product_id,
                    MAX(inventory.warehouse_id) as warehouse_id,
                    MAX(inventory.quantity_incoming) as actual_quantity_incoming,
                    SUM(IFNULL(data_collection_records.quantity_requested - data_collection_records.total_transferred_in, 0)) as expected_quantity_incoming

            FROM inventory

            LEFT JOIN data_collections
              ON data_collections.warehouse_id = inventory.warehouse_id
              AND data_collections.deleted_at IS NULL

            LEFT JOIN data_collection_records
              ON data_collection_records.data_collection_id = data_collections.id
              AND data_collection_records.product_id = inventory.product_id

            WHERE 1=1
            '.($this->product_id ? ' AND inventory.product_id = '.$this->product_id : '').'
            '.($this->warehouse_id ? ' AND inventory.warehouse_id = '.$this->warehouse_id : '').'

            GROUP BY inventory.id

            HAVING actual_quantity_incoming != expected_quantity_incoming
        ');

        collect($inventoryRecords)
            ->each(function ($incorrectRecord) {
                Inventory::query()
                    ->where('id', $incorrectRecord->id)
                    ->get()
                    ->each(function ($inventory) use ($incorrectRecord) {
                        $inventory->quantity_incoming = $incorrectRecord->expected_quantity_incoming;
                        $inventory->save();
                    });
            });
    }
}
