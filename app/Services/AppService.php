<?php

namespace App\Services;

use App\Models\DataCollection;
use App\Models\DataCollectionRecord;
use App\Models\Heartbeat;
use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\Module;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\OrderProductTotal;
use App\Models\OrderStatus;
use App\Models\Product;
use App\Models\ProductAlias;
use App\Models\Session;
use App\Models\Warehouse;
use App\Modules\Api2cart\src\Models\Api2cartConnection;
use App\Modules\Api2cart\src\Models\Api2cartProductLink;
use App\Modules\Automations\src\Models\Action;
use App\Modules\Automations\src\Models\Automation;
use App\Modules\Automations\src\Models\Condition;
use App\Modules\DpdIreland\src\Models\DpdIreland;
use App\Modules\InventoryMovements\src\InventoryMovementsServiceProvider;
use App\Modules\InventoryReservations\src\EventServiceProviderBase as InventoryReservationsServiceProvider;
use App\Modules\InventoryReservations\src\Models\Configuration;
use App\Modules\InventoryTotals\src\Models\InventoryTotal;
use App\Modules\InventoryTotals\src\Models\InventoryTotalByWarehouseTag;
use App\Modules\MagentoApi\src\Models\MagentoConnection;
use App\Modules\MagentoApi\src\Models\MagentoProduct;
use App\Modules\PrintNode\src\Models\Client;
use App\Modules\Rmsapi\src\Models\RmsapiConnection;
use App\User;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;
use Spatie\Permission\PermissionRegistrar;
use Spatie\Tags\Tag;

class AppService
{
    public static function resetDatabase(): void
    {
        \App\Modules\InventoryTotals\src\Models\Configuration::query()->forceDelete();



            Activity::query()->forceDelete();

        Product::query()->forceDelete();
        Inventory::query()->forceDelete();

        InventoryTotal::query()->forceDelete();
        InventoryTotalByWarehouseTag::query()->forceDelete();
        \App\Modules\InventoryMovements\src\Models\Configuration::query()->forceDelete();
        ProductAlias::query()->forceDelete();
        OrderProduct::query()->forceDelete();
        Order::query()->forceDelete();
        OrderStatus::query()->forceDelete();
        Configuration::query()->forceDelete();
        Tag::query()->forceDelete();
        OrderProductTotal::query()->forceDelete();
        InventoryMovement::query()->forceDelete();

        Automation::query()->forceDelete();
        Condition::query()->forceDelete();
        Action::query()->forceDelete();
        Heartbeat::query()->forceDelete();

        Module::query()->forceDelete();

        Client::query()->forceDelete();
        Api2cartProductLink::query()->forceDelete();
        Api2cartConnection::query()->forceDelete();

        RmsapiConnection::query()->forceDelete();
        DataCollection::query()->forceDelete();
        DataCollectionRecord::query()->forceDelete();

        MagentoProduct::query()->forceDelete();
        MagentoConnection::query()->forceDelete();

        DpdIreland::query()->forceDelete();

        ModulesService::updateModulesTable();

        DB::table('modules_queue_monitor_jobs')->delete();
        InventoryMovement::query()->forceDelete();
        User::query()->forceDelete();
        Warehouse::query()->forceDelete();
        Session::query()->forceDelete();

        // now re-register all the roles and permissions (clears cache and reloads relations)
        app()->make(PermissionRegistrar::class)->forgetCachedPermissions();

        \App\Models\Configuration::query()->forceDelete();
        \App\Models\Configuration::query()->updateOrCreate([], ['disable_2fa' => true]);

        InventoryReservationsServiceProvider::enableModule();
        InventoryMovementsServiceProvider::enableModule();
    }
}
