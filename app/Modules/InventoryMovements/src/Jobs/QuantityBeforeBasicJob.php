<?php

namespace App\Modules\InventoryMovements\src\Jobs;

use App\Abstracts\UniqueJob;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class QuantityBeforeBasicJob extends UniqueJob
{
    public function handle()
    {
        $maxRounds = 500;
        $minMovementId = null;

        do {
            $maxRounds--;

            if ($maxRounds % 10 === 0 or ($minMovementId === null)) {
                $minMovementId = data_get($this->getMin(), 'movement_id');
            }

            Log::debug('QuantityBeforeBasicJob: rounds left ' . $maxRounds);

            $recordsUpdated = DB::update('
                WITH tbl AS (
                    SELECT
                       inventory_movements.id as movement_id,
                       previous_movement.quantity_after as quantity_before_expected

                    FROM inventory_movements

                    INNER JOIN inventory_movements as previous_movement
                        ON previous_movement.id = inventory_movements.previous_movement_id

                    WHERE
                        inventory_movements.id >= ?
                        AND inventory_movements.type != "stocktake"
                        AND inventory_movements.quantity_before != previous_movement.quantity_after

                    ORDER BY inventory_movements.id asc

                    LIMIT 5
                )

                UPDATE inventory_movements

                INNER JOIN tbl
                  ON tbl.movement_id = inventory_movements.id

                SET inventory_movements.quantity_before = tbl.quantity_before_expected,
                    inventory_movements.quantity_after = tbl.quantity_before_expected + inventory_movements.quantity_delta,
                    updated_at = NOW()

                WHERE inventory_movements.type != "stocktake"
            ', [
                'minMovementId' => $minMovementId,
            ]);

            Log::debug('QuantityBeforeBasicJob: records updated ' . $recordsUpdated);
            usleep(200000); // 0.2 sec
        } while ($recordsUpdated > 0 && $maxRounds > 0);
    }

    /**
     * @return mixed
     */
    private function getMin(): mixed
    {
        return DB::select('
            SELECT
               inventory_movements.id as movement_id

            FROM inventory_movements

            INNER JOIN inventory_movements as previous_movement
                ON previous_movement.id = inventory_movements.previous_movement_id

            WHERE
                inventory_movements.type != "stocktake"
                AND inventory_movements.quantity_before != previous_movement.quantity_after

            ORDER BY inventory_movements.id asc

            LIMIT 1
        ');
    }
}
