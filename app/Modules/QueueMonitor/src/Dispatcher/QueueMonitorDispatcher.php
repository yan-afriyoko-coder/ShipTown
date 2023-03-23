<?php

namespace App\Modules\QueueMonitor\src\Dispatcher;

use Exception;
use Illuminate\Bus\Dispatcher;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Guid\Guid;

class QueueMonitorDispatcher extends Dispatcher
{
    public function __construct($app, $dispatcher)
    {
        parent::__construct($app, $dispatcher->queueResolver);
    }

    public function dispatchToQueue($command)
    {
        try {
            DB::table('modules_queue_monitor_jobs')->insert([
                'uuid' => null,
                'job_class' => get_class($command),
                'dispatched_at' => now(),
            ]);
        } catch (Exception $e) {
            report($e);
        }

        return parent::dispatchToQueue($command);
    }
}
