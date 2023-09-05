<?php

namespace App\Modules\InventoryTotals\src\Jobs;

use App\Abstracts\UniqueJob;
use Illuminate\Support\Facades\DB;

class LastCountedAtJob extends UniqueJob
{
    public function handle()
    {
        $this->setNullOnDeleted();
        $this->updateNewOnes();
    }

    public function setNullOnDeleted()
    {
        $maxRounds = 100;

        do {
            $recordsUpdated = DB::update('
                WITH tempTable AS (
                    SELECT
                      distinct inventory.id as inventory_id
                    FROM `inventory`

                    LEFT JOIN inventory_movements
                      ON inventory_movements.inventory_id = inventory.id
                      AND inventory_movements.type = "stocktake"
                      AND inventory_movements.created_at = inventory.last_counted_at

                    WHERE inventory.last_counted_at is not null

                    AND inventory_movements.id is null

                    LIMIT 1000
                )

                UPDATE inventory

                INNER JOIN tempTable
                  ON tempTable.inventory_id = inventory.id

                SET inventory.last_counted_at = null
            ');
            sleep(1);
            $maxRounds--;
        } while ($recordsUpdated > 0 and $maxRounds > 0);
    }

    public function updateNewOnes()
    {
        $maxRounds = 100;

        do {
            $recordsUpdated = DB::update('
                WITH tempTable AS (
                    SELECT
                      DISTINCT inventory_id
                    FROM `inventory`

                    INNER JOIN inventory_movements
                      ON inventory_movements.inventory_id = inventory.id
                      AND inventory_movements.type = "stocktake"
                      AND inventory_movements.created_at > IFNULL(inventory.last_counted_at, inventory.created_at)

                    LIMIT 1000
                )

                UPDATE inventory

                INNER JOIN tempTable
                  ON tempTable.inventory_id = inventory.id

                SET inventory.last_counted_at = (
                    SELECT MAX(created_at)
                    FROM inventory_movements
                    WHERE inventory_movements.inventory_id = inventory.id
                    AND inventory_movements.type = "stocktake"
                )
            ');
            sleep(1);
            $maxRounds--;
        } while ($recordsUpdated > 0 and $maxRounds > 0);
    }
}
