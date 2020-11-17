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

    Route::apiResource('users/me', 'Api\UserMeController')->only(['index']);
    Route::apiResource('logs', 'Api\LogController')->only(['index']);

    Route::apiResource('products', 'Api\ProductsController')->only(['index','store']);
    Route::apiResource('product/aliases', 'Api\Product\ProductAliasController')->only(['index']);
    Route::apiResource('product/inventory', 'Api\Product\ProductInventoryController')->only(['index','store']);

    Route::apiResource('orders', 'Api\OrdersController');
    Route::apiResource('order/products', 'Api\Order\OrderProductController')->only(['index','update']);
    Route::apiResource('order/shipments', 'Api\Order\OrderShipmentController')->only(['index', 'store']);
    Route::apiResource('order/comments', 'Api\Order\OrderCommentController')->only(['store']);

    Route::apiResource('packlist/order', 'Api\PacklistOrderController')->only(['index']);
    // todo Route::apiResource('picklist', 'Api\PicklistOrderController')->only(['index']);

    Route::apiResource('settings/printers', 'Api\PrintersController')->only(['index']);
    Route::apiResource('settings/widgets', 'Api\WidgetsController');
    Route::apiResource('settings/modules/rms_api/connections', "Api\Settings\Module\Rmsapi\RmsapiConnectionController");
    Route::apiResource('settings/modules/api2cart/connections', "Api\Settings\Module\Api2cart\Api2cartConnectionController");

    Route::put('print/order/{order_number}/{view}', 'Api\PrintOrderController@store');

    Route::apiResource('picks', 'Api\PickController')->except('store');

    Route::resource('widgets', 'Api\WidgetsController');
    Route::resource("rms_api_configuration", "Api\Settings\Module\Rmsapi\RmsapiConnectionController");
    Route::resource("api2cart_configuration", "Api\Settings\Module\Api2cart\Api2cartConnectionController");

    // Routes for users with the admin role only
    Route::group(['middleware' => ['role:admin']], function () {
        Route::post('invites', 'InvitesController@store');
        Route::get('roles', 'Api\RolesController@index')->middleware('can:list roles');
        Route::resource('users', 'Api\UsersController')->middleware('can:manage users');
        Route::resource('configuration', 'Api\ConfigurationsController');
    });


    Route::get('sync', "Api\SyncController@index");
    Route::get('run/maintenance', function () {
        \App\Jobs\RunMaintenanceJobs::dispatch();
        return 'Maintenance jobs dispatched';
    });

    // to remove
    Route::apiResource('inventory', 'Api\Product\InventoryController')->only(['index','store']);
    Route::get('products/{sku}/sync', 'Api\ProductsController@publish');
    Route::put('printers/use/{printerId}', 'Api\PrintersController@use');
});
