<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropColumns('inventory', ['recount_requested']);

        Schema::table('inventory', function (Blueprint $table) {
            $table->boolean('recount_requested')->default(false)->after('shelve_location');

            $table->index('recount_requested');
        });
    }
};
