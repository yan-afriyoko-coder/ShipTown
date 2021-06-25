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
Route::middleware('auth:api')->group(function () {
    Route::put('print/order/{order_number}/dpd_label', 'Api\PrintDpdLabelController@store');
    Route::put('print/order/{order_number}/{view}', 'Api\PrintOrderController@store');

    Route::apiResource('run/sync', 'Api\Run\SyncController')->only('index');
    Route::apiResource('run/sync/api2cart', 'Api\Run\SyncApi2CartController')->only('index');
    Route::apiResource('run/hourly/jobs', 'Api\Run\HourlyJobsController', ['as' => 'run.hourly'])->only('index');
    Route::apiResource('run/daily/jobs', 'Api\Run\DailyJobsController', ['as' => 'run.daily'])->only('index');

    Route::apiResource('logs', 'Api\LogController')->only(['index']);

    Route::apiResource('products', 'Api\ProductController')->only(['index','store']);
    Route::apiResource('product/aliases', 'Api\Product\ProductAliasController', ['as' => 'product'])->only(['index']);
    Route::apiResource('product/inventory', 'Api\Product\ProductInventoryController')->only(['index','store']);
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


    Route::apiResource('settings/user/me', 'Api\Settings\UserMeController')->only(['index','store']);
    Route::apiResource('settings/widgets', 'Api\Settings\WidgetController')->only(['store','update']);

    Route::group(['prefix' => 'settings', 'namespace' => 'Api\Settings', 'as' => 'api.settings.'], function () {

        // modules
        Route::group(['prefix' => 'modules', 'namespace' => 'Module', 'as' => 'module.'], function () {
            // api2cart
            Route::group(['prefix' => 'api2cart', 'namespace' => 'Api2cart', 'as' => 'api2cart.'], function () {
                Route::apiResource('connections', "Api2cartConnectionController")->only(['index', 'store', 'destroy']);
                Route::apiResource('products', "ProductsController")->only(['index']);
            });

            // dpdireland
            Route::group(['prefix' => 'dpd-ireland', 'namespace' => 'DpdIreland', 'as' => 'dpd-ireland.'], function () {
                Route::apiResource('', 'DpdIrelandController')->only(['index', 'store', 'destroy']);
            });

            // printnode
            Route::group(['prefix' => 'printnode', 'namespace' => 'Printnode', 'as' => 'printnode.'], function () {
                Route::apiResource('printers', 'PrinterController')->only(['index']);
                Route::apiResource('printjobs', 'PrintJobController')->only(['store']);
                Route::apiResource('clients', 'ClientController')->only(['index', 'store']);
            });
            // rms_api
            Route::group(['prefix' => 'rms_api', 'namespace' => 'Rmsapi', 'as' => 'rmsapi.'], function () {
                Route::apiResource('connections', "RmsapiConnectionController")->only(['index', 'store', 'destroy']);
            });
        });
    });

    // Routes for users with the admin role only
    Route::middleware('role:admin')->group(function () {
        Route::apiResource('configuration', 'Api\Settings\ConfigurationController')->only(['store','show']);
        Route::apiResource('admin/user/invites', 'Api\Admin\UserInviteController')->only(['store']);
        Route::apiResource('admin/user/roles', 'Api\Admin\UserRoleController', ['as' => 'admin.users'])->only(['index'])->middleware('can:list roles');
        Route::apiResource('admin/users', 'Api\Admin\UserController')->only(['index','show','update','destroy'])->middleware('can:manage users');
    });
});
