<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPacklistTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('packlists', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->bigInteger('order_id')->unsigned();
            $table->bigInteger('order_product_id')->unsigned();
            $table->bigInteger('product_id')->unsigned();
            $table->string('location_id');
            $table->string('sku_ordered')->default('');
            $table->string('name_ordered')->default('');
            $table->decimal('quantity_requested')->default(0);
            $table->decimal('quantity_packed')->default(0);
            $table->bigInteger('packer_user_id')->unsigned()->nullable(true);
            $table->timestamp('packed_at')->nullable(true);
            $table->softDeletes();

            $table->foreign('order_id')
                ->on('orders')
                ->references('id')
                ->onDelete('cascade');

            $table->foreign('order_product_id')
                ->on('order_products')
                ->references('id')
                ->onDelete('cascade');

            $table->foreign('product_id')
                ->on('products')
                ->references('id')
                ->onDelete('cascade');

            $table->foreign('packer_user_id')
                ->on('users')
                ->references('id')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('packlist');
    }
}
