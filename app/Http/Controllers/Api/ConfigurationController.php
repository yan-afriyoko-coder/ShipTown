<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use App\Models\Configuration;

class ConfigurationController extends Controller
{
    public function store(Request $request)
    {
        $config = Configuration::firstOrNew([
            'key' => $request->input('key')
        ]);
        $config->value = $request->input('value');

        $config->save();

        return new JsonResource($config);
    }

    public function show(Request $request, $key)
    {
        $config = Configuration::where('key', $key)->firstOrFail();

        return new JsonResource($config);
    }
}
