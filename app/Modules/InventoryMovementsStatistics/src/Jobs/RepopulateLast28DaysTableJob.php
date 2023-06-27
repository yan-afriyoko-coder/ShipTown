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
class RepopulateLast28DaysTableJob implements ShouldQueue
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
        DB::table('modules_inventory_movements_statistics_last28days_sale_movements')->truncate();

        DB::statement('
            INSERT INTO modules_inventory_movements_statistics_last28days_sale_movements (
                inventory_movement_id, sold_at, inventory_id, quantity_sold
            )
            SELECT
                inventory_movements.id as inventory_movement_id,
                inventory_movements.created_at as sold_at,
                inventory_movements.inventory_id,
                inventory_movements.quantity_delta * -1 as quantity_sold
            FROM `inventory_movements`
            WHERE inventory_movements.type = "sale"
            AND inventory_movements.created_at >= DATE_SUB(NOW(), INTERVAL 28 DAY)
        ');
    }
}
