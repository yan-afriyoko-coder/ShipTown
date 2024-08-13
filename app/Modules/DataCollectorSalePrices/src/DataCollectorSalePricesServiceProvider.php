<?php

namespace App\Modules\DataCollectorSalePrices\src;

use App\Events\DataCollectionRecord\DataCollectionRecordCreatedEvent;
use App\Events\DataCollectionRecord\DataCollectionRecordUpdatedEvent;
use App\Modules\BaseModuleServiceProvider;

class DataCollectorSalePricesServiceProvider extends BaseModuleServiceProvider
{
    public static string $module_name = 'Data Collector - Sale Prices';

    public static string $module_description = 'Module provides an ability to use sale prices in data collection.';

    public static bool $autoEnable = true;

    protected $listen = [
        DataCollectionRecordCreatedEvent::class => [
            Listeners\DataCollectionRecordCreatedEventListener::class,
        ],

        DataCollectionRecordUpdatedEvent::class => [
            Listeners\DataCollectionRecordUpdatedEventListener::class,
        ],
    ];

    public static function enabling(): bool
    {
        return parent::enabling();
    }
}
