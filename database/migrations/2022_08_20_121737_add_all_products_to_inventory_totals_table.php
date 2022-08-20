<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAllProductsToInventoryTotalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Illuminate\Support\Facades\DB::statement('
            INSERT INTO inventory_totals (product_id, quantity, quantity_reserved, quantity_incoming)
            SELECT product_id, SUM(quantity), SUM(quantity_reserved), SUM(quantity_incoming)
            FROM inventory
            GROUP BY product_id
            ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inventory_totals', function (Blueprint $table) {
            //
        });
    }
}
