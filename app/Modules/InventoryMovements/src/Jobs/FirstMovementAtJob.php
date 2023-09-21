<?php

namespace App\Modules\InventoryMovements\src\Jobs;

use App\Abstracts\UniqueJob;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FirstMovementAtJob extends UniqueJob
{
    public function handle()
    {
        $maxRounds = 100;

        do {
            $recordsUpdated = DB::update('
                WITH tempTable AS (
                    SELECT DISTINCT inventory.id as inventory_id
                    FROM inventory

                    WHERE
                        inventory.last_movement_id IS NOT NULL
                        AND inventory.first_movement_at IS NULL

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
            Log::debug('Job processing', ['job' => self::class, 'records_updated' => $recordsUpdated]);
        } while ($recordsUpdated > 0 and $maxRounds > 0);
    }
}
