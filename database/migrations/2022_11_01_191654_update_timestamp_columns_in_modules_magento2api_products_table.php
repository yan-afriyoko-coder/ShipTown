<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTimestampColumnsInModulesMagento2apiProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('modules_magento2api_products', function (Blueprint $table) {
            $table->dateTime('magento_sale_price_start_date')->nullable()->change();
            $table->dateTime('magento_sale_price_end_date')->nullable()->change();
        });
    }
}
