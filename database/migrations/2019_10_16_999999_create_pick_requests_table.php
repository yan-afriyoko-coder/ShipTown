<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePickRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('pick_requests')) {
            return;
        }

        Schema::create('pick_requests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('order_product_id');
            $table->decimal('quantity_required');
            $table->decimal('quantity_picked')->default(0);
            $table->unsignedBigInteger('pick_id')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table('pick_requests', function (Blueprint $table) {
            $table->bigInteger('order_id')
                ->unsigned()
                ->nullable(true)
                ->after('id');

            $table->foreign('order_id')
                ->references('id')
                ->on('orders')
                ->onDelete('SET NULL');
        });

        Schema::table('pick_requests', function (Blueprint $table) {
            $table->foreign('order_product_id')
                ->references('id')
                ->on('order_products')
                ->onDelete('CASCADE');
        });

        Schema::table('pick_requests', function (Blueprint $table) {
            $table->foreign('pick_id')
                ->references('id')
                ->on('picks')
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
        Schema::dropIfExists('pick_requests');
    }
}
