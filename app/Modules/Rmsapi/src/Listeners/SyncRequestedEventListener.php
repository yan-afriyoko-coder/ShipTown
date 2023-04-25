<?php

namespace App\Modules\Rmsapi\src\Listeners;

use App\Modules\Rmsapi\src\Jobs\ImportProductsJob;
use App\Modules\Rmsapi\src\Jobs\ImportSalesJob;
use App\Modules\Rmsapi\src\Jobs\ImportShippingsJob;
use App\Modules\Rmsapi\src\Jobs\ProcessImportedProductRecordsJob;
use App\Modules\Rmsapi\src\Jobs\ProcessImportedSalesRecordsJob;
use App\Modules\Rmsapi\src\Jobs\UpdateImportedSalesRecordsJob;
use App\Modules\Rmsapi\src\Models\RmsapiConnection;
use Exception;
use Illuminate\Support\Facades\Log;

class SyncRequestedEventListener
{
    /**
     * @throws Exception
     */
    public function handle()
    {
        foreach (RmsapiConnection::all() as $rmsapiConnection) {
            ImportSalesJob::dispatch($rmsapiConnection->id);
            ImportShippingsJob::dispatch($rmsapiConnection->id);
            ImportProductsJob::dispatch($rmsapiConnection->id);

            Log::debug('RMSAPI Sync jobs dispatched', [
                'warehouse_code' => $rmsapiConnection->location_id,
                'connection_id' => $rmsapiConnection->id
            ]);
        }

        UpdateImportedSalesRecordsJob::dispatch();
        ProcessImportedProductRecordsJob::dispatch();
        ProcessImportedSalesRecordsJob::dispatch();
    }
}
