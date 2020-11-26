<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddQuantityOutstandingColumnToOrderProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('order_products', 'quantity_outstanding')) {
            return;
        }

        Schema::table('order_products', function (Blueprint $table) {
            $table->decimal('quantity_outstanding', 10, 2)->default(0)
                ->after('quantity_ordered');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
