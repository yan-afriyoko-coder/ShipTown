<?php

namespace App\Http\Controllers;

use App\Models\ConfigurationRmsApi;
use App\Http\Requests\StoreConfigurationRmsApiRequest;
use App\Http\Resources\ConfigurationRmsApiResource;

class ConfigurationRmsApiController extends Controller
{
    public function index()
    {
        return ConfigurationRmsApiResource::collection(ConfigurationRmsApi::all());
    }

    public function store(StoreConfigurationRmsApiRequest $request)
    {
        $config = new ConfigurationRmsApi();
        $config->fill($request->only($config->getFillable()));
        $config->save();

        return new ConfigurationRmsApiResource($config);
    }

    public function destroy(ConfigurationRmsApi $rms_api_configuration)
    {
        $rms_api_configuration->delete();

        return response('ok');
    }
}
