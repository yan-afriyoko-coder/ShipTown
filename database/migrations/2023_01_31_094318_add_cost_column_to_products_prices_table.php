<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCostColumnToProductsPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products_prices', function (Blueprint $table) {
            $table->decimal('cost', 20, 2)->default(0)->after('warehouse_code');
        });
    }
}
