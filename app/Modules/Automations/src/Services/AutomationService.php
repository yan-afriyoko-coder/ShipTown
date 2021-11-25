<?php

namespace App\Modules\Automations\src\Services;

use App\Events\Order\ActiveOrderCheckEvent;
use App\Modules\Automations\src\Models\Action;
use App\Modules\Automations\src\Models\Automation;
use App\Modules\Automations\src\Models\OrderLock;
use Exception;
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
        try {
            // this will prevent two automation processes running on same order
            OrderLock::where('created_at', '<', now()->subMinutes(10));

            /** @var OrderLock $lock */
            $lock = OrderLock::create(['order_id' => $event->order->getKey()]);
        } catch (Exception $exception) {
            // early exit, cannot lock order, automation is already running for it
            return;
        }

        Automation::where('event_class', get_class($event))
            ->where(['enabled' => true])
            ->orderBy('priority')
            ->get()
            ->each(function (Automation $automation) use ($event) {
                AutomationService::validateAndRunAutomation($automation, $event);
            });

        if ($lock) {
            $lock->forceDelete();
        }
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
