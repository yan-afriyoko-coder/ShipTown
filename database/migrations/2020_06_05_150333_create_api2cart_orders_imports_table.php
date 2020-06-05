<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApi2cartOrdersImportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('api2cart_orders_imports', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->dateTime('when_processed')->nullable();
            $table->json('raw_import');
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
        Schema::dropIfExists('api2cart_orders_imports');
    }
}
