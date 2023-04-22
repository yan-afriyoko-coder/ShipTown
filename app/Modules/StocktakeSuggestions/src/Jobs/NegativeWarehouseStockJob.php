<?php

namespace App\Modules\StocktakeSuggestions\src\Jobs;

use App\Models\Warehouse;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class NegativeWarehouseStockJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private int $warehouse_id;

    public function __construct(int $warehouse_id)
    {
        $this->warehouse_id = $warehouse_id;
    }

    public function handle(): bool
    {
        $reason = 'negative warehouse stock';
        $points = 20;

        $fulfilmentWarehousesIDs = Warehouse::withAllTags(['fulfilment'])->pluck('id');

        if ($fulfilmentWarehousesIDs->isEmpty()) {
            DB::statement("DELETE FROM stocktake_suggestions WHERE reason = ?", [$reason]);
            return true;
        }

        $this->insertNewSuggestions($this->warehouse_id, $reason, $points, $fulfilmentWarehousesIDs);
        $this->deleteIncorrectSuggestions($this->warehouse_id, $reason);
        $this->deleteSuggestionsForFulfilmentWarehouses($fulfilmentWarehousesIDs, $reason);

        return true;
    }

    /**
     * @param $warehouseIDs
     * @param string $reason
     */
    private function deleteSuggestionsForFulfilmentWarehouses($warehouseIDs, string $reason): void
    {
        DB::statement("
            DELETE FROM stocktake_suggestions
            WHERE warehouse_id IN (?)
            AND reason = ?
        ", [$warehouseIDs->implode(','), $reason]);
    }

    /**
     * @param $fulfilmentWarehousesIDs
     * @param int $startingPoints
     * @param string $reason
     */
    private function insertNewSuggestions(int $warehouse_id, string $reason, int $startingPoints, $fulfilmentWarehousesIDs): void
    {
        DB::statement('
            INSERT INTO stocktake_suggestions (inventory_id, product_id, warehouse_id, points, reason, created_at, updated_at)
                SELECT DISTINCT inventory.id as inventory_id,
                    inventory.product_id as product_id,
                    inventory.warehouse_id as warehouse_id,
                    ? + ABS(inventory.quantity) as points,
                    ? as reason,
                    now() as created_at,
                    now() as updated_at
                FROM inventory
                INNER JOIN inventory as inventory_fullfilment
                    ON inventory_fullfilment.product_id = inventory.product_id
                    AND inventory_fullfilment.warehouse_id IN (?)
                    AND inventory_fullfilment.quantity > 0
                WHERE inventory.warehouse_id = ?
                    AND inventory.quantity < 0
                    AND NOT EXISTS (
                        SELECT NULL
                        FROM stocktake_suggestions
                        WHERE stocktake_suggestions.inventory_id = inventory.id
                        AND stocktake_suggestions.reason = ?
                    )
        ', [$startingPoints, $reason, $fulfilmentWarehousesIDs->implode(','), $warehouse_id, $reason]);
    }

    /**
     * @param string $reason
     */
    private function deleteIncorrectSuggestions(int $warehouse_id, string $reason): void
    {
        DB::statement('
            DELETE stocktake_suggestions
            FROM stocktake_suggestions
            LEFT JOIN inventory
                ON inventory.id = stocktake_suggestions.inventory_id
            WHERE stocktake_suggestions.warehouse_id = ?
            AND stocktake_suggestions.reason = ?
            AND inventory.quantity = 0
        ', [$warehouse_id, $reason]);
    }
}
