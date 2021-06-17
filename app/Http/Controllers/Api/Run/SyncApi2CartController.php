<?php

namespace App\Http\Controllers\Api\Run;

use App\Http\Controllers\Controller;
use App\Modules\Api2cart\src\Jobs\SyncProductsJob;

/**
 * Class SyncApi2CartController
 * @package App\Http\Controllers\Api\Run
 */
class SyncApi2CartController extends Controller
{
    /**
     *
     */
    public function index()
    {
        SyncProductsJob::dispatch();

        $this->respondOK200('Job dispatched');
    }
}
