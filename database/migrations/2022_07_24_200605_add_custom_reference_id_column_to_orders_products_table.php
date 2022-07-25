<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCustomReferenceIdColumnToOrdersProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders_products', function (Blueprint $table) {
            $table->string('custom_unique_reference_id')->unique()->nullable()->after('id');
        });
    }
}
