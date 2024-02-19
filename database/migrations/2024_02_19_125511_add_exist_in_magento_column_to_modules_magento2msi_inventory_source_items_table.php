<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('modules_magento2msi_inventory_source_items', 'exist_in_magento')) {
            Schema::table('modules_magento2msi_inventory_source_items', function (Blueprint $table) {
                $table->boolean('exist_in_magento')->nullable()->after('inventory_totals_by_warehouse_tag_id');

            });
        }

        Schema::table('modules_magento2msi_inventory_source_items', function (Blueprint $table) {
            $table->index(['exist_in_magento'], 'exist_in_magento_index');
        });

    }
};
