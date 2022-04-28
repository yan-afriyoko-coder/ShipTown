<?php

namespace App\Modules;

use App\Models\Module;
use Exception;
use Illuminate\Foundation\Support\Providers\EventServiceProvider;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

/**
 * Class BaseModuleServiceProvider.
 */
abstract class BaseModuleServiceProvider extends EventServiceProvider
{
    /**
     * @var string
     */
    public static string $module_name = '';

    /**
     * @var string
     */
    public static string $module_description = '';

    /**
     * @var string
     */
    public static string $settings_link = '';

    /**
     * Should we automatically enable it
     * When module first registered.
     *
     * @var bool
     */
    public bool $autoEnable = false;

    /**
     * Register any events for your application.
     *
     * @throws Exception
     *
     * @return void
     */
    public function boot()
    {
        if (empty(static::$module_name) or empty(static::$module_description)) {
            throw new Exception('Module "'.get_called_class().'" missing name or description');
        }

        if ($this->isEnabled()) {
            parent::boot();
        }
    }


    public static function enabling(): bool
    {
        // this method is fired when module is being enabled
        // return false if you want to prevent enabling
        return true;
    }

    public static function disabling(): bool
    {
        // this method is fired when module is being disabled
        // return false if you want to prevent enabling
        return true;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        try {
            $module = Module::firstOrCreate([
                'service_provider_class' => get_called_class(),
            ], [
                'enabled' => $this->autoEnable,
            ]);

            return $module->enabled;
        } catch (Exception $exception) {
            return false;
        }
    }

    /**
     *
     */
    public static function enableModule()
    {
        $module = Module::firstOrCreate(['service_provider_class' => get_called_class()], ['enabled' => false]);

        if ($module->enabled) {
            return;
        }

        $module->enabled = true;
        $module->save();

        app()->singleton(get_called_class(), function () {
            return app(get_called_class());
        });
    }

    /**
     * @return bool
     */
    public static function uninstallModule(): bool
    {
        try {
            /** @var Module $module */
            $module = Module::where(['service_provider_class' => get_called_class()])->first();

            if ($module) {
                $module->forceDelete();
            }

            return true;
        } catch (Exception $exception) {
            Log::emergency($exception);
            return false;
        }
    }

    /**
     *
     */
    public static function disableModule()
    {
        $module = Module::firstOrCreate(['service_provider_class' => get_called_class()], ['enabled' => false]);

        if ($module->enabled) {
            $module->enabled = false;
            $module->save();
        }
    }
}
