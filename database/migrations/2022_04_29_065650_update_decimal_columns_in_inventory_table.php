<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateDecimalColumnsInInventoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {


        if (Schema::hasColumn('inventory', 'quantity_available')) {
            Schema::table('inventory', function (Blueprint $table) {
                $table->dropColumn('quantity_available');
            });
        }

        Schema::table('inventory', function (Blueprint $table) {
            $table->decimal('quantity_available', 20)
                ->storedAs('quantity - quantity_reserved')
                ->comment('quantity - quantity_reserved')
                ->after('shelve_location');
        });

        if (Schema::hasColumn('inventory', 'quantity_required')) {
            Schema::table('inventory', function (Blueprint $table) {
                $table->dropColumn('quantity_required');
            });
        }

        Schema::table('inventory', function (Blueprint $table) {
            $table->decimal('quantity_required', 20)
                ->storedAs('CASE WHEN (quantity - quantity_reserved) < reorder_point ' .
                    'THEN restock_level - (quantity - quantity_reserved) ' .
                    'ELSE 0 END')
                ->comment('CASE WHEN (quantity - quantity_reserved) < reorder_point ' .
                    'THEN restock_level - (quantity - quantity_reserved) ' .
                    'ELSE 0 END')
                ->after('quantity_reserved');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->decimal('quantity', 20)->default(0)->change();
            $table->decimal('quantity_reserved', 20)->default(0)->change();
            $table->decimal('quantity_available', 20)->default(0)->change();
        });
    }
}
