<?php

namespace App\Jobs\Rmsapi;

use App\Jobs\ChainedJobWrapper;
use App\Models\RmsapiConnection;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SyncRmsapiConnectionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $rmsapiConnection = null;

    /**
     * Create a new job instance.
     *
     * @param RmsapiConnection $rmsapiConnection
     */
    public function __construct(RmsapiConnection $rmsapiConnection)
    {
        $this->rmsapiConnection = $rmsapiConnection;
        logger('Job Rmsapi\SyncRmsapiConnectionJob dispatched');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        ImportProductsJob::dispatch($this->rmsapiConnection);
//        ProcessImportedProductsJob::dispatch();
    }
}
