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

    Route::apiResource('logs', 'Api\LogController')->only(['index']);

    Route::apiResource('products', 'Api\ProductsController')->only(['index','store']);
    Route::apiResource('product/aliases', 'Api\Product\ProductAliasController')->only(['index']);
    Route::apiResource('product/inventory', 'Api\Product\ProductInventoryController')->only(['index','store']);

    Route::apiResource('orders', 'Api\OrdersController');
    Route::apiResource('order/products', 'Api\Order\OrderProductController');
    Route::apiResource('order/shipments', 'Api\Order\OrderShipmentController');
    Route::apiResource('order/comments', 'Api\Order\OrderCommentController')->only(['store']);

    Route::apiResource('picklist', 'Api\PicklistController')->only(['index','store']);

    Route::apiResource('packlist/order', 'Api\PacklistOrderController')->only(['index']);

    Route::apiResource('settings/user/me', 'Api\Settings\User\UserMeController')->only(['index']);
    Route::apiResource('settings/printers', 'Api\Settings\PrinterController')->only(['index']);
    Route::apiResource('settings/widgets', 'Api\WidgetsController');
    Route::apiResource('settings/modules/rms_api/connections', "Api\Settings\Module\Rmsapi\RmsapiConnectionController");
    Route::apiResource('settings/modules/api2cart/connections', "Api\Settings\Module\Api2cart\Api2cartConnectionController");

    Route::put('print/order/{order_number}/{view}', 'Api\PrintOrderController@store');

    Route::apiResource('picks', 'Api\PickController')->except('store');

    Route::resource('widgets', 'Api\WidgetsController');
    // Routes for users with the admin role only
    Route::group(['middleware' => ['role:admin']], function () {
        Route::post('invites', 'InvitesController@store');
        Route::get('roles', 'Api\RolesController@index')->middleware('can:list roles');
        Route::resource('users', 'Api\Settings\UsersController')->middleware('can:manage users');
        Route::resource('configuration', 'Api\Settings\ConfigurationController');
    });


    Route::get('sync', "Api\SyncController@index");
    Route::get('run/maintenance', function () {
        \App\Jobs\RunMaintenanceJobs::dispatch();
        return 'Maintenance jobs dispatched';
    });

    // to remove
    Route::resource("rms_api_configuration", "Api\Settings\Module\Rmsapi\RmsapiConnectionController");
    Route::resource("api2cart_configuration", "Api\Settings\Module\Api2cart\Api2cartConnectionController");
    Route::apiResource('users/me', 'Api\Settings\User\UserMeController')->only(['index']);
    Route::apiResource('inventory', 'Api\Product\ProductInventoryController')->only(['index','store']);
    Route::get('products/{sku}/sync', 'Api\ProductsController@publish');
    Route::put('printers/use/{printerId}', 'Api\Settings\PrinterController@use');
});
