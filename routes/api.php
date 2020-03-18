<?php

use Illuminate\Http\Request;

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
    Route::get("user/configuration", "UserConfigurationController@show");
    Route::post("user/configuration", "UserConfigurationController@store");

    Route::get('products', 'ProductsController@index');
    Route::post('products', 'ProductsController@store');
    Route::get('products/{sku}/sync', 'ProductsController@publish');

    Route::get("inventory", "InventoryController@index");
    Route::post("inventory", "InventoryController@store");

    Route::get('orders', 'OrdersController@index');
    Route::post('orders', 'OrdersController@store');
    Route::delete('orders/{order_number}', 'OrdersController@destroy');

    Route::get("import/orders/api2cart", "OrdersController@importFromApi2Cart");

    Route::get("import/orders/from/api2cart", "ImportController@importOrdersFromApi2cart");

});



