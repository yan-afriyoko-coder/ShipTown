<?php

namespace App\Http\Controllers\Api\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Configuration\UpdateRequest;
use App\Http\Resources\ConfigurationResource;
use App\Models\Configuration;

class ConfigurationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return ConfigurationResource
     */
    public function index(): ConfigurationResource
    {
        $configuration = Configuration::first();

        return new ConfigurationResource($configuration);
    }

    /**
     * Update bulk resource in storage.
     *
     * @param UpdateRequest $request
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request)
    {
        $configuration = Configuration::first();
        $configuration->update($request->validated());

        return new ConfigurationResource($configuration);
    }
}
