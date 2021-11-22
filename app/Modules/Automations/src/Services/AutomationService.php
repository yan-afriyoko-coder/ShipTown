<?php

namespace App\Modules\Automations\src\Services;

use App\Events\Order\ActiveOrderCheckEvent;
use App\Modules\Automations\src\Models\Action;
use App\Modules\Automations\src\Models\Automation;
use Log;

/**
 *
 */
class AutomationService
{
    /**
     * @param ActiveOrderCheckEvent $event
     */
    public static function runAllAutomations(ActiveOrderCheckEvent $event)
    {
        Automation::where('event_class', get_class($event))
            ->where(['enabled' => true])
            ->orderBy('priority')
            ->get()
            ->each(function (Automation $automation) use ($event) {
                AutomationService::validateAndRunAutomation($automation, $event);
            });
    }

    /**
     * @param Automation $automation
     * @param ActiveOrderCheckEvent $event
     */
    public static function validateAndRunAutomation(Automation $automation, ActiveOrderCheckEvent $event)
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

    /**
     * @param Action $action
     * @param ActiveOrderCheckEvent $event
     */
    private static function runAction(Action $action, ActiveOrderCheckEvent $event): void
    {
        $runAction = new $action->action_class($event);

        $runAction->handle($action->action_value);
    }
}
