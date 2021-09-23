<?php

namespace App\Modules\Automations\src\Services;

use App\Modules\Automations\src\Models\Automation;
use App\Modules\Automations\src\Models\Condition;
use App\Modules\Automations\src\Models\Action;
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
                AutomationService::runAutomation($automation, $event);
            });
    }

    public static function runAutomation(Automation $automation, $event)
    {
        // check all conditions
        $allConditionsTrue = $automation->allConditionsTrue($event);

        Log::debug('Ran automation', [
            'class' => class_basename($automation),
            'name' => $automation->name,
            'conditions_passed' => $allConditionsTrue
        ]);

        if ($allConditionsTrue === false) {
            return;
        }

        // run all actions
        $automation->actions()
            ->orderBy('priority')
            ->get()
            ->each(function (Action $action) use ($event) {
                AutomationService::runAction($action, $event);
            });
    }

    private static function runAction(Action $action, $event): void
    {
        $runAction = new $action->action_class($event);

        $runAction->handle($action->action_value);
    }
}
