<?php

namespace App\Modules\Telescope\src;

use App\Events\DailyEvent;
use App\Modules\BaseModuleServiceProvider;
use Exception;

/**
 * Class Api2cartServiceProvider.
 */
class TelescopeModuleServiceProvider extends BaseModuleServiceProvider
{
    /**
     * @var string
     */
    public static string $module_name = 'Telescope';

    /**
     * @var string
     */
    public static string $module_description = ' Telescope module will help you to debug your application. ';

    /**
     * @var string
     */
    public static string $settings_link = '/telescope';

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
//        Every10minEvent::class => [
//
//],
//
//        HourlyEvent::class => [],
//
        DailyEvent::class => [
            Listeners\DailyEventListener::class
        ],
//
//        ProductPriceUpdatedEvent::class => [],
//
//        ProductTagAttachedEvent::class => [],
//
//        ProductTagDetachedEvent::class => [],
//
//        InventoryUpdatedEvent::class => [],
//
//        OrderUpdatedEvent::class => []
    ];

    /**
     * @throws Exception
     */
    public function boot()
    {
        parent::boot();

        $module_filename = '';

//        $this->publishes(
//            [__DIR__. '/../config/' . $module_filename . '.php' => config_path('!!!!!!!!!!!!!!!!!!!_SampleModuleStructure********.php')],
//            'config'
//        );
//
//        $this->mergeConfigFrom(
//            __DIR__.'/../config/' . $module_filename . '.php',
//            $module_filename
//        );
//
//        $this->loadViewsFrom(
//            __DIR__.'/../resources/views',
//            $module_filename
//        );
//
//        if ($this->app->runningInConsole()) {
//            $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
//        }
    }

    public static function disabling(): bool
    {
        // TODO: Implement disabling() method.

        return parent::disabling();
    }

    public static function enabling(): bool
    {
        return parent::enabling();
    }
}
