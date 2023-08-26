<?php

namespace App\Modules\InventoryMovements\src\Jobs;

use App\Abstracts\UniqueJob;
use Illuminate\Support\Facades\DB;

class PreviousMovementIdJob extends UniqueJob
{
    public function handle()
    {
        $maxRounds = 50;

        do {
            $recordsUpdated = DB::update('
            WITH tbl AS (
                SELECT id, inventory_id,
                       (
                           SELECT MAX(ID) as id
                           FROM inventory_movements as previous_inventory_movement
                           WHERE previous_inventory_movement.inventory_id = inventory_movements.inventory_id
                             AND previous_inventory_movement.id < inventory_movements.id
                       ) as previous_movement_id
                FROM inventory_movements
                WHERE
                    inventory_movements.previous_movement_id IS NULL
                    AND is_first_movement IS NULL
                LIMIT 5000
            )
            UPDATE inventory_movements
            INNER JOIN tbl ON
                tbl.id = inventory_movements.id
            SET
                is_first_movement = ISNULL(tbl.previous_movement_id),
                inventory_movements.previous_movement_id = tbl.previous_movement_id
            ');
            sleep(1);
            $maxRounds--;
        } while ($recordsUpdated > 0 and $maxRounds > 0);
    }
}
