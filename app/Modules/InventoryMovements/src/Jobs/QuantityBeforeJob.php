<?php

namespace App\Modules\InventoryMovements\src\Jobs;

use App\Abstracts\UniqueJob;
use Illuminate\Support\Facades\DB;

class QuantityBeforeJob extends UniqueJob
{
    public function handle()
    {
        $minMovementId = null;

        do {
            if (($minMovementId === null) or (rand(0, 1000) === 0)) {
                $minMovementId = $this->getMinMovementId($minMovementId);
            }

            if ($minMovementId === null) {
                return;
            }

            $recordsUpdated = DB::update('
                CREATE TEMPORARY TABLE tempTable AS
                  SELECT
                    inventory_movements.is_first_movement,
                    inventory_movements.quantity_before as quantity_before_actual,
                    inventory_movements.quantity_delta as quantity_delta_actual,
                    inventory_movements.quantity_after as quantity_after_actual,
                    inventory_movements.created_at,
                    inventory_movements.inventory_id,
                    inventory_movements.id as movement_id,
                    (sum(inventory_movements.quantity_delta + IF(is_first_movement, quantity_before, 0)) over (order by inventory_movements.id asc)) - inventory_movements.quantity_delta as quantity_before_expected,
                    quantity_delta,
                    sum(inventory_movements.quantity_delta + IF(is_first_movement, quantity_before, 0)) over (order by inventory_movements.id asc) as quantity_after_expected
                  FROM inventory_movements
                  INNER JOIN inventory
                    ON inventory.id = inventory_movements.inventory_id
                    AND inventory_movements.created_at > IFNULL(inventory.last_counted_at, inventory.created_at)
                  WHERE  inventory_movements.inventory_id = (
                      SELECT
                            inventory_movements.inventory_id as inventory_id
                        FROM inventory_movements

                        INNER JOIN inventory as inventory
                          ON inventory.id = inventory_movements.inventory_id
                          AND inventory_movements.created_at > IFNULL(inventory.last_counted_at, inventory.created_at)

                        INNER JOIN inventory_movements as previous_movement
                         ON previous_movement.id = inventory_movements.previous_movement_id
                         AND inventory_movements.quantity_before != previous_movement.quantity_after

                        WHERE inventory_movements.id >= IFNULL(?, 0)
                        AND inventory_movements.type != "stocktake"
                        LIMIT 1
                      )

                  ORDER BY inventory_movements.inventory_id asc, inventory_movements.id asc;


                UPDATE inventory_movements
                INNER JOIN tempTable
                  ON tempTable.inventory_id = inventory_movements.inventory_id
                  AND tempTable.movement_id = inventory_movements.id

                SET
                 inventory_movements.quantity_before = tempTable.quantity_before_expected,
                  inventory_movements.quantity_after = tempTable.quantity_after_expected

                WHERE inventory_movements.type != "stocktake"
                    ;
            ', [$minMovementId]);
            sleep(1);
        } while ($recordsUpdated > 0);
    }

    private function getMinMovementId(int $minMovementId = null): mixed
    {
        $firstIncorrectMovementRecord = DB::select('
                    SELECT
                        inventory_movements.created_at as created_at,
                        inventory_movements.id as movement_id,
                        inventory_movements.type as type,
                        inventory_movements.inventory_id as inventory_id,
                        previous_movement.quantity_after - inventory_movements.quantity_before as quantity_before_delta

                    FROM inventory_movements

                    INNER JOIN inventory as inventory
                      ON inventory.id = inventory_movements.inventory_id
                      AND inventory_movements.created_at > IFNULL(inventory.last_counted_at, inventory.created_at)

                    INNER JOIN inventory_movements as previous_movement
                     ON previous_movement.id = inventory_movements.previous_movement_id
                     AND inventory_movements.quantity_before != previous_movement.quantity_after

                    WHERE inventory_movements.id >= IFNULL(?, 0)
                    LIMIT 1
        ', [$minMovementId]);

        return data_get($firstIncorrectMovementRecord, '0.movement_id');
    }
}
