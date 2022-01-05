<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddComment2ToOrderProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_products', function (Blueprint $table) {
            $table->dropColumn('quantity_to_pick');
        });

        Schema::table('order_products', function (Blueprint $table) {
            $table->decimal('quantity_to_pick', 10)
                ->nullable(false)
                ->storedAs('quantity_ordered - quantity_split - quantity_picked - quantity_skipped_picking')
                ->after('quantity_shipped')
                ->comment('quantity_ordered - quantity_split - quantity_picked - quantity_skipped_picking');
        });
    }
}
