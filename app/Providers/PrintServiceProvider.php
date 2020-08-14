<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use PrintNode\ApiKey;
use PrintNode\Credentials;
use PrintNode\Request;

use App\Services\PrintService;
use App\Models\Configuration;

class PrintServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(Request::class, function ($app) {
            $configuration = null;

            if (Schema::hasTable('configurations')) {
                $configuration = Configuration::where('key', env('PRINTNODE_CONFIG_KEY_NAME'))->first();
            }
            
            $credentials = new Credentials();

            if ($configuration) {
                $credentials->setApiKey($configuration->value);
            } else {
                $credentials->setApiKey(null);
            }

            return new Request($credentials);
        });

        $this->app->bind(PrintService::class, function ($app) {
            return new PrintService(app(Request::class));
        });
    }
}
