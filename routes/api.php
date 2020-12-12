<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user()->id;
});

Route::middleware('auth:api')->get('/user/me', function (Request $request) {
    return new \App\Http\Resources\UserResource($request->user());
});

Route::middleware('auth:api')->group(function () {

    Route::get('/csv/products/shipped', 'Csv\ProductsShippedFromWarehouseController@index');

    Route::put('print/order/{order_number}/{view}', 'Api\PrintOrderController@store');

    Route::apiResource('run/sync', 'Api\Run\SyncController')->only('index');
    Route::apiResource('run/hourly/jobs', 'Api\Run\HourlyJobsController')->only('index');
    Route::apiResource('run/daily/jobs', 'Api\Run\DailyJobsController')->only('index');

    Route::apiResource('logs', 'Api\LogController')->only(['index']);

    Route::apiResource('products', 'Api\ProductsController')->only(['index','store']);
    Route::apiResource('product/aliases', 'Api\Product\ProductAliasController')->only(['index']);
    Route::apiResource('product/inventory', 'Api\Product\ProductInventoryController')->only(['index','store']);
    Route::apiResource('product/tags', 'Api\Product\ProductTagController')->only(['index','store']);

    Route::apiResource('orders', 'Api\OrdersController');
    Route::apiResource('order/products', 'Api\Order\OrderProductController');
    Route::apiResource('order/shipments', 'Api\Order\OrderShipmentController');
    Route::apiResource('order/comments', 'Api\Order\OrderCommentController')->only(['index', 'store']);

    Route::apiResource('picklist', 'Api\PicklistController')->only(['index']);
    Route::apiResource('picklist/picks', 'Api\Picklist\PicklistPickController')->only(['store']);
    Route::apiResource('picks', 'Api\PickController')->except('store');

    Route::apiResource('packlist/order', 'Api\PacklistOrderController')->only(['index']);

    Route::apiResource('settings/user/me', 'Api\Settings\User\UserMeController')->only(['index']);
    Route::apiResource('settings/printers', 'Api\Settings\PrinterController')->only(['index']);
    Route::apiResource('settings/widgets', 'Api\Settings\WidgetsController');
    Route::apiResource('settings/modules/rms_api/connections', "Api\Settings\Module\Rmsapi\RmsapiConnectionController");
    Route::apiResource('settings/modules/api2cart/connections', "Api\Settings\Module\Api2cart\Api2cartConnectionController");

    // Routes for users with the admin role only
    Route::group(['middleware' => ['role:admin']], function () {
        Route::apiResource('admin/users', 'Api\Admin\UserController')->middleware('can:manage users');
        Route::apiResource('admin/user/invites', 'Api\Admin\UserInviteController')->only(['store']);
        Route::apiResource('admin/user/roles', 'Api\Admin\UserRoleController')->only(['index'])->middleware('can:list roles');
        Route::apiResource('configuration', 'Api\Settings\ConfigurationController');
    });

    // to remove
    Route::apiResource('inventory', 'Api\Product\ProductInventoryController')->only(['index','store']);
    Route::get('products/{sku}/sync', 'Api\ProductsController@publish');
    Route::put('printers/use/{printerId}', 'Api\Settings\PrinterController@use');
    Route::apiResource('widgets', 'Api\Settings\WidgetsController');
    Route::get('run/maintenance', function () {
        \App\Jobs\RunMaintenanceJobs::dispatch();
        return 'Maintenance jobs dispatched';
    });
});
