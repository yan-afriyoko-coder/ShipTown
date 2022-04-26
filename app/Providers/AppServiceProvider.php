<?php

namespace App\Providers;

use App\Models\Inventory;
use App\Models\Module;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\OrderProductShipment;
use App\Models\OrderShipment;
use App\Models\OrderStatus;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\Warehouse;
use App\Modules\Api2cart\src\Models\Api2cartOrderImports;
use App\Modules\Api2cart\src\Observers\Api2cartOrderImportsObserver;
use App\Observers\InventoryObserver;
use App\Observers\ModuleObserver;
use App\Observers\OrderObserver;
use App\Observers\OrderProductObserver;
use App\Observers\OrderProductShipmentObserver;
use App\Observers\OrderShipmentObserver;
use App\Observers\OrderStatusObserver;
use App\Observers\ProductObserver;
use App\Observers\ProductPriceObserver;
use App\Observers\WarehouseObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        parent::register();

        \Laravel\Passport\Passport::ignoreMigrations();
    }

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
        OrderShipment::observe(OrderShipmentObserver::class);
        OrderStatus::observe(OrderStatusObserver::class);
        OrderProductShipment::observe(OrderProductShipmentObserver::class);
        Warehouse::observe(WarehouseObserver::class);
        Module::observe(ModuleObserver::class);

        // Modules
        Api2cartOrderImports::observe(Api2cartOrderImportsObserver::class);
    }
}
