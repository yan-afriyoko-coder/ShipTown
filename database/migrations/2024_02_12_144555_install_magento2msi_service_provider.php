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
            $table->string('magento_source_code')->nullable();
            $table->unsignedBigInteger('inventory_source_warehouse_tag_id')->nullable();
            $table->longText('api_access_token');
            $table->timestamps();
        });

        \Illuminate\Support\Facades\Schema::create('modules_magento2msi_requests', function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('connection_id');
            $table->json('response')->nullable();
            $table->timestamps();
        });

        \Illuminate\Support\Facades\Schema::create('modules_magento2msi_inventory_source_items', function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->id();
            $table->foreignId('connection_id')->constrained('modules_magento2msi_connections')->cascadeOnDelete();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('inventory_totals_by_warehouse_tag_id')->nullable();
            $table->string('custom_uuid')->nullable()->unique();
            $table->string('sku')->nullable();
            $table->string('source_code')->nullable();
            $table->decimal('quantity', 20)->nullable();
            $table->boolean('status')->nullable();
            $table->timestamp('inventory_source_items_fetched_at')->nullable();
            $table->json('inventory_source_items')->nullable();
            $table->timestamps();

            $table->foreign('product_id')
                ->references('id')
                ->on('products');

            $table->foreign('inventory_totals_by_warehouse_tag_id', 'inventory_totals_by_warehouse_tag_id')
                ->references('id')
                ->on('inventory_totals_by_warehouse_tag');

        });

        Magento2MsiServiceProvider::installModule();
    }
};
