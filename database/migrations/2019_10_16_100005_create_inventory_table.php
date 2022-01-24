<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('inventory')) {
            return;
        }

        Schema::create('inventory', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('warehouse_id')->nullable();
            $table->unsignedBigInteger('product_id')->index();
            $table->string('location_id')->default('');
            $table->string('shelve_location')->default('');
            $table->decimal('quantity', 10, 2)->default(0);
            $table->decimal('quantity_reserved', 10, 2)->default(0);
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->onDelete('cascade');
        });

        Schema::table('inventory', function (Blueprint $table) {
            $table->foreign('warehouse_id')
                ->on('warehouses')
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
        Schema::dropIfExists('inventory');
    }
}
