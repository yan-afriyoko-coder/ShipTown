<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('order_products')) {
            return;
        }

        Schema::create('order_products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->string('sku_ordered');
            $table->string('name_ordered');
            $table->decimal('price', 10, 2)->default(0);
            $table->decimal('quantity_ordered', 10, 2)->default(0);
            $table->decimal('quantity_split')->default(0);
            $table->decimal('quantity_outstanding', 10, 2)->default(0);
            $table->decimal('quantity_shipped', 10, 2)->default(0);
            $table->decimal('quantity_to_ship')->storedAs('quantity_ordered - quantity_split - quantity_shipped');
            $table->decimal('quantity_to_pick', 10, 2)->default(0);
            $table->decimal('quantity_picked', 10, 2)->default(0);
            $table->decimal('quantity_skipped_picking', 10, 2)->default(0);
            $table->decimal('quantity_not_picked', 10, 2)->default(0);
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('product_id')
                ->on('products')
                ->references('id')
                ->onDelete('SET NULL');

            $table->foreign('order_id')
                ->on('orders')
                ->references('id')
                ->onDelete('cascade');
        });

        if (Schema::hasColumn('order_products', 'quantity_outstanding')) {
            Schema::table('order_products', function (Blueprint $table) {
                $table->dropColumn('quantity_outstanding');
            });
        }

        if (!Schema::hasColumn('order_products', 'quantity_reserved')) {
            Schema::table('order_products', function (Blueprint $table) {
                $table->decimal('quantity_reserved')
                    ->default(0)
                    ->nullable(false)
                    ->after('quantity_ordered');
            });
        }

        if (Schema::hasColumn('order_products', 'quantity_reserved')) {
            Schema::table('order_products', function (Blueprint $table) {
                $table->dropColumn('quantity_reserved');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_products');
    }
}
