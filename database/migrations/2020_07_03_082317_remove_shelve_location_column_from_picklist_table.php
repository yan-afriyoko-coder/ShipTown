<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveShelveLocationColumnFromPicklistTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('picklists', function (Blueprint $table) {
            $table->dropColumn('shelve_location');
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
            $table->string('shelve_location')
                ->after('location_id')
                ->default('');
        });
    }
}
