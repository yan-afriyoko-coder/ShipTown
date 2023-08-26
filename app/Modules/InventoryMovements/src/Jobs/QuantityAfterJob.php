<?php

namespace App\Modules\InventoryMovements\src\Jobs;

use App\Abstracts\UniqueJob;
use Illuminate\Support\Facades\DB;

class QuantityAfterJob extends UniqueJob
{
    public function handle()
    {
        $maxRounds = 10;

        do {
            $recordsUpdated = DB::update('
            UPDATE inventory_movements

            SET inventory_movements.quantity_after = quantity_before + quantity_delta

            WHERE  inventory_movements.quantity_after != quantity_before + quantity_delta
                AND NOT (inventory_movements.type = "stocktake" OR inventory_movements.description = "stocktake")
            ');
            sleep(1);
            $maxRounds--;
        } while ($recordsUpdated > 0 and $maxRounds > 0);
    }
}
