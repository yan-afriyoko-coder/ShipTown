<?php

namespace App\Modules\Magento2MSI\src;

use App\Events\EveryHourEvent;
use App\Events\EveryMinuteEvent;
use App\Events\Product\ProductTagAttachedEvent;
use App\Events\Product\ProductTagDetachedEvent;
use App\Events\RecalculateInventoryRequestEvent;
use App\Events\SyncRequestedEvent;
use App\Models\ManualRequestJob;
use App\Modules\BaseModuleServiceProvider;

class Magento2MsiServiceProvider extends BaseModuleServiceProvider
{
    public static string $module_name = 'eCommerce - Magento 2 MSI';

    public static string $module_description = 'Module provides connectivity to Magento 2 API - Multi Source Inventory';

    public static string $settings_link = '/settings/modules/magento2msi';

    public static bool $autoEnable = false;

    protected $listen = [
        SyncRequestedEvent::class => [
            \App\Modules\Magento2MSI\src\Listeners\SyncRequestedEventListener::class,
        ],

        RecalculateInventoryRequestEvent::class => [
            \App\Modules\Magento2MSI\src\Listeners\RecalculateInventoryRequestEventListener::class,
        ],

        EveryMinuteEvent::class => [
            \App\Modules\Magento2MSI\src\Listeners\EveryMinuteEventListener::class
        ],

        EveryHourEvent::class => [
            \App\Modules\Magento2MSI\src\Listeners\EveryHourEventListener::class
        ],

        ProductTagAttachedEvent::class => [
            \App\Modules\Magento2MSI\src\Listeners\ProductTagAttachedEventListener::class,
        ],

        ProductTagDetachedEvent::class => [
            Listeners\ProductTagDetachedEventListener::class,
        ],
    ];

    public static function enabling(): bool
    {
        ManualRequestJob::query()->upsert([
            'job_name' => 'Magento 2 MSI - AssignInventorySourceJob',
            'job_class' => \App\Modules\Magento2MSI\src\Jobs\AssignInventorySourceJob::class,
        ], ['job_class'], ['job_name']);

        ManualRequestJob::query()->upsert([
            'job_name' => 'Magento 2 MSI - CheckIfSyncIsRequiredJob',
            'job_class' => \App\Modules\Magento2MSI\src\Jobs\CheckIfSyncIsRequiredJob::class,
        ], ['job_class'], ['job_name']);

        ManualRequestJob::query()->upsert([
            'job_name' => 'Magento 2 MSI - EnsureProductRecordsExistJob',
            'job_class' => \App\Modules\Magento2MSI\src\Jobs\EnsureProductRecordsExistJob::class,
        ], ['job_class'], ['job_name']);

        ManualRequestJob::query()->upsert([
            'job_name' => 'Magento 2 MSI - FetchStockItemsJob',
            'job_class' => \App\Modules\Magento2MSI\src\Jobs\FetchStockItemsJob::class,
        ], ['job_class'], ['job_name']);

        ManualRequestJob::query()->upsert([
            'job_name' => 'Magento 2 MSI - GetProductIdsJob',
            'job_class' => \App\Modules\Magento2MSI\src\Jobs\GetProductIdsJob::class,
        ], ['job_class'], ['job_name']);

        ManualRequestJob::query()->upsert([
            'job_name' => 'Magento 2 MSI - SyncProductInventoryJob',
            'job_class' => \App\Modules\Magento2MSI\src\Jobs\SyncProductInventoryJob::class,
        ], ['job_class'], ['job_name']);

        return parent::enabling();
    }
}
