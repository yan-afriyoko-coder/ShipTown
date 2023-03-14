<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTimingColumnsToModulesQueueMonitorJobsTable extends Migration
{
    public function up()
    {
        Schema::table('modules_queue_monitor_jobs', function (Blueprint $table) {
            $table->unsignedInteger('seconds_dispatching')
                ->nullable()
                ->storedAs('TIMESTAMPDIFF(SECOND, dispatched_at, processing_at)')
                ->comment('TIMESTAMPDIFF(SECOND, dispatched_at, processing_at)')
                ->after('processed_at');

            $table->unsignedInteger('seconds_running')
                ->nullable()
                ->storedAs('TIMESTAMPDIFF(SECOND, processing_at, processed_at)')
                ->comment('TIMESTAMPDIFF(SECOND, processing_at, processed_at)')
                ->after('processed_at');
        });
    }
}
