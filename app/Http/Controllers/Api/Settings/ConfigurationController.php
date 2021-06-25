<?php

namespace App\Http\Controllers\Api\Settings;

use App\Http\Controllers\Controller;
use App\Models\Configuration;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class ConfigurationController.
 */
class ConfigurationController extends Controller
{
    /**
     * @param Request $request
     *
     * @return JsonResource
     */
    public function store(Request $request)
    {
        $config = Configuration::firstOrNew([
            'key' => $request->input('key'),
        ]);
        $config->value = $request->input('value');

        $config->save();

        return new JsonResource($config);
    }

    /**
     * @param Request $request
     * @param $key
     *
     * @return JsonResource
     */
    public function show(Request $request, $key)
    {
        $config = Configuration::where('key', $key)->firstOrFail();

        return new JsonResource($config);
    }
}
