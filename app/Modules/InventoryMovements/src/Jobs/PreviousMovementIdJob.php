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
                                 AND previous_inventory_movement.id < inventory_movements.id
                               ORDER BY ID DESC
                               LIMIT 1
                           ) as previous_movement_id
                    FROM inventory_movements
                    WHERE
                        inventory_movements.previous_movement_id IS NULL
                        AND inventory_movements.is_first_movement IS NULL
                    LIMIT 500;
            ');

            $recordsUpdated = DB::update('
                UPDATE inventory_movements
                INNER JOIN tempTable ON
                    tempTable.id = inventory_movements.id
                SET
                    is_first_movement = ISNULL(tempTable.previous_movement_id),
                    inventory_movements.previous_movement_id = tempTable.previous_movement_id,
                    inventory_movements.updated_at = NOW()
            ');

            Log::info('Job processing', [
                'job' => self::class,
                'recordsUpdated' => $recordsUpdated
            ]);

            sleep(1);
        } while ($recordsUpdated > 0 and $maxRounds-- > 0);
    }
}
