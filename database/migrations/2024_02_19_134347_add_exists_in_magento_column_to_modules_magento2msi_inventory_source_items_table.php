<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropColumns('magento_column_to_modules_magento2msi_inventory_source_items', ['exist_in_magento']);

        Schema::table('modules_magento2msi_inventory_source_items', function (Blueprint $table) {
            $table->boolean('exists_in_magento')->nullable()->after('inventory_totals_by_warehouse_tag_id');
            $table->index(['exists_in_magento'], 'exists_in_magento_index');
        });
    }
};
