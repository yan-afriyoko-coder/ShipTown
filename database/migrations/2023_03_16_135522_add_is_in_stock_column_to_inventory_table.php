<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('inventory', function (Blueprint $table) {
            $table->boolean('is_in_stock')
                ->storedAs('quantity_available > 0')
                ->comment('quantity_available > 0');

            $table->index('is_in_stock');
        });
    }
};
