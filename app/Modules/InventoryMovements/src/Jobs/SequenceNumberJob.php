<?php

namespace App\Modules\InventoryMovements\src\Jobs;

use App\Abstracts\UniqueJob;
use App\Models\InventoryMovement;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class SequenceNumberJob extends UniqueJob
{
    public function handle()
    {
        do {
            $minOccurred = InventoryMovement::query()->whereNull('sequence_number')->min('occurred_at');
            $maxOccurred = Carbon::parse($minOccurred)->addDay();

            Schema::dropIfExists('inventoryIdsToProcess');

            // Create a temporary table with the first 100 movements that need to be processed
            DB::statement('
                CREATE TEMPORARY TABLE inventoryIdsToProcess AS
                SELECT
                    id as movement_id,
                    inventory_id,
                    occurred_at
                FROM inventory_movements
                WHERE sequence_number IS NULL
                ORDER BY occurred_at
                LIMIT 100;
            ');

            // Reset sequence_number to null for all movements that occurred after the first movement for the inventory_id with s
            DB::update('
                UPDATE inventory_movements
                INNER JOIN inventoryIdsToProcess
                  ON inventory_movements.inventory_id = inventoryIdsToProcess.inventory_id
                  AND inventory_movements.occurred_at >= inventoryIdsToProcess.occurred_at
                  AND CASE WHEN inventory_movements.occurred_at = inventoryIdsToProcess.occurred_at THEN inventory_movements.id > inventoryIdsToProcess.movement_id ELSE TRUE END
                  AND inventory_movements.sequence_number IS NOT NULL

                SET inventory_movements.sequence_number = null;
            ');

            Schema::dropIfExists('tempTable');

            // Recalculate running totals for all movements that occurred after the first movement for the inventory_id
            DB::statement('
                CREATE TEMPORARY TABLE tempTable AS
                SELECT
                    id as movement_id,
                    tempTable2.inventory_id,
                    (SELECT sequence_number FROM inventory_movements as im_table WHERE im_table.inventory_id = tempTable2.inventory_id AND im_table.sequence_number IS NOT NULL ORDER BY im_table.sequence_number DESC LIMIT 1) as max_sequence_number,
                    row_number() over (partition by inventory_id order by occurred_at, id) as sequence_number,
                    (sum(quantity_delta) over (partition by inventory_id order by occurred_at, id)) as quantity_delta_sum
                FROM (
                    SELECT inventory_movements.id,
                            inventory_movements.inventory_id,
                            inventory_movements.occurred_at,
                            inventory_movements.type,
                            IF(inventory_movements.type != "stocktake", inventory_movements.quantity_delta, 0) as quantity_delta,
                            inventory_movements.quantity_after,
                            inventory_movements.quantity_before,
                            inventory_movements.sequence_number,
                            inventory_movements.created_at,
                            inventory_movements.updated_at
                    FROM inventory_movements
                    WHERE inventory_id IN (SELECT inventory_id FROM inventoryIdsToProcess)
                    AND sequence_number IS NULL
                    AND occurred_at >= ?
                    AND occurred_at <= ?
                ) tempTable2;
            ', [$minOccurred, $maxOccurred]);

            // Update the sequence_number, quantity_before and quantity_after for all movements that occurred after the first movement for the inventory_id
            $recordsUpdated = DB::update('
                UPDATE inventory_movements

                INNER JOIN tempTable
                    ON inventory_movements.id = tempTable.movement_id

                LEFT JOIN inventory_movements as last_sequenced_movement
                    ON last_sequenced_movement.inventory_id = inventory_movements.inventory_id
                    AND last_sequenced_movement.sequence_number = max_sequence_number

                SET
                    inventory_movements.sequence_number = tempTable.sequence_number + IFNULL(tempTable.max_sequence_number, 0),
                    inventory_movements.quantity_before = CASE
                        WHEN inventory_movements.type = "stocktake"
                        THEN IFNULL(last_sequenced_movement.quantity_after, 0) + quantity_delta_sum
                        ELSE IFNULL(last_sequenced_movement.quantity_after, 0) + quantity_delta_sum - inventory_movements.quantity_delta
                    END,
                    inventory_movements.quantity_after = CASE
                        WHEN inventory_movements.type = "stocktake"
                        THEN inventory_movements.quantity_after
                        ELSE IFNULL(last_sequenced_movement.quantity_after, 0) + quantity_delta_sum
                    END;
            ');

            // Update the quantity_delta for all stocktake movements that occurred after the first movement for the inventory_id
            DB::update('
                UPDATE inventory_movements

                SET inventory_movements.quantity_delta = quantity_after - quantity_before,
                    inventory_movements.updated_at = NOW()

                WHERE inventory_movements.type = "stocktake"
                AND inventory_movements.quantity_delta != quantity_after - quantity_before
                AND inventory_movements.occurred_at BETWEEN ? AND ?
                AND inventory_movements.inventory_id IN (SELECT DISTINCT inventory_id FROM tempTable)
            ', [$minOccurred, $maxOccurred]);

            DB::update('
                UPDATE inventory

                SET inventory.recount_required = 1

                WHERE inventory.id IN (SELECT DISTINCT inventory_id FROM tempTable);
            ');

            Schema::dropIfExists('incorrectMovementsTable');

            DB::statement('
                CREATE TEMPORARY TABLE incorrectMovementsTable AS
                SELECT inventory_movements.*
                FROM inventory_movements

                INNER JOIN inventory_movements as previous_movement
                    ON inventory_movements.inventory_id = previous_movement.inventory_id
                    AND inventory_movements.sequence_number - 1 = previous_movement.sequence_number
                    AND (
                           inventory_movements.quantity_before != previous_movement.quantity_after
                           OR inventory_movements.occurred_at < previous_movement.occurred_at
                       )

                WHERE inventory_movements.occurred_at BETWEEN ? AND ?
                AND inventory_movements.inventory_id IN (SELECT DISTINCT inventory_id FROM tempTable)
            ', [$minOccurred, $maxOccurred]);

            DB::update('
                UPDATE inventory_movements
                INNER JOIN incorrectMovementsTable
                ON incorrectMovementsTable.id = inventory_movements.id
                SET inventory_movements.sequence_number = null;
            ');

            Log::info('Job processing', [
                'job' => self::class,
                'recordsUpdated' => $recordsUpdated,
            ]);

            usleep(100000); // 0.1 seconds
        } while ($recordsUpdated > 0);
    }
}
