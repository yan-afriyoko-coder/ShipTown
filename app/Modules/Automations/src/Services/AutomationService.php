<?php

namespace App\Modules\Automations\src\Services;

use App\Modules\Automations\src\Helpers\AutomationHelper;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

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
     */
    public static function runAutomationsOnOrdersQuery(Builder $automationsToRunQuery, Builder $ordersToRunQuery): bool
    {
        // we will clone the queries so originals are not affected
        $automations = clone $automationsToRunQuery;
        $orders = clone $ordersToRunQuery;

        return AutomationHelper::runAutomationsOnOrdersQuery(
            $automations->orderBy('priority', 'asc'),
            $orders->orderBy('id', 'desc')
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
