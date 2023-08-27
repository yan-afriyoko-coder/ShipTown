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
                        max(inventory_movements.id) as last_movement_id
                        max(inventory_movements.created_at) as last_movement_at
                    FROM inventory
                    INNER JOIN inventory_movements
                      ON inventory_movements.inventory_id = inventory.id
                      AND inventory_movements.id > IFNULL(inventory.last_movement_id, 0)

                    GROUP BY inventory.id

                    LIMIT 50000
                )
                UPDATE inventory
                INNER JOIN tbl
                 ON tbl.inventory_id = inventory.id
                SET
                    inventory.last_movement_id = tbl.last_movement_id,
                    inventory.last_movement_at = tbl.last_movement_at,
                    inventory.updated_at = now();
            ');
            sleep(1);
            $maxRounds--;
        } while ($recordsUpdated > 0 and $maxRounds > 0);
    }
}
