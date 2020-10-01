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
    Route::get('products', 'Api\ProductsController@index');
    Route::post('products', 'Api\ProductsController@store');
    Route::get('products/{sku}/sync', 'Api\ProductsController@publish');

    Route::get("inventory", "Api\InventoryController@index");
    Route::post("inventory", "Api\InventoryController@store");

    Route::apiResource('orders', 'Api\OrdersController');
    Route::apiResource('order/products', 'Api\OrderProductController');
    Route::apiResource('order/shipments', 'Api\OrderShipmentController');

    Route::put('print/order/{order_number}/{view}', 'Api\PrintOrderController@store');

    Route::get('packlist/order', 'Api\PacklistOrderController@index');
    Route::post('packlist/{packlist}', 'Api\PacklistController@store');


    Route::get('printers', 'Api\PrintersController@index');
    Route::put('printers/use/{printerId}', 'Api\PrintersController@use');

    Route::post('invites', 'InvitesController@store');

    Route::apiResource('picks', 'Api\PickController')->except('store');

    Route::resource('widgets', 'Api\WidgetsController');
    Route::resource("rms_api_configuration", "Api\RmsapiConnectionController");
    Route::resource("api2cart_configuration", "Api\Api2cartConnectionController");

    Route::get('users/me', 'Api\UsersController@me');

    // Routes for users with the admin role only
    Route::group(['middleware' => ['role:admin']], function () {
        Route::get('roles', 'Api\RolesController@index')->middleware('can:list roles');
        Route::resource('users', 'Api\UsersController')->middleware('can:manage users');
        Route::resource('configuration', 'Api\ConfigurationsController');
    });


    Route::get('sync', "Api\SyncController@index");
    Route::get('run/maintenance', function () {
        \App\Jobs\Maintenance\RecalculateOrderProductLineCountJob::dispatch();
        \App\Jobs\Maintenance\RecalculateOrderTotalQuantityOrderedJob::dispatch();
        \App\Jobs\Maintenance\RecalculateProductQuantityJob::dispatch();
        \App\Jobs\Maintenance\RecalculateProductQuantityReservedJob::dispatch();
        \App\Jobs\Maintenance\RecalculateOrderProductQuantityPicked::dispatch();
        \App\Jobs\Maintenance\RecalculatePickedAtForPickingOrders::dispatch();
        \App\Jobs\Maintenance\MakeSureOrdersAreOnPicklist::dispatch();
        \App\Jobs\Maintenance\RunPackingWarehouseRuleOnPaidOrdersJob::dispatch();
        \App\Jobs\Maintenance\UpdateAllProcessingIfPaidJob::dispatch();
        \App\Jobs\Maintenance\RefillWebPickingStatusListJob::dispatch();
        return 'Maintenance jobs dispatched';
    });
});
