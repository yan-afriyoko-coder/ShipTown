<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddQuantityReservedColumnToOrderProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('order_products', 'quantity_reserved')) {
            Schema::table('order_products', function (Blueprint $table) {
                $table->decimal('quantity_reserved')
                    ->default(0)
                    ->nullable(false)
                    ->after('quantity_ordered');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
