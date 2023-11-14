<?php

namespace App\Modules\InventoryMovements\src\Jobs;

use App\Abstracts\UniqueJob;
use App\Models\InventoryMovement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class SequenceNumberJob extends UniqueJob
{
    public function handle()
    {
        do {
            $minOccurred = InventoryMovement::whereNull('sequence_number')->min('occurred_at');

            Schema::dropIfExists('inventoryIdsToProcess');

            DB::statement('
                CREATE TEMPORARY TABLE inventoryIdsToProcess AS
                SELECT
                    id as movement_id,
                    inventory_id,
                    occurred_at
                FROM inventory_movements
                WHERE sequence_number IS NULL
                ORDER BY occurred_at
                LIMIT 10;
            ');

            DB::update('
                UPDATE inventory_movements
                INNER JOIN inventoryIdsToProcess
                  ON inventory_movements.inventory_id = inventoryIdsToProcess.inventory_id
                  AND inventory_movements.occurred_at > inventoryIdsToProcess.occurred_at
                  AND inventory_movements.sequence_number IS NOT NULL

                SET inventory_movements.sequence_number = null;
            ');

            Schema::dropIfExists('tempTable');

            DB::statement('
                CREATE TEMPORARY TABLE tempTable AS
                SELECT
                    id as movement_id,
                    tempTable2.inventory_id,
                    (SELECT sequence_number FROM inventory_movements as im_table WHERE im_table.inventory_id = tempTable2.inventory_id AND im_table.sequence_number IS NOT NULL ORDER BY im_table.sequence_number DESC LIMIT 1) as max_sequence_number,
                    row_number() over (partition by inventory_id order by occurred_at, id) as sequence_number,
                    (sum(quantity_delta) over (partition by inventory_id order by occurred_at, id)) as quantity_delta_sum
                FROM (
                    SELECT inventory_movements.*
                    FROM inventory_movements
                    WHERE inventory_id IN (SELECT inventory_id FROM inventoryIdsToProcess)
                    AND sequence_number IS NULL
                    AND occurred_at >= ?
                ) tempTable2;
            ', [$minOccurred]);

            $recordsUpdated = DB::update('
                UPDATE inventory_movements
                    INNER JOIN tempTable
                    ON inventory_movements.id = tempTable.movement_id

                    LEFT JOIN inventory_movements as last_sequenced_movement
                    ON last_sequenced_movement.inventory_id = inventory_movements.inventory_id
                        AND last_sequenced_movement.sequence_number = max_sequence_number

                SET
                    inventory_movements.sequence_number = tempTable.sequence_number + IFNULL(tempTable.max_sequence_number, 0),
                    inventory_movements.quantity_before = IFNULL(last_sequenced_movement.quantity_after, 0) + quantity_delta_sum - inventory_movements.quantity_delta,
                    inventory_movements.quantity_after = CASE
                        WHEN inventory_movements.type = "stocktake"
                        THEN inventory_movements.quantity_after
                        ELSE IFNULL(last_sequenced_movement.quantity_after, 0) + quantity_delta_sum
                    END;
            ');

            DB::update('
                UPDATE inventory

                SET
                    inventory.last_sequence_number = (SELECT MAX(sequence_number) FROM inventory_movements WHERE inventory_id = inventory.id)

                WHERE inventory.id IN (SELECT inventory_id FROM tempTable);
            ');

            DB::update('
                UPDATE inventory

                    INNER JOIN inventory_movements
                    ON inventory_movements.inventory_id = inventory.id
                        AND inventory_movements.sequence_number = inventory.last_sequence_number

                SET inventory.last_movement_id = inventory_movements.id,
                    inventory.last_movement_at = inventory_movements.occurred_at,
                    inventory.quantity = inventory_movements.quantity_after

                WHERE inventory.id IN (SELECT inventory_id FROM tempTable);
            ');

            Log::info('Job processing', [
                'job' => self::class,
                'recordsUpdated' => $recordsUpdated
            ]);

            usleep(100000); // 0.2 seconds
        } while ($recordsUpdated > 0);
    }

    private function ensureSequenceNumberAreNull(): void
    {
        do {
            Schema::dropIfExists('tempTable');

            DB::statement('
                CREATE TEMPORARY TABLE tempTable AS
                SELECT future_movements.*

                FROM inventory_movements

                INNER JOIN inventory_movements as future_movements
                    ON future_movements.sequence_number IS NOT NULL
                    AND future_movements.inventory_id = inventory_movements.inventory_id
                    AND future_movements.occurred_at >= inventory_movements.occurred_at

                WHERE inventory_movements.sequence_number IS NULL

                LIMIT 1000;
            ');

            DB::update('
                UPDATE `inventory_movements`

                INNER JOIN tempTable ON tempTable.id = inventory_movements.id

                SET inventory_movements.sequence_number = NULL;
            ');

            usleep(200000); // 0.2 seconds
        } while (DB::table('tempTable')->count() > 0);
    }
}
