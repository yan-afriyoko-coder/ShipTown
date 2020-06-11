<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddProductsLastTimestampColumnToRmsapiConnectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rmsapi_connections', function (Blueprint $table) {
            $table->unsignedBigInteger('products_last_timestamp')
                ->default(0)
                ->after('password');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rmsapi_connections', function (Blueprint $table) {
            $table->dropColumn('products_last_timestamp');
        });
    }
}
