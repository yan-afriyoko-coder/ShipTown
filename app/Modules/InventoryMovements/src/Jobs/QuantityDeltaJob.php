<?php

namespace App\Modules\InventoryMovements\src\Jobs;

use App\Abstracts\UniqueJob;
use Illuminate\Support\Facades\DB;

class QuantityDeltaJob extends UniqueJob
{
    public function handle()
    {
        do {
            $recordsUpdated = DB::update('
                WITH tbl AS (
                    SELECT inventory_movements.id
                    FROM inventory_movements
                    WHERE
                        inventory_movements.type = "stocktake"
                        AND inventory_movements.quantity_delta != quantity_after - quantity_before

                    LIMIT 10
                )

                UPDATE inventory_movements

                INNER JOIN tbl
                  ON tbl.id = inventory_movements.id

                SET inventory_movements.quantity_delta = quantity_after - quantity_before
            ');
            sleep(1);
        } while ($recordsUpdated > 0);
    }
}
