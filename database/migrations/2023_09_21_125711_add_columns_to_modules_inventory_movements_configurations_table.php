<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('modules_inventory_movements_configurations', function (Blueprint $table) {
            $table->unsignedBigInteger('quantity_before_basic_job_last_movement_id_checked')->default(0)->after('quantity_before_job_last_movement_id_checked');
            $table->unsignedBigInteger('quantity_before_stocktake_job_last_movement_id_checked')->default(0)->after('quantity_before_basic_job_last_movement_id_checked');
        });
    }
};
