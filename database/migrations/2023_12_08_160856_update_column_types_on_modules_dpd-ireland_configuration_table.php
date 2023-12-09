<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('modules_dpd-ireland_configuration', 'modules_dpd_ireland_configuration');

        Schema::table('modules_dpd_ireland_configuration', function (Blueprint $table) {
            $table->longText('token')->change();
            $table->longText('user')->change();
            $table->longText('password')->change();
        });
    }
};
