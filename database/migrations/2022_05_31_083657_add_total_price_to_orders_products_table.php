<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTotalPriceToOrdersProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders_products', function (Blueprint $table) {
            $table->decimal('total_price', 20)->after('quantity_split')
                ->storedAs('(quantity_ordered - quantity_split) * price')
                ->comment('(quantity_ordered - quantity_split) * price');
        });
    }
}
