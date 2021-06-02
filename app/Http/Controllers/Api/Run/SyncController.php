<?php

namespace App\Http\Controllers\Api\Run;

use App\Events\SyncRequestedEvent;
use App\Http\Controllers\Controller;
use App\Models\RmsapiConnection;
use App\Modules\Api2cart\src\Jobs\DispatchImportOrdersJobs;
use App\Modules\Rmsapi\src\Jobs\FetchUpdatedProductsJob;
use Illuminate\Support\Facades\Request;

/**
 * Class SyncController
 * @package App\Http\Controllers
 */
class SyncController extends Controller
{
    /**
     * @param Request $request
     */
    public function index(Request $request)
    {
        logger('Dispatching sync jobs');

        SyncRequestedEvent::dispatch();

        // import RMSAPI products
        foreach (RmsapiConnection::all() as $rmsapiConnection) {
            FetchUpdatedProductsJob::dispatch($rmsapiConnection->id);
            logger('Rmsapi sync job dispatched', ['connection_id' => $rmsapiConnection->id]);
        }

        info('Sync jobs dispatched');

        $this->respondOK200('Sync jobs dispatched');
    }
}
