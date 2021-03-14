<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class PrintServiceProvider extends ServiceProvider
{
    public function register()
    {
//        $this->app->bind(Request::class, function ($app) {
//            $configuration = null;
//
//            try {
//                if (Schema::hasTable('configurations')) {
//                    $configuration = Configuration::where('key', config('printnode.config_key_name'))->first();
//                }
//            } catch (Exception $e) {
//                $configuration = null;
//            }
//
//            $credentials = new Credentials();
//
//            if ($configuration) {
//                $credentials->setApiKey($configuration->value);
//            } else {
//                $credentials->setApiKey(null);
//            }
//
//            return new Request($credentials);
//        });
//
//        $this->app->bind(PrintService::class, function ($app) {
//            return new PrintService(app(Request::class));
//        });
    }
}
