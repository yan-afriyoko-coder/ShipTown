<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventory', function (Blueprint $table) {
            $table->timestamp('in_stock_since')->nullable()->after('last_counted_at');

            $table->index(['in_stock_since']);
        });
    }
};
