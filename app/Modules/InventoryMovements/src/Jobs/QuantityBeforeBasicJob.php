<?php

namespace App\Modules\InventoryMovements\src\Jobs;

use App\Abstracts\UniqueJob;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class QuantityBeforeBasicJob extends UniqueJob
{
    public function handle()
    {
        $maxRounds = 500;
        $minMovementId = null;

        do {
            $maxRounds--;

            if (($maxRounds % 10 === 0) or ($minMovementId === null)) {
                $minMovementId = data_get($this->getMin($minMovementId), 'movement_id');
            }

            Schema::dropIfExists('tempTable');

            DB::statement('
                CREATE TEMPORARY TABLE tempTable AS
                    SELECT
                       inventory_movements.id as movement_id,
                       previous_movement.quantity_after as quantity_before_expected,
                        inventory_movements.created_at

                    FROM inventory_movements

                    INNER JOIN inventory_movements as previous_movement
                        ON previous_movement.id = inventory_movements.previous_movement_id

                    WHERE
                        inventory_movements.id >= ?
                        AND inventory_movements.type != "stocktake"
                        AND inventory_movements.quantity_before != previous_movement.quantity_after

                    ORDER BY inventory_movements.id asc

                    LIMIT 5;
            ', [$minMovementId ?? 0]);

            $recordsUpdated = DB::update('
                UPDATE inventory_movements

                INNER JOIN tempTable
                  ON tempTable.movement_id = inventory_movements.id

                SET inventory_movements.quantity_before = tempTable.quantity_before_expected,
                    inventory_movements.quantity_after = tempTable.quantity_before_expected + inventory_movements.quantity_delta,
                    inventory_movements.updated_at = NOW()

                WHERE inventory_movements.type != "stocktake"
            ');

            Log::info('Job processing', [
                'job' => self::class,
                'recordsUpdated' => $recordsUpdated
            ]);

            usleep(200000); // 0.2 sec
        } while ($recordsUpdated > 0 && $maxRounds > 0);
    }

    private function getMin($minMovementId): array
    {
        return DB::select('
            SELECT
               inventory_movements.id as movement_id

            FROM inventory_movements

            INNER JOIN inventory_movements as previous_movement
                ON previous_movement.id = inventory_movements.previous_movement_id

            WHERE inventory_movements.id >= ?
                AND inventory_movements.quantity_before != previous_movement.quantity_after
                AND inventory_movements.type != "stocktake"

            ORDER BY inventory_movements.id asc

            LIMIT 1
        ', [$minMovementId ?? 0]);
    }
}
