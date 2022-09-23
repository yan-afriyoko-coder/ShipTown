<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropSyncFirstFailedAtColumnFromModulesApi2cartProductLinksTable extends Migration
{
    public function up()
    {
        Schema::table('modules_api2cart_product_links', function (Blueprint $table) {
            $table->dropColumn('sync_first_failed_at');
        });
    }
}
