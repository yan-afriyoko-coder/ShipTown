<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\PrintService;

class PrintServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(PrintService::class, function ($app) {
            return new PrintService();
        });
    }
}
