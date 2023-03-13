<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSecondsRunningCalculatedColumnToModulesQueueMonitorJobsTable extends Migration
{
    public function up()
    {
        Schema::table('modules_queue_monitor_jobs', function (Blueprint $table) {
            $table->unsignedInteger('seconds_running')
                ->nullable()
                ->storedAs('UNIX_TIMESTAMP(processed_at) - UNIX_TIMESTAMP(processing_at)')
                ->comment('UNIX_TIMESTAMP(processed_at) - UNIX_TIMESTAMP(processing_at)')
                ->index();
        });
    }
}
