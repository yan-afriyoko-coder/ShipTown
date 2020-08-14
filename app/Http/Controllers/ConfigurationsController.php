<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use App\Models\Configuration;

class ConfigurationsController extends Controller
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
