<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Routes for users with the admin role only]
Route::apiResource('admin/user/roles', 'Api\Admin\UserRoleController', ['as' => 'admin.users'])->only(['index'])->middleware('can:list roles');
Route::apiResource('admin/users', 'Api\Admin\UserController')->only(['index', 'store', 'show', 'update', 'destroy'])->middleware('can:manage users');

Route::apiResource('modules/autostatus/picking/configuration', 'Api\Modules\AutoStatus\ConfigurationController', ['as' => 'modules.autostatus.picking'])->only('index', 'store');

Route::group(['prefix' => 'settings', 'namespace' => 'Api\Settings', 'as' => 'api.settings.'], function () {
    Route::apiResource('modules', 'ModuleController')->only(['index', 'update']);
    Route::apiResource('order-statuses', 'OrderStatusController')->only(['index', 'store', 'update', 'destroy']);
    Route::apiResource('mail-templates', 'MailTemplateController')->only(['index', 'update']);
    Route::apiResource('navigation-menu', 'NavigationMenuController')->only(['index', 'store', 'update', 'destroy']);
    Route::apiResource('warehouses', 'WarehouseController')->only(['index', 'store', 'update', 'destroy']);

    Route::apiResource('configurations', 'ConfigurationController')->only(['index', 'store']);

    // modules
    Route::group(['prefix' => 'modules', 'namespace' => 'Module', 'as' => 'module.'], function () {
        // api2cart
        Route::group(['prefix' => 'api2cart', 'namespace' => 'Api2cart', 'as' => 'api2cart.'], function () {
            Route::apiResource('connections', 'Api2cartConnectionController')->only(['index', 'store', 'destroy']);
            Route::apiResource('products', 'ProductsController')->only(['index']);
        });

        // dpdireland
        Route::group(['prefix' => 'dpd-ireland', 'namespace' => 'DpdIreland', 'as' => 'dpd-ireland.'], function () {
            Route::apiResource('connections', 'DpdIrelandController')->only(['index', 'store', 'destroy']);
        });

        // printnode
        Route::group(['prefix' => 'printnode', 'namespace' => 'Printnode', 'as' => 'printnode.'], function () {
            Route::apiResource('printjobs', 'PrintJobController')->only(['store']);
            Route::apiResource('clients', 'ClientController')->only(['index', 'store', 'destroy']);
        });
        // rms_api
        Route::group(['prefix' => 'rms_api', 'namespace' => 'Rmsapi', 'as' => 'rmsapi.'], function () {
            Route::apiResource('connections', 'RmsapiConnectionController')->only(['index', 'store', 'destroy']);
        });

        // Automations
        Route::get('automations/config', 'Automation\AutomationController@getConfig')->name('automations.config');
        Route::apiResource('automations', 'Automation\AutomationController')->only(['index', 'store', 'show', 'update', 'destroy']);
    });
});
