<?php

namespace App\Modules\InventoryMovements\src\Jobs;

use App\Abstracts\UniqueJob;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RecalculateInventoryRecordsJob extends UniqueJob
{
    public function handle()
    {
        do {
            $recordsUpdated = DB::update('
                UPDATE inventory

                SET
                    inventory.recount_required      = 0,
                    inventory.last_sequence_number  = (SELECT sequence_number FROM inventory_movements WHERE inventory_id = inventory.id AND sequence_number IS NOT NULL ORDER BY occurred_at DESC, sequence_number DESC LIMIT 1),
                    inventory.quantity              = (SELECT quantity_after FROM inventory_movements WHERE inventory_id = inventory.id ORDER BY occurred_at DESC, sequence_number DESC LIMIT 1),
                    inventory.last_movement_id      = (SELECT id FROM inventory_movements WHERE inventory_id = inventory.id ORDER BY occurred_at DESC, sequence_number DESC LIMIT 1),
                    inventory.first_movement_at     = (SELECT MIN(occurred_at) FROM inventory_movements WHERE inventory_id = inventory.id),
                    inventory.last_movement_at      = (SELECT MAX(occurred_at) FROM inventory_movements WHERE inventory_id = inventory.id),
                    inventory.first_counted_at      = (SELECT MIN(occurred_at) FROM inventory_movements WHERE inventory_id = inventory.id AND type = "stocktake"),
                    inventory.last_counted_at       = (SELECT MAX(occurred_at) FROM inventory_movements WHERE inventory_id = inventory.id AND type = "stocktake"),
                    inventory.first_sold_at         = (SELECT MIN(occurred_at) FROM inventory_movements WHERE inventory_id = inventory.id AND type = "sale"),
                    inventory.last_sold_at          = (SELECT MAX(occurred_at) FROM inventory_movements WHERE inventory_id = inventory.id AND type = "sale"),
                    inventory.first_received_at     = (SELECT MIN(occurred_at) FROM inventory_movements WHERE inventory_id = inventory.id AND quantity_delta > 0),
                    inventory.last_received_at      = (SELECT MAX(occurred_at) FROM inventory_movements WHERE inventory_id = inventory.id AND quantity_delta > 0),
                    inventory.updated_at            = now()

                WHERE inventory.id IN (SELECT id FROM (SELECT id FROM inventory WHERE inventory.recount_required = 1 LIMIT 100) as tbl);
            ');

            Log::info('Job processing', [
                'job' => self::class,
                'recordsUpdated' => $recordsUpdated
            ]);

            usleep(100000); // 0.1 seconds
        } while ($recordsUpdated > 0);
    }
}
