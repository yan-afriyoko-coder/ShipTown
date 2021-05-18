<?php

namespace App\Providers;

use App\Models\Inventory;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Modules\Api2cart\src\Models\Api2cartOrderImports;
use App\Observers\InventoryObserver;
use App\Modules\Api2cart\src\Observers\Api2cartOrderImportsObserver;
use App\Observers\OrderObserver;
use App\Observers\OrderProductObserver;
use App\Observers\ProductObserver;
use App\Observers\ProductPriceObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Core Models
        Product::observe(ProductObserver::class);
        ProductPrice::observe(ProductPriceObserver::class);
        Inventory::observe(InventoryObserver::class);
        Order::observe(OrderObserver::class);
        OrderProduct::observe(OrderProductObserver::class);

        // Modules
        Api2cartOrderImports::observe(Api2cartOrderImportsObserver::class);
    }
}
