<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserConfiguration;
use Illuminate\Http\Request;

class UserConfigurationController extends Controller
{
    public function show()
    {
        $config = UserConfiguration::query()->firstOrCreate([], ["config" => "{}"]);

        $config["config"] = json_decode($config["config"]);

        return response($config, 200);
    }

    public function store(Request $request)
    {
        $data = [
            "config" => $request->getContent()
    ];

        $config = UserConfiguration::query()->updateOrCreate([], $data);

        $config["config"] = json_decode($config["config"]);

        return response($config, 200);
    }
}
