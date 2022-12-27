<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Configuration\StoreRequest;
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
     * Update resource in storage.
     *
     * @param StoreRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        $configuration = Configuration::first();
        $configuration->update($request->validated());

        return new ConfigurationResource($configuration);
    }
}
