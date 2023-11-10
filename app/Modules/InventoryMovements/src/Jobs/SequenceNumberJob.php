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
                       row_number() over (partition by inventory_id order by occurred_at asc, id asc) as sequence_number,
                       inventory_movements.id as movement_id
                    FROM inventory_movements
                       WHERE
                        inventory_id IN (SELECT * FROM (
                            SELECT DISTINCT inventory_id
                            FROM inventory_movements
                            WHERE sequence_number IS NULL
                            LIMIT 100
                        ) as tbl);
            ');

            $recordsUpdated = DB::update('
                UPDATE inventory_movements
                INNER JOIN tempTable
                    ON inventory_movements.id = tempTable.movement_id
                SET
                    inventory_movements.sequence_number = tempTable.sequence_number;
            ');

            Log::info('Job processing', [
                'job' => self::class,
                'recordsUpdated' => $recordsUpdated
            ]);

            usleep(200000); // 0.2 seconds
        } while ($recordsUpdated > 0 and $maxRounds-- > 0);
    }
}
