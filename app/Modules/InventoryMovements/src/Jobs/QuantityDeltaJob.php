<?php

namespace App\Modules\InventoryMovements\src\Jobs;

use App\Abstracts\UniqueJob;
use Illuminate\Support\Facades\DB;

class QuantityDeltaJob extends UniqueJob
{
    public function handle()
    {
        $maxRounds = 500;
        $minMovementId = null;

        do {
            $maxRounds--;

            $recordsUpdated = DB::update('
                WITH tbl AS (
                    SELECT inventory_movements.id
                    FROM inventory_movements
                    WHERE
                        inventory_movements.type = "stocktake"
                        AND inventory_movements.quantity_delta != quantity_after - quantity_before

                    LIMIT 100
                )

                UPDATE inventory_movements

                INNER JOIN tbl
                  ON tbl.id = inventory_movements.id

                SET inventory_movements.quantity_delta = quantity_after - quantity_before,
                    inventory_movements.updated_at = NOW()

                WHERE inventory_movements.type = "stocktake"
            ');

            usleep(400000); // 0.4 seconds
        } while ($recordsUpdated > 0 && $maxRounds > 0);
    }
}
