<?php


namespace App\Modules;

use App;
use App\Module;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Support\Providers\EventServiceProvider;

/**
 * Class ModuleServiceProvider
 * @package App\Modules
 */
class ModuleServiceProvider extends EventServiceProvider
{
    /**
     * Should we automatically enable it
     * When module first registered
     *
     * @var bool
     */
    public bool $autoEnable = true;

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->isEnabled()) {
            parent::boot();
        }
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        try {
            $module = Module::firstOrCreate([
                    'service_provider_class' => get_called_class()
                ], [
                    'enabled' => $this->autoEnable
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
        Module::updateOrCreate(['service_provider_class' => get_called_class()], ['enabled' => true]);
        App::register(get_called_class());
    }

    /**
     * @return Module|Model
     */
    public static function disableModule()
    {
        return Module::updateOrCreate(['service_provider_class' => get_called_class()], ['enabled' => false]);
    }
}
