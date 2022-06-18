<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderCheckRequest\StoreRequest;
use App\Modules\Automations\src\Jobs\RunEnabledAutomationsOnSpecificOrderJob;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderCheckRequestController extends Controller
{
    /**
     * @param StoreRequest $request
     * @return JsonResource
     */
    public function store(StoreRequest $request): JsonResource
    {
        RunEnabledAutomationsOnSpecificOrderJob::dispatch($request->validated()['order_id']);

        return JsonResource::make($request->validated());
    }
}
