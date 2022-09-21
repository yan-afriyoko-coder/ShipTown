<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSyncFirstFailedAtColumnToModulesApi2cartProductLinksTable extends Migration
{
    public function up()
    {
        Schema::table('modules_api2cart_product_links', function (Blueprint $table) {
            $table->timestamp('sync_first_failed_at')->nullable()->after('api2cart_product_id');
        });
    }
}
