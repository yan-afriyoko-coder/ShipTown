<?php

namespace App\Modules\InventoryMovements\src\Jobs;

use App\Abstracts\UniqueJob;
use App\Models\InventoryMovement;
use App\Modules\InventoryMovements\src\Models\Configuration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class QuantityBeforeBasicJob extends UniqueJob
{
    public function handle()
    {
        /** @var Configuration $configuration */
        $configuration = Configuration::query()->firstOrCreate();
        $lastMovementId = data_get(InventoryMovement::query()->latest('id')->first('id'), 'id', 0);

        $minMovementId = $configuration->quantity_before_basic_job_last_movement_id_checked ?? 0;

        do {
            $maxMovementId = min($minMovementId + 10000, $lastMovementId);

            do {
                $recordsUpdated = $this->getRecordsUpdated($minMovementId, $maxMovementId);

                Log::info('Job processing', [
                    'job' => self::class,
                    'records_updated' => $recordsUpdated
                ]);
            } while ($recordsUpdated > 0);

            $configuration->update(['quantity_before_basic_job_last_movement_id_checked' => $maxMovementId]);

            $minMovementId = $maxMovementId;

            usleep(400000); // 0.4 seconds
        } while ($minMovementId < $lastMovementId);
    }

    private function getRecordsUpdated(int $minMovementId, int $maxMovementId): int
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
}
