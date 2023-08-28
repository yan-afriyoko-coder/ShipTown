<?php

namespace App\Modules\InventoryTotals\src\Jobs;

use App\Abstracts\UniqueJob;
use Illuminate\Support\Facades\DB;

/**
 *
 */
class FirstMovementAtJob extends UniqueJob
{
    public function handle()
    {
        $maxRounds = 100;

        do {
            $recordsUpdated = DB::update('
                UPDATE `inventory`

                SET first_movement_at = (
                    SELECT MIN(created_at)
                    FROM inventory_movements
                    WHERE inventory_movements.inventory_id = inventory.id
                )

                WHERE `last_movement_id` IS NOT NULL
                AND first_movement_at IS NULL
                LIMIT 5000
            ');
            sleep(1);
            $maxRounds--;
        } while ($recordsUpdated > 0 and $maxRounds > 0);
    }
}
