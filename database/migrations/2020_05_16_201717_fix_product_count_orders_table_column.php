<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixProductCountOrdersTableColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('product_count');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->double('products_count')
                ->default(0)
                ->after('order_number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('products_count');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->string('product_count')
                ->nullable(true)
                ->after('order_number');
        });
    }
}
