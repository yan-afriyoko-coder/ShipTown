<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropQuantityRequiredColumnFromPicksTable extends Migration
{
    public function up()
    {
        Schema::table('picks', function (Blueprint $table) {
            $table->dropColumn('quantity_required');
            $table->dropColumn('picker_user_id');
            $table->dropColumn('picked_at');
        });
    }

    public function down()
    {
        Schema::table('picks', function (Blueprint $table) {
            //
        });
    }
}
