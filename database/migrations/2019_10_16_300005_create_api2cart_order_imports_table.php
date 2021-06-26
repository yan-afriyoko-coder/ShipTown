<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApi2cartOrderImportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('api2cart_order_imports')) {
            return;
        }

        Schema::create('api2cart_order_imports', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('connection_id')->nullable();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->dateTime('when_processed')->nullable();
            $table->string('order_number')->nullable();
            $table->json('raw_import');
            $table->timestamps();

            $table->foreign('order_id')
                ->references('id')
                ->on('orders')
                ->onDelete('SET NULL');

            $table->foreign('connection_id')
                ->references('id')
                ->on('api2cart_connections')
                ->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('api2cart_order_imports');
    }
}
