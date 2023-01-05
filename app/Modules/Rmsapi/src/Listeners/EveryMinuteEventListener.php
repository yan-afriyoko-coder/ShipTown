<?php

namespace App\Modules\Rmsapi\src\Listeners;

use App\Modules\Rmsapi\src\Jobs\ImportProductsJob;
use App\Modules\Rmsapi\src\Jobs\ImportShippingsJob;
use App\Modules\Rmsapi\src\Jobs\ProcessImportedProductRecordsJob;
use App\Modules\Rmsapi\src\Models\RmsapiConnection;
use App\Modules\Rmsapi\src\Models\RmsapiProductImport;
use Illuminate\Support\Facades\Log;

class EveryMinuteEventListener
{
    public function handle()
    {
        foreach (RmsapiConnection::all() as $rmsapiConnection) {
            ImportProductsJob::dispatch($rmsapiConnection->id);
            ImportShippingsJob::dispatch($rmsapiConnection->id);
            ProcessImportedProductRecordsJob::dispatch($rmsapiConnection->id);

            Log::debug('RMSAPI Sync jobs dispatched', [
                'warehouse_code' => $rmsapiConnection->location_id,
                'connection_id' => $rmsapiConnection->id
            ]);
        }
    }
}
