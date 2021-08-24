<?php

namespace App\Http\Controllers\Api\Settings;

use App\Http\Controllers\Controller;
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
        ray($request->validated());
        if ($request->filterKeys) {
            $configurations->whereIn('key', $request->filterKeys);
        }

        $configurations = $configurations->get();
        return ConfigurationResource::collection($configurations->keyBy->key);
    }

    /**
     * Update bulk resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function bulkUpdate(Request $request)
    {
        //
    }
}
