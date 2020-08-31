<?php

namespace App\Providers;

use App\Models\Inventory;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Packlist;
use App\Models\Pick;
use App\Models\Picklist;
use App\Models\PickRequest;
use App\Models\Product;
use App\Modules\Api2cart\src\Models\Api2cartOrderImports;
use App\Observers\InventoryObserver;
use App\Observers\Modules\Api2cart\Api2cartOrderImportsObserver;
use App\Observers\OrderObserver;
use App\Observers\OrderProductObserver;
use App\Observers\PacklistsObserver;
use App\Observers\PicklistsObserver;
use App\Observers\PickObserver;
use App\Observers\PickRequestObserver;
use App\Observers\ProductObserver;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\ServiceProvider;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if (empty(env('TENANT_NAME'))) {
            dd('TENANT_NAME not specified');
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Queue::before(function (JobProcessing $event) {
            logger('Job starting '.$event->job->resolveName());
        });

        Queue::after(function (JobProcessed $event) {
            logger('Job processed '.$event->job->resolveName());
        });

        // Core Models
        Product::observe(ProductObserver::class);
        Order::observe(OrderObserver::class);
        OrderProduct::observe(OrderProductObserver::class);

        Pick::observe(PickObserver::class);
        PickRequest::observe(PickRequestObserver::class);

        Picklist::observe(PicklistsObserver::class);
        Packlist::observe(PacklistsObserver::class);

        Inventory::observe(InventoryObserver::class);

        // Modules
        Api2cartOrderImports::observe(Api2cartOrderImportsObserver::class);
    }
}
