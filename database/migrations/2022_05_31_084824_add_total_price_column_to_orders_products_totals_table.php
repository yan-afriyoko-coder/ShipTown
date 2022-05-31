<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTotalPriceColumnToOrdersProductsTotalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders_products_totals', function (Blueprint $table) {
            $table->decimal('total_price', 20)
                ->after('quantity_split')
                ->default(0);
        });
    }
}
