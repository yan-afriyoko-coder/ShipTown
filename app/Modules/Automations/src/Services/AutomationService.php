<?php

namespace App\Modules\Automations\src\Services;

use App\Modules\Automations\src\Helpers\AutomationHelper;
use Illuminate\Support\Collection;

/**
 *
 */
class AutomationService
{
    /**
     * This method will run selected Automations on selected orders
     * Each Condition provides QueryBuilder scope, which id added
     * to original query
     *
     * We utilize power or database to run Automation Actions
     * only very specifically selected orders
     *
     * In case of an issue, and one of the order will block application for some reason,
     * we will start automations from newest to oldest,
     * that will effectively process new orders first and lastly process problematic order
     *
     * @param $automationsToRunQuery
     * @param $ordersToRunQuery
     * @return bool
     */
    public static function runAutomationsOnOrdersQuery($automationsToRunQuery, $ordersToRunQuery): bool
    {
        return AutomationHelper::runAutomationsOnOrdersQuery(
            $automationsToRunQuery->orderBy('priority', 'asc'),
            $ordersToRunQuery->orderBy('id', 'desc')
        );
    }

    public static function availableConditions(): Collection
    {
        return collect(config('automations.conditions'));
    }

    public static function availableActions(): Collection
    {
        return collect(config('automations.actions'));
    }
}
