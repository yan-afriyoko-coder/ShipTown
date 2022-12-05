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

Route::prefix('settings')->namespace('Api\Settings')->name('api.settings.')->group(function () {
    Route::apiResource('modules', 'ModuleController')->only(['index', 'update']);
    Route::apiResource('order-statuses', 'OrderStatusController')->only(['index', 'store', 'update', 'destroy']);
    Route::apiResource('mail-templates', 'MailTemplateController')->only(['index', 'update']);
    Route::apiResource('navigation-menu', 'NavigationMenuController')->only(['index', 'store', 'update', 'destroy']);

    Route::apiResource('configurations', 'ConfigurationController')->only(['index', 'store']);

    // modules
    Route::prefix('modules')->namespace('Module')->name('module.')->group(function () {
        // api2cart
        Route::prefix('api2cart')->namespace('Api2cart')->name('api2cart.')->group(function () {
            Route::apiResource('connections', 'Api2cartConnectionController')->only(['index', 'store', 'destroy']);
            Route::apiResource('products', 'ProductsController')->only(['index']);
        });

        // dpdireland
        Route::prefix('dpd-ireland')->namespace('DpdIreland')->name('dpd-ireland.')->group(function () {
            Route::apiResource('connections', 'DpdIrelandController')->only(['index', 'store', 'destroy']);
        });

        // printnode
        Route::prefix('printnode')->namespace('Printnode')->name('printnode.')->group(function () {
            Route::apiResource('printjobs', 'PrintJobController')->only(['store']);
            Route::apiResource('clients', 'ClientController')->only(['index', 'store', 'destroy']);
        });
        // rms_api
        Route::prefix('rms_api')->namespace('Rmsapi')->name('rmsapi.')->group(function () {
            Route::apiResource('connections', 'RmsapiConnectionController')->only(['index', 'store', 'destroy']);
        });

        // Automations
        Route::get('automations/config', 'Automation\AutomationController@getConfig')->name('automations.config');
        Route::apiResource('automations', 'Automation\AutomationController')->only(['index', 'store', 'show', 'update', 'destroy']);
    });
});
