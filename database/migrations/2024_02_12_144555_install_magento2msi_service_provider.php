<?php

use App\Modules\Magento2MSI\src\Magento2MsiServiceProvider;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        \Illuminate\Support\Facades\Schema::create('modules_magento2msi_connections', function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->id();
            $table->string('base_url');
            $table->string('api_access_token');
            $table->string('store_code');
            $table->unsignedBigInteger('inventory_source_warehouse_tag_id')->nullable();
            $table->timestamps();
        });

        \Illuminate\Support\Facades\Schema::create('modules_magento2msi_requests', function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('connection_id');
            $table->json('response')->nullable();
            $table->timestamps();
        });

        \Illuminate\Support\Facades\Schema::create('modules_magento2msi_products', function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->id();
            $table->foreignId('connection_id')->constrained('modules_magento2api_connections')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->unsignedBigInteger('inventory_totals_by_warehouse_tag_id')->nullable();
            $table->string('magento_sku')->nullable();
            $table->decimal('magento_price', 20)->nullable();
            $table->decimal('magento_sale_price', 20)->nullable();
            $table->dateTime('magento_sale_price_start_date')->nullable();
            $table->dateTime('magento_sale_price_end_date')->nullable();
            $table->decimal('magento_quantity', 20)->nullable();
            $table->boolean('exists_in_magento')->nullable();
            $table->boolean('is_inventory_in_sync')->nullable();
            $table->boolean('is_in_stock')->nullable();
            $table->timestamp('stock_items_fetched_at')->nullable();
            $table->json('stock_items_raw_import')->nullable();
            $table->timestamp('base_prices_fetched_at')->nullable();
            $table->json('base_prices_raw_import')->nullable();
            $table->timestamp('special_prices_fetched_at')->nullable();
            $table->json('special_prices_raw_import')->nullable();
            $table->timestamps();

            $table->foreign('inventory_totals_by_warehouse_tag_id', 'inventory_totals_by_warehouse_tag_id')
                ->references('id')
                ->on('inventory_totals_by_warehouse_tag');

        });

        Magento2MsiServiceProvider::installModule();
    }
};
