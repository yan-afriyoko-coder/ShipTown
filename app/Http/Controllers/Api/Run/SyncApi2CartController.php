<?php

namespace App\Http\Controllers\Api\Run;

use App\Http\Controllers\Controller;
use App\Modules\Api2cart\src\Jobs\SyncProductsJob;
use Illuminate\Http\Request;

/**
 * Class SyncApi2CartController
 * @package App\Http\Controllers\Api\Run
 */
class SyncApi2CartController extends Controller
{
    /**
     * @param Request $request
     */
    public function index(Request $request)
    {
        SyncProductsJob::dispatch();

        $this->respondOK200('Job dispatched');
    }
}
