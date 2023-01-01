<?php

namespace App\Http\Controllers\Api\Modules\OrderAutomations;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderAutomationConfigIndexRequest;

class ConfigController extends Controller
{
    public function index(OrderAutomationConfigIndexRequest $request)
    {
        return config('automations');
    }
}
