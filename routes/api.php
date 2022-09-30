<?php

use App\RoutesBuilder;
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
Route::apiResource('csv-import', 'Api\CsvImportController')->only(['store']);
Route::apiResource('csv-import/data-collections', 'Api\CsvImport\DataCollectionsImportController')->names('csv-import-data-collections')->only(['store']);
Route::apiResource('stocktake-suggestions', 'Api\StocktakeSuggestionController')->only(['index']);
Route::apiResource('stocktake-suggestions-details', 'Api\StocktakeSuggestionDetailController')->only(['index']);

Route::put('print/order/{order_number}/{view}', 'Api\PrintOrderController@store');

RoutesBuilder::apiResource('modules/dpd-uk/dpd-uk-connections')->only(['index']);
RoutesBuilder::apiResource('modules/printnode/printjobs')->only(['store']);
RoutesBuilder::apiResource('modules/webhooks/subscriptions')->only(['index', 'store']);

Route::apiResource('shipments', 'Api\ShipmentControllerNew', ['as' => 'new'])->only(['store']);
Route::apiResource('shipping-services', 'Api\ShippingServiceController')->only(['index']);
Route::apiResource('shipping-labels', 'Api\ShippingLabelController')->only(['store']);
Route::apiResource('restocking', 'Api\RestockingController')->only(['index']);

Route::post('settings/modules/automations/run', 'Api\Settings\Modules\RunAutomationController@store')->name('settings.modules.automations.run');

Route::apiResource('run/sync', 'Api\Run\SyncController')->only('index');
Route::apiResource('run/sync/api2cart', 'Api\Run\SyncApi2CartController')->only('index');
Route::apiResource('run/hourly/jobs', 'Api\Run\HourlyJobsController', ['as' => 'run.hourly'])->only('index');
Route::apiResource('run/daily/jobs', 'Api\Run\DailyJobsController', ['as' => 'run.daily'])->only('index');

Route::apiResource('logs', 'Api\LogController')->only(['index']);
Route::apiResource('activities', 'Api\ActivityController')->only(['index', 'store']);

Route::apiResource('products', 'Api\ProductController')->only(['index', 'store']);
Route::apiResource('product/aliases', 'Api\Product\ProductAliasController', ['as' => 'product'])->only(['index']);
Route::apiResource('product/inventory', 'Api\Product\ProductInventoryController')->only(['index', 'store']);
Route::apiResource('product/tags', 'Api\Product\ProductTagController')->only(['index']);

Route::apiResource('inventory-movements', 'Api\InventoryMovementController')->only(['store', 'index']);
Route::apiResource('stocktakes', 'Api\StocktakesController')->only(['store']);
Route::apiResource('data-collector', 'Api\DataCollectorController')->only(['index', 'store', 'update']);
Route::apiResource('data-collector-records', 'Api\DataCollectorRecordController')->only(['store', 'index']);

Route::apiResource('order-check-request', 'Api\OrderCheckRequestController')->only(['store']);

Route::apiResource('orders', 'Api\OrderController')->except('destroy');
Route::apiResource('order/products', 'Api\Order\OrderProductController', ['as' => 'order'])->only(['index', 'update']);
Route::apiResource('orders/products/shipments', 'Api\Order\OrderProductShipmentController')->only(['store']);
Route::apiResource('order/shipments', 'Api\Order\OrderShipmentController')->only(['index', 'store']);
Route::apiResource('order/comments', 'Api\Order\OrderCommentController')->only(['index', 'store']);
Route::apiResource('order-statuses', 'Api\OrderStatusController')->only(['index']);

Route::apiResource('picklist', 'Api\PicklistController')->only(['index']);
Route::apiResource('picklist/picks', 'Api\Picklist\PicklistPickController')->only(['store']);

// this should be called "order reservation"
// its job is to fetch next order and block it so no other user gets it again
Route::apiResource('packlist/order', 'Api\PacklistOrderController', ['as' => 'packlist'])->only(['index']);

Route::apiResource('settings/user/me', 'Api\Settings\UserMeController')->only(['index', 'store']);
Route::apiResource('settings/widgets', 'Api\Settings\WidgetController')->only(['store', 'update']);
Route::apiResource('navigation-menu', 'Api\Settings\NavigationMenuController')->only(['index']);
Route::apiResource('heartbeats', 'Api\HeartbeatsController')->only(['index']);


Route::resource(
    'modules/printnode/printers',
    'Api\Settings\Module\Printnode\PrinterController'
)->only(['index']);
