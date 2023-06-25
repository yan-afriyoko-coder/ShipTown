<?php

namespace App\Modules\InventoryMovementsStatistics\src\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

/**
 *
 */
class EnsureCorrectLastSoldAtJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        DB::statement("
            UPDATE inventory_movements_statistics

            LEFT JOIN inventory
              ON inventory.id = inventory_movements_statistics.inventory_id
              AND inventory.last_sold_at > DATE_SUB(now(), INTERVAL 28 DAY)

            SET inventory_movements_statistics.last_sold_at = inventory.last_sold_at

            WHERE inventory.id IS NOT NULL
              AND IFNULL(inventory_movements_statistics.last_sold_at, 0) <> IFNULL(inventory.last_sold_at, 0)
        ");
    }
}
