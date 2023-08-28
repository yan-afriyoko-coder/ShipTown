<?php

namespace App\Modules\InventoryTotals\src\Jobs;

use App\Abstracts\UniqueJob;
use Illuminate\Support\Facades\DB;

class LastCountedAtJob extends UniqueJob
{
    public function handle()
    {
        $maxRounds = 100;

        do {
            $recordsUpdated = DB::update('
                UPDATE `inventory`

                SET last_counted_at = (
                    SELECT MAX(created_at)
                    FROM inventory_movements
                    WHERE inventory_movements.inventory_id = inventory.id
                    AND inventory_movements.description = "stocktake"
                )

                WHERE `last_movement_id` IS NOT NULL
                AND last_counted_at IS NULL
                LIMIT 5000
            ');
            sleep(1);
            $maxRounds--;
        } while ($recordsUpdated > 0 and $maxRounds > 0);
    }
}
