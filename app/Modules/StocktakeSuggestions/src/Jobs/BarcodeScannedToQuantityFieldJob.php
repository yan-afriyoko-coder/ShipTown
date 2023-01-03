<?php

namespace App\Modules\StocktakeSuggestions\src\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class BarcodeScannedToQuantityFieldJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use IsMonitored;

    private int $warehouse_id;

    public function __construct(int $warehouse_id)
    {
        $this->warehouse_id = $warehouse_id;
    }

    public function handle(): bool
    {
        $reason = 'possible barcode scanned into quantity field';
        $points = 100;

        $warehouse_id = $this->warehouse_id;

        $this->insertNewSuggestions($warehouse_id, $reason, $points);
        $this->deleteIncorrectSuggestions($warehouse_id, $reason);

        return true;
    }

    /**
     * @param int $points
     * @param string $reason
     * @param int $warehouse_id
     */
    private function insertNewSuggestions(int $warehouse_id, string $reason, int $points): void
    {
        DB::statement('
            INSERT INTO stocktake_suggestions (inventory_id, product_id, warehouse_id, points, reason, created_at, updated_at)
            SELECT id, product_id, warehouse_id, ?, ?, NOW(), NOW()
            FROM inventory
            WHERE warehouse_id = ?
                AND quantity > 100000
                AND NOT EXISTS (
                    SELECT NULL
                    FROM stocktake_suggestions
                    WHERE stocktake_suggestions.inventory_id = inventory.id
                    AND stocktake_suggestions.reason = ?
                )
            ORDER BY quantity DESC
        ', [$points, $reason, $warehouse_id, $reason]);
    }

    /**
     * @param int $warehouse_id
     * @param string $reason
     */
    private function deleteIncorrectSuggestions(int $warehouse_id, string $reason): void
    {
        DB::statement('
            DELETE stocktake_suggestions
            FROM stocktake_suggestions
            INNER JOIN inventory
                ON inventory.id = stocktake_suggestions.inventory_id
                AND inventory.quantity < 100000

            WHERE stocktake_suggestions.warehouse_id = ?
            AND stocktake_suggestions.reason = ?
        ', [$warehouse_id, $reason]);
    }
}
