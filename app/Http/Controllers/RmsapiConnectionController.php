<?php

namespace App\Http\Controllers;

use App\Models\RmsapiConnection;
use App\Http\Requests\StoreConfigurationRmsApiRequest;
use App\Http\Resources\RmsapiConnectionResource;

class RmsapiConnectionController extends Controller
{
    public function index()
    {
        return RmsapiConnectionResource::collection(RmsapiConnection::all());
    }

    public function store(StoreConfigurationRmsApiRequest $request)
    {
        $config = new RmsapiConnection();
        $config->fill($request->only($config->getFillable()));
        $config->save();

        return new RmsapiConnectionResource($config);
    }

    public function destroy(RmsapiConnection $rms_api_configuration)
    {
        $rms_api_configuration->delete();

        return response('ok');
    }
}
