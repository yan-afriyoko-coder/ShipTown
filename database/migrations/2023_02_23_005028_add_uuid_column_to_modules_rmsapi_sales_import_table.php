<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('modules_rmsapi_sales_imports', function (Blueprint $table) {
            $table->string('uuid')->after('id')->nullable()->index();
            $table->string('type')->after('uuid')->nullable()->index();
        });
    }
};
