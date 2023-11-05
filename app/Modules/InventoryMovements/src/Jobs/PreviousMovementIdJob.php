<?php

namespace App\Modules\InventoryMovements\src\Jobs;

use App\Abstracts\UniqueJob;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class PreviousMovementIdJob extends UniqueJob
{
    public function handle()
    {
        $maxRounds = 100;

        do {
            Schema::dropIfExists('tempTable');

            DB::statement('
                CREATE TEMPORARY TABLE tempTable AS
                    SELECT id, inventory_id,
                           (
                               SELECT id as id
                               FROM inventory_movements as previous_inventory_movement
                               WHERE previous_inventory_movement.inventory_id = inventory_movements.inventory_id
                                 AND previous_inventory_movement.occurred_at <= inventory_movements.occurred_at
                                 AND previous_inventory_movement.id != inventory_movements.id
                                 AND (
                                      (previous_inventory_movement.occurred_at = inventory_movements.occurred_at AND previous_inventory_movement.id < inventory_movements.id)
                                     OR (previous_inventory_movement.occurred_at < inventory_movements.occurred_at)
                                 )
                               ORDER BY
                                    previous_inventory_movement.occurred_at DESC,
                                    id DESC
                               LIMIT 1
                           ) as previous_movement_id
                    FROM inventory_movements
                    WHERE inventory_movements.is_first_movement IS NULL
                    LIMIT 500;
            ');

             DB::update('
                UPDATE inventory_movements

                INNER JOIN tempTable ON
                    tempTable.id = inventory_movements.previous_movement_id

                SET
                    inventory_movements.is_first_movement = null,
                    inventory_movements.previous_movement_id = null,
                    inventory_movements.updated_at = NOW()
            ');

            $recordsUpdated = DB::update('
                UPDATE inventory_movements
                INNER JOIN tempTable ON
                    tempTable.id = inventory_movements.id
                LEFT JOIN inventory_movements as previous_inventory_movement
                    ON previous_inventory_movement.id = tempTable.previous_movement_id
                LEFT JOIN inventory
                    ON inventory.id = inventory_movements.inventory_id
                SET
                    inventory_movements.is_first_movement = ISNULL(tempTable.previous_movement_id),
                    inventory_movements.product_id = inventory.product_id,
                    inventory_movements.warehouse_id = inventory.warehouse_id,
                    inventory_movements.quantity_before = IFNULL(previous_inventory_movement.quantity_after, 0),
                    inventory_movements.previous_movement_id = tempTable.previous_movement_id,
                    inventory_movements.updated_at = NOW()
            ');

            Log::info('Job processing', [
                'job' => self::class,
                'recordsUpdated' => $recordsUpdated
            ]);

            usleep(400000); // 0.4 seconds
        } while ($recordsUpdated > 0 and $maxRounds-- > 0);
    }
}
