<?php

namespace App\Http\Controllers\Api\Settings\Module\Automation;

use App\Http\Controllers\Controller;
use App\Http\Requests\Automations\StoreRequest;
use App\Http\Requests\Automations\UpdateRequest;
use App\Http\Resources\AutomationResource;
use App\Modules\Automations\src\Models\Automation;
use Illuminate\Http\Request;

class AutomationController extends Controller
{

    /**
     * Display a listing of avaliable config automation.
     *
     * @return \Illuminate\Http\Response
     */
    public function getConfig()
    {
        $configs = config('automations');

        return $configs;
    }

    /**
     * Display a listing of the resource.
     *
     * @return AutomationResource
     */
    public function index()
    {
        $automations = Automation::all();

        return AutomationResource::collection($automations);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreRequest  $request
     * @return AutomationResource
     */
    public function store(StoreRequest $request)
    {
        $dataAutomation = $request->only(['name', 'event_class', 'enabled', 'prioriry']);

        $automation = Automation::create($dataAutomation);
        $automation->conditions()->createMany($request->conditions);
        $automation->executions()->createMany($request->executions);

        return new AutomationResource($automation);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Modules\Automations\src\Models\Automation  $automation
     * @return AutomationResource
     */
    public function show(Automation $automation)
    {
        return new AutomationResource($automation);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateRequest  $request
     * @param  \App\Modules\Automations\src\Models\Automation  $automation
     * @return AutomationResource
     */
    public function update(UpdateRequest $request, Automation $automation)
    {
        $automation->fill($request->validated());
        $automation->save();

        return new AutomationResource($automation);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Modules\Automations\src\Models\Automation  $automation
     * @return \Illuminate\Http\Response
     */
    public function destroy(Automation $automation)
    {
        $automation->delete();

        return true;
    }
}
