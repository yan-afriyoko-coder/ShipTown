<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateOrderTotalsViewQuantityOrderedSumColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("
            CREATE OR REPLACE VIEW orders_totals_view AS
            SELECT
                orders.id as order_id,
                COUNT(orders_products.id) as product_line_count,
                IFNULL(SUM(quantity_ordered - quantity_split), 0) as quantity_ordered_sum,
                IFNULL(SUM(quantity_to_ship), 0) as quantity_to_ship_sum,
                IFNULL(SUM(price * (quantity_ordered - quantity_split)), 0) as total_ordered

            FROM orders
            LEFT JOIN orders_products ON orders.id = orders_products.order_id
            GROUP BY orders.id
        ");
    }
}
