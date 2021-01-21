<?php

namespace App\Http\Controllers\Api\Run;

use App\Http\Controllers\Controller;
use App\Jobs\SyncProductsToApi2cart_MG;
use Illuminate\Http\Request;

/**
 * Class tempController
 * @package App\Http\Controllers\Api\Run
 */
class tempController extends Controller
{
    /**
     * @param Request $request
     * @return string
     */
    public function index(Request $request): string
    {
        SyncProductsToApi2cart_MG::dispatch();
        return 'Jobs Dispatched';
    }
}
