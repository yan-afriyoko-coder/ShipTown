<?php

namespace App\Modules\InventoryQuantityIncoming\src;

use App\Events\DataCollection\DataCollectionDeletedEvent;
use App\Events\DataCollectionRecord\DataCollectionRecordCreatedEvent;
use App\Events\DataCollectionRecord\DataCollectionRecordDeletedEvent;
use App\Events\DataCollectionRecord\DataCollectionRecordUpdatedEvent;
use App\Events\EveryHourEvent;
use App\Modules\BaseModuleServiceProvider;

/**
 * Class EventServiceProviderBase.
 */
class InventoryQuantityIncomingServiceProvider extends BaseModuleServiceProvider
{
    /**
     * @var string
     */
    public static string $module_name = 'Inventory Quantity Incoming';

    /**
     * @var string
     */
    public static string $module_description = 'Reserves quantity incoming for open Inventory Transfers In';

    /**
     * @var bool
     */
    public static bool $autoEnable = true;

    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        EveryHourEvent::class => [
            Listeners\HourlyEventListener::class,
        ],

        DataCollectionRecordCreatedEvent::class => [
            Listeners\DataCollectionRecordCreatedEventListener::class,
        ],

        DataCollectionRecordUpdatedEvent::class => [
            Listeners\DataCollectionRecordUpdatedEventListener::class,
        ],

        DataCollectionRecordDeletedEvent::class => [
            Listeners\DataCollectionRecordDeletedEventListener::class,
        ],

        DataCollectionDeletedEvent::class => [
            Listeners\DataCollectionDeletedEventListener::class,
        ],
    ];

    public static function enableModule(): bool
    {
        if (! parent::enableModule()) {
            return false;
        }

        return true;
    }

    public static function disabling(): bool
    {
        return false;
    }
}
