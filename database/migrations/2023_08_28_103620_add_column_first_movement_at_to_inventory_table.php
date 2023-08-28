<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('inventory', 'first_movement_at')) {
            return;
        }

        Schema::table('inventory', function (Blueprint $table) {
            $table->timestamp('first_movement_at')->nullable()->after('restock_level');
        });
    }
};
