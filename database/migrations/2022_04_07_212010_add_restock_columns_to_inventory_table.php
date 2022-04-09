<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRestockColumnsToInventoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasColumn('inventory', 'reorder_point')) {
            Schema::table('inventory', function (Blueprint $table) {
                $table->decimal('reorder_point', 10)->default(0)->after('quantity_reserved');
                $table->decimal('restock_level', 10)->default(0)->after('reorder_point');
            });
        }
    }
}
