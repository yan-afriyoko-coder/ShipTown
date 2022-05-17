<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddQuantityIncomingToQuantityRequiredCalculationsOnInventoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inventory', function (Blueprint $table) {
            $table->dropColumn('quantity_required');
        });

        Schema::table('inventory', function (Blueprint $table) {
            $table->decimal('quantity_required', 20)
                ->storedAs('CASE WHEN (quantity - quantity_reserved + quantity_incoming) BETWEEN 0 AND reorder_point ' .
                    'THEN restock_level - (quantity - quantity_reserved + quantity_incoming)' .
                    'ELSE 0 END')
                ->comment('CASE WHEN (quantity - quantity_reserved + quantity_incoming) BETWEEN 0 AND reorder_point ' .
                    'THEN restock_level - (quantity - quantity_reserved + quantity_incoming)' .
                    'ELSE 0 END')
                ->after('quantity_incoming');
        });
    }
}
