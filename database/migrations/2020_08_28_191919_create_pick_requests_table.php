<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePickRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pick_requests', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('order_product_id')->unsigned();
            $table->decimal('quantity_required');
            $table->decimal('quantity_picked')->default(0);
            $table->bigInteger('pick_id')->unsigned()->nullable(true);

            $table->softDeletes();
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
        Schema::dropIfExists('pick_requests');
    }
}
