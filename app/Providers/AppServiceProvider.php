<?php

namespace App\Providers;

use App\Models\Inventory;
use App\Models\InventoryMovement;
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
use App\Observers\InventoryMovementObserver;
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
use Exception;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
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
        $this->registerEnabledModules();

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
        InventoryMovement::observe(InventoryMovementObserver::class);

        // Modules
        Api2cartOrderImports::observe(Api2cartOrderImportsObserver::class);
    }

    private function registerEnabledModules(): void
    {
        try {
            $enabled_modules = Module::query()
                ->where(['enabled' => true])
                ->get();
        } catch (Exception $exception) {
            report($exception);
            return;
        }

        $enabled_modules->each(function (Module $module) {
            $this->app->singleton($module->service_provider_class, $module->service_provider_class);
            App::register($module->service_provider_class);
        });
    }
}
