<?php

namespace App\Modules\InventoryMovements\src\Jobs;

use App\Abstracts\UniqueJob;
use Illuminate\Support\Facades\DB;

class FirstMovementAtJob extends UniqueJob
{
    public function handle()
    {
        $maxRounds = 100;

        do {
            $recordsUpdated = DB::update('
                WITH tempTable AS (
                    SELECT DISTINCT inventory.id as inventory_id
                    FROM  inventory

                    INNER JOIN inventory_movements
                        ON inventory_movements.inventory_id = inventory.id
                        AND inventory_movements.created_at < IFNULL(inventory.first_movement_at, now())

                    WHERE inventory.last_movement_id IS NOT NULL
                    LIMIT 500
                )

                UPDATE inventory

                INNER JOIN tempTable
                  ON tempTable.inventory_id = inventory.id

                SET inventory.updated_at = now(),
                    inventory.first_movement_at = (
                        SELECT MIN(created_at)
                        FROM inventory_movements
                        WHERE inventory_id = inventory.id
                    )
            ');
            sleep(1);
            $maxRounds--;
        } while ($recordsUpdated > 0 and $maxRounds > 0);
    }
}
