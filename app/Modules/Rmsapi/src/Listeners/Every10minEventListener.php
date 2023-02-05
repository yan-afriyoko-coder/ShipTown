<?php

namespace App\Modules\Rmsapi\src\Listeners;

use App\Modules\Rmsapi\src\Jobs\ImportProductsJob;
use App\Modules\Rmsapi\src\Jobs\ProcessImportedProductRecordsJob;
use App\Modules\Rmsapi\src\Jobs\RunWarehouseJobs;
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
        //
    }
}
