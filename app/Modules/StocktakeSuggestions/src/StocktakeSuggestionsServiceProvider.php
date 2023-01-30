<?php

namespace App\Modules\StocktakeSuggestions\src;

use App\Events\DailyEvent;
use App\Events\Every10minEvent;
use App\Events\Inventory\InventoryUpdatedEvent;
use App\Events\SyncRequestedEvent;
use App\Modules\BaseModuleServiceProvider;

/**
 * Class EventServiceProviderBase.
 */
class StocktakeSuggestionsServiceProvider extends BaseModuleServiceProvider
{
    /**
     * @var string
     */
    public static string $module_name = 'Stocktake Suggestions AI';

    /**
     * @var string
     */
    public static string $module_description = 'This AI prevents stock issues and suggests stocktakes' .
    ' based various product factors';

    /**
     * @var bool
     */
    public static bool $autoEnable = true;

    /**
     * @var array
     */
    protected $listen = [
        SyncRequestedEvent::class => [
            Listeners\SyncRequestedEventListener::class,
        ],

        Every10minEvent::class => [
            Listeners\Every10minEventListener::class,
        ],

        DailyEvent::class => [
            Listeners\DailyEventListener::class,
        ],

        InventoryUpdatedEvent::class => [
            Listeners\InventoryUpdatedEventListener::class,
        ],
    ];
}
