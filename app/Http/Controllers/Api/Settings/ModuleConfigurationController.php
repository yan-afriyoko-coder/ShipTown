<?php

namespace App\Http\Controllers\Api\Settings;

use App\Http\Controllers\Controller;
use App\Http\Resources\ModuleResource;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ModuleConfigurationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     *
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $modules = Module::all();

        return ModuleResource::collection($modules);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Module  $module
     *
     * @return ModuleResource
     */
    public function update(Request $request, Module $module): ModuleResource
    {
        $module->enabled = !$module->enabled;
        $module->save();

        return ModuleResource::make($module);
    }
}
