<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('modules_magento2msi_inventory_source_items', function (Blueprint $table) {
            $table->unsignedBigInteger('magento_product_id')->nullable()->after('inventory_totals_by_warehouse_tag_id');
        });
    }
};
