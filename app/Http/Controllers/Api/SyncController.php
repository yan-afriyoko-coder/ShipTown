<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RmsapiConnection;
use App\Modules\Api2cart\src\Jobs\DispatchImportOrdersJobs;
use App\Modules\Rmsapi\src\Jobs\FetchUpdatedProductsJob;

/**
 * Class SyncController
 * @package App\Http\Controllers
 */
class SyncController extends Controller
{
    /**
     *
     */
    public function index() {
        // import API2CART orders
        DispatchImportOrdersJobs::dispatch();

        // import RMSAPI products
        foreach (RmsapiConnection::all() as $rmsapiConnection) {
            FetchUpdatedProductsJob::dispatch($rmsapiConnection->id);
        }

        return $this->respond_OK_200('Sync jobs dispatched');
    }
}
