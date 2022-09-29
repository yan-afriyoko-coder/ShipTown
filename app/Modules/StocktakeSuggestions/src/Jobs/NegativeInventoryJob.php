<?php

namespace App\Modules\StocktakeSuggestions\src\Jobs;

use App\Models\Inventory;
use App\Models\StocktakeSuggestion;
use App\Models\Warehouse;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class NegativeInventoryJob implements ShouldQueue
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
        $reason = 'stock below 0';
        $points = 20;

        DB::statement('DELETE FROM stocktake_suggestions WHERE reason = ?', [$reason]);

//        DB::statement('
//            INSERT INTO stocktake_suggestions (inventory_id, points, reason, created_at, updated_at)
//            SELECT id, ?, ?, NOW(), NOW()
//            FROM inventory
//            WHERE warehouse_id = ?
//                AND quantity < 0
//                AND NOT EXISTS (
//                    SELECT NULL
//                    FROM stocktake_suggestions
//                    WHERE stocktake_suggestions.inventory_id = inventory.id
//                    AND stocktake_suggestions.reason = ?
//                )
//            ORDER BY quantity ASC
//            LIMIT 500
//        ', [$points, $reason, $this->warehouse_id, $reason]);

        return true;
    }
}
