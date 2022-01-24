<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateOrderStatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('
            CREATE OR REPLACE VIEW '. DB::getTablePrefix() .'order_stats AS
            SELECT
                id as order_id,
                DATEDIFF(date(now()), date(order_placed_at)) as age_in_days
            FROM '.DB::getTablePrefix().'orders
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('DROP VIEW IF EXISTS '.DB::getTablePrefix().'order_stats');
    }
}
