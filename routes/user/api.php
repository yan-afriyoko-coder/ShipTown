<?php

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

    Route::put('print/order/{order_number}/dpd_label', 'Api\PrintDpdLabelController@store');
    Route::put('print/order/{order_number}/{view}', 'Api\PrintOrderController@store');

    Route::apiResource('run/sync', 'Api\Run\SyncController')->only('index');
    Route::apiResource('run/sync/api2cart', 'Api\Run\SyncApi2CartController')->only('index');
    Route::apiResource('run/hourly/jobs', 'Api\Run\HourlyJobsController', ['as' => 'run.hourly'])->only('index');
    Route::apiResource('run/daily/jobs', 'Api\Run\DailyJobsController', ['as' => 'run.daily'])->only('index');

    Route::apiResource('logs', 'Api\LogController')->only(['index']);

    Route::apiResource('products', 'Api\ProductController')->only(['index', 'store']);
    Route::apiResource('product/aliases', 'Api\Product\ProductAliasController', ['as' => 'product'])->only(['index']);
    Route::apiResource('product/inventory', 'Api\Product\ProductInventoryController')->only(['index', 'store']);
    Route::apiResource('product/tags', 'Api\Product\ProductTagController')->only(['index']);

    Route::apiResource('orders', 'Api\OrderController')->except('destroy');
    Route::apiResource('order/products', 'Api\Order\OrderProductController', ['as' => 'order'])->only(['index', 'update']);
    Route::apiResource('order/shipments', 'Api\Order\OrderShipmentController')->only(['index', 'store']);
    Route::apiResource('order/comments', 'Api\Order\OrderCommentController')->only(['index', 'store']);

    Route::apiResource('picklist', 'Api\PicklistController')->only(['index']);
    Route::apiResource('picklist/picks', 'Api\Picklist\PicklistPickController')->only(['store']);

    // this should be called "order reservation"
    // its job is to fetch next order and block it so no other user gets it again
    Route::apiResource('packlist/order', 'Api\PacklistOrderController', ['as' => 'packlist'])->only(['index']);

    Route::apiResource('settings/user/me', 'Api\Settings\UserMeController')->only(['index', 'store']);
    Route::apiResource('settings/widgets', 'Api\Settings\WidgetController')->only(['store', 'update']);
