<?php


namespace App\Modules\Reports\src;

use App\Modules\BaseModuleServiceProvider;
use Exception;

/**
 *
 */
class ReportsServiceProvider extends BaseModuleServiceProvider
{

    /**
     * @var string
     */
    public static string $module_name = '.CORE - Reports';

    /**
     * @var string
     */
    public static string $module_description = 'Provides reports under menu section';

    /**
     * @var string
     */
    public static string $settings_link = '';

    /**
     * @var bool
     */
    public static bool $autoEnable = true;

    /**
     * @var array
     */
    protected $listen = [];

    public static function disabling(): bool
    {
        return false;
    }

    /**
     * @throws Exception
     */
    public function boot()
    {
        parent::boot();

        $module_filename = 'reports';

//        $this->publishes(
//            [__DIR__. '/../config/' . $module_filename . '.php' => config_path($module_filename.'.php')],
//            'config'
//        );
//
//        $this->mergeConfigFrom(
//            __DIR__.'/../config/' . $module_filename . '.php',
//            $module_filename
//        );

        $this->loadViewsFrom(
            __DIR__.'/../resources/views',
            $module_filename
        );

//        if ($this->app->runningInConsole()) {
//            $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
//        }
    }
}
