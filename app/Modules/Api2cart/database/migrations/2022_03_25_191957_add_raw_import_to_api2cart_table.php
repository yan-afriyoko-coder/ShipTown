<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRawImportToApi2cartTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('modules_api2cart_product_links', function (Blueprint $table) {
            $table->json('raw_import')->after('api2cart_sale_price_end_date')->nullable();
        });
    }
}
