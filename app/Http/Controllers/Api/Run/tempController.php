<?php

namespace App\Http\Controllers\Api\Run;

use App\Http\Controllers\Controller;
use App\Jobs\SyncProductsToApi2Cart_MG;
use Illuminate\Http\Request;

class tempController extends Controller
{
    /**
     * @param Request $request
     * @return string
     */
    public function index(Request $request): string
    {
        SyncProductsToApi2Cart_MG::dispatch();
        return 'Jobs Dispatched';
    }
}
