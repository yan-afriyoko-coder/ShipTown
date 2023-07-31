<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventory_movements_statistics', function (Blueprint $table) {
            $table->dropForeign(['inventory_id']);
            $table->dropUnique(['inventory_id']);

            $table->dropForeign(['warehouse_id']);
            $table->dropIndex(['warehouse_id']);

            $table->dropColumn('warehouse_id');
            $table->dropColumn('quantity_sold_last_7_days');
            $table->dropColumn('quantity_sold_last_14_days');
            $table->dropColumn('quantity_sold_last_28_days');
            $table->dropColumn('quantity_sold_this_week');
            $table->dropColumn('quantity_sold_last_week');
            $table->dropColumn('quantity_sold_2weeks_ago');
            $table->dropColumn('quantity_sold_3weeks_ago');
            $table->dropColumn('quantity_sold_4weeks_ago');
            $table->dropColumn('last_inventory_movement_id');
        });

        Schema::table('inventory_movements_statistics', function (Blueprint $table) {
            $table->string('type')->nullable()->after('id');

            $table->double('last7days_quantity_delta', 10, 2)->default(0)->after('warehouse_code');
            $table->double('last14days_quantity_delta', 10, 2)->default(0)->after('last7days_quantity_delta');
            $table->double('last28days_quantity_delta', 10, 2)->default(0)->after('last14days_quantity_delta');

            $table->unsignedBigInteger('last7days_min_movement_id')->nullable()->after('last28days_quantity_delta');
            $table->unsignedBigInteger('last7days_max_movement_id')->nullable()->after('last7days_min_movement_id');
            $table->unsignedBigInteger('last14days_min_movement_id')->nullable()->after('last7days_max_movement_id');
            $table->unsignedBigInteger('last14days_max_movement_id')->nullable()->after('last14days_min_movement_id');
            $table->unsignedBigInteger('last28days_min_movement_id')->nullable()->after('last14days_max_movement_id');
            $table->unsignedBigInteger('last28days_max_movement_id')->nullable()->after('last28days_min_movement_id');

            $table->foreign('inventory_id')
                ->references('id')
                ->on('inventory')
                ->cascadeOnDelete();

            $table->unique(['type', 'inventory_id']);
        });
    }
};
