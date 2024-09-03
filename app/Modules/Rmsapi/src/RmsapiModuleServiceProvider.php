<?php

namespace App\Modules\Rmsapi\src;

use App\Events\EveryDayEvent;
use App\Events\EveryFiveMinutesEvent;
use App\Events\EveryMinuteEvent;
use App\Modules\BaseModuleServiceProvider;
use App\Modules\Rmsapi\src\Jobs\CleanupImportTablesJob;
use App\Modules\Rmsapi\src\Jobs\ImportAllJob;
use App\Modules\Rmsapi\src\Jobs\ProcessImportedProductRecordsJob;
use App\Modules\Rmsapi\src\Jobs\ProcessImportedSalesRecordsJob;
use App\Modules\Rmsapi\src\Jobs\UpdateImportedSalesRecordsJob;

class RmsapiModuleServiceProvider extends BaseModuleServiceProvider
{
    /**
     * @var string
     */
    public static string $module_name = 'eCommerce - RMSAPI Integration';

    /**
     * @var string
     */
    public static string $module_description = 'Provides connectivity to Microsoft RMS 2.0';

    /**
     * @var string
     */
    public static string $settings_link = '/settings/rmsapi';

    /**
     * @var bool
     */
    public static bool $autoEnable = false;

    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        EveryMinuteEvent::class => [
            Listeners\EveryMinuteEventListener::class,
        ],

        EveryFiveMinutesEvent::class => [
            Listeners\EveryFiveMinutesEventListener::class,
        ],

        EveryDayEvent::class => [
            Listeners\EveryDayEventListener::class,
        ],
    ];

    public static function enabling(): bool
    {
        CleanupImportTablesJob::dispatch();

        ImportAllJob::dispatch();

        UpdateImportedSalesRecordsJob::dispatch();
        ProcessImportedProductRecordsJob::dispatch();
        ProcessImportedSalesRecordsJob::dispatch();

        return parent::enabling();
    }
}
