<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::middleware('auth:api')->group(function() {
    Route::get('/', function () {
        return view('welcome');
    });


});

Auth::routes();



Route::get('/settings', 'SettingsController@index')->name('settings');
Route::get('/products', 'ProductsWebController@index')->name('products');
Route::get('/products/{sku}/sync', 'ProductSyncController@index')->name('productsSync');
