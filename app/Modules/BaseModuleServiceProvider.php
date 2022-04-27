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
     *
     */
    public static function enableModule()
    {
        $module = Module::updateOrCreate(['service_provider_class' => get_called_class()], ['enabled' => false]);

        $module->update(['enabled' => true]);
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
}
