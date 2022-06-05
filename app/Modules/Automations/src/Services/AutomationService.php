<?php

namespace App\Modules\Automations\src\Services;

use App\Events\Order\ActiveOrderCheckEvent;
use App\Models\Order;
use App\Modules\Automations\src\Abstracts\BaseOrderConditionAbstract;
use App\Modules\Automations\src\Models\Action;
use App\Modules\Automations\src\Models\Automation;
use App\Modules\Automations\src\Models\Condition;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

/**
 *
 */
class AutomationService
{
    /**
     * @param Automation $automation
     * @param $query
     */
    public static function run(Automation $automation, $query)
    {
        $automation->conditions
            ->each(function (Condition $condition) use ($query) {
                /** @var BaseOrderConditionAbstract $c */
                $c = $condition->condition_class;
                $c::addQueryScope($query, $condition->condition_value);
            });

        $query->inRandomOrder()
            ->get()
            ->each(function (Order $order) use ($automation) {
                AutomationService::validateAndRunAutomation($automation, $order);
            });
    }

    /**
     * @param Automation $automation
     * @param Order $order
     */
    public static function validateAndRunAutomation(Automation $automation, Order $order)
    {
        $allConditionsPassed = $automation->allConditionsTrue($order);

        if ($allConditionsPassed === true) {
            $automation->actions
                ->each(function (Action $action) use ($order) {
                    AutomationService::runAction($action, $order);
                });
        }

        Log::debug('Ran automation', [
            'order_number' => $order->order_number,
            'event_class' => class_basename($automation),
            'automation_name' => $automation->name,
            'all_conditions_passed' => $allConditionsPassed
        ]);
    }

    /**
     * @param Action $action
     * @param Order $order
     */
    private static function runAction(Action $action, Order $order): void
    {
        $runAction = new $action->action_class($order);

        $runAction->handle($action->action_value);

        Log::debug('Executed Order Action', [
            'order_number' => $order->order_number,
            'action_class' => class_basename($action->action_class),
            'action_value' => $action->action_value,
        ]);
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
}
