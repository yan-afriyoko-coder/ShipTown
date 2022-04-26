<?php

namespace App\Http\Controllers\Api\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\ModuleIndexRequest;
use App\Http\Requests\ModuleUpdateRequest;
use App\Http\Resources\ModuleResource;
use App\Models\Module;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ModuleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param ModuleIndexRequest $request
     *
     * @return AnonymousResourceCollection
     */
    public function index(ModuleIndexRequest $request): AnonymousResourceCollection
    {
        $modules = Module::query()->get();

        return ModuleResource::collection($modules);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ModuleUpdateRequest $request
     * @param Module $module
     *
     * @return ModuleResource
     */
    public function update(ModuleUpdateRequest $request, Module $module): ModuleResource
    {
        $module->update($request->validated());

        return ModuleResource::make($module);
    }
}
