<?php

namespace App\Modules\InventoryMovements\src\Jobs;

use App\Abstracts\UniqueJob;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class SequenceNumberJob extends UniqueJob
{
    public function handle()
    {
        $maxRounds = 1000;

        do {
            Schema::dropIfExists('tempTable');

            DB::statement('
CREATE TEMPORARY TABLE tempTable AS
SELECT
    id as movement_id,
    tempTable2.inventory_id,
    (SELECT sequence_number FROM inventory_movements as im_table WHERE im_table.inventory_id = tempTable2.inventory_id AND im_table.sequence_number IS NOT NULL ORDER BY im_table.sequence_number DESC LIMIT 1) as max_sequence_number,
    row_number() over (partition by inventory_id order by occurred_at) as sequence_number,
    (sum(quantity_delta) over (partition by inventory_id order by occurred_at asc, id asc)) as quantity_delta_sum
FROM (SELECT
          inventory_movements.*
      FROM inventory_movements
      WHERE sequence_number IS NULL
      LIMIT 1000
     ) tempTable2;

# SELECT * FROM tempTable;

            ');

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

            Log::info('Job processing', [
                'job' => self::class,
                'recordsUpdated' => $recordsUpdated
            ]);

            usleep(200000); // 0.2 seconds
        } while ($recordsUpdated > 0 and $maxRounds-- > 0);
    }
}
