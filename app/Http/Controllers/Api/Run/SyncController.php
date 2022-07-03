<?php

namespace App\Http\Controllers\Api\Run;

use App\Events\Every10minEvent;
use App\Events\SyncRequestedEvent;
use App\Http\Controllers\Controller;
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
        logger('Dispatching sync jobs');

        SyncRequestedEvent::dispatch();

        Every10minEvent::dispatch();

        info('Sync jobs dispatched');

        $this->respondOK200('Sync jobs dispatched');
    }
}
