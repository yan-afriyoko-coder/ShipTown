<?php

namespace App\Http\Controllers\Api\Modules\Automation;

use App\Http\Controllers\Controller;
use App\Http\Requests\Automations\StoreRequest;
use App\Http\Requests\Automations\UpdateRequest;
use App\Http\Resources\AutomationResource;
use App\Modules\Automations\src\Models\Automation;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Throwable;

class AutomationController extends Controller
{
    public function getConfig()
    {
        return config('automations');
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
    public function store(StoreRequest $request): AutomationResource
    {
        $automation = new Automation();

        try {
            DB::beginTransaction();

            $automation->fill($request->only(['name', 'event_class', 'enabled', 'priority', 'description']));
            $automation->save();

            // filter out empty conditions and actions
            $conditions = collect($request->validated()['conditions'])
                ->filter(function (array $condition) {
                    return $condition['condition_class'] != "";
                });

            $actions = collect($request->validated()['actions'])
                ->filter(function (array $action) {
                    return $action['action_class'] != "";
                });

            $automation->conditions()->createMany($conditions);
            $automation->actions()->createMany($actions);

            DB::commit();

            return new AutomationResource($automation);
        } catch (\Exception $e) {
            DB::rollback();
            report($e);
            abort(500, "Something wrong");
        }

        return new AutomationResource($automation);
    }

    public function show(int $automation_id): AutomationResource
    {
        $automation = Automation::query()
            ->with(['conditions', 'actions'])
            ->findOrFail($automation_id);

        return AutomationResource::make($automation);
    }

    public function update(UpdateRequest $request, int $automation_id): AutomationResource
    {
        /** @var Automation $automation */
        $automation = Automation::query()->findOrFail($automation_id);

        try {
            DB::beginTransaction();
            $dataAutomation = $request->only(['name', 'event_class', 'enabled', 'priority', 'description']);
            $automation->update($dataAutomation);

            // Delete current data
            $automation->conditions()->delete();
            $automation->actions()->delete();

            // filter out empty conditions and actions
            $conditions = collect($request->validated()['conditions'])
                ->filter(function (array $condition) {
                    return $condition['condition_class'] != "";
                });

            $actions = collect($request->validated()['actions'])
                ->filter(function (array $action) {
                    return $action['action_class'] != "";
                });

            // create conditions and actions
            $automation->conditions()->createMany($conditions);
            $automation->actions()->createMany($actions);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            report($e);
            abort(500, "Something wrong");
        }

        return new AutomationResource($automation);
    }

    public function destroy(int $automation_id): AutomationResource
    {
        $automation = Automation::query()->findOrFail($automation_id);

        $automation->delete();

        return AutomationResource::make($automation);
    }
}
