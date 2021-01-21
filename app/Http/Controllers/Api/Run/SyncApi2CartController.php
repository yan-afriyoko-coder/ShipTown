<?php

namespace App\Http\Controllers\Api\Run;

use App\Http\Controllers\Controller;
use App\Jobs\SyncProductsToApi2Cart;
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
        SyncProductsToApi2Cart::dispatch();
        return $this->respondOK200('Job dispatched');
    }
}
