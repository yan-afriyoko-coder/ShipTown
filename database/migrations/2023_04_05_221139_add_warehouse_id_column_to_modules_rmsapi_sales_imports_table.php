<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('modules_rmsapi_sales_imports', 'warehouse_id')) {
            return;
        }

        Schema::table('modules_rmsapi_sales_imports', function (Blueprint $table) {
            $table->foreignId('warehouse_id')->nullable()->after('connection_id');
        });
    }
};
