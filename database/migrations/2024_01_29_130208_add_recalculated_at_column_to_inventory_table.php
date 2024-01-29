<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventory', function (Blueprint $table) {
            $table->timestamp('recalculated_at')->nullable()->after('shelve_location');

            $table->index(['recalculated_at']);
        });
    }
};
