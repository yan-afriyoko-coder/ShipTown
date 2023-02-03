<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateModulesQueueMonitorJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('modules_queue_monitor_jobs', function (Blueprint $table) {
            $table->uuid('uuid')->unique();
            $table->string('job_class')->index();
            $table->timestamp('dispatched_at')->default(DB::raw('CURRENT_TIMESTAMP'))->index();
        });
    }
}
