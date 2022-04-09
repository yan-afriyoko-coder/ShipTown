<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuantityRequiredColumnOnInventoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inventory', function (Blueprint $table) {
            $table->decimal('quantity_available', 10)
                ->storedAs('quantity - quantity_reserved')
                ->always()
                ->comment('quantity - quantity_reserved')
                ->after('shelve_location')
                ->change();
        });

        Schema::table('inventory', function (Blueprint $table) {
            $table->decimal('quantity_required', 10)
                ->storedAs('CASE WHEN quantity_available < reorder_point ' .
                    'THEN restock_level - quantity_available ' .
                    'ELSE 0 END')
                ->always()
                ->comment('CASE WHEN quantity_available < reorder_point ' .
                    'THEN restock_level - quantity_available ' .
                    'ELSE 0 END')
                ->after('restock_level');
        });
    }
}
