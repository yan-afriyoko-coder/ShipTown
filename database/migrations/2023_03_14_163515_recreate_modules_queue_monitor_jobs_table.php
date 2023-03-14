<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RecreateModulesQueueMonitorJobsTable extends Migration
{
    public function up()
    {
        Schema::drop('modules_queue_monitor_jobs');

        Schema::create('modules_queue_monitor_jobs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->nullable()->unique();
            $table->string('job_class')->index();
            $table->timestamp('dispatched_at')->default(DB::raw('CURRENT_TIMESTAMP'))->index();
            $table->timestamp('processing_at')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->unsignedInteger('seconds_dispatching')
                ->storedAs('TIMESTAMPDIFF(SECOND, dispatched_at, processing_at)')
                ->comment('TIMESTAMPDIFF(SECOND, dispatched_at, processing_at)');

            $table->unsignedInteger('seconds_running')
                ->storedAs('TIMESTAMPDIFF(SECOND, processing_at, processed_at)')
                ->comment('TIMESTAMPDIFF(SECOND, processing_at, processed_at)');
        });
    }
}
