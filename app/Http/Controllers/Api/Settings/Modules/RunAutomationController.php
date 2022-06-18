<?php

namespace App\Http\Controllers\Api\Settings\Modules;

use App\Http\Controllers\Controller;
use App\Http\Requests\RunAutomationRequest;
use App\Models\CacheLock;
use App\Modules\Automations\src\Jobs\RunAutomationJob;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 *
 */
class RunAutomationController extends Controller
{
    /**
     * @param RunAutomationRequest $request
     * @return JsonResource
     */
    public function store(RunAutomationRequest $request): JsonResource
    {
        RunAutomationJob::dispatch($request->validated()['automation_id']);

        return JsonResource::make([
            'job_requested' => true,
            'automation_id' => $request->validated()['automation_id'],
            'time' => Carbon::now(),
        ]);
    }
}
