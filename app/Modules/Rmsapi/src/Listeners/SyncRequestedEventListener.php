<?php

namespace App\Modules\Rmsapi\src\Listeners;

use App\Events\SyncRequestedEvent;
use App\Modules\Rmsapi\src\Jobs\ImportShippingsJob;
use App\Modules\Rmsapi\src\Jobs\ImportProductsJob;
use App\Modules\Rmsapi\src\Jobs\ProcessImportedProductRecordsJob;
use App\Modules\Rmsapi\src\Models\RmsapiConnection;

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
            ImportProductsJob::dispatch($rmsapiConnection->id);
            ImportShippingsJob::dispatch($rmsapiConnection->id);
            logger('Rmsapi sync job dispatched', ['connection_id' => $rmsapiConnection->id]);
        }

        ProcessImportedProductRecordsJob::dispatch();
    }
}
