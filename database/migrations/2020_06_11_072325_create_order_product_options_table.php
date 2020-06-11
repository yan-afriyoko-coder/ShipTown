<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderProductOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_product_options', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedInteger('order_product_id');
            $table->string('option_id');
            $table->string('name');
            $table->string('value')->nullable();
            $table->decimal('price', 15, 2)->default('0.00');
            $table->unsignedInteger('weight')->default(0);
            $table->string('type')->nullable();
            $table->string('product_option_value_id')->nullable();
            $table->json('additional_fields')->nullable();
            $table->json('custom_fields')->nullable();

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
        Schema::dropIfExists('order_product_options');
    }
}
