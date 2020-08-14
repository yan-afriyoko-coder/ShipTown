<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use PrintNode\ApiKey;

use App\Services\PrintService;
use App\Models\Configuration;

class PrintServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(PrintService::class, function ($app) {
            $service = new PrintService();
            $configuration = Configuration::where('key', env('PRINTNODE_CONFIG_KEY_NAME'))->first();

            if ($configuration) {
                $service->setApiKey($configuration->value);
            } else {
                $service->setApiKey(null);
            }

            return $service;
        });
    }
}
