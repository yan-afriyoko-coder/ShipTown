<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPricingColumnsToModulesMagento2apiProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('modules_magento2api_products', function (Blueprint $table) {
            $table->decimal('magento_price', 10)->after('product_id')->nullable();
            $table->decimal('magento_sale_price', 10)->after('magento_price')->nullable();
            $table->timestamp('magento_sale_price_start_date')->after('magento_sale_price')->nullable();
            $table->timestamp('magento_sale_price_end_date')->after('magento_sale_price_start_date')->nullable();

            $table->timestamp('base_prices_fetched_at')->nullable()->after('stock_items_raw_import');
            $table->json('base_prices_raw_import')->nullable()->after('base_prices_fetched_at');

            $table->timestamp('special_prices_fetched_at')->nullable()->after('base_prices_raw_import');
            $table->json('special_prices_raw_import')->nullable()->after('special_prices_fetched_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('modules_magento2api_products', function (Blueprint $table) {
            //
        });
    }
}
