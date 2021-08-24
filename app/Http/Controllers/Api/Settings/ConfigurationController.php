<?php

namespace App\Http\Controllers\Api\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Configuration\BulkUpdateRequest;
use App\Http\Requests\Configuration\IndexRequest;
use App\Http\Resources\ConfigurationResource;
use App\Models\Configuration;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ConfigurationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param IndexRequest $request
     * @return ConfigurationResource
     */
    public function index(IndexRequest $request)
    {
        $configurations = Configuration::query();
        if ($request->filterKeys) {
            $configurations->whereIn('key', $request->filterKeys);
        }

        $configurations = $configurations->get();
        return ConfigurationResource::collection($configurations->keyBy->key);
    }

    /**
     * Update bulk resource in storage.
     *
     * @param BulkUpdateRequest $request
     * @return \Illuminate\Http\Response
     */
    public function bulkUpdate(BulkUpdateRequest $request)
    {
        $configs = $request->configs;

        foreach ($configs as $configKey => $configValue) {
            $config = Configuration::where('key', $configKey)->first();
            if ($config) {
                $config->value = $configValue;
                $config->save();
            }
        }

        return true;
    }
}
