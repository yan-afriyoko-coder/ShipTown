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
    public function index(ModuleIndexRequest $request): AnonymousResourceCollection
    {
        $modules = Module::query()->get();

        $collection = $modules->sortBy('name');

        return ModuleResource::collection($collection);
    }

    public function update(ModuleUpdateRequest $request, int $module_id): ModuleResource
    {
        $module = Module::query()->findOrFail($module_id);

        $module->update($request->validated());

        return ModuleResource::make($module->refresh());
    }
}
