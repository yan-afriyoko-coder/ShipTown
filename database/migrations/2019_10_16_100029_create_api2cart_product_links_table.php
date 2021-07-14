<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApi2cartProductLinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('api2cart_product_links')) {
            return;
        }

        Schema::create('api2cart_product_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->nullable();
            $table->string('api2cart_connection_id')->nullable();
            $table->string('api2cart_product_type')->nullable();
            $table->string('api2cart_product_id')->nullable();
            $table->dateTime('last_fetched_at')->nullable();
            $table->json('last_fetched_data')->nullable();
            $table->decimal('api2cart_quantity', 10, 2)->nullable();
            $table->decimal('api2cart_price', 10, 2)->nullable();
            $table->decimal('api2cart_sale_price', 10, 2)->nullable();
            $table->date('api2cart_sale_price_start_date')->nullable();
            $table->date('api2cart_sale_price_end_date')->nullable();
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
        Schema::dropIfExists('api2cart_product_links');
    }
}
