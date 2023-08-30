<?php

namespace App\Modules\OrderTotals\src\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

/**
 *
 */
class EnsureCorrectTotalsJob implements ShouldQueue
{
    use Dispatchable,
        InteractsWithQueue,
        Queueable,
        SerializesModels;

    /**
     * @var Builder
     */
    private Builder $recalculationsTempTable;

    /**
     * @var string
     */
    private string $recalculations_temp_table_name;

    /**
     *
     */
    public function __construct()
    {
        $this->recalculations_temp_table_name = 'recalculations_' . rand(10000000, 99999999);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        DB::statement('
            CREATE TEMPORARY TABLE temp_order_totals_recalculation
            AS (
                SELECT
                        order_id,
                        count(id) as count_expected,
                        sum(quantity_ordered) as quantity_ordered_expected,
                        sum(quantity_split) as quantity_split_expected,
                        sum(total_price) as total_price_expected,
                        sum(quantity_picked) as quantity_picked_expected,
                        sum(quantity_skipped_picking) as quantity_skipped_picking_expected,
                        sum(quantity_not_picked) as quantity_not_picked_expected,
                        sum(quantity_shipped) as quantity_shipped_expected,
                        sum(quantity_to_pick) as quantity_to_pick_expected,
                        sum(quantity_to_ship) as quantity_to_ship_expected,

                        max(updated_at) as max_updated_at_expected
                FROM orders_products
                GROUP BY order_id
            );

            UPDATE orders_products_totals
            LEFT JOIN temp_order_totals_recalculation AS recalculations
                ON recalculations.order_id = orders_products_totals.order_id
            SET
                orders_products_totals.count                    = recalculations.count_expected,
                orders_products_totals.quantity_ordered         = recalculations.quantity_ordered_expected,
                orders_products_totals.quantity_split           = recalculations.quantity_split_expected,
                orders_products_totals.quantity_picked          = recalculations.quantity_picked_expected,
                orders_products_totals.total_price              = recalculations.total_price_expected,
                orders_products_totals.quantity_skipped_picking = recalculations.quantity_skipped_picking_expected,
                orders_products_totals.quantity_not_picked      = recalculations.quantity_not_picked_expected,
                orders_products_totals.quantity_shipped         = recalculations.quantity_shipped_expected,
                orders_products_totals.quantity_to_pick         = recalculations.quantity_to_pick_expected,
                orders_products_totals.quantity_to_ship         = recalculations.quantity_to_ship_expected,
                orders_products_totals.max_updated_at           = recalculations.max_updated_at_expected
            WHERE
                orders_products_totals.count                       != recalculations.count_expected
                OR orders_products_totals.quantity_ordered         != recalculations.quantity_ordered_expected
                OR orders_products_totals.quantity_split           != recalculations.quantity_split_expected
                OR orders_products_totals.total_price              != recalculations.total_price_expected
                OR orders_products_totals.quantity_picked          != recalculations.quantity_picked_expected
                OR orders_products_totals.quantity_skipped_picking != recalculations.quantity_skipped_picking_expected
                OR orders_products_totals.quantity_not_picked      != recalculations.quantity_not_picked_expected
                OR orders_products_totals.quantity_shipped         != recalculations.quantity_shipped_expected
                OR orders_products_totals.quantity_to_pick         != recalculations.quantity_to_pick_expected
                OR orders_products_totals.quantity_to_ship         != recalculations.quantity_to_ship_expected
                OR orders_products_totals.max_updated_at           != recalculations.max_updated_at_expected;


            UPDATE orders
            LEFT JOIN orders_products_totals
                ON orders_products_totals.order_id = orders.id
            SET total_products = orders_products_totals.total_price
            WHERE orders.total_products != orders_products_totals.total_price;

            UPDATE orders
            SET total = total_products + total_shipping
            WHERE total != total_products + total_shipping;
        ');
    }
}
