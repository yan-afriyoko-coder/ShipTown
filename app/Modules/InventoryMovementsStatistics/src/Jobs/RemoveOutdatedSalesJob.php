<?php

namespace App\Modules\InventoryMovementsStatistics\src\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class RemoveOutdatedSalesJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function handle()
    {
        $this->removeOutdatedSales(7);
        $this->removeOutdatedSales(14);
        $this->removeOutdatedSales(28);
    }

    private function removeOutdatedSales($days): void
    {
        $records = collect(DB::select('
            SELECT
              inventory_movements.type,
              inventory_movements.inventory_id,
              max(inventory_movements_statistics.last'.$days.'days_min_movement_id) as current_min_movement_id,
              max(inventory_movements.id) as last_movement_id,
              sum(inventory_movements.quantity_delta) as sum_quantity_delta
            FROM inventory_movements_statistics
            INNER JOIN inventory_movements
             ON inventory_movements.inventory_id = inventory_movements_statistics.inventory_id
             AND inventory_movements.type = inventory_movements_statistics.type
             AND inventory_movements.id >= inventory_movements_statistics.last'.$days.'days_min_movement_id
             AND inventory_movements.created_at < date_sub(now(), interval '.$days.' day)
            GROUP BY inventory_movements.type, inventory_movements.inventory_id
            LIMIT 200;
        '));

        $records->each(function ($record) use ($days) {
            DB::transaction(function () use ($record, $days) {
                DB::update('
                      UPDATE inventory_movements_statistics
                      SET
                         last'.$days.'days_quantity_delta = last'.$days.'days_quantity_delta - :sum_quantity_delta,
                         last'.$days.'days_min_movement_id = :new_min_movement_id
                      WHERE
                         inventory_id = :inventory_id
                         AND type = :type
                         AND last'.$days.'days_min_movement_id = :current_min_movement_id
                 ', [
                    'sum_quantity_delta' => $record->sum_quantity_delta,
                    'new_min_movement_id' => $record->last_movement_id + 1,
                    'inventory_id' => $record->inventory_id,
                    'type' => $record->type,
                    'current_min_movement_id' => $record->current_min_movement_id,
                ]);
            });
        });
    }
}
