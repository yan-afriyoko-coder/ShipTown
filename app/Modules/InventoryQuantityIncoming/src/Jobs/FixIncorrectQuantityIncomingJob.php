<?php

namespace App\Modules\InventoryQuantityIncoming\src\Jobs;

use App\Models\Inventory;
use Exception;
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

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->zeroQuantityIncomingIfNotComing();
        $this->fixIncorrectQuantityIncoming();
    }

    private function zeroQuantityIncomingIfNotComing(): void
    {
        $inventoryRecords = DB::select("
            SELECT inventory.id as id,
                    MAX(inventory.product_id) as product_id,
                    MAX(inventory.warehouse_id) as warehouse_id,
                    MAX(inventory.quantity_incoming) as actual_quantity_incoming,
                    SUM(IFNULL(data_collection_records.quantity_requested - data_collection_records.total_transferred_in, 0)) as expected_quantity_incoming
            FROM inventory

            LEFT JOIN data_collections
                ON data_collections.warehouse_id = inventory.warehouse_id
                AND data_collections.deleted_at IS NULL
                AND data_collections.type = ?

            LEFT JOIN data_collection_records
              ON data_collection_records.data_collection_id = data_collections.id
              AND data_collection_records.product_id = inventory.product_id

           WHERE
                inventory.quantity_incoming != 0

           GROUP BY inventory.id

           HAVING actual_quantity_incoming != expected_quantity_incoming
        ", [\App\Models\DataCollectionTransferIn::class]);

        collect($inventoryRecords)
            ->each(function ($incorrectRecord) {
                Inventory::query()
                    ->where('id', $incorrectRecord->id)
                    ->get()
                    ->each(function ($inventory) use ($incorrectRecord) {
                        report(new Exception('Incorrect quantity_incoming detected', [
                            'sku'                        => $inventory->product->sku,
                            'quantity_incoming_expected' => $incorrectRecord->expected_quantity_incoming,
                            'quantity_incoming_actual'   => $incorrectRecord->actual_quantity_incoming,
                        ]));
                        $inventory->quantity_incoming = $incorrectRecord->expected_quantity_incoming;
                        $inventory->save();
                    });
            });
    }

    private function fixIncorrectQuantityIncoming(): void
    {
        $inventoryRecords = DB::select("
            SELECT inventory.id as id,
                    MAX(inventory.product_id) as product_id,
                    MAX(inventory.warehouse_id) as warehouse_id,
                    MAX(inventory.quantity_incoming) as actual_quantity_incoming,
                    SUM(IFNULL(data_collection_records.quantity_requested - data_collection_records.total_transferred_in, 0)) as expected_quantity_incoming

            FROM data_collections

            LEFT JOIN data_collection_records
              ON data_collection_records.data_collection_id = data_collections.id

            LEFT JOIN inventory
              ON data_collections.warehouse_id = inventory.warehouse_id
              AND data_collection_records.product_id = inventory.product_id

           WHERE
                data_collections.type = ?
                AND data_collections.deleted_at IS NULL

           GROUP BY inventory.id

           HAVING actual_quantity_incoming <> expected_quantity_incoming
        ", [\App\Models\DataCollectionTransferIn::class]);

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
