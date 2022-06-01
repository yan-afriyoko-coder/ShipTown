<?php

namespace App\Http\Controllers\Api\Settings\Modules;

use App\Events\Order\ActiveOrderCheckEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\RunAutomationRequest;
use App\Models\Order;
use App\Modules\Automations\src\Models\Automation;
use App\Modules\Automations\src\Services\AutomationService;
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
        /** @var Automation $automation */
        $automation = Automation::findOrFail($request->get('automation_id'));

        AutomationService::run($automation, Order::query());

        return JsonResource::make([
            'automation_id' => $automation->getKey(),
            'time' => Carbon::now(),
        ]);
    }
}
