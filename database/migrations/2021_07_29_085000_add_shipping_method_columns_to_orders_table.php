<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddShippingMethodColumnsToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('shipping_method_code')
                ->after('total_paid')
                ->default('')
                ->nullable(true);
            $table->string('shipping_method_name')
                ->after('shipping_method_code')
                ->default('')
                ->nullable(true);
        });
    }
}
