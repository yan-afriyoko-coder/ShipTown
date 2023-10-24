<?php

namespace App\Modules\InventoryMovements\src\Jobs;

use App\Abstracts\UniqueJob;
use App\Models\InventoryMovement;
use App\Modules\InventoryMovements\src\Models\Configuration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class QuantityBeforeJob extends UniqueJob
{
    public function handle()
    {
        /** @var Configuration $configuration */
        $configuration = Configuration::query()->firstOrCreate();
        $lastMovementId = data_get(InventoryMovement::query()->orderByRaw('occurred_at DESC, id DESC')->first('id'), 'id', 0);

        $minMovementId = $configuration->quantity_before_job_last_movement_id_checked ?? 0;

        do {
            $maxMovementId = min($minMovementId + 10000, $lastMovementId);

            do {
                $totalRecordsUpdated = 0;

                $recordsUpdated = $this->updateRanges($minMovementId, $maxMovementId);
                $totalRecordsUpdated += $recordsUpdated;

                Log::info('Job processing', [
                    'job' => self::class,
                    'records_updated' => $recordsUpdated,
                    'min_movement_id' => $minMovementId,
                    'max_movement_id' => $maxMovementId,
                    'last_movement_id' => $lastMovementId,
                ]);

                $recordsUpdated = $this->basicWalktrough($minMovementId, $maxMovementId);
                $totalRecordsUpdated += $recordsUpdated;

                Log::info('Job processing', [
                    'job' => self::class,
                    'records_updated' => $recordsUpdated,
                    'min_movement_id' => $minMovementId,
                    'max_movement_id' => $maxMovementId,
                    'last_movement_id' => $lastMovementId,
                ]);

                $recordsUpdated = $this->updateStocktakes($minMovementId, $maxMovementId);
                $totalRecordsUpdated += $recordsUpdated;

                Log::info('Job processing', [
                    'job' => self::class,
                    'records_updated' => $recordsUpdated,
                    'min_movement_id' => $minMovementId,
                    'max_movement_id' => $maxMovementId,
                    'last_movement_id' => $lastMovementId,
                ]);
            } while ($totalRecordsUpdated > 0);

            $configuration->update(['quantity_before_job_last_movement_id_checked' => $maxMovementId]);

            $minMovementId = $maxMovementId;

            usleep(400000); // 0.4 seconds
        } while ($minMovementId < $lastMovementId);
    }

    private function updateRanges(int $minMovementId, int $maxMovementId): int
    {
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

                            WHERE inventory_movements.id BETWEEN ? AND ?
                            AND inventory_movements.type != "stocktake"

                            ORDER BY inventory_movements.id
                            LIMIT 1
                        )
                    ORDER BY inventory_movements.inventory_id, inventory_movements.id;
            ', [$minMovementId, $maxMovementId]);

        return DB::update('
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
    }

    private function basicWalktrough(int $minMovementId, int $maxMovementId): int
    {
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
                        inventory_movements.id BETWEEN ? AND ?
                        AND inventory_movements.type != "stocktake"
                        AND inventory_movements.quantity_before != previous_movement.quantity_after

                    ORDER BY inventory_movements.id

                    LIMIT 5;
            ', [$minMovementId, $maxMovementId]);

        return DB::update('
                UPDATE inventory_movements

                INNER JOIN tempTable
                  ON tempTable.movement_id = inventory_movements.id

                SET inventory_movements.quantity_before = tempTable.quantity_before_expected,
                    inventory_movements.quantity_after = tempTable.quantity_before_expected + inventory_movements.quantity_delta,
                    inventory_movements.updated_at = NOW()

                WHERE inventory_movements.type != "stocktake"
            ');
    }

    private function updateStocktakes(int $minMovementId, int $maxMovementId): int
    {
        Schema::dropIfExists('tempTable');

        DB::statement('
                CREATE TEMPORARY TABLE tempTable AS
                    SELECT
                       inventory_movements.id as movement_id,
                       previous_movement.quantity_after as quantity_before_expected,
                       inventory_movements.quantity_after - previous_movement.quantity_after as quantity_delta_expected,
                       inventory_movements.quantity_after as quantity_after_expected,
                       inventory_movements.quantity_before,
                       inventory_movements.quantity_delta,
                       inventory_movements.quantity_after

                    FROM inventory_movements
                    LEFT JOIN inventory_movements as previous_movement
                      ON previous_movement.id = inventory_movements.previous_movement_id

                    WHERE inventory_movements.id BETWEEN ? AND ?
                    AND inventory_movements.type = "stocktake"
                    AND previous_movement.quantity_after != inventory_movements.quantity_before

                    LIMIT 1000;
            ', [$minMovementId, $maxMovementId]);

        return DB::update('
                UPDATE inventory_movements
                INNER JOIN tempTable
                  ON tempTable.movement_id = inventory_movements.id

                SET inventory_movements.quantity_before = tempTable.quantity_before_expected,
                    inventory_movements.quantity_delta = tempTable.quantity_delta_expected,
                    inventory_movements.updated_at = NOW()

                WHERE inventory_movements.type = "stocktake"
            ');
    }
}
