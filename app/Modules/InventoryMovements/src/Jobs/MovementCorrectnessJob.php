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
            Schema::dropIfExists('tempTable');

            DB::statement('
                CREATE TEMPORARY TABLE tempTable AS
                SELECT inventory_movements.*
                FROM inventory_movements

                INNER JOIN inventory_movements as previous_movement
                    ON inventory_movements.inventory_id = previous_movement.inventory_id
                    AND inventory_movements.sequence_number = previous_movement.sequence_number + 1
                    AND (
                     inventory_movements.quantity_before != previous_movement.quantity_after
                     OR inventory_movements.occurred_at < previous_movement.occurred_at
                )

                LIMIT 10
            ');

            $recordsUpdated =  DB::update('
                UPDATE inventory_movements SET sequence_number = null WHERE ID IN (select id from tempTable);
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
