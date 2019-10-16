<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RecreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('products');

        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->string('sku', 20);
            $table->string('name', 100);
            $table->decimal('price', 8,2)->default(0);
            $table->decimal('sale_price', 8,2)->default(0);
            $table->date('sale_price_start_date')->default('1899-01-01');
            $table->date('sale_price_end_date')->default('1899-01-01');
            $table->decimal('quantity')->default(0);
            $table->decimal('quantity_reserved')->default(0);
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
