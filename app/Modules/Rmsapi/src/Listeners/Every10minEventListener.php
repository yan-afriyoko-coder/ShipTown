<?php

namespace App\Modules\Rmsapi\src\Listeners;

use App\Modules\Rmsapi\src\Jobs\ImportProductsJob;
use App\Modules\Rmsapi\src\Jobs\ImportSalesJob;
use App\Modules\Rmsapi\src\Jobs\ImportShippingsJob;
use App\Modules\Rmsapi\src\Jobs\ProcessImportedProductRecordsJob;
use App\Modules\Rmsapi\src\Jobs\ProcessImportedSalesRecordsJob;
use App\Modules\Rmsapi\src\Models\RmsapiConnection;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;

class Every10minEventListener
{
    /**
     * @throws \Exception
     */
    public function handle()
    {
        foreach (RmsapiConnection::all() as $rmsapiConnection) {
            Bus::chain([
                new ImportProductsJob($rmsapiConnection->id),
                new ProcessImportedProductRecordsJob($rmsapiConnection->id),
            ])->dispatch();
        }
    }
}
