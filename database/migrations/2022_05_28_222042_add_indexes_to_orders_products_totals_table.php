<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexesToOrdersProductsTotalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders_products_totals', function (Blueprint $table) {
            $table->foreign('order_id')
                ->references('id')
                ->on('orders')
                ->onDelete('CASCADE');

            $table->index('count');
            $table->index('quantity_ordered');
            $table->index('quantity_split');
            $table->index('quantity_picked');
            $table->index('quantity_skipped_picking');
            $table->index('quantity_not_picked');
            $table->index('quantity_shipped');
            $table->index('quantity_to_pick');
            $table->index('quantity_to_ship');
            $table->index('updated_at');
        });
    }
}
