<?php

namespace App\Modules\Rmsapi\src\Listeners\SyncRequestedEvent;

use App\Events\SyncRequestedEvent;
use App\Models\RmsapiConnection;
use App\Modules\Rmsapi\src\Jobs\FetchUpdatedProductsJob;

class DisptachSyncJobsListener
{
    /**
     * Handle the event.
     *
     * @param SyncRequestedEvent $event
     *
     * @return void
     */
    public function handle(SyncRequestedEvent $event)
    {
        foreach (RmsapiConnection::all() as $rmsapiConnection) {
            FetchUpdatedProductsJob::dispatch($rmsapiConnection->id);
            logger('Rmsapi sync job dispatched', ['connection_id' => $rmsapiConnection->id]);
        }
    }
}
