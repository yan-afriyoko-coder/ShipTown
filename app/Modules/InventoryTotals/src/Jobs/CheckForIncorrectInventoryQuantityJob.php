<?php

namespace App\Modules\InventoryTotals\src\Jobs;

use App\Abstracts\UniqueJob;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckForIncorrectInventoryQuantityJob extends UniqueJob
{
    public function handle()
    {
        do {
            $recordsUpdated = DB::update('
                UPDATE inventory

                SET recount_required = 1

                WHERE ID IN (
                    SELECT id FROM (
                        SELECT inventory.id
                        FROM inventory

                        LEFT JOIN inventory_movements
                          ON inventory.id = inventory_movements.inventory_id
                          AND inventory.last_sequence_number = inventory_movements.sequence_number
                          AND inventory.quantity != inventory_movements.quantity_after

                        WHERE inventory.recount_required = 0
                        AND inventory_movements.id is not null

                        LIMIT 50
                    ) as tbl
                )
            ');

            Log::info('Job processing', [
                'job' => self::class,
                'recordsUpdated' => $recordsUpdated
            ]);

            usleep(100000); // 0.1 seconds
        } while ($recordsUpdated > 0);
    }
}
