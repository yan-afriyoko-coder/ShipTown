<?php

use App\Http\Controllers\Api;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| User API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::put('print/order/{order_number}/{view}', [Api\PrintOrderController::class, 'store']);

// this should be called "order reservation"
// its job is to fetch next order and block it so no other user gets it again
Route::apiResource('packlist/order', Api\PacklistOrderController::class, ['as' => 'packlist'])->only(['index']);

Route::apiResource('csv-import/data-collections', Api\CsvImport\DataCollectionsImportController::class)->names('csv-import-data-collections')->only(['store']);
Route::apiResource('csv-import', Api\CsvImportController::class)->only(['store']);
Route::apiResource('stocktake-suggestions', Api\StocktakeSuggestionController::class)->only(['index']);
Route::apiResource('stocktake-suggestions-details', Api\StocktakeSuggestionDetailController::class)->only(['index']);
Route::apiResource('shipping-services', Api\ShippingServiceController::class)->only(['index']);
Route::apiResource('shipping-labels', Api\ShippingLabelController::class)->only(['store']);
Route::apiResource('restocking', Api\RestockingController::class)->only(['index']);
Route::apiResource('run/sync', Api\Run\SyncController::class)->only('index');
Route::apiResource('run/sync/api2cart', Api\Run\SyncApi2CartController::class)->only('index');
Route::apiResource('run/hourly/jobs', Api\Run\HourlyJobsController::class, ['as' => 'run.hourly'])->only('index');
Route::apiResource('run/daily/jobs', Api\Run\DailyJobsController::class, ['as' => 'run.daily'])->only('index');
Route::apiResource('logs', Api\LogController::class)->only(['index']);
Route::apiResource('activities', Api\ActivityController::class)->only(['index', 'store']);
Route::apiResource('warehouses', Api\WarehouseController::class)->only(['index', 'store', 'update', 'destroy']);
Route::apiResource('products', Api\ProductController::class)->only(['index', 'store']);
Route::apiResource('product/aliases', Api\Product\ProductAliasController::class, ['as' => 'product'])->only(['index']);
Route::apiResource('product/tags', Api\Product\ProductTagController::class)->only(['index']);
Route::apiResource('inventory-movements', Api\InventoryMovementController::class)->only(['store', 'index']);
Route::apiResource('stocktakes', Api\StocktakesController::class)->only(['store']);
Route::apiResource('data-collector', Api\DataCollectorController::class)->only(['index', 'store', 'update', 'destroy']);
Route::apiResource('data-collector-records', Api\DataCollectorRecordController::class)->only(['store', 'index']);
Route::apiResource('data-collector-actions/transfer-to-warehouse', Api\DataCollectorActions\TransferToWarehouseController::class, ['as' => 'api.data-collector-actions'])->only(['store']);
Route::apiResource('order-check-request', Api\OrderCheckRequestController::class)->only(['store']);
Route::apiResource('orders', Api\OrderController::class)->except('destroy');
Route::apiResource('order/products', Api\OrderProductController::class, ['as' => 'order'])->only(['index', 'update']);
Route::apiResource('orders/products/shipments', Api\OrderProductShipmentController::class)->only(['store']);
Route::apiResource('order/shipments', Api\Order\OrderShipmentController::class)->only(['index', 'store']);
Route::apiResource('order/comments', Api\OrderCommentController::class)->only(['index', 'store']);
Route::apiResource('order-statuses', Api\OrderStatusController::class)->only(['index']);
Route::apiResource('picklist', Api\PicklistController::class)->only(['index']);
Route::apiResource('picklist/picks', Api\Picklist\PicklistPickController::class)->only(['store']);
Route::apiResource('navigation-menu', Api\NavigationMenuController::class)->only(['index']);
Route::apiResource('heartbeats', Api\HeartbeatsController::class)->only(['index']);

Route::apiResource('modules/printnode/printers', Api\Modules\Printnode\PrinterController::class)->only(['index']);


Route::apiResource('admin/user/roles', Api\UserRoleController::class, ['as' => 'admin.users'])->only(['index']);
Route::apiResource('admin/users', Api\UserController::class);

Route::apiResource('settings/user/me', Api\UserMeController::class)->only(['index', 'store']);
Route::apiResource('settings/widgets', Api\WidgetController::class)->only(['store', 'update']);
Route::apiResource('settings/modules/automations/run', Api\Modules\OrderAutomations\RunAutomationController::class, ['as' => 'settings.modules.automations'])->only(['store']);

Route::apiResource('modules/autostatus/picking/configuration', Api\Modules\AutoStatus\ConfigurationController::class, ['as' => 'modules.autostatus.picking'])->only('index', 'store');

Route::group(['as' => 'api.'], function () {
    Route::apiResource('inventory', Api\InventoryController::class)->only(['index', 'store']);
    Route::apiResource('shipments', Api\ShipmentController::class)->only(['store']);

    Route::apiResource('settings/modules', Api\ModuleController::class, ['as' => 'settings'])->only(['index', 'update']);
    Route::apiResource('settings/order-statuses', Api\OrderStatusController::class, ['as' => 'settings'])->only(['index', 'store', 'update', 'destroy']);
    Route::apiResource('settings/mail-templates', Api\MailTemplateController::class, ['as' => 'settings'])->only(['index', 'update']);
    Route::apiResource('settings/navigation-menu', Api\NavigationMenuController::class, ['as' => 'settings'])->only(['index', 'store', 'update', 'destroy']);
    Route::apiResource('settings/configurations', Api\ConfigurationController::class, ['as' => 'settings'])->only(['index', 'store']);
    Route::apiResource('settings/automations/config', Api\Modules\OrderAutomations\ConfigController::class, ['as' => 'settings.module.automations'])->only(['index']);
    Route::apiResource('settings/automations', Api\Modules\OrderAutomations\AutomationController::class, ['as' => 'settings.module'])->only(['index', 'store', 'show', 'update', 'destroy']);

    Route::apiResource('settings/modules/api2cart/connections', Api\Modules\Api2cart\Api2cartConnectionController::class, ['as' => 'settings.module.api2cart'])->only(['index', 'store', 'destroy']);
    Route::apiResource('settings/modules/api2cart/products', Api\Modules\Api2cart\ProductsController::class, ['as' => 'settings.module.api2cart'])->only(['index']);
    Route::apiResource('settings/modules/dpd-ireland/connections', Api\Modules\DpdIreland\DpdIrelandController::class, ['as' => 'settings.module.dpd-ireland'])->only(['index', 'store', 'destroy']);
    Route::apiResource('settings/modules/printnode/printjobs', Api\Modules\Printnode\PrintJobController::class, ['as' => 'settings.module.printnode'])->only(['store']);
    Route::apiResource('settings/modules/printnode/clients', Api\Modules\Printnode\ClientController::class, ['as' => 'settings.module.printnode'])->only(['index', 'store', 'destroy']);
    Route::apiResource('settings/modules/rms_api/connections', Api\Modules\Rmsapi\RmsapiConnectionController::class, ['as' => 'settings.module.rmsapi'])->only(['index', 'store', 'destroy']);

    Route::apiResource('modules/dpd-uk/dpd-uk-connections', Api\Modules\DpdUk\DpdUkConnectionController::class, ['as' => 'modules.dpd-uk'])->only(['index']);
    Route::apiResource('modules/printnode/printjobs', Api\Modules\Printnode\PrintJobController::class, ['as' => 'modules.printnode'])->only(['store']);
    Route::apiResource('modules/webhooks/subscriptions', Api\Modules\Webhooks\SubscriptionController::class, ['as' => 'modules.webhooks'])->only(['index', 'store']);
});
