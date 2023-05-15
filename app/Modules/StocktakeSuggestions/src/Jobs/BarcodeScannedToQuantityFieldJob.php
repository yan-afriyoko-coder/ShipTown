<?php

namespace App\Modules\StocktakeSuggestions\src\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class BarcodeScannedToQuantityFieldJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function handle(): bool
    {
        $reason = 'possible barcode scanned into quantity field';
        $points = 100;

        $this->insertNewSuggestions($warehouse_id, $reason, $points);
        $this->deleteIncorrectSuggestions($warehouse_id, $reason);

        return true;
    }

    /**
     * @param int $points
     * @param string $reason
     * @param int $warehouse_id
     */
    private function insertNewSuggestions(string $reason, int $points): void
    {
        DB::statement('
            INSERT INTO stocktake_suggestions (inventory_id, product_id, warehouse_id, points, reason, created_at, updated_at)
            SELECT id, product_id, warehouse_id, ?, ?, NOW(), NOW()
            FROM inventory
            WHERE quantity > 100000
                AND NOT EXISTS (
                    SELECT NULL
                    FROM stocktake_suggestions
                    WHERE stocktake_suggestions.inventory_id = inventory.id
                    AND stocktake_suggestions.reason = ?
                )
            ORDER BY quantity DESC
        ', [$points, $reason, $reason]);
    }

    /**
     * @param int $warehouse_id
     * @param string $reason
     */
    private function deleteIncorrectSuggestions(string $reason): void
    {
        DB::statement('
            DELETE stocktake_suggestions
            FROM stocktake_suggestions
            LEFT JOIN inventory
                ON inventory.id = stocktake_suggestions.inventory_id

            WHERE stocktake_suggestions.reason = ?
            AND inventory.quantity < 100000
        ', [$reason]);
    }
}
