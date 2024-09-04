<?php

namespace App\Modules\OrderTotals\src\Jobs;

use App\Abstracts\UniqueJob;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class EnsureCorrectTotalsJob extends UniqueJob
{
    private Carbon $fromDateTime;

    private Carbon $toDateTime;

    public function __construct($fromDateTime = null, $toDateTime = null)
    {
        $this->fromDateTime = $fromDateTime ?? now()->subHour();
        $this->toDateTime = $toDateTime ?? now();
    }

    public function handle()
    {
        Schema::dropIfExists('tempTable');
        DB::statement('
            CREATE TEMPORARY TABLE tempTable AS
                SELECT
                        order_id,
                        count(orders_products.id) as count_expected,
                        sum(orders_products.quantity_ordered) as quantity_ordered_expected,
                        sum(orders_products.quantity_split) as quantity_split_expected,
                        sum(orders_products.quantity_picked) as quantity_picked_expected,
                        sum(orders_products.quantity_skipped_picking) as quantity_skipped_picking_expected,
                        sum(orders_products.quantity_not_picked) as quantity_not_picked_expected,
                        sum(orders_products.quantity_shipped) as quantity_shipped_expected,
                        sum(orders_products.quantity_to_pick) as quantity_to_pick_expected,
                        sum(orders_products.quantity_to_ship) as quantity_to_ship_expected,
                        sum(orders_products.total_price) as total_price_expected,

                        max(orders_products.updated_at) as max_updated_at_expected
                FROM orders

                INNER JOIN orders_products ON orders_products.order_id = orders.id

                WHERE orders.order_placed_at BETWEEN ? AND ?

                GROUP BY order_id;
        ', [$this->fromDateTime, $this->toDateTime]);

        DB::update('
            UPDATE orders_products_totals

            LEFT JOIN tempTable AS recalculations
                ON recalculations.order_id = orders_products_totals.order_id

            SET
                orders_products_totals.count                    = recalculations.count_expected,
                orders_products_totals.quantity_ordered         = recalculations.quantity_ordered_expected,
                orders_products_totals.quantity_split           = recalculations.quantity_split_expected,
                orders_products_totals.quantity_picked          = recalculations.quantity_picked_expected,
                orders_products_totals.quantity_skipped_picking = recalculations.quantity_skipped_picking_expected,
                orders_products_totals.quantity_not_picked      = recalculations.quantity_not_picked_expected,
                orders_products_totals.quantity_shipped         = recalculations.quantity_shipped_expected,
                orders_products_totals.quantity_to_pick         = recalculations.quantity_to_pick_expected,
                orders_products_totals.quantity_to_ship         = recalculations.quantity_to_ship_expected,
                orders_products_totals.total_price              = recalculations.total_price_expected,
                orders_products_totals.max_updated_at           = recalculations.max_updated_at_expected

            WHERE
                orders_products_totals.count                       != recalculations.count_expected
                OR orders_products_totals.quantity_ordered         != recalculations.quantity_ordered_expected
                OR orders_products_totals.quantity_split           != recalculations.quantity_split_expected
                OR orders_products_totals.quantity_picked          != recalculations.quantity_picked_expected
                OR orders_products_totals.quantity_skipped_picking != recalculations.quantity_skipped_picking_expected
                OR orders_products_totals.quantity_not_picked      != recalculations.quantity_not_picked_expected
                OR orders_products_totals.quantity_shipped         != recalculations.quantity_shipped_expected
                OR orders_products_totals.quantity_to_pick         != recalculations.quantity_to_pick_expected
                OR orders_products_totals.quantity_to_ship         != recalculations.quantity_to_ship_expected
                OR orders_products_totals.total_price              != recalculations.total_price_expected
                OR orders_products_totals.max_updated_at           != recalculations.max_updated_at_expected
        ');

        DB::update('
            UPDATE orders

            INNER JOIN tempTable AS recalculations
                ON recalculations.order_id = orders.id

            SET
                orders.product_line_count = recalculations.count_expected,
                orders.total_products = recalculations.total_price_expected,
                orders.updated_at  = now()

            WHERE
                recalculations.count_expected != orders.product_line_count
                OR recalculations.total_price_expected != orders.total_products
        ');
    }
}
