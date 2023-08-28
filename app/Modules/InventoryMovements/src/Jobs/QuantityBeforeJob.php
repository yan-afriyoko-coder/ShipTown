<?php

namespace App\Modules\InventoryMovements\src\Jobs;

use App\Abstracts\UniqueJob;
use Illuminate\Support\Facades\DB;

class QuantityBeforeJob extends UniqueJob
{
    public function handle()
    {
        $refreshCounter = 0;
        $minMovementId = null;

        do {
            if ($refreshCounter <= 0) {
                $minMovementId = $this->getMinMovementId($minMovementId);
                $refreshCounter = 10;
            }

            $refreshCounter--;

            if ($minMovementId === null) {
                return;
            }

            $recordsUpdated = DB::update('
                WITH tbl AS (
                    SELECT
                        inventory_movements.created_at as created_at,
                        inventory_movements.id as movement_id,
                        inventory_movements.description as description,
                        inventory_movements.inventory_id as inventory_id,
                        previous_movement.quantity_after - inventory_movements.quantity_before as quantity_before_delta

                    FROM inventory_movements
                    INNER JOIN inventory_movements as previous_movement
                     ON previous_movement.id = inventory_movements.previous_movement_id
                     AND inventory_movements.quantity_before != previous_movement.quantity_after

                    WHERE inventory_movements.id >= IFNULL(?, 0)
                    LIMIT 1
                )

                UPDATE inventory_movements
                INNER JOIN tbl ON
                    tbl.movement_id = inventory_movements.id
                SET
                    inventory_movements.quantity_before = inventory_movements.quantity_before + tbl.quantity_before_delta,

                    inventory_movements.quantity_delta = CASE WHEN inventory_movements.description = "stocktake"
                            THEN inventory_movements.quantity_delta - tbl.quantity_before_delta
                            ElSE inventory_movements.quantity_delta
                            END,

                    inventory_movements.quantity_after = CASE WHEN inventory_movements.description = "stocktake"
                            THEN inventory_movements.quantity_after
                            ELSE inventory_movements.quantity_after + tbl.quantity_before_delta
                            END
                    ;
            ', [$minMovementId]);
            usleep(100000);
        } while ($recordsUpdated > 0);
    }

    private function getMinMovementId(int $minMovementId = null): mixed
    {
        $firstIncorrectMovementRecord = DB::select('
                SELECT
                    inventory_movements.id as min_movement_id

                FROM inventory_movements
                INNER JOIN inventory_movements as previous_movement
                 ON previous_movement.id = inventory_movements.previous_movement_id

                WHERE inventory_movements.quantity_before != previous_movement.quantity_after
                AND inventory_movements.id >= IFNULL(?, 0)

                ORDER BY inventory_movements.id ASC
                LIMIT 1
        ', [$minMovementId]);

        return data_get($firstIncorrectMovementRecord, '0.min_movement_id');
    }
}
