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
                WITH tempTable AS (
                    SELECT
                      DISTINCT inventory_id
                    FROM `inventory`

                    INNER JOIN inventory_movements
                      ON inventory_movements.inventory_id = inventory.id
                      AND inventory_movements.type = "stocktake"
                      AND inventory_movements.created_at > IFNULL(inventory.last_counted_at, "2000-01-01 00:00:00")

                    LIMIT 1000
                )

                UPDATE inventory

                INNER JOIN tempTable
                  ON tempTable.inventory_id = inventory.id

                SET inventory.last_counted_at = (
                    SELECT MAX(created_at)
                    FROM inventory_movements
                    WHERE inventory_movements.inventory_id = inventory.id
                    AND inventory_movements.type = "stocktake"
                )
            ');
            sleep(1);
            $maxRounds--;
        } while ($recordsUpdated > 0 and $maxRounds > 0);
    }
}
