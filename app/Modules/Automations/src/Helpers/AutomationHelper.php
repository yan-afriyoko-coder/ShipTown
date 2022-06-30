<?php

namespace App\Modules\Automations\src\Helpers;

use App\Models\CacheLock;
use App\Models\Order;
use App\Modules\Automations\src\Models\Action;
use App\Modules\Automations\src\Models\Automation;
use App\Modules\Automations\src\Models\Condition;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;

class AutomationHelper
{

    /**
     * This method will run selected Automations on selected orders
     * Each Condition provides QueryBuilder scope, which id added
     * to original query
     *
     * We utilize power or database to run Automation Actions
     * only very specifically selected orders
     *
     * @param Builder $automationsToRunQuery
     * @param Builder $ordersToRunQuery
     * @return bool
     */
    public static function runAutomationsOnOrdersQuery(Builder $automationsToRunQuery, Builder $ordersToRunQuery): bool
    {
        return $automationsToRunQuery
            ->get()
            ->every(function (Automation $automation) use ($ordersToRunQuery) {
                return self::runAutomation($automation, $ordersToRunQuery);
            });
    }

    /**
     * @param Automation $automation
     * @param Builder $ordersToRunQuery
     * @return bool
     */
    public static function runAutomation(Automation $automation, Builder $ordersToRunQuery): bool
    {
        $orders = clone $ordersToRunQuery;

        self::addAutomationConditions($automation, $orders);

        $orders->get()
            ->each(function (Order $order) use ($automation) {
                try {
                    AutomationHelper::runIfValid($automation, $order);

                    //
                } catch (Exception $exception) {
                    report($exception);
                    Log::error('Exception occurred when running actions', [
                        'automation' => $automation->name,
                        'order_number' => $order->order_number,
                        'exception' => $exception->getMessage(),
                    ]);
                }
            });

        return true;
    }

    /**
     * @param Automation $automation
     * @param Builder $query
     * @return bool
     */
    public static function addAutomationConditions(Automation $automation, Builder $query): bool
    {
        try {
            $automation->conditions
                ->each(function (Condition $condition) use ($query) {
                    $condition->condition()::addQueryScope($query, $condition->condition_value);
                });
        } catch (Exception $exception) {
            report($exception);
            Log::error('Exception occurred when adding Automation Query conditions', [
                'automation' => $automation->name,
                'exception' => $exception->getMessage()
            ]);
            return false;
        }

        return true;
    }

    /**
     * @param Automation $automation
     * @param Order $order
     * @return bool
     */
    public static function runIfValid(Automation $automation, Order $order): bool
    {
        // validate
        if ($order->is_editing) {
            return false;
        }

        if (! $order->orderProductsTotals()->where('quantity_ordered', '>', 0)->exists()) {
            return false;
        }

        if (! $automation->allConditionsTrue($order)) {
            return false;
        }

        // run
        return self::runActions($automation, $order);
    }

    /**
     * @param Automation $automation
     * @param Order $order
     * @return bool
     */
    private static function runActions(Automation $automation, Order $order): bool
    {
        if (! CacheLock::acquire(__METHOD__, $order->id, 60)) {
            return false;
        }

        try {
            activity()->on($order)
                ->withProperties([$automation->name])
                ->causedByAnonymous()
                ->log('Running automation');

            $result = $automation->actions
                ->every(function (Action $action) use ($order) {
                    $runAction = new $action->action_class($order);

                    $actionRan = $runAction->handle($action->action_value);

                    Log::debug('Executed Order Action', [
                        'order_number' => $order->order_number,
                        'action_class' => class_basename($action->action_class),
                        'action_value' => $action->action_value,
                    ]);

                    return $actionRan;
                });
        } finally {
            CacheLock::release(__METHOD__, $order->id);
        }


        // log
        Log::debug('Ran automation actions', [
            'order_number' => $order->order_number,
            'event_class' => class_basename($automation),
            'automation_name' => $automation->name,
        ]);

        return $result;
    }
}
