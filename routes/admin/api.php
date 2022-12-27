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
Route::apiResource('admin/user/roles', Api\UserRoleController::class, ['as' => 'admin.users'])->only(['index']);
Route::apiResource('admin/users', Api\UserController::class);
Route::apiResource('settings/modules', Api\ModuleController::class, ['as' => 'api.settings'])->only(['index', 'update']);
Route::apiResource('settings/order-statuses', Api\OrderStatusController::class, ['as' => 'api.settings'])->only(['index', 'store', 'update', 'destroy']);
Route::apiResource('settings/mail-templates', Api\Settings\MailTemplateController::class, ['as' => 'api.settings'])->only(['index', 'update']);
Route::apiResource('settings/navigation-menu', Api\Settings\NavigationMenuController::class, ['as' => 'api.settings'])->only(['index', 'store', 'update', 'destroy']);
Route::apiResource('settings/configurations', Api\Settings\ConfigurationController::class, ['as' => 'api.settings'])->only(['index', 'store']);
Route::apiResource('settings/modules/api2cart/connections', Api\Settings\Module\Api2cart\Api2cartConnectionController::class, ['as' => 'api.settings.module.api2cart'])->only(['index', 'store', 'destroy']);
Route::apiResource('settings/modules/api2cart/products', Api\Settings\Module\Api2cart\ProductsController::class, ['as' => 'api.settings.module.api2cart'])->only(['index']);
Route::apiResource('settings/modules/dpd-ireland/connections', Api\Settings\Module\DpdIreland\DpdIrelandController::class, ['as' => 'api.settings.module.dpd-ireland'])->only(['index', 'store', 'destroy']);
Route::apiResource('settings/modules/printnode/printjobs', Api\Settings\Module\Printnode\PrintJobController::class, ['as' => 'api.settings.module.printnode'])->only(['store']);
Route::apiResource('settings/modules/printnode/clients', Api\Settings\Module\Printnode\ClientController::class, ['as' => 'api.settings.module.printnode'])->only(['index', 'store', 'destroy']);
Route::apiResource('settings/modules/rms_api/connections', Api\Settings\Module\Rmsapi\RmsapiConnectionController::class, ['as' => 'api.settings.module.rmsapi'])->only(['index', 'store', 'destroy']);
Route::get('settings/automations/config', [Api\Settings\Module\Automation\AutomationController::class, 'getConfig'])->name('api.settings.module.automations.config');
Route::apiResource('settings/automations', Api\Settings\Module\Automation\AutomationController::class, ['as' => 'api.settings.module'])->only(['index', 'store', 'show', 'update', 'destroy']);
Route::apiResource('modules/autostatus/picking/configuration', Api\Modules\AutoStatus\ConfigurationController::class, ['as' => 'modules.autostatus.picking'])->only('index', 'store');
