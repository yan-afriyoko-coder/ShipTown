<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateSaleDatesDefaultValuesOnProductsPricesTable extends Migration
{
    public function up()
    {
        Schema::table('products_prices', function (Blueprint $table) {
            $table->date('sale_price_start_date')->default('2000-01-01')->change();
            $table->date('sale_price_end_date')->default('2000-01-01')->change();
        });
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
