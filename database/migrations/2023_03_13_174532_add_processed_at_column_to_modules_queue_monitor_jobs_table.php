<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProcessedAtColumnToModulesQueueMonitorJobsTable extends Migration
{
    public function up()
    {
        Schema::table('modules_queue_monitor_jobs', function (Blueprint $table) {
            $table->timestamp('processed_at')->nullable()->after('processing_at');
        });
    }
}
