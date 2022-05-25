<?php

namespace App\Modules\Automations\src\Services;

use App\Events\Order\ActiveOrderCheckEvent;
use App\Modules\Automations\src\Jobs\RunAutomationsOnActiveOrdersJob;
use App\Modules\Automations\src\Models\Action;
use App\Modules\Automations\src\Models\Automation;
use App\Modules\Automations\src\Models\OrderLock;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

/**
 *
 */
class AutomationService
{
    /**
     * @param ActiveOrderCheckEvent|null $event
     */
    public static function runAllAutomations(ActiveOrderCheckEvent $event = null)
    {
        RunAutomationsOnActiveOrdersJob::dispatch();
    }

    /**
     * @param Automation $automation
     * @param ActiveOrderCheckEvent $event
     */
    public static function validateAndRunAutomation(Automation $automation, ActiveOrderCheckEvent $event)
    {
        // this will prevent two automation processes running on same order
        try {
            OrderLock::query()
                ->where('created_at', '<', now()->subMinutes(10))
                ->forceDelete();

            /** @var OrderLock $lock */
            $lock = OrderLock::create(['order_id' => $event->order->getKey()]);
        } catch (Exception $exception) {
            // early exit, cannot lock order, automation is already running for it
            return;
        }

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

        $lock->forceDelete();
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

    public static function availableConditions(): Collection
    {
        return collect(config('automations.conditions'));
    }

    public static function availableActions(): Collection
    {
        return collect(config('automations.actions'));
    }

    public static function availableEvents(): Collection
    {
        return collect()->push(['class' => ActiveOrderCheckEvent::class]);
    }

    public static function runAutomationsOn(\App\Models\Order $order)
    {
        RunAutomationsOnActiveOrdersJob::dispatch($order->getKey());
    }

    public static function runAutomationsOnActiveOrders()
    {
        RunAutomationsOnActiveOrdersJob::dispatch();
    }
}
