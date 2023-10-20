<?php

namespace App\Modules\InventoryMovements\src\Jobs;

use App\Abstracts\UniqueJob;
use Illuminate\Support\Facades\DB;

class InventoryLastMovementIdJob extends UniqueJob
{
    public function handle()
    {
        $maxRounds = 10;

        do {
            $recordsUpdated = DB::update('
                WITH tbl AS (
                    SELECT
                        inventory.id as inventory_id,
                        (
                            SELECT ID
                            FROM inventory_movements
                            WHERE inventory_movements.inventory_id = inventory.id
                            AND inventory_movements.occurred_at > inventory.last_movement_at
                            ORDER BY occurred_at DESC, id DESC
                            LIMIT 1
                        ) as last_movement_id


                    FROM inventory
                    INNER JOIN inventory_movements
                      ON inventory_movements.inventory_id = inventory.id
                      AND inventory_movements.occurred_at > IFNULL(inventory.last_movement_at, "2000-01-01 00:00:00")

                    GROUP BY inventory.id

                    LIMIT 50000
                )

                UPDATE inventory
                INNER JOIN tbl
                 ON tbl.inventory_id = inventory.id
                INNER JOIN inventory_movements
                 ON inventory_movements.occurred_at = tbl.last_movement_id
                SET
                    inventory.last_movement_id  = inventory_movements.id,
                    inventory.last_movement_at  = inventory_movements.occurred_at,
                    inventory.quantity          = inventory_movements.quantity_after,
                    inventory.updated_at        = now();
            ');
            sleep(1);
            $maxRounds--;
        } while ($recordsUpdated > 0 and $maxRounds > 0);
    }
}
