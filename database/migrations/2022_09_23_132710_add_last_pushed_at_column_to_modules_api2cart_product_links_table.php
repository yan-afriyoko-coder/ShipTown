<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLastPushedAtColumnToModulesApi2cartProductLinksTable extends Migration
{
    public function up()
    {
        Schema::table('modules_api2cart_product_links', function (Blueprint $table) {
            $table->timestamp('last_pushed_at')->nullable()->after('api2cart_product_id');
            $table->timestamp('last_pushed_response')->nullable()->after('last_pushed_at');
        });
    }
}
