<?php

namespace App\Modules\InventoryMovements\src\Jobs;

use App\Abstracts\UniqueJob;
use Illuminate\Support\Facades\DB;

class RowNumJob extends UniqueJob
{
    public function handle()
    {
        $maxRounds = 10;

        do {
            $recordsUpdated = DB::update('
                WITH tbl AS (
                    SELECT row_number() over ( partition by inventory_id order by occurred_at, id desc ) as expected_row_num,
                    inventory_movements.*

                    FROM inventory_movements

                    WHERE row_num is null

                    LIMIT 3
                )

                UPDATE inventory_movements

                INNER JOIN tbl
                  ON tbl.id = inventory_movements.id

                SET inventory_movements.row_num = tbl.expected_row_num
            ');
            sleep(1);
            $maxRounds--;
        } while ($recordsUpdated > 0 and $maxRounds > 0);
    }
}
