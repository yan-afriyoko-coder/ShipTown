<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            CREATE OR REPLACE VIEW modules_inventory_movements_statistics_view AS
            SELECT inventory_movements.inventory_id,
                sum(case when inventory_movements.created_at > date_sub(now(), interval 28 day) then -quantity_delta else 0 end) as expected_quantity_sold_last_28_days,
                sum(case when inventory_movements.created_at > date_sub(now(), interval 14 day) then -quantity_delta else 0 end) as expected_quantity_sold_last_14_days,
                sum(case when inventory_movements.created_at > date_sub(now(), interval 7 day) then -quantity_delta else 0 end) as expected_quantity_sold_last_7_days,
                max(inventory_movements_statistics.quantity_sold_last_28_days) as actual_quantity_sold_last_28_days,
                max(inventory_movements_statistics.quantity_sold_last_14_days) as actual_quantity_sold_last_14_days,
                max(inventory_movements_statistics.quantity_sold_last_7_days) as actual_quantity_sold_last_7_days
            FROM inventory_movements_statistics
            LEFT JOIN inventory_movements
              ON inventory_movements_statistics.inventory_id = inventory_movements.inventory_id
            WHERE inventory_movements.created_at > DATE_SUB(now(), INTERVAL 28 DAY)
              AND inventory_movements.type = 'sale'
            GROUP BY inventory_movements.inventory_id
        ");
    }
};
