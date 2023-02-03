<?php

use App\Modules\QueueMonitor\src\QueueMonitorServiceProvider;
use Illuminate\Database\Migrations\Migration;

class InstallQueueMonitorModule extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        QueueMonitorServiceProvider::installModule();
    }
}
