<?php

namespace App\Modules\InventoryMovements\src\Jobs;

use App\Abstracts\UniqueJob;
use App\Modules\InventoryMovements\src\Models\Configuration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class QuantityBeforeJob extends UniqueJob
{
    public function handle()
    {
        $maxRounds = 500;

        /** @var Configuration $configuration */
        $configuration = Configuration::query()->firstOrCreate();

        $minMovementId = $configuration->quantity_before_job_last_movement_id_checked;
        $maxMovementId = DB::table('inventory_movements')->max('id') ?? 0;

        do {
            $maxRounds--;

            if (($minMovementId === null) or ($maxRounds % 10 === 0)) {
                $minMovementId = $this->getMinMovementId($minMovementId ?? 0);
            }

            if ($minMovementId === null) {
                $configuration->update(['quantity_before_job_last_movement_id_checked' => $maxMovementId]);
                return;
            }

            Schema::dropIfExists('tempTable');

            DB::statement('
                CREATE TEMPORARY TABLE tempTable AS
                    SELECT
                        previous_movement.type as previous_movement_type,
                        previous_movement.is_first_movement as previous_movement_is_first_movement,
                        inventory_movements.type,
                        inventory_movements.is_first_movement,
                        inventory_movements.quantity_before as quantity_before_actual,
                        inventory_movements.quantity_delta as quantity_delta_actual,
                        inventory_movements.quantity_after as quantity_after_actual,
                        inventory_movements.created_at,
                        inventory_movements.inventory_id,
                        inventory_movements.id as movement_id,
                        (sum(inventory_movements.quantity_delta + IF(previous_movement.is_first_movement or previous_movement.type="stocktake", previous_movement.quantity_after, 0)) over (order by inventory_movements.id asc)) - inventory_movements.quantity_delta as quantity_before_expected,
                        inventory_movements.quantity_delta,
                        sum(inventory_movements.quantity_delta + IF(previous_movement.is_first_movement or previous_movement.type="stocktake", previous_movement.quantity_after, 0)) over (order by inventory_movements.id asc) as quantity_after_expected
                    FROM inventory_movements
                    INNER JOIN inventory
                        ON inventory.id = inventory_movements.inventory_id
                        AND inventory_movements.created_at >= IFNULL(inventory.last_counted_at, inventory.created_at)
                    INNER JOIN inventory_movements as previous_movement
                        ON previous_movement.id = inventory_movements.previous_movement_id
                    WHERE
                        inventory_movements.type != "stocktake"
                        AND inventory_movements.inventory_id = (
                            SELECT
                                inventory_movements.inventory_id as inventory_id
                            FROM inventory_movements

                            INNER JOIN inventory as inventory
                                ON inventory.id = inventory_movements.inventory_id
                                AND inventory_movements.created_at >= IFNULL(inventory.last_counted_at, inventory.created_at)

                            INNER JOIN inventory_movements as previous_movement
                                 ON previous_movement.id = inventory_movements.previous_movement_id
                                 AND inventory_movements.quantity_before != previous_movement.quantity_after

                            WHERE inventory_movements.id >= IFNULL(?, 0)
                            AND inventory_movements.type != "stocktake"

                            ORDER BY inventory_movements.id asc
                            LIMIT 1
                        )
                    ORDER BY inventory_movements.inventory_id asc, inventory_movements.id asc;
            ', [$minMovementId ?? 0]);

            $recordsUpdated = DB::update('
                UPDATE inventory_movements

                INNER JOIN tempTable
                    ON tempTable.inventory_id = inventory_movements.inventory_id
                    AND tempTable.movement_id = inventory_movements.id

                SET
                    inventory_movements.quantity_before = tempTable.quantity_before_expected,
                    inventory_movements.quantity_after = tempTable.quantity_after_expected,
                    inventory_movements.updated_at = NOW()

                WHERE inventory_movements.type != "stocktake";
            ');

            Log::info('Job processing', [
                'job' => self::class,
                'recordsUpdated' => $recordsUpdated
            ]);

            usleep(400000); // 0.4 seconds
        } while ($recordsUpdated > 0 && $maxRounds > 0);
    }

    private function getMinMovementId(int $minMovementId = 0): mixed
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
                AND inventory_movements.created_at >= IFNULL(inventory.last_counted_at, inventory.created_at)

            INNER JOIN inventory_movements as previous_movement
                 ON previous_movement.id = inventory_movements.previous_movement_id
                 AND inventory_movements.quantity_before != previous_movement.quantity_after

            WHERE inventory_movements.id >= IFNULL(?, 0)
            AND inventory_movements.type != "stocktake"

            ORDER BY inventory_movements.id asc
            LIMIT 1
        ', [$minMovementId]);

        return data_get($firstIncorrectMovementRecord, '0.movement_id');
    }
}
