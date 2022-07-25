<?php

namespace App\Http\Controllers\Api\Run;

use App\Events\Every10minEvent;
use App\Events\SyncRequestedEvent;
use App\Http\Controllers\Controller;
use App\Jobs\SyncRequestJob;
use Illuminate\Support\Facades\Request;

/**
 * Class SyncController.
 */
class SyncController extends Controller
{
    /**
     * @param Request $request
     */
    public function index(Request $request)
    {
        SyncRequestJob::dispatchAfterResponse();

        info('SyncRequestJob dispatched');

        $this->respondOK200('Sync requested successfully');
    }
}
