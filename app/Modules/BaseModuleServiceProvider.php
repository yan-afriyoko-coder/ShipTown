<?php


namespace App\Modules;

use App;
use App\Module;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Support\Providers\EventServiceProvider;
use function PHPUnit\Framework\throwException;

/**
 * Class BaseModuleServiceProvider
 * @package App\Modules
 */
class BaseModuleServiceProvider extends EventServiceProvider
{
    /**
     * @var string
     */
    public string $module_name;

    /**
     * @var string
     */
    public string $module_description;

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
     * @throws Exception
     */
    public function boot()
    {
        if (empty($this->module_name) or empty($this->module_description)) {
            throw new Exception('Module "'.get_called_class().'" missing name or description');
        }

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
