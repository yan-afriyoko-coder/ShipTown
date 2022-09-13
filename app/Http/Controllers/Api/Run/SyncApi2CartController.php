<?php

namespace App\Http\Controllers\Api\Run;

use App\Http\Controllers\Controller;
use App\Modules\Api2cart\src\Jobs\SyncProductsJob;
use App\Modules\Api2cart\src\Jobs\UpdateMissingTypeAndIdJob;

/**
 * Class SyncApi2CartController.
 */
class SyncApi2CartController extends Controller
{
    public function index()
    {
        UpdateMissingTypeAndIdJob::dispatch();
        SyncProductsJob::dispatch();

        $this->respondOK200('Job dispatched');
    }
}
