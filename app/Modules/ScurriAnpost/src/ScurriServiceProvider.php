<?php

namespace App\Modules\ScurriAnpost\src;

use App\Modules\BaseModuleServiceProvider;

class ScurriServiceProvider extends BaseModuleServiceProvider
{
    /**
     * @var string
     */
    public string $module_name = 'An Post Ireland';

    /**
     * @var string
     */
    public string $module_description = 'Provides connectivity to AnPost.ie';

    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [

    ];

    public function boot()
    {
        parent::boot();

        $this->publishes([
            __DIR__.'/../config/scurri.php' => config_path('scurri.php'),
        ], 'config');

        $this->mergeConfigFrom(__DIR__.'/../config/scurri.php', 'scurri');
    }
}
