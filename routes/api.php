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

Route::middleware('auth:api')->group(function() {
    Route::get('sync', "SyncController@index");

    Route::post('company/configuration', "CompanyController@storeConfiguration");

    Route::get('products', 'ProductsController@index');
    Route::post('products', 'ProductsController@store');
    Route::get('products/{sku}/sync', 'ProductsController@publish');

    Route::get("inventory", "InventoryController@index");
    Route::post("inventory", "InventoryController@store");

    Route::get('orders', 'OrdersController@index');
    Route::post('orders', 'OrdersController@store');
    Route::delete('orders/{order_number}', 'OrdersController@destroy');

    Route::resource("rms_api_configuration", "RmsapiConnectionController");

    Route::resource("api2cart_configuration", "Api2cartConnectionController");

    Route::resource('widgets', 'WidgetsController');

    Route::post('invites', 'InvitesController@store');

    Route::resource('users', 'UsersController')->middleware('can:manage users');

    Route::get('roles', 'RolesController@index')->middleware('can:list roles');
});



