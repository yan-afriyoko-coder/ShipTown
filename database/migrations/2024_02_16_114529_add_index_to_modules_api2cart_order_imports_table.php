<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('modules_api2cart_order_imports', function (Blueprint $table) {
            $table->index(['connection_id', 'api2cart_order_id', 'when_processed'], 'connection_id_api2cart_order_id_when_processed_index');
        });
    }
};
