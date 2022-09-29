<?php

namespace App\Modules\StocktakeSuggestions\src\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class BelowMinus50InventoryJob implements ShouldQueue
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

    /**
     * @throws Exception
     */
    public function handle(): bool
    {
        $reason = 'stock below -50';
        $points = 10;

        retry(2, function () use ($reason, $points) {
            $this->runQuery($points, $reason);
        }, 10);

        return true;
    }

    /**
     * @param int $points
     * @param string $reason
     */
    private function runQuery(int $points, string $reason): void
    {
        DB::statement('
            INSERT INTO stocktake_suggestions (inventory_id, points, reason, created_at, updated_at)
            SELECT id, ?, ?, NOW(), NOW()
            FROM inventory
            WHERE warehouse_id = ?
                AND quantity < -50
                AND NOT EXISTS (
                    SELECT NULL
                    FROM stocktake_suggestions
                    WHERE stocktake_suggestions.inventory_id = inventory.id
                    AND stocktake_suggestions.reason = ?
                )
            ORDER BY quantity ASC
            LIMIT 500
        ', [$points, $reason, $this->warehouse_id, $reason]);
    }
}
