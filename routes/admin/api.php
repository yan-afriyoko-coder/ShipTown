<?php

use App\Http\Controllers\Api;
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
Route::apiResource('settings/modules', Api\ModuleController::class, ['as' => 'api.settings'])->only(['index', 'update']);
Route::apiResource('settings/order-statuses', Api\OrderStatusController::class, ['as' => 'api.settings'])->only(['index', 'store', 'update', 'destroy']);
Route::apiResource('settings/mail-templates', Api\MailTemplateController::class, ['as' => 'api.settings'])->only(['index', 'update']);
Route::apiResource('settings/navigation-menu', Api\NavigationMenuController::class, ['as' => 'api.settings'])->only(['index', 'store', 'update', 'destroy']);
Route::apiResource('settings/configurations', Api\ConfigurationController::class, ['as' => 'api.settings'])->only(['index', 'store']);

Route::apiResource('settings/modules/api2cart/connections', Api\Modules\Api2cart\Api2cartConnectionController::class, ['as' => 'api.settings.module.api2cart'])->only(['index', 'store', 'destroy']);
Route::apiResource('settings/modules/api2cart/products', Api\Modules\Api2cart\ProductsController::class, ['as' => 'api.settings.module.api2cart'])->only(['index']);
Route::apiResource('settings/modules/dpd-ireland/connections', Api\Modules\DpdIreland\DpdIrelandController::class, ['as' => 'api.settings.module.dpd-ireland'])->only(['index', 'store', 'destroy']);
Route::apiResource('settings/modules/printnode/printjobs', Api\Modules\Printnode\PrintJobController::class, ['as' => 'api.settings.module.printnode'])->only(['store']);
Route::apiResource('settings/modules/printnode/clients', Api\Modules\Printnode\ClientController::class, ['as' => 'api.settings.module.printnode'])->only(['index', 'store', 'destroy']);
Route::apiResource('settings/modules/rms_api/connections', Api\Modules\Rmsapi\RmsapiConnectionController::class, ['as' => 'api.settings.module.rmsapi'])->only(['index', 'store', 'destroy']);
Route::apiResource('settings/automations/config', Api\Modules\OrderAutomations\ConfigController::class, ['as' => 'api.settings.module.automations'])->only(['index']);
Route::apiResource('settings/automations', Api\Modules\OrderAutomations\AutomationController::class, ['as' => 'api.settings.module'])->only(['index', 'store', 'show', 'update', 'destroy']);
Route::apiResource('modules/autostatus/picking/configuration', Api\Modules\AutoStatus\ConfigurationController::class, ['as' => 'modules.autostatus.picking'])->only('index', 'store');
