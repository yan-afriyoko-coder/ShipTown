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
            $table->bigInteger('order_id')->unsigned()->nullable(true);
            $table->bigInteger('order_product_id')->unsigned()->nullable(true);
            $table->bigInteger('product_id')->unsigned()->nullable(true);
            $table->string('location_id');
            $table->string('sku_ordered')->default('');
            $table->string('name_ordered')->default('');
            $table->decimal('quantity_requested')->default(0);
            $table->decimal('quantity_packed')->default(0);
            $table->bigInteger('packer_user_id')->unsigned()->nullable(true);
            $table->timestamp('packed_at')->nullable(true);

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('order_id')
                ->on('orders')
                ->references('id')
                ->onDelete('SET NULL');

            $table->foreign('order_product_id')
                ->on('order_products')
                ->references('id')
                ->onDelete('SET NULL');

            $table->foreign('product_id')
                ->on('products')
                ->references('id')
                ->onDelete('SET NULL');

            $table->foreign('packer_user_id')
                ->on('users')
                ->references('id')
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
        Schema::dropIfExists('packlist');
    }
}
