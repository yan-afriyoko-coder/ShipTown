<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCurrentlyRunningTaskColumnToDataCollectionsTable extends Migration
{
    public function up()
    {
        Schema::table('data_collections', function (Blueprint $table) {
            $table->string('currently_running_task')->nullable();
        });
    }
}
