<?php

namespace App\Http\Controllers;

use App\Models\RmsApiConnection;
use App\Http\Requests\StoreConfigurationRmsApiRequest;
use App\Http\Resources\ConfigurationRmsApiResource;

class ConfigurationRmsApiController extends Controller
{
    public function index()
    {
        return ConfigurationRmsApiResource::collection(RmsApiConnection::all());
    }

    public function store(StoreConfigurationRmsApiRequest $request)
    {
        $config = new RmsApiConnection();
        $config->fill($request->only($config->getFillable()));
        $config->save();

        return new ConfigurationRmsApiResource($config);
    }

    public function destroy(RmsApiConnection $rms_api_configuration)
    {
        $rms_api_configuration->delete();

        return response('ok');
    }
}
