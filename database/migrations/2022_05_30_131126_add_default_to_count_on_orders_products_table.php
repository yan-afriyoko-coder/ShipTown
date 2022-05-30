<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDefaultToCountOnOrdersProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('orders_products_totals', 'quantity_to_pick')) {
            Schema::table('orders_products_totals', function (Blueprint $table) {
                $table->dropColumn('quantity_to_pick');
                $table->dropColumn('quantity_to_ship');
            });
        }

        Schema::table('orders_products_totals', function (Blueprint $table) {
            $table->integer('count')->default(0)->change();

            $table->decimal('quantity_to_pick', 20)->default(0);
            $table->decimal('quantity_to_ship', 20)->default(0);
            $table->timestamp('max_updated_at')->default('2000-01-01 00:00:00');
        });
    }
}
