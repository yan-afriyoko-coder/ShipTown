<?php

namespace App\Modules\Automations\src\Services;

use App\Modules\Automations\src\Models\Action;
use App\Modules\Automations\src\Models\Automation;
use Log;

class AutomationService
{
    public static function runAllAutomations($event)
    {
        Automation::where('event_class', get_class($event))
            ->where(['enabled' => true])
            ->orderBy('priority')
            ->get()
            ->each(function (Automation $automation) use ($event) {
                AutomationService::validateAndRunAutomation($automation, $event);
            });
    }

    public static function validateAndRunAutomation(Automation $automation, $event)
    {
        $allConditionsPassed = $automation->allConditionsTrue($event);

        if ($allConditionsPassed === true) {
            $automation->actions()
                ->orderBy('priority')
                ->get()
                ->each(function (Action $action) use ($event) {
                    AutomationService::runAction($action, $event);
                });
        }

        Log::debug('Ran automation', [
            'class' => class_basename($automation),
            'name' => $automation->name,
            'all_conditions_passed' => $allConditionsPassed
        ]);
    }

    private static function runAction(Action $action, $event): void
    {
        $runAction = new $action->action_class($event);

        $runAction->handle($action->action_value);
    }
}
