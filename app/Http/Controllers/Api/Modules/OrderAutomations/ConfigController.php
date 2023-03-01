<?php

namespace App\Http\Controllers\Api\Modules\OrderAutomations;

use App\Http\Controllers\Controller;
use App\Modules\Automations\src\Http\Requests\AutomationConfigIndexRequest;

class ConfigController extends Controller
{
    public function index(AutomationConfigIndexRequest $request)
    {
        return config('automations');
    }
}
