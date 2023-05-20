<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateAccessTokenColumnTypeOnModuleMagento2apiConnectionsTable extends Migration
{
    public function up(): void
    {
        Schema::table('modules_magento2api_connections', function (Blueprint $table) {
            $table->longText('access_token_encrypted')->nullable()->change();
        });
    }
}
