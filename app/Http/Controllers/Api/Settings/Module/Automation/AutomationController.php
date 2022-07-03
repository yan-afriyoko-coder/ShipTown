<?php

namespace App\Http\Controllers\Api\Settings\Module\Automation;

use App\Http\Controllers\Controller;
use App\Http\Requests\Automations\StoreRequest;
use App\Http\Requests\Automations\UpdateRequest;
use App\Http\Resources\AutomationResource;
use App\Modules\Automations\src\Models\Automation;
use App\Modules\Automations\src\Models\Condition;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Throwable;

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

        ray($configs);

        return $configs;
    }

    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        $automations = Automation::query()
            ->orderBy('priority')
            ->get();

        return AutomationResource::collection($automations);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest $request
     * @return AutomationResource
     * @throws Throwable
     */
    public function store(StoreRequest $request)
    {
        try {
            DB::beginTransaction();

            $dataAutomation = $request->only(['name', 'event_class', 'enabled', 'priority', 'description']);
            $automation = Automation::create($dataAutomation);

            $conditions = collect($request->conditions)
                ->filter(function ($condition) {
                    return $condition['condition_class'] != "";
                });

            $actions = collect($request->actions)
                ->filter(function ($action) {
                    return $action['action_class'] != "";
                });

            if (count($conditions)) {
                $automation->conditions()->createMany($request->conditions);
            }

            if (count($actions)) {
                $automation->actions()->createMany($request->actions);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            ray($e);
            abort(500, "Something wrong");
        }

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
        $automation->load('conditions', 'actions');

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
        try {
            DB::beginTransaction();
            $dataAutomation = $request->only(['name', 'event_class', 'enabled', 'priority', 'description']);
            $automation->update($dataAutomation);

            // Delete current data
            $automation->conditions()->delete();
            $automation->actions()->delete();

            // Insert new
            $automation->conditions()->createMany($request->conditions);
            $automation->actions()->createMany($request->actions);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            abort(500, "Something wrong");
        }

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
