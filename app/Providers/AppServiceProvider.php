<?php

namespace App\Providers;

use App\Models\Configuration;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\OrderShipment;
use App\Models\OrderStatus;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Modules\Api2cart\src\Models\Api2cartOrderImports;
use App\Modules\Api2cart\src\Observers\Api2cartOrderImportsObserver;
use App\Observers\InventoryObserver;
use App\Observers\OrderObserver;
use App\Observers\OrderProductObserver;
use App\Observers\OrderShipmentObserver;
use App\Observers\OrderStatusObserver;
use App\Observers\ProductObserver;
use App\Observers\ProductPriceObserver;
use Illuminate\Support\Facades\Config;
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
        OrderShipment::observe(OrderShipmentObserver::class);
        OrderStatus::observe(OrderStatusObserver::class);

        // Modules
        Api2cartOrderImports::observe(Api2cartOrderImportsObserver::class);

        // sync new config to database
        $configurations = collect(config('configuration'));
        $configurations->each(function ($item, $key) {
            Configuration::firstOrCreate(['key' => $key], ['value' => '']);
        });

        // Create config from database
        Configuration::get()->each(function (Configuration $config) {
            Config::set('configuration.' . $config->key, $config->value);
        });
    }
}
