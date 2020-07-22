<?php

namespace App\Observers\Modules\Api2cart;

use App\Jobs\Api2cart\ProcessApi2cartImportedOrderJob;
use App\Modules\Api2cart\src\Models\Api2cartOrderImports;

class Api2cartOrderImportsObserver
{
    /**
     * Handle the api2cart order imports "created" event.
     *
     * @param  Api2cartOrderImports  $api2cartOrderImports
     * @return void
     */
    public function created(Api2cartOrderImports $api2cartOrderImports)
    {
        ProcessApi2cartImportedOrderJob::dispatch($api2cartOrderImports);
    }

    /**
     * Handle the api2cart order imports "updated" event.
     *
     * @param  Api2cartOrderImports  $api2cartOrderImports
     * @return void
     */
    public function updated(Api2cartOrderImports $api2cartOrderImports)
    {
        //
    }

    /**
     * Handle the api2cart order imports "deleted" event.
     *
     * @param  Api2cartOrderImports  $api2cartOrderImports
     * @return void
     */
    public function deleted(Api2cartOrderImports $api2cartOrderImports)
    {
        //
    }

    /**
     * Handle the api2cart order imports "restored" event.
     *
     * @param  Api2cartOrderImports  $api2cartOrderImports
     * @return void
     */
    public function restored(Api2cartOrderImports $api2cartOrderImports)
    {
        //
    }

    /**
     * Handle the api2cart order imports "force deleted" event.
     *
     * @param  Api2cartOrderImports  $api2cartOrderImports
     * @return void
     */
    public function forceDeleted(Api2cartOrderImports $api2cartOrderImports)
    {
        //
    }
}
