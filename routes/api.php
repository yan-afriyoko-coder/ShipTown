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

Route::middleware('auth:api')->group(function() {


    Route::get('products'                           ,'Api\ProductsController@index');
    Route::post('products'                          ,'Api\ProductsController@store');
    Route::get('products/{sku}/sync'                ,'Api\ProductsController@publish');

    Route::get("inventory"                          ,"Api\InventoryController@index");
    Route::post("inventory"                         ,"Api\InventoryController@store");

    Route::get('orders'                             ,'Api\OrdersController@index');
    Route::post('orders'                            ,'Api\OrdersController@store');
    Route::delete('orders/{order_number}'           ,'Api\OrdersController@destroy');

    Route::put('print/order/{order_number}/{view}'  ,'Api\PrintOrderController@store');

    Route::get('picklist'                           ,'Api\PicklistController@index');
    Route::post('picklist/{picklist}'               ,'Api\PicklistController@store');

    Route::get('packlist'                           ,'Api\PacklistController@index');
    Route::post('packlist/{packlist}'               ,'Api\PacklistController@store');

    Route::get('sync'                               ,"Api\SyncController@index");

    Route::get('printers'                           ,'Api\PrintersController@index');
    Route::put('printers/use/{printerId}'           ,'Api\PrintersController@use');

    Route::post('invites'                           ,'InvitesController@store');

    Route::resource('widgets'                     ,'Api\WidgetsController');
    Route::resource("rms_api_configuration"       ,"Api\RmsapiConnectionController");
    Route::resource("api2cart_configuration"      ,"Api\Api2cartConnectionController");

    // Routes for users with the admin role only
    Route::group(['middleware' => ['role:admin']], function () {
        Route::get('roles'                  ,'Api\RolesController@index')->middleware('can:list roles');
        Route::resource('users'           ,'UsersController')->middleware('can:manage users');
        Route::resource('configuration'   ,'Api\ConfigurationsController');
    });
});




