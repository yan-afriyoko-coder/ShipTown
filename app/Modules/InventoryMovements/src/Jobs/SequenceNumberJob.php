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
                       inventory_movements.inventory_id,
                       (SELECT MAX(sequence_number) FROM inventory_movements as im_table WHERE im_table.inventory_id = inventory_movements.inventory_id) as max_sequence_number,
                       row_number() over (partition by inventory_id order by occurred_at asc, id asc) as sequence_number,
                       inventory_movements.id as movement_id
                    FROM inventory_movements
                    WHERE sequence_number IS NULL;
            ');

            $recordsUpdated = DB::update('
                UPDATE inventory_movements
                INNER JOIN tempTable
                    ON inventory_movements.id = tempTable.movement_id
                SET
                    inventory_movements.sequence_number = tempTable.sequence_number + ISNULL(tempTable.max_sequence_number);
            ');

            Log::info('Job processing', [
                'job' => self::class,
                'recordsUpdated' => $recordsUpdated
            ]);

            usleep(200000); // 0.2 seconds
        } while ($recordsUpdated > 0 and $maxRounds-- > 0);
    }
}
