<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPickerUserIdToPicklistsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('picklists', function (Blueprint $table) {
            $table->bigInteger('picker_user_id')
                ->unsigned()
                ->nullable(true)
                ->after('quantity_picked');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('picklists', function (Blueprint $table) {
            $table->dropColumn('picker_user_id');
        });
    }
}
