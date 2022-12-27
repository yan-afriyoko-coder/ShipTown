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

Route::apiResource('csv-import', Api\CsvImportController::class)->only(['store']);
Route::apiResource('csv-import/data-collections', Api\CsvImport\DataCollectionsImportController::class)->names('csv-import-data-collections')->only(['store']);
Route::apiResource('stocktake-suggestions', Api\StocktakeSuggestionController::class)->only(['index']);
Route::apiResource('stocktake-suggestions-details', Api\StocktakeSuggestionDetailController::class)->only(['index']);
Route::apiResource('shipments', Api\ShipmentControllerNew::class, ['as' => 'new'])->only(['store']);
Route::apiResource('shipping-services', Api\ShippingServiceController::class)->only(['index']);
Route::apiResource('shipping-labels', Api\ShippingLabelController::class)->only(['store']);
Route::apiResource('restocking', Api\RestockingController::class)->only(['index']);
Route::apiResource('settings/modules/automations/run', Api\Modules\OrderAutomations\RunAutomationController::class, ['as' => 'settings.modules.automations'])->only(['store']);
Route::apiResource('run/sync', Api\Run\SyncController::class)->only('index');
Route::apiResource('run/sync/api2cart', Api\Run\SyncApi2CartController::class)->only('index');
Route::apiResource('run/hourly/jobs', Api\Run\HourlyJobsController::class, ['as' => 'run.hourly'])->only('index');
Route::apiResource('run/daily/jobs', Api\Run\DailyJobsController::class, ['as' => 'run.daily'])->only('index');
Route::apiResource('logs', Api\LogController::class)->only(['index']);
Route::apiResource('activities', Api\ActivityController::class)->only(['index', 'store']);
Route::apiResource('warehouses', Api\WarehouseController::class)->only(['index', 'store', 'update', 'destroy']);
Route::apiResource('products', Api\ProductController::class)->only(['index', 'store']);
Route::apiResource('product/aliases', Api\Product\ProductAliasController::class, ['as' => 'product'])->only(['index']);
Route::apiResource('product/inventory', Api\Product\ProductInventoryController::class)->only(['index', 'store']);
Route::apiResource('product/tags', Api\Product\ProductTagController::class)->only(['index']);
Route::apiResource('inventory-movements', Api\InventoryMovementController::class)->only(['store', 'index']);
Route::apiResource('stocktakes', Api\StocktakesController::class)->only(['store']);
Route::apiResource('data-collector', Api\DataCollectorController::class)->only(['index', 'store', 'update', 'destroy']);
Route::apiResource('data-collector-records', Api\DataCollectorRecordController::class)->only(['store', 'index']);
Route::apiResource('data-collector-actions/transfer-to-warehouse', Api\DataCollectorActions\TransferToWarehouseController::class, ['as' => 'api.data-collector-actions'])->only(['store']);
Route::apiResource('order-check-request', Api\OrderCheckRequestController::class)->only(['store']);
Route::apiResource('orders', Api\OrderController::class)->except('destroy');
Route::apiResource('order/products', Api\Order\OrderProductController::class, ['as' => 'order'])->only(['index', 'update']);
Route::apiResource('orders/products/shipments', Api\Order\OrderProductShipmentController::class)->only(['store']);
Route::apiResource('order/shipments', Api\Order\OrderShipmentController::class)->only(['index', 'store']);
Route::apiResource('order/comments', Api\OrderCommentController::class)->only(['index', 'store']);
Route::apiResource('order-statuses', Api\OrderStatusController::class)->only(['index']);
Route::apiResource('picklist', Api\PicklistController::class)->only(['index']);
Route::apiResource('picklist/picks', Api\Picklist\PicklistPickController::class)->only(['store']);
Route::apiResource('settings/user/me', Api\UserMeController::class)->only(['index', 'store']);
Route::apiResource('settings/widgets', Api\WidgetController::class)->only(['store', 'update']);
Route::apiResource('navigation-menu', Api\NavigationMenuController::class)->only(['index']);
Route::apiResource('heartbeats', Api\HeartbeatsController::class)->only(['index']);

Route::apiResource('modules/dpd-uk/dpd-uk-connections', Api\Modules\DpdUk\DpdUkConnectionController::class, ['as' => 'api.modules.dpd-uk'])->only(['index']);
Route::apiResource('modules/printnode/printers', Api\Modules\Printnode\PrinterController::class)->only(['index']);
Route::apiResource('modules/printnode/printjobs', Api\Modules\Printnode\PrintJobController::class, ['as' => 'api.modules.printnode'])->only(['store']);
Route::apiResource('modules/webhooks/subscriptions', Api\Modules\Webhooks\SubscriptionController::class, ['as' => 'api.modules.webhooks'])->only(['index', 'store']);
