<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOrderNumberToApi2cartOrderImportsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('api2cart_order_imports', function (Blueprint $table) {
            $table->string('order_number')
                ->nullable(true)
                ->after('when_processed');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('api2cart_order_imports', function (Blueprint $table) {
            $table->dropColumn('order_number');
        });
    }
}
