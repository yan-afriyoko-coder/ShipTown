<?php

namespace App\Modules\Rmsapi\src\Listeners;

use App\Events\SyncRequestedEvent;
use App\Modules\Rmsapi\src\Models\RmsapiConnection;
use App\Modules\Rmsapi\src\Jobs\FetchUpdatedProductsJob;

class SyncRequestedEventListener
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
        // dispatch Fetch jobs for all connections
        foreach (RmsapiConnection::all() as $rmsapiConnection) {
            FetchUpdatedProductsJob::dispatch($rmsapiConnection->id);
            logger('Rmsapi sync job dispatched', ['connection_id' => $rmsapiConnection->id]);
        }
    }
}
