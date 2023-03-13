<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProcessedAtColumnToModulesQueueMonitorJobsTable extends Migration
{
    public function up()
    {
        if (! Schema::hasColumn('modules_queue_monitor_jobs', 'processed_at')) {
            Schema::table('modules_queue_monitor_jobs', function (Blueprint $table) {
                $table->timestamp('processed_at')->nullable()->after('processing_at');
            });
        }
    }
}
