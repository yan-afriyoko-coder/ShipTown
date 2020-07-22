<?php

namespace App\Http\Controllers;

use App\Modules\Api2cart\src\Jobs\DispatchImportOrdersJobs;
use App\Modules\Rmsapi\src\Jobs\ImportProductsJob;
use App\Models\RmsapiConnection;
use Illuminate\Http\Request;

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
            ImportProductsJob::dispatch($rmsapiConnection->id);
        }

        return $this->respond_OK_200('Sync jobs dispatched');
    }
}
