<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('modules_magento2msi_inventory_source_items')->delete();

        Schema::table('modules_magento2msi_inventory_source_items', function (Blueprint $table) {
            $table->string('sku', 255)->nullable(false)->change();

            $table->unique(['connection_id', 'sku'], 'unique_connection_id_sku');
            $table->index('connection_id');
            $table->index('product_id');
            $table->index('inventory_totals_by_warehouse_tag_id', 'inventory_totals_by_warehouse_tag_id_index');
            $table->index('source_assigned', 'source_assigned_index');
            $table->index('sync_required', 'sync_required_index');
            $table->index('magento_product_id', 'magento_product_id_index');
            $table->index('custom_uuid', 'custom_uuid_index');

            $table->foreign('sku')->references('sku')->on('products')->cascadeOnUpdate();
        });
    }
};
