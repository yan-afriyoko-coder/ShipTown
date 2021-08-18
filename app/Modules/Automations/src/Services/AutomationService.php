<?php

namespace App\Modules\Automations\src\Services;

use App\Modules\Automations\src\Models\Automation;
use App\Modules\Automations\src\Models\Condition;
use App\Modules\Automations\src\Models\Execution;
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
        $allConditionsPass = $automation->conditions()
            ->get()
            ->every(function (Condition $condition) use ($event) {
                return AutomationService::isConditionValid($condition, $event);
            });

        Log::debug('Ran automation', [
            'class' => class_basename($automation),
            'name' => $automation->name,
            'conditions_passed' => $allConditionsPass
        ]);

        if ($allConditionsPass === false) {
            return;
        }

        // run all executions
        $automation->executions()
            ->orderBy('priority')
            ->get()
            ->each(function (Execution $execution) use ($event) {
                AutomationService::executeAutomation($execution, $event);
            });
    }

    private static function isConditionValid(Condition $condition, $event): bool
    {
        $validator = new $condition->validation_class($event);

        return $validator->isValid($condition->condition_value);
    }

    private static function executeAutomation(Execution $execution, $event): void
    {
        $executor = new $execution->execution_class($event);

        $executor->handle($execution->execution_value);
    }
}
