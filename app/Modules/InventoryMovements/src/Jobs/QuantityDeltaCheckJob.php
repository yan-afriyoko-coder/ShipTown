<?php

namespace App\Modules\InventoryMovements\src\Jobs;

use App\Abstracts\UniqueJob;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class QuantityDeltaCheckJob extends UniqueJob
{
    public function handle()
    {
        $maxRounds = 500;

        do {
            $maxRounds--;

            $recordsUpdated = DB::update('
                UPDATE inventory_movements

                SET inventory_movements.quantity_delta = quantity_after - quantity_before,
                    inventory_movements.updated_at = NOW()

                WHERE inventory_movements.occurred_at BETWEEN DATE_SUB(now(), INTERVAL 1 DAY) AND now()
                    AND inventory_movements.sequence_number IS NOT NULL
                    AND inventory_movements.type = "stocktake"
                    AND inventory_movements.quantity_delta != quantity_after - quantity_before;
            ');

            Log::info('Job processing', [
                'job' => self::class,
                'recordsUpdated' => $recordsUpdated
            ]);

            usleep(100000); // 0.1 seconds
        } while ($recordsUpdated > 0 && $maxRounds > 0);
    }
}
