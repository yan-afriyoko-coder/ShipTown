<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateColumnTypesToUniformOnOrderProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_products', function (Blueprint $table) {
            $table->decimal('quantity_split', 10)->default(0)->change();
            $table->dropColumn('quantity_to_ship');
        });

        Schema::table('order_products', function (Blueprint $table) {
            $table->decimal('quantity_to_ship', 10)
                ->nullable(false)
                ->storedAs('quantity_ordered - quantity_split - quantity_shipped')
                ->after('quantity_shipped');
        });
    }
}

