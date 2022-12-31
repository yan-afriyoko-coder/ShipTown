<?php

namespace App\Http\Controllers\Api\Modules\OrderAutomations;

use App\Http\Controllers\Controller;

class ConfigController extends Controller
{
    public function index()
    {
        return config('automations');
    }
}
