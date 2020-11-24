<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRmsapiConnectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('rmsapi_connections')) {
            return;
        }

        Schema::create('rmsapi_connections', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('location_id');
            $table->string('url');
            $table->string('username');
            $table->string('password');
            $table->unsignedBigInteger('products_last_timestamp')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('configuration_rms_apis');
    }
}
