<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexesToInventoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inventory', function (Blueprint $table) {
            $table->index('product_id');
            $table->index('shelve_location');
            $table->index('quantity_available');
            $table->index('quantity');
            $table->index('quantity_reserved');
            $table->index('quantity_incoming');
            $table->index('quantity_required');
            $table->index('restock_level');
            $table->index('reorder_point');
        });
    }
}
